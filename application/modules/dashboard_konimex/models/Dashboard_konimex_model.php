<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_konimex_model extends CI_Model
{
    /**
     * Membangun klausa WHERE untuk hierarki wilayah & salesman
     */
    private function build_salesman_filter($data, $table_alias = 'a', $salesman_col = 'salesmanid')
    {
        $where = "";
        $col = !empty($table_alias) ? "{$table_alias}.{$salesman_col}" : $salesman_col;

        if (!empty($data['salesmanid'])) {
            if (is_array($data['salesmanid'])) {
                $escaped = array_map([$this->db, 'escape_str'], $data['salesmanid']);
                $where .= " AND {$col} IN ('" . implode("','", $escaped) . "')";
            } else {
                $where .= " AND {$col} = '" . $this->db->escape_str($data['salesmanid']) . "'";
            }
        } elseif (!empty($data['subareaid'])) {
            $where .= " AND {$col} IN (SELECT DISTINCT salesmanid FROM m_salesman_area WHERE subareaid = '" . $this->db->escape_str($data['subareaid']) . "')";
        } elseif (!empty($data['areaid'])) {
            $where .= " AND {$col} IN (SELECT DISTINCT salesmanid FROM m_salesman_area WHERE areaid = '" . $this->db->escape_str($data['areaid']) . "')";
        } elseif (!empty($data['regionalid'])) {
            $where .= " AND {$col} IN (SELECT DISTINCT salesmanid FROM m_salesman_area WHERE regionalid = '" . $this->db->escape_str($data['regionalid']) . "')";
        } else {
            // Restrict level session jika ada
            if (!empty($data['restrict_level']) && !empty($data['usersession'])) {
                $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
                if ($restrict_query) {
                    $where .= " AND {$col} IN ({$restrict_query})";
                }
            }
        }

        return $where;
    }

    /**
     * 1. LOAD SUMMARY 7 KPI UTAMA (LEVEL 1)
     */
    public function load_summary_kpi($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_tsa = $this->build_salesman_filter($data, 'tsa', 'salesmanid');
        $filter_salesman_rrk = $this->build_salesman_filter($data, 'tr', 'salesmanid');
        $filter_salesman_dub = $this->build_salesman_filter($data, 'rdv', 'salesmanid');
        $filter_salesman_prd = $this->build_salesman_filter($data, 'rpv', 'salesmanid');
        $filter_salesman_spc = $this->build_salesman_filter($data, 'rsv', 'salesmanid');
        $filter_salesman_gen = $this->build_salesman_filter($data, 'a', 'salesmanid');

        // -------------------------------------------------------------
        // A. ABSENSI & KEHADIRAN
        // -------------------------------------------------------------
        $sql_absensi = "
            SELECT 
                COUNT(CASE WHEN tsa.status = 'H' THEN 1 END) AS total_hadir,
                COUNT(CASE WHEN tsa.status = 'S' THEN 1 END) AS total_sakit,
                COUNT(CASE WHEN tsa.status = 'C' THEN 1 END) AS total_cuti,
                COUNT(CASE WHEN tsa.status = 'HF' THEN 1 END) AS total_hf,
                COUNT(1) AS total_record
            FROM t_sales_absensi tsa
            WHERE tsa.periode BETWEEN ? AND ?
            {$filter_salesman_tsa}
        ";
        $res_absensi = $this->db->query($sql_absensi, [$start_date, $end_date])->row_array();

        // -------------------------------------------------------------
        // B. CALL & EFFECTIVE CALL (EC)
        // -------------------------------------------------------------
        $sql_call = "
            SELECT 
                COUNT(1) AS total_call,
                COUNT(CASE WHEN tr.customerid IN (
                    SELECT tm.customerid FROM t_sales_master tm WHERE tm.tanggal = tr.periode AND tm.salesmanid = tr.salesmanid
                ) THEN 1 END) AS total_effective_call
            FROM t_sales_rrk_trans tr
            WHERE tr.periode BETWEEN ? AND ?
            {$filter_salesman_rrk}
        ";
        $res_call = $this->db->query($sql_call, [$start_date, $end_date])->row_array();

        // -------------------------------------------------------------
        // C. KETERCAPAIAN KAT A & USER WAJIB (DUB)
        // -------------------------------------------------------------
        $sql_dub = "
            SELECT 
                IFNULL(SUM(rdv.actual_call_planned), 0) AS actual_kat_a,
                IFNULL(SUM(rdv.actual_call_visit), 0) AS actual_user_wajib,
                IFNULL(SUM(t.target_dub), 0) AS target_kat_a,
                IFNULL(SUM(t.target_call_visit), 0) AS target_user_wajib
            FROM rekap_dub_visit rdv
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_dub * rmt.target_call_dub) AS target_dub,
                    SUM(rmt.target_call_visit * rmt.target_hk) AS target_call_visit
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = ?
                GROUP BY ar.role_name
            ) t ON t.role_name = rdv.tipe_sales
            WHERE rdv.tanggal BETWEEN ? AND ?
            {$filter_salesman_dub}
        ";
        $res_dub = $this->db->query($sql_dub, ["{$year}-{$month}", $start_date, $end_date])->row_array();

        // -------------------------------------------------------------
        // D. KETERCAPAIAN DETAILING & SELLING PRODUK
        // -------------------------------------------------------------
        $sql_produk = "
            SELECT 
                IFNULL(SUM(rpv.actual_visit), 0) AS actual_detailing,
                IFNULL(SUM(rpv.actual_qty), 0) AS actual_selling_qty,
                IFNULL(SUM(t.target_detailing), 0) AS target_detailing,
                IFNULL(SUM(t.target_selling_qty), 0) AS target_selling_qty
            FROM rekap_produk_visit rpv
            LEFT JOIN (
                SELECT 
                    mpt.product_id,
                    SUM(mpt.target) AS target_detailing,
                    SUM(mpt.target_qty) AS target_selling_qty
                FROM m_sales_produk_target mpt
                WHERE CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
                GROUP BY mpt.product_id
            ) t ON t.product_id COLLATE utf8mb4_general_ci = rpv.product_id COLLATE utf8mb4_general_ci
            WHERE rpv.tanggal BETWEEN ? AND ?
            {$filter_salesman_prd}
        ";
        $res_produk = $this->db->query($sql_produk, ["{$year}-{$month}", $start_date, $end_date])->row_array();

        // -------------------------------------------------------------
        // E. JUMLAH JOIN ACTIVITY (SUPERVISI)
        // -------------------------------------------------------------
        $sql_join = "
            SELECT COUNT(1) AS total_detailing
            FROM trx_visit_detailing a
            WHERE a.periode BETWEEN ? AND ?
            {$filter_salesman_gen}
        ";
        $res_join = $this->db->query($sql_join, [$start_date, $end_date])->row_array();

        // Kalkulasi Persentase
        $total_hadir = (int)($res_absensi['total_hadir'] ?? 0);
        $total_absen_rec = (int)($res_absensi['total_record'] ?? 0);
        $pct_absensi = $total_absen_rec > 0 ? round(($total_hadir / $total_absen_rec) * 100, 1) : 0;

        $total_call = (int)($res_call['total_call'] ?? 0);
        $total_ec = (int)($res_call['total_effective_call'] ?? 0);
        $pct_ec = $total_call > 0 ? round(($total_ec / $total_call) * 100, 1) : 0;

        $act_kat_a = (float)($res_dub['actual_kat_a'] ?? 0);
        $tgt_kat_a = (float)($res_dub['target_kat_a'] ?? 0);
        $pct_kat_a = $tgt_kat_a > 0 ? round(($act_kat_a / $tgt_kat_a) * 100, 1) : 0;

        $act_user_wajib = (float)($res_dub['actual_user_wajib'] ?? 0);
        $tgt_user_wajib = (float)($res_dub['target_user_wajib'] ?? 0);
        $pct_user_wajib = $tgt_user_wajib > 0 ? round(($act_user_wajib / $tgt_user_wajib) * 100, 1) : 0;

        $act_detailing = (float)($res_produk['actual_detailing'] ?? 0);
        $tgt_detailing = (float)($res_produk['target_detailing'] ?? 0);
        $pct_detailing = $tgt_detailing > 0 ? round(($act_detailing / $tgt_detailing) * 100, 1) : 0;

        $act_selling = (float)($res_produk['actual_selling_qty'] ?? 0);
        $tgt_selling = (float)($res_produk['target_selling_qty'] ?? 0);
        $pct_selling = $tgt_selling > 0 ? round(($act_selling / $tgt_selling) * 100, 1) : 0;

        $total_join = (int)($res_join['total_detailing'] ?? 0);

        return [
            'periode' => [
                'tahun' => $year,
                'bulan' => $month,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
            'kpi' => [
                'absensi' => [
                    'title' => 'Kehadiran TPE',
                    'actual' => $total_hadir,
                    'total' => $total_absen_rec,
                    'percentage' => $pct_absensi,
                    'sakit' => (int)($res_absensi['total_sakit'] ?? 0),
                    'cuti' => (int)($res_absensi['total_cuti'] ?? 0),
                    'hf' => (int)($res_absensi['total_hf'] ?? 0),
                ],
                'call' => [
                    'title' => 'Call & Effective Call',
                    'actual_call' => $total_call,
                    'actual_ec' => $total_ec,
                    'percentage_ec' => $pct_ec,
                ],
                'kategori_a' => [
                    'title' => 'Ketercapaian Kat A',
                    'actual' => $act_kat_a,
                    'target' => $tgt_kat_a,
                    'percentage' => $pct_kat_a,
                ],
                'user_wajib' => [
                    'title' => 'Ketercapaian User Wajib',
                    'actual' => $act_user_wajib,
                    'target' => $tgt_user_wajib,
                    'percentage' => $pct_user_wajib,
                ],
                'detailing_produk' => [
                    'title' => 'Detailing Produk',
                    'actual' => $act_detailing,
                    'target' => $tgt_detailing,
                    'percentage' => $pct_detailing,
                ],
                'selling_produk' => [
                    'title' => 'Selling Produk Wajib',
                    'actual' => $act_selling,
                    'target' => $tgt_selling,
                    'percentage' => $pct_selling,
                ],
                'join_activity' => [
                    'title' => 'Join Activity (Supervisi)',
                    'actual' => $total_join,
                ],
            ]
        ];
    }

    /**
     * 2. LOAD TREND BULANAN (JAN - DES)
     */
    public function load_trend_monthly($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $month_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $filter_salesman_dub = $this->build_salesman_filter($data, 'rdv', 'salesmanid');
        $filter_salesman_prd = $this->build_salesman_filter($data, 'rpv', 'salesmanid');
        $filter_salesman_rrk = $this->build_salesman_filter($data, 'tr', 'salesmanid');

        // Query Trend Kunjungan DUB per bulan
        $sql_trend_dub = "
            SELECT 
                DATE_FORMAT(rdv.tanggal, '%m') AS bulan,
                IFNULL(SUM(rdv.actual_call_planned), 0) AS act_kat_a,
                IFNULL(SUM(rdv.actual_call_visit), 0) AS act_user_wajib
            FROM rekap_dub_visit rdv
            WHERE DATE_FORMAT(rdv.tanggal, '%Y') = ?
            {$filter_salesman_dub}
            GROUP BY DATE_FORMAT(rdv.tanggal, '%m')
        ";
        $res_trend_dub = $this->db->query($sql_trend_dub, [$year])->result_array();
        $dub_map = [];
        foreach ($res_trend_dub as $r) {
            $dub_map[$r['bulan']] = $r;
        }

        // Query Trend Detailing & Selling per bulan
        $sql_trend_prd = "
            SELECT 
                DATE_FORMAT(rpv.tanggal, '%m') AS bulan,
                IFNULL(SUM(rpv.actual_visit), 0) AS act_detailing,
                IFNULL(SUM(rpv.actual_qty), 0) AS act_selling
            FROM rekap_produk_visit rpv
            WHERE DATE_FORMAT(rpv.tanggal, '%Y') = ?
            {$filter_salesman_prd}
            GROUP BY DATE_FORMAT(rpv.tanggal, '%m')
        ";
        $res_trend_prd = $this->db->query($sql_trend_prd, [$year])->result_array();
        $prd_map = [];
        foreach ($res_trend_prd as $r) {
            $prd_map[$r['bulan']] = $r;
        }

        // Query Trend Call & EC per bulan
        $sql_trend_call = "
            SELECT 
                DATE_FORMAT(tr.periode, '%m') AS bulan,
                COUNT(1) AS act_call
            FROM t_sales_rrk_trans tr
            WHERE DATE_FORMAT(tr.periode, '%Y') = ?
            {$filter_salesman_rrk}
            GROUP BY DATE_FORMAT(tr.periode, '%m')
        ";
        $res_trend_call = $this->db->query($sql_trend_call, [$year])->result_array();
        $call_map = [];
        foreach ($res_trend_call as $r) {
            $call_map[$r['bulan']] = $r;
        }

        $series_kat_a = [];
        $series_user_wajib = [];
        $series_detailing = [];
        $series_selling = [];
        $series_call = [];

        foreach ($months as $m) {
            $series_kat_a[] = (int)($dub_map[$m]['act_kat_a'] ?? 0);
            $series_user_wajib[] = (int)($dub_map[$m]['act_user_wajib'] ?? 0);
            $series_detailing[] = (int)($prd_map[$m]['act_detailing'] ?? 0);
            $series_selling[] = (int)($prd_map[$m]['act_selling'] ?? 0);
            $series_call[] = (int)($call_map[$m]['act_call'] ?? 0);
        }

        return [
            'labels' => $month_labels,
            'series' => [
                'kat_a' => $series_kat_a,
                'user_wajib' => $series_user_wajib,
                'detailing' => $series_detailing,
                'selling' => $series_selling,
                'call' => $series_call,
            ]
        ];
    }

    /**
     * 3. FILTER OPTIONS (CASCADING DROPDOWN)
     */
    public function get_regional_list($data = [])
    {
        return $this->db->select('regionalid, nama_regional')
            ->from('m_area_regional')
            ->order_by('nama_regional', 'ASC')
            ->get()->result_array();
    }

    public function get_area_list($regionalid = '')
    {
        $this->db->select('areaid, nama_area')->from('m_area_areasite');
        if (!empty($regionalid)) {
            $this->db->where('regionalid', $regionalid);
        }
        return $this->db->order_by('nama_area', 'ASC')->get()->result_array();
    }

    public function get_subarea_list($areaid = '')
    {
        $this->db->select('subareaid, nama_area as nama_subarea')->from('m_area_subarea');
        if (!empty($areaid)) {
            $this->db->where('areaid', $areaid);
        }
        return $this->db->order_by('nama_area', 'ASC')->get()->result_array();
    }

    public function get_salesman_list($data = [])
    {
        $this->db->select('s.salesmanid, s.nama_salesman, s.tipe_sales')
            ->from('m_sales_salesman s')
            ->where('s.aktif', 1);

        if (!empty($data['subareaid'])) {
            $this->db->where("s.salesmanid IN (SELECT salesmanid FROM m_salesman_area WHERE subareaid = '" . $this->db->escape_str($data['subareaid']) . "')", NULL, FALSE);
        } elseif (!empty($data['areaid'])) {
            $this->db->where("s.salesmanid IN (SELECT salesmanid FROM m_salesman_area WHERE areaid = '" . $this->db->escape_str($data['areaid']) . "')", NULL, FALSE);
        } elseif (!empty($data['regionalid'])) {
            $this->db->where("s.salesmanid IN (SELECT salesmanid FROM m_salesman_area WHERE regionalid = '" . $this->db->escape_str($data['regionalid']) . "')", NULL, FALSE);
        }

        return $this->db->order_by('s.nama_salesman', 'ASC')->get()->result_array();
    }

    /**
     * 4. LEVEL 2: BREAKDOWN AREA & TPE
     */
    public function load_breakdown_area($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman = $this->build_salesman_filter($data, 's', 'salesmanid');

        $sql = "
            SELECT 
                r.nama_regional,
                ar.nama_area,
                sub.nama_area AS nama_subarea,
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                -- Absensi
                IFNULL(abs.total_hadir, 0) AS total_hadir,
                IFNULL(abs.total_hari, 0) AS total_hari,
                ROUND(CASE WHEN IFNULL(abs.total_hari, 0) > 0 THEN (abs.total_hadir / abs.total_hari) * 100 ELSE 0 END, 1) AS pct_absensi,
                -- Call & EC
                IFNULL(cl.total_call, 0) AS total_call,
                IFNULL(cl.total_ec, 0) AS total_ec,
                ROUND(CASE WHEN IFNULL(cl.total_call, 0) > 0 THEN (cl.total_ec / cl.total_call) * 100 ELSE 0 END, 1) AS pct_ec,
                -- Kat A & User Wajib
                IFNULL(dub.act_kat_a, 0) AS act_kat_a,
                IFNULL(dub_tgt.target_kat_a, 0) AS tgt_kat_a,
                ROUND(CASE WHEN IFNULL(dub_tgt.target_kat_a, 0) > 0 THEN (dub.act_kat_a / dub_tgt.target_kat_a) * 100 ELSE 0 END, 1) AS pct_kat_a,
                IFNULL(dub.act_user_wajib, 0) AS act_user_wajib,
                IFNULL(dub_tgt.target_user_wajib, 0) AS tgt_user_wajib,
                ROUND(CASE WHEN IFNULL(dub_tgt.target_user_wajib, 0) > 0 THEN (dub.act_user_wajib / dub_tgt.target_user_wajib) * 100 ELSE 0 END, 1) AS pct_user_wajib,
                -- Detailing
                IFNULL(dtl.total_detailing, 0) AS total_detailing,
                -- Selling
                IFNULL(prd.act_selling, 0) AS act_selling,
                IFNULL(prd_tgt.target_selling, 0) AS tgt_selling,
                ROUND(CASE WHEN IFNULL(prd_tgt.target_selling, 0) > 0 THEN (prd.act_selling / prd_tgt.target_selling) * 100 ELSE 0 END, 1) AS pct_selling
            FROM m_sales_salesman s
            LEFT JOIN m_salesman_area msa ON msa.salesmanid = s.salesmanid
            LEFT JOIN m_area_regional r ON r.regionalid = msa.regionalid
            LEFT JOIN m_area_areasite ar ON ar.areaid = msa.areaid
            LEFT JOIN m_area_subarea sub ON sub.subareaid = msa.subareaid
            -- Subquery Absensi
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    COUNT(CASE WHEN status = 'H' THEN 1 END) AS total_hadir,
                    COUNT(1) AS total_hari
                FROM t_sales_absensi
                WHERE periode BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY salesmanid
            ) abs ON abs.salesmanid = s.salesmanid
            -- Subquery Call
            LEFT JOIN (
                SELECT 
                    tr.salesmanid,
                    COUNT(1) AS total_call,
                    COUNT(CASE WHEN tr.customerid IN (
                        SELECT tm.customerid FROM t_sales_master tm WHERE tm.tanggal = tr.periode AND tm.salesmanid = tr.salesmanid
                    ) THEN 1 END) AS total_ec
                FROM t_sales_rrk_trans tr
                WHERE tr.periode BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY tr.salesmanid
            ) cl ON cl.salesmanid = s.salesmanid
            -- Subquery DUB Actual
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    SUM(actual_call_planned) AS act_kat_a,
                    SUM(actual_call_visit) AS act_user_wajib
                FROM rekap_dub_visit
                WHERE tanggal BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY salesmanid
            ) dub ON dub.salesmanid = s.salesmanid
            -- Subquery DUB Target
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_dub * rmt.target_call_dub) AS target_kat_a,
                    SUM(rmt.target_call_visit * rmt.target_hk) AS target_user_wajib
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = '{$year}-{$month}'
                GROUP BY ar.role_name
            ) dub_tgt ON dub_tgt.role_name = s.tipe_sales
            -- Subquery Detailing
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    COUNT(1) AS total_detailing
                FROM trx_visit_detailing
                WHERE periode BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY salesmanid
            ) dtl ON dtl.salesmanid = s.salesmanid
            -- Subquery Produk Actual
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    SUM(actual_qty) AS act_selling
                FROM rekap_produk_visit
                WHERE tanggal BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY salesmanid
            ) prd ON prd.salesmanid = s.salesmanid
            -- Subquery Produk Target
            LEFT JOIN (
                SELECT 
                    SUM(target_qty) AS target_selling
                FROM m_sales_produk_target
                WHERE CONCAT(tahun, '-', LPAD(bulan, 2, '0')) = '{$year}-{$month}'
            ) prd_tgt ON 1=1
            WHERE s.aktif = 1
            {$filter_salesman}
            ORDER BY r.nama_regional ASC, ar.nama_area ASC, s.nama_salesman ASC
        ";

        return $this->db->query($sql)->result_array();
    }

    /**
     * 5. LEVEL 2: BREAKDOWN CHANNEL & SPESIALISASI USER
     */
    public function load_breakdown_channel_spesialis($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_dtl = $this->build_salesman_filter($data, 'tvd', 'salesmanid');

        $sql = "
            SELECT 
                COALESCE(NULLIF(TRIM(rs.name), ''), 'Lain-lain / Belum Diisi') AS nama_spesialisasi,
                COALESCE(NULLIF(TRIM(c.typeid), ''), 'Umum / Lainnya') AS jenis_channel,
                COUNT(DISTINCT tvd.user_id) AS total_user,
                COUNT(1) AS total_kunjungan_detailing
            FROM trx_visit_detailing tvd
            LEFT JOIN ref_professional rp ON rp.id = tvd.user_id
            LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
            LEFT JOIN m_customer c ON c.customerid = tvd.customerid
            WHERE tvd.periode BETWEEN ? AND ?
            {$filter_salesman_dtl}
            GROUP BY 
                COALESCE(NULLIF(TRIM(rs.name), ''), 'Lain-lain / Belum Diisi'),
                COALESCE(NULLIF(TRIM(c.typeid), ''), 'Umum / Lainnya')
            ORDER BY total_kunjungan_detailing DESC, nama_spesialisasi ASC
        ";

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    /**
     * 6. LEVEL 2: BREAKDOWN TARGET & REALISASI PRODUK
     */
    public function load_breakdown_produk($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_prd = $this->build_salesman_filter($data, 'rpv', 'salesmanid');

        $sql = "
            SELECT 
                rpv.product_id,
                COALESCE(NULLIF(rpv.nama_invoice, ''), rpv.product_id) AS nama_produk,
                IFNULL(SUM(rpv.actual_visit), 0) AS actual_detailing,
                IFNULL(t.target_detailing, 0) AS target_detailing,
                ROUND(CASE WHEN IFNULL(t.target_detailing, 0) > 0 THEN (SUM(rpv.actual_visit) / t.target_detailing) * 100 ELSE 0 END, 1) AS pct_detailing,
                IFNULL(SUM(rpv.actual_qty), 0) AS actual_selling_qty,
                IFNULL(t.target_selling_qty, 0) AS target_selling_qty,
                ROUND(CASE WHEN IFNULL(t.target_selling_qty, 0) > 0 THEN (SUM(rpv.actual_qty) / t.target_selling_qty) * 100 ELSE 0 END, 1) AS pct_selling
            FROM rekap_produk_visit rpv
            LEFT JOIN (
                SELECT 
                    mpt.product_id,
                    SUM(mpt.target) AS target_detailing,
                    SUM(mpt.target_qty) AS target_selling_qty
                FROM m_sales_produk_target mpt
                WHERE CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
                GROUP BY mpt.product_id
            ) t ON t.product_id COLLATE utf8mb4_general_ci = rpv.product_id COLLATE utf8mb4_general_ci
            WHERE rpv.tanggal BETWEEN ? AND ?
            {$filter_salesman_prd}
            GROUP BY rpv.product_id, rpv.nama_invoice, t.target_detailing, t.target_selling_qty
            ORDER BY actual_detailing DESC, rpv.nama_invoice ASC
        ";

        return $this->db->query($sql, ["{$year}-{$month}", $start_date, $end_date])->result_array();
    }

    /**
     * 7. LEVEL 3: DETAIL AKTIVITAS HARIAN TPE (DRILLDOWN LOGS)
     */
    public function load_detail_tpe_activity($data)
    {
        $salesmanid = !empty($data['salesmanid']) ? $data['salesmanid'] : '';
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        if (empty($salesmanid)) {
            return [
                'salesman' => null,
                'kunjungan' => [],
                'detailing' => [],
                'absensi' => []
            ];
        }

        // Info Profil Salesman & Wilayah
        $sql_salesman = "
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                r.nama_regional,
                ar.nama_area,
                sub.nama_area AS nama_subarea
            FROM m_sales_salesman s
            LEFT JOIN m_salesman_area msa ON msa.salesmanid = s.salesmanid
            LEFT JOIN m_area_regional r ON r.regionalid = msa.regionalid
            LEFT JOIN m_area_areasite ar ON ar.areaid = msa.areaid
            LEFT JOIN m_area_subarea sub ON sub.subareaid = msa.subareaid
            WHERE s.salesmanid = ?
            LIMIT 1
        ";
        $salesman_info = $this->db->query($sql_salesman, [$salesmanid])->row_array();

        // A. Log Kunjungan / Call Harian
        $sql_kunjungan = "
            SELECT 
                tr.periode AS tanggal,
                tr.customerid,
                COALESCE(c.nama_customer, tr.customerid) AS nama_outlet,
                COALESCE(c.typeid, '-') AS tipe_outlet,
                tr.check_in,
                tr.check_out,
                TIMESTAMPDIFF(MINUTE, tr.check_in, tr.check_out) AS durasi_menit,
                CASE WHEN tm.customerid IS NOT NULL THEN 1 ELSE 0 END AS is_effective_call,
                CASE WHEN tr.crc_time IS NOT NULL THEN 'Ya' ELSE '-' END AS has_crc,
                CASE WHEN tr.promo_time IS NOT NULL THEN 'Ya' ELSE '-' END AS has_promo,
                CASE WHEN tr.competitor_time IS NOT NULL THEN 'Ya' ELSE '-' END AS has_competitor,
                CASE WHEN tr.sos_time IS NOT NULL THEN 'Ya' ELSE '-' END AS has_sos
            FROM t_sales_rrk_trans tr
            LEFT JOIN m_customer c ON c.customerid = tr.customerid
            LEFT JOIN t_sales_master tm ON tm.tanggal = tr.periode AND tm.salesmanid = tr.salesmanid AND tm.customerid = tr.customerid
            WHERE tr.salesmanid = ?
              AND tr.periode BETWEEN ? AND ?
            ORDER BY tr.periode DESC, tr.check_in ASC
        ";
        $kunjungan_list = $this->db->query($sql_kunjungan, [$salesmanid, $start_date, $end_date])->result_array();

        // B. Log Detailing Dokter / User
        $sql_detailing = "
            SELECT 
                tvd.periode AS tanggal,
                COALESCE(rp.nama_professional, tvd.user_id) AS nama_dokter,
                COALESCE(rs.name, 'Umum') AS spesialisasi,
                COALESCE(c.nama_customer, tvd.customerid) AS nama_instansi,
                COALESCE(c.typeid, '-') AS jenis_channel,
                COALESCE(tvd.array_product, '-') AS kode_produk,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT COALESCE(p.nama_invoice, p.productid) ORDER BY p.productid SEPARATOR ', ')
                        FROM m_product p
                        WHERE FIND_IN_SET(p.productid, tvd.array_product) > 0
                    ),
                    (
                        SELECT GROUP_CONCAT(DISTINCT COALESCE(mp.nama_invoice, mp.productid) ORDER BY mp.productid SEPARATOR ', ')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, tvd.array_product) > 0
                    ),
                    tvd.array_product,
                    '-'
                ) AS nama_produk,
                CASE 
                    WHEN tvd.status = 3 THEN 'Valid'
                    WHEN tvd.status = 5 THEN 'Tidak Valid'
                    ELSE 'Tercatat'
                END AS status_detailing
            FROM trx_visit_detailing tvd
            LEFT JOIN ref_professional rp ON rp.id = tvd.user_id
            LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
            LEFT JOIN m_customer c ON c.customerid = tvd.customerid
            WHERE tvd.salesmanid = ?
              AND tvd.periode BETWEEN ? AND ?
            ORDER BY tvd.periode DESC, tvd.start_detailing DESC
        ";
        $detailing_list = $this->db->query($sql_detailing, [$salesmanid, $start_date, $end_date])->result_array();

        // C. Log Presensi Parma & Absensi
        $sql_absensi = "
            SELECT 
                tsa.periode AS tanggal,
                tsa.status,
                CASE 
                    WHEN tsa.status = 'H' THEN 'Hadir'
                    WHEN tsa.status = 'S' THEN 'Sakit'
                    WHEN tsa.status = 'C' THEN 'Cuti'
                    WHEN tsa.status = 'HF' THEN 'Half Day / Libur'
                    ELSE tsa.status
                END AS status_label,
                tsa.checkin,
                tsa.checkout,
                tsa.keterangan,
                ap.start_time AS parma_checkin,
                ap.end_time AS parma_checkout,
                ap.start_image AS foto_checkin,
                ap.end_image AS foto_checkout
            FROM t_sales_absensi tsa
            LEFT JOIN attendance_parma ap ON ap.salesmanid = tsa.salesmanid AND ap.periode = tsa.periode
            WHERE tsa.salesmanid = ?
              AND tsa.periode BETWEEN ? AND ?
            ORDER BY tsa.periode DESC
        ";
        $absensi_list = $this->db->query($sql_absensi, [$salesmanid, $start_date, $end_date])->result_array();

        return [
            'salesman' => $salesman_info,
            'kunjungan' => $kunjungan_list,
            'detailing' => $detailing_list,
            'absensi' => $absensi_list
        ];
    }
}
