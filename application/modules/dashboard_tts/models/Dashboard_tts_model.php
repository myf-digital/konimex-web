<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_tts_model extends CI_Model
{
    public function get_role_sales()
    {
        $param_role_query = $this->db->query("SELECT value FROM ref_param_global WHERE key_param = 'key_role_sales'")->result_array();
        $valid_roles = [];
        foreach ($param_role_query as $rq) {
            if (!empty($rq['value'])) {
                $parts = explode('|', $rq['value']);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '') {
                        $valid_roles[] = $p;
                    }
                }
            }
        }
        return array_values(array_unique($valid_roles));
    }

    public function load_summary_kpi($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_ap  = $this->build_salesman_filter($data, 'ap', 'salesmanid');
        $filter_salesman_tsa = $this->build_salesman_filter($data, 'tsa', 'salesmanid');
        $filter_salesman_rrk = $this->build_salesman_filter($data, 'tr', 'salesmanid');

        // Kehadiran
        $sql_absensi = "
            SELECT 
            (
                SELECT COUNT(DISTINCT ap.salesmanid, ap.periode)
                FROM attendance_parma ap
                LEFT JOIN (
                    SELECT salesmanid, periode, COUNT(1) AS total_detailing
                    FROM trx_visit_detailing
                    WHERE periode BETWEEN ? AND ?
                    GROUP BY salesmanid, periode
                ) vd ON vd.salesmanid = ap.salesmanid AND vd.periode = ap.periode
                WHERE ap.periode BETWEEN ? AND ?
                AND ap.start_time IS NOT NULL
                AND (
                    (DAYOFWEEK(ap.periode) BETWEEN 2 AND 6 AND IFNULL(vd.total_detailing, 0) >= 1)
                    OR (DAYOFWEEK(ap.periode) = 7)
                )
                {$filter_salesman_ap}
            ) AS total_hadir_fisik,
            (
                SELECT COUNT(CASE WHEN tsa.status = 'S' THEN 1 END)
                FROM t_sales_absensi tsa
                WHERE tsa.periode BETWEEN ? AND ?
                {$filter_salesman_tsa}
            ) AS total_sakit,
            (
                SELECT COUNT(CASE WHEN tsa.status = 'C' THEN 1 END)
                FROM t_sales_absensi tsa
                WHERE tsa.periode BETWEEN ? AND ?
                {$filter_salesman_tsa}
            ) AS total_cuti,
            (
                SELECT COUNT(CASE WHEN tsa.status = 'HF' THEN 1 END)
                FROM t_sales_absensi tsa
                WHERE tsa.periode BETWEEN ? AND ?
                {$filter_salesman_tsa}
            ) AS total_izin
        ";
        $res_absensi = $this->db->query($sql_absensi, [
            $start_date, $end_date, // vd subquery
            $start_date, $end_date, // attendance_parma
            $start_date, $end_date, // sakit
            $start_date, $end_date, // cuti
            $start_date, $end_date  // izin
        ])->row_array();

        $hk = get_hari_kerja($start_date, $end_date);

        $sql_sales_count = "
            SELECT COUNT(DISTINCT s.salesmanid) AS total_salesman
            FROM m_sales_salesman s
            LEFT JOIN m_salesman_area msa ON msa.salesmanid = s.salesmanid
            WHERE s.aktif = 1 AND s.nama_salesman NOT LIKE '%vacant%'
            " . $this->build_salesman_filter($data, 's', 'salesmanid');
        $total_salesman = (int)($this->db->query($sql_sales_count)->row()->total_salesman ?? 1);
        $total_hk = $hk * max(1, $total_salesman);

        $hadir_fisik = (int)($res_absensi['total_hadir_fisik'] ?? 0);
        $sakit       = (int)($res_absensi['total_sakit'] ?? 0);
        $cuti        = (int)($res_absensi['total_cuti'] ?? 0);
        $izin        = (int)($res_absensi['total_izin'] ?? 0);

        $total_hadir = $hadir_fisik + $sakit + $cuti + $izin;
        $pct_absensi = $total_hk > 0 ? round(($total_hadir / $total_hk) * 100, 1) : 0;

        // Performa TPE
        $filter_salesman_mss = $this->build_salesman_filter($data, 'mss', 'salesmanid');

        $sql_call = "
            SELECT 
                COUNT(
                    DISTINCT CASE
                        WHEN tvd.salesmanid IS NOT NULL
                            AND rpdd.salesmanid IS NOT NULL
                        THEN CONCAT(tvd.customerid, '-', tvd.periode, '-', tvd.nourut)
                    END
                ) AS total_call,
                COUNT(
                    DISTINCT CASE
                        WHEN tvd.salesmanid IS NOT NULL
                            AND rpdd.salesmanid IS NULL
                        THEN CONCAT(tvd.customerid, '-', tvd.periode, '-', tvd.nourut)
                    END
                ) AS total_extra_call
            FROM m_sales_salesman mss
            LEFT JOIN t_sales_rrk_trans tsrt
                ON tsrt.salesmanid = mss.salesmanid
                AND tsrt.periode BETWEEN ? AND ?
            LEFT JOIN (
                SELECT DISTINCT customerid, salesmanid, periode
                FROM req_pjp_daily_detail
                WHERE periode BETWEEN ? AND ?
            ) rpdd
                ON rpdd.salesmanid = tsrt.salesmanid
                AND rpdd.customerid = tsrt.customerid
                AND rpdd.periode = tsrt.periode
            LEFT JOIN trx_visit_detailing tvd
                ON tvd.salesmanid = tsrt.salesmanid
                AND tvd.customerid = tsrt.customerid
                AND tvd.periode = tsrt.periode
                AND tvd.periode BETWEEN ? AND ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
        ";
        $res_call = $this->db->query($sql_call, [
            $start_date, $end_date, // tsrt
            $start_date, $end_date, // rpdd
            $start_date, $end_date  // tvd
        ])->row_array();

        $sql_target_call = "
            SELECT 
                IFNULL(SUM(rmt.target_call_visit), 0) AS total_target
            FROM m_sales_salesman mss
            LEFT JOIN app_role ar ON ar.role_name = mss.tipe_sales
            LEFT JOIN role_mapping_target rmt ON rmt.role_id = ar.role_id
                AND CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
        ";
        $res_target_call = $this->db->query($sql_target_call, ["{$year}-{$month}"])->row_array();

        $total_planned        = (int)($res_target_call['total_target'] ?? 0);
        $total_call_act       = (int)($res_call['total_call'] ?? 0);
        $total_extra_call     = (int)($res_call['total_extra_call'] ?? 0);
        $total_realisasi_call = $total_call_act + $total_extra_call;
        $pct_call             = $total_planned > 0 ? round(($total_realisasi_call / $total_planned) * 100, 1) : 0;

        // Ketercapaian Kategori
        $sql_kategori_total = "
            SELECT 
                COUNT(DISTINCT tr.customerid) AS total_outlet,
                COUNT(1) AS total_kunjungan
            FROM t_sales_rrk_trans tr
            WHERE tr.periode BETWEEN ? AND ?
            {$filter_salesman_rrk}
        ";
        $res_kat_total = $this->db->query($sql_kategori_total, [$start_date, $end_date])->row_array();
        $total_outlet_kategori = (int)($res_kat_total['total_outlet'] ?? 0);
        $total_kunjungan_kategori = (int)($res_kat_total['total_kunjungan'] ?? 0);

        $sql_kategori_rincian = "
            SELECT 
                COALESCE(NULLIF(TRIM(c.typeid), ''), 'Lainnya') AS typeid,
                COUNT(DISTINCT tr.customerid) AS total_outlet,
                COUNT(1) AS total_kunjungan
            FROM t_sales_rrk_trans tr
            LEFT JOIN m_customer c ON c.customerid = tr.customerid
            WHERE tr.periode BETWEEN ? AND ?
            {$filter_salesman_rrk}
            GROUP BY COALESCE(NULLIF(TRIM(c.typeid), ''), 'Lainnya')
            ORDER BY total_outlet DESC, total_kunjungan DESC
        ";
        $res_kat_rincian = $this->db->query($sql_kategori_rincian, [$start_date, $end_date])->result_array();

        $rincian_parts = [];
        foreach ($res_kat_rincian as $kr) {
            $rincian_parts[] = "{$kr['typeid']}: {$kr['total_outlet']}";
        }
        $rincian_kategori_text = !empty($rincian_parts) ? implode(' | ', $rincian_parts) : '-';

        // User Wajib
        $filter_salesman_tvd = $this->build_salesman_filter($data, 'tvd', 'salesmanid');

        $sql_user_wajib_act = "
            SELECT 
                rp.spesialisasi_id,
                COALESCE(NULLIF(TRIM(rs.name), ''), 'Lainnya') AS nama_spesialisasi,
                COUNT(1) AS total_actual
            FROM trx_visit_detailing tvd
            JOIN ref_professional rp ON rp.id = tvd.user_id
            LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
            WHERE tvd.periode BETWEEN ? AND ?
            {$filter_salesman_tvd}
            GROUP BY rp.spesialisasi_id, COALESCE(NULLIF(TRIM(rs.name), ''), 'Lainnya')
        ";
        $res_uw_act = $this->db->query($sql_user_wajib_act, [$start_date, $end_date])->result_array();

        $act_spec_map = [];
        $total_act_user_wajib = 0;
        foreach ($res_uw_act as $r) {
            $spId = $r['spesialisasi_id'] ?? 0;
            $act_spec_map[$spId] = [
                'nama_spesialisasi' => $r['nama_spesialisasi'],
                'actual' => (int)$r['total_actual']
            ];
            $total_act_user_wajib += (int)$r['total_actual'];
        }

        $sql_user_wajib_tgt = "
            SELECT 
                mst.spesialisasi_id,
                COALESCE(NULLIF(TRIM(mst.nama_spesialisasi), ''), rs.name, 'Lainnya') AS nama_spesialisasi,
                SUM(mst.target) AS total_target
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_spesialis_target mst ON mst.role_id = ar.role_id
                AND CONCAT(mst.tahun, '-', LPAD(mst.bulan, 2, '0')) = ?
            LEFT JOIN ref_spesialisasi rs ON rs.id = mst.spesialisasi_id
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY mst.spesialisasi_id, COALESCE(NULLIF(TRIM(mst.nama_spesialisasi), ''), rs.name, 'Lainnya')
        ";
        $res_uw_tgt = $this->db->query($sql_user_wajib_tgt, ["{$year}-{$month}"])->result_array();

        $tgt_spec_map = [];
        $total_tgt_user_wajib = 0;
        foreach ($res_uw_tgt as $r) {
            $spId = $r['spesialisasi_id'] ?? 0;
            $tgt_spec_map[$spId] = [
                'nama_spesialisasi' => $r['nama_spesialisasi'],
                'target' => (int)$r['total_target']
            ];
            $total_tgt_user_wajib += (int)$r['total_target'];
        }

        $all_spec_ids = array_unique(array_merge(array_keys($act_spec_map), array_keys($tgt_spec_map)));
        $combined_specs = [];
        foreach ($all_spec_ids as $spId) {
            $nama = $act_spec_map[$spId]['nama_spesialisasi'] ?? $tgt_spec_map[$spId]['nama_spesialisasi'] ?? 'Lainnya';
            $act = $act_spec_map[$spId]['actual'] ?? 0;
            $tgt = $tgt_spec_map[$spId]['target'] ?? 0;
            $pct = $tgt > 0 ? round(($act / $tgt) * 100, 1) : 0;
            $combined_specs[] = [
                'spesialisasi_id' => $spId,
                'nama_spesialisasi' => $nama,
                'actual' => $act,
                'target' => $tgt,
                'percentage' => $pct
            ];
        }

        usort($combined_specs, function ($a, $b) {
            if ($b['actual'] === $a['actual']) {
                return $b['target'] <=> $a['target'];
            }
            return $b['actual'] <=> $a['actual'];
        });

        $top4_specs = array_slice($combined_specs, 0, 4);

        $top4_text_parts = [];
        foreach ($top4_specs as $ts) {
            $top4_text_parts[] = "{$ts['nama_spesialisasi']}: {$ts['actual']}/{$ts['target']}";
        }
        $top4_user_wajib_text = !empty($top4_text_parts) ? implode(' | ', $top4_text_parts) : '-';

        $act_user_wajib = $total_act_user_wajib;
        $tgt_user_wajib = $total_tgt_user_wajib;
        $pct_user_wajib = $tgt_user_wajib > 0 ? round(($act_user_wajib / $tgt_user_wajib) * 100, 1) : 0;

        // Produk (Detailing & Selling)
        $sql_tvd_array = "
            SELECT tvd.array_product, COUNT(1) AS cnt
            FROM trx_visit_detailing tvd
            WHERE tvd.periode BETWEEN ? AND ?
              AND tvd.array_product IS NOT NULL
              AND TRIM(tvd.array_product) <> ''
            {$filter_salesman_tvd}
            GROUP BY tvd.array_product
        ";
        $res_tvd_array = $this->db->query($sql_tvd_array, [$start_date, $end_date])->result_array();

        $sql_all_products = "SELECT productid, COALESCE(NULLIF(nama_invoice, ''), productid) AS nama_invoice FROM m_product";
        $all_prod_rows = $this->db->query($sql_all_products)->result_array();
        $prod_name_map = [];
        foreach ($all_prod_rows as $pr) {
            $prod_name_map[trim($pr['productid'])] = $pr['nama_invoice'];
        }

        $act_prd_map = [];
        $total_act_detailing = 0;
        $total_act_selling = 0;

        foreach ($res_tvd_array as $row) {
            $rawList = explode(',', $row['array_product']);
            $count = (int)$row['cnt'];
            foreach ($rawList as $pId) {
                $pId = trim($pId);
                if (empty($pId)) continue;
                if (!isset($act_prd_map[$pId])) {
                    $act_prd_map[$pId] = [
                        'nama_produk' => $prod_name_map[$pId] ?? $pId,
                        'actual_detailing' => 0,
                        'actual_selling' => 0
                    ];
                }
                $act_prd_map[$pId]['actual_detailing'] += $count;
                $act_prd_map[$pId]['actual_selling'] += $count;
                $total_act_detailing += $count;
                $total_act_selling += $count;
            }
        }

        $sql_produk_tgt = "
            SELECT 
                mpt.product_id,
                COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id) AS nama_produk,
                SUM(mpt.target) AS total_target_detailing,
                SUM(mpt.target_qty) AS total_target_selling
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_produk_target mpt ON mpt.role_id = ar.role_id
                AND CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY mpt.product_id, COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id)
        ";
        $res_prd_tgt = $this->db->query($sql_produk_tgt, ["{$year}-{$month}"])->result_array();
        if (empty($res_prd_tgt)) {
            $sql_fb_prd_tgt = "
                SELECT 
                    mpt.product_id,
                    COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id) AS nama_produk,
                    SUM(mpt.target) AS total_target_detailing,
                    SUM(mpt.target_qty) AS total_target_selling
                FROM m_sales_produk_target mpt
                WHERE CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
                GROUP BY mpt.product_id, COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id)
            ";
            $res_prd_tgt = $this->db->query($sql_fb_prd_tgt, ["{$year}-{$month}"])->result_array();
        }

        $tgt_prd_map = [];
        $total_tgt_detailing = 0;
        $total_tgt_selling = 0;
        foreach ($res_prd_tgt as $r) {
            $pId = $r['product_id'];
            $tgt_prd_map[$pId] = [
                'nama_produk' => $r['nama_produk'],
                'target_detailing' => (float)$r['total_target_detailing'],
                'target_selling' => (float)$r['total_target_selling']
            ];
            $total_tgt_detailing += (float)$r['total_target_detailing'];
            $total_tgt_selling += (float)$r['total_target_selling'];
        }

        $all_prd_ids = array_unique(array_merge(array_keys($act_prd_map), array_keys($tgt_prd_map)));
        $combined_detailing = [];
        $combined_selling = [];

        foreach ($all_prd_ids as $pId) {
            $nama = $act_prd_map[$pId]['nama_produk'] ?? $tgt_prd_map[$pId]['nama_produk'] ?? $pId;
            $act_det = $act_prd_map[$pId]['actual_detailing'] ?? 0;
            $tgt_det = $tgt_prd_map[$pId]['target_detailing'] ?? 0;
            $pct_det = $tgt_det > 0 ? round(($act_det / $tgt_det) * 100, 1) : 0;

            $act_sel = $act_prd_map[$pId]['actual_selling'] ?? 0;
            $tgt_sel = $tgt_prd_map[$pId]['target_selling'] ?? 0;
            $pct_sel = $tgt_sel > 0 ? round(($act_sel / $tgt_sel) * 100, 1) : 0;

            $combined_detailing[] = [
                'product_id' => $pId,
                'nama_produk' => $nama,
                'actual' => $act_det,
                'target' => $tgt_det,
                'percentage' => $pct_det
            ];

            $combined_selling[] = [
                'product_id' => $pId,
                'nama_produk' => $nama,
                'actual' => $act_sel,
                'target' => $tgt_sel,
                'percentage' => $pct_sel
            ];
        }

        usort($combined_detailing, function ($a, $b) {
            if ($b['actual'] === $a['actual']) {
                return $b['target'] <=> $a['target'];
            }
            return $b['actual'] <=> $a['actual'];
        });
        $top4_detailing = array_slice($combined_detailing, 0, 4);
        $top4_det_text_parts = [];
        foreach ($top4_detailing as $td) {
            $top4_det_text_parts[] = "{$td['nama_produk']}: {$td['actual']}/{$td['target']}";
        }
        $top4_detailing_text = !empty($top4_det_text_parts) ? implode(' | ', $top4_det_text_parts) : '-';

        usort($combined_selling, function ($a, $b) {
            if ($b['actual'] === $a['actual']) {
                return $b['target'] <=> $a['target'];
            }
            return $b['actual'] <=> $a['actual'];
        });
        $top4_selling = array_slice($combined_selling, 0, 4);
        $top4_sel_text_parts = [];
        foreach ($top4_selling as $ts) {
            $top4_sel_text_parts[] = "{$ts['nama_produk']}: {$ts['actual']}/{$ts['target']}";
        }
        $top4_selling_text = !empty($top4_sel_text_parts) ? implode(' | ', $top4_sel_text_parts) : '-';

        $act_detailing = $total_act_detailing;
        $tgt_detailing = $total_tgt_detailing;
        $pct_detailing = $tgt_detailing > 0 ? round(($act_detailing / $tgt_detailing) * 100, 1) : 0;

        $act_selling = $total_act_selling;
        $tgt_selling = $total_tgt_selling;
        $pct_selling = $tgt_selling > 0 ? round(($act_selling / $tgt_selling) * 100, 1) : 0;

        // Join Visit
        $filter_salesman_rvp = $this->build_salesman_filter($data, 'rvp', 'salesmanid');

        $sql_join = "
            SELECT 
                COUNT(1) AS total_join,
                COUNT(CASE WHEN rvp.status = '1' THEN 1 END) AS total_pending,
                COUNT(CASE WHEN rvp.status = '3' THEN 1 END) AS total_approved,
                COUNT(CASE WHEN rvp.status = '3' AND (rvp.visit_keterangan LIKE '%Sudah Detailing%' OR rvp.keterangan LIKE '%Sudah Detailing%') THEN 1 END) AS total_sudah_detailing,
                COUNT(CASE WHEN rvp.status NOT IN ('1', '3') OR rvp.status IS NULL THEN 1 END) AS total_reject
            FROM req_visit_professional rvp
            WHERE rvp.periode BETWEEN ? AND ?
            {$filter_salesman_rvp}
        ";
        $res_join = $this->db->query($sql_join, [$start_date, $end_date])->row_array();

        $total_join            = (int)($res_join['total_join'] ?? 0);
        $total_pending         = (int)($res_join['total_pending'] ?? 0);
        $total_approved        = (int)($res_join['total_approved'] ?? 0);
        $total_sudah_detailing = (int)($res_join['total_sudah_detailing'] ?? 0);
        $total_reject          = (int)($res_join['total_reject'] ?? 0);

        $sql_target_join = "
            SELECT 
                IFNULL(SUM(rmt.target_join_activity), 0) AS total_target
            FROM m_sales_salesman mss
            LEFT JOIN app_role ar ON ar.role_name = mss.tipe_sales
            LEFT JOIN role_mapping_target rmt ON rmt.role_id = ar.role_id
                AND CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
        ";
        $res_target_join = $this->db->query($sql_target_join, ["{$year}-{$month}"])->row_array();
        $target_join = (int)($res_target_join['total_target'] ?? 0);
        if ($target_join == 0) {
            $target_join = $total_salesman * 1;
        }
        $pct_join = $target_join > 0 ? round(($total_approved / $target_join) * 100, 1) : 0;

        // TPE Aktif
        $valid_roles = $this->get_role_sales();
        $filter_salesman_tpe = $this->build_salesman_filter($data, 's', 'salesmanid');
        $role_where = '';
        if (!empty($valid_roles)) {
            $escaped_roles = array_map(function($r) {
                return "'" . $this->db->escape_str($r) . "'";
            }, $valid_roles);
            $role_where = " AND s.tipe_sales IN (" . implode(',', $escaped_roles) . ") ";
        }

        $sql_tpe_rincian = "
            SELECT 
                COALESCE(NULLIF(s.tipe_sales, ''), 'Lainnya') AS tipe_sales,
                COUNT(DISTINCT s.salesmanid) AS total
            FROM m_sales_salesman s
            LEFT JOIN m_salesman_area msa ON msa.salesmanid = s.salesmanid
            WHERE s.aktif = 1 
              AND LOWER(s.nama_salesman) NOT LIKE '%vacant%'
              {$role_where}
              {$filter_salesman_tpe}
            GROUP BY COALESCE(NULLIF(s.tipe_sales, ''), 'Lainnya')
            ORDER BY total DESC, tipe_sales ASC
        ";
        $res_tpe_rincian = $this->db->query($sql_tpe_rincian)->result_array();

        $total_tpe_aktif = 0;
        foreach ($res_tpe_rincian as $tr) {
            $total_tpe_aktif += (int)$tr['total'];
        }

        return [
            'periode' => [
                'tahun' => $year,
                'bulan' => $month,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'hari_kerja' => $hk,
            ],
            'kpi' => [
                'absensi' => [
                    'title' => 'Kehadiran TPE',
                    'actual' => $total_hadir,
                    'actual_fisik' => $hadir_fisik,
                    'total' => $total_hk,
                    'hari_kerja' => $hk,
                    'percentage' => $pct_absensi,
                    'sakit' => $sakit,
                    'cuti' => $cuti,
                    'izin' => $izin,
                    'hf' => $izin,
                ],
                'call' => [
                    'title' => 'Performa TPE',
                    'actual' => $total_realisasi_call,
                    'actual_call' => $total_call_act,
                    'planned_call' => $total_planned,
                    'extra_call' => $total_extra_call,
                    'actual_ec' => $total_extra_call,
                    'percentage_ec' => $pct_call,
                ],
                'kategori' => [
                    'title' => 'Ketercapaian Kategori',
                    'actual' => $total_outlet_kategori,
                    'total_outlet' => $total_outlet_kategori,
                    'total_kunjungan' => $total_kunjungan_kategori,
                    'rincian' => $res_kat_rincian,
                    'rincian_text' => $rincian_kategori_text,
                ],
                'user_wajib' => [
                    'title' => 'Ketercapaian User Wajib',
                    'actual' => $act_user_wajib,
                    'target' => $tgt_user_wajib,
                    'percentage' => $pct_user_wajib,
                    'rincian' => $top4_specs,
                    'rincian_all' => $combined_specs,
                    'rincian_text' => $top4_user_wajib_text,
                ],
                'detailing_produk' => [
                    'title' => 'Detailing Produk',
                    'actual' => $act_detailing,
                    'target' => $tgt_detailing,
                    'percentage' => $pct_detailing,
                    'rincian' => $top4_detailing,
                    'rincian_all' => $combined_detailing,
                    'rincian_text' => $top4_detailing_text,
                ],
                'selling_produk' => [
                    'title' => 'Selling Produk Wajib',
                    'actual' => $act_selling,
                    'target' => $tgt_selling,
                    'percentage' => $pct_selling,
                    'rincian' => $top4_selling,
                    'rincian_all' => $combined_selling,
                    'rincian_text' => $top4_selling_text,
                ],
                'join_activity' => [
                    'title' => 'Total Supervisi Join Activity',
                    'actual' => $total_join,
                    'actual_approved' => $total_approved,
                    'target' => $target_join,
                    'percentage' => $pct_join,
                    'pending' => $total_pending,
                    'approved' => $total_approved,
                    'sudah_detailing' => $total_sudah_detailing,
                    'reject' => $total_reject,
                ],
                'tpe' => [
                    'title' => 'Jumlah TPE Aktif',
                    'actual' => $total_tpe_aktif,
                    'total' => $total_tpe_aktif,
                    'rincian' => $res_tpe_rincian,
                ],
            ]
        ];
    }

    public function load_trend_monthly($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $month_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $year_start = "{$year}-01-01";
        $year_end   = "{$year}-12-31";

        $filter_salesman_s   = $this->build_salesman_filter($data, 's', 'salesmanid');
        $filter_salesman_ap  = $this->build_salesman_filter($data, 'ap', 'salesmanid');
        $filter_salesman_tsa = $this->build_salesman_filter($data, 'tsa', 'salesmanid');
        $filter_salesman_mss = $this->build_salesman_filter($data, 'mss', 'salesmanid');

        $sql_sales_count = "
            SELECT COUNT(DISTINCT s.salesmanid) AS total_salesman
            FROM m_sales_salesman s
            LEFT JOIN m_salesman_area msa ON msa.salesmanid = s.salesmanid
            WHERE s.aktif = 1 AND LOWER(s.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_s}
        ";
        $total_salesman = (int)($this->db->query($sql_sales_count)->row()->total_salesman ?? 1);
        $total_salesman = max(1, $total_salesman);

        $hk_map = [];
        $total_hk_map = [];
        foreach ($months as $m) {
            $s_date = "{$year}-{$m}-01";
            $e_date = date("Y-m-t", strtotime($s_date));
            $hk = get_hari_kerja($s_date, $e_date);
            $hk_map[$m] = $hk;
            $total_hk_map[$m] = $hk * $total_salesman;
        }

        // Kehadiran TPE
        $sql_abs_fisik = "
            SELECT 
                DATE_FORMAT(ap.periode, '%m') AS bulan,
                COUNT(DISTINCT ap.salesmanid, ap.periode) AS hadir_fisik
            FROM attendance_parma ap
            LEFT JOIN (
                SELECT salesmanid, periode, COUNT(1) AS total_detailing
                FROM trx_visit_detailing
                WHERE periode BETWEEN ? AND ?
                GROUP BY salesmanid, periode
            ) vd ON vd.salesmanid = ap.salesmanid AND vd.periode = ap.periode
            WHERE ap.periode BETWEEN ? AND ?
              AND ap.start_time IS NOT NULL
              AND (
                  (DAYOFWEEK(ap.periode) BETWEEN 2 AND 6 AND IFNULL(vd.total_detailing, 0) >= 1)
                  OR (DAYOFWEEK(ap.periode) = 7)
              )
              {$filter_salesman_ap}
            GROUP BY DATE_FORMAT(ap.periode, '%m')
        ";
        $res_abs_fisik = $this->db->query($sql_abs_fisik, [$year_start, $year_end, $year_start, $year_end])->result_array();
        $abs_fisik_map = [];
        foreach ($res_abs_fisik as $r) {
            $abs_fisik_map[$r['bulan']] = (int)$r['hadir_fisik'];
        }

        $sql_abs_leave = "
            SELECT 
                DATE_FORMAT(tsa.periode, '%m') AS bulan,
                COUNT(1) AS total_leave
            FROM t_sales_absensi tsa
            WHERE tsa.periode BETWEEN ? AND ?
              AND tsa.status IN ('S', 'C', 'HF')
              {$filter_salesman_tsa}
            GROUP BY DATE_FORMAT(tsa.periode, '%m')
        ";
        $res_abs_leave = $this->db->query($sql_abs_leave, [$year_start, $year_end])->result_array();
        $abs_leave_map = [];
        foreach ($res_abs_leave as $r) {
            $abs_leave_map[$r['bulan']] = (int)$r['total_leave'];
        }

        // Performa TPE
        $sql_call_act = "
            SELECT 
                DATE_FORMAT(tvd.periode, '%m') AS bulan,
                COUNT(DISTINCT CONCAT(tvd.customerid, '-', tvd.periode, '-', tvd.nourut)) AS total_call
            FROM trx_visit_detailing tvd
            JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
            WHERE tvd.periode BETWEEN ? AND ?
              AND mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
              {$filter_salesman_mss}
            GROUP BY DATE_FORMAT(tvd.periode, '%m')
        ";
        $res_call_act = $this->db->query($sql_call_act, [$year_start, $year_end])->result_array();
        $call_act_map = [];
        foreach ($res_call_act as $r) {
            $call_act_map[$r['bulan']] = (int)$r['total_call'];
        }

        $sql_call_tgt = "
            SELECT 
                LPAD(rmt.bulan, 2, '0') AS bulan,
                SUM(rmt.target_call_visit) AS target_call
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN role_mapping_target rmt ON rmt.role_id = ar.role_id
                AND rmt.tahun = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY LPAD(rmt.bulan, 2, '0')
        ";
        $res_call_tgt = $this->db->query($sql_call_tgt, [$year])->result_array();
        $call_tgt_map = [];
        foreach ($res_call_tgt as $r) {
            $m = $r['bulan'];
            $call_tgt_map[$m] = (int)$r['target_call'];
        }

        // User Wajib
        $sql_uw_act = "
            SELECT 
                DATE_FORMAT(tvd.periode, '%m') AS bulan,
                COUNT(1) AS total_act
            FROM trx_visit_detailing tvd
            JOIN ref_professional rp ON rp.id = tvd.user_id
            JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
            WHERE tvd.periode BETWEEN ? AND ?
              AND mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
              {$filter_salesman_mss}
            GROUP BY DATE_FORMAT(tvd.periode, '%m')
        ";
        $res_uw_act = $this->db->query($sql_uw_act, [$year_start, $year_end])->result_array();
        $uw_act_map = [];
        foreach ($res_uw_act as $r) {
            $uw_act_map[$r['bulan']] = (int)$r['total_act'];
        }

        $sql_uw_tgt = "
            SELECT 
                LPAD(mst.bulan, 2, '0') AS bulan,
                SUM(mst.target) AS total_target
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_spesialis_target mst ON mst.role_id = ar.role_id
                AND mst.tahun = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY LPAD(mst.bulan, 2, '0')
        ";
        $res_uw_tgt = $this->db->query($sql_uw_tgt, [$year])->result_array();
        $uw_tgt_map = [];
        foreach ($res_uw_tgt as $r) {
            $uw_tgt_map[$r['bulan']] = (int)$r['total_target'];
        }

        // Detailing & Selling Produk
        $sql_prod_act = "
            SELECT 
                DATE_FORMAT(tvd.periode, '%m') AS bulan,
                tvd.array_product, 
                COUNT(1) AS cnt
            FROM trx_visit_detailing tvd
            JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
            WHERE tvd.periode BETWEEN ? AND ?
              AND tvd.array_product IS NOT NULL
              AND TRIM(tvd.array_product) <> ''
              AND mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
              {$filter_salesman_mss}
            GROUP BY DATE_FORMAT(tvd.periode, '%m'), tvd.array_product
        ";
        $res_prod_act = $this->db->query($sql_prod_act, [$year_start, $year_end])->result_array();
        $detailing_act_map = [];
        $selling_act_map = [];
        foreach ($res_prod_act as $row) {
            $m = $row['bulan'];
            $count = (int)$row['cnt'];
            $rawList = explode(',', $row['array_product']);
            $numProducts = 0;
            foreach ($rawList as $p) {
                if (trim($p) !== '') $numProducts++;
            }
            if ($numProducts > 0) {
                $detailing_act_map[$m] = ($detailing_act_map[$m] ?? 0) + ($count * $numProducts);
                $selling_act_map[$m]   = ($selling_act_map[$m] ?? 0) + ($count * $numProducts);
            }
        }

        // Target Produk
        $sql_prod_tgt = "
            SELECT 
                LPAD(mpt.bulan, 2, '0') AS bulan,
                SUM(mpt.target) AS target_detailing,
                SUM(mpt.target_qty) AS target_selling
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_produk_target mpt ON mpt.role_id = ar.role_id
                AND mpt.tahun = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY LPAD(mpt.bulan, 2, '0')
        ";
        $res_prod_tgt = $this->db->query($sql_prod_tgt, [$year])->result_array();
        if (empty($res_prod_tgt)) {
            $sql_fb_prod_tgt = "
                SELECT 
                    LPAD(mpt.bulan, 2, '0') AS bulan,
                    SUM(mpt.target) AS target_detailing,
                    SUM(mpt.target_qty) AS target_selling
                FROM m_sales_produk_target mpt
                WHERE mpt.tahun = ?
                GROUP BY LPAD(mpt.bulan, 2, '0')
            ";
            $res_prod_tgt = $this->db->query($sql_fb_prod_tgt, [$year])->result_array();
        }
        $detailing_tgt_map = [];
        $selling_tgt_map = [];
        foreach ($res_prod_tgt as $r) {
            $m = $r['bulan'];
            $detailing_tgt_map[$m] = (int)$r['target_detailing'];
            $selling_tgt_map[$m]   = (int)$r['target_selling'];
        }

        $series_absensi    = ['actual' => [], 'target' => [], 'pct' => []];
        $series_call       = ['actual' => [], 'target' => [], 'pct' => []];
        $series_user_wajib = ['actual' => [], 'target' => [], 'pct' => []];
        $series_detailing  = ['actual' => [], 'target' => [], 'pct' => []];
        $series_selling    = ['actual' => [], 'target' => [], 'pct' => []];

        foreach ($months as $m) {
            $act_abs = ($abs_fisik_map[$m] ?? 0) + ($abs_leave_map[$m] ?? 0);
            $tgt_abs = $total_hk_map[$m] ?? 0;
            $pct_abs = $tgt_abs > 0 ? round(($act_abs / $tgt_abs) * 100, 1) : 0;
            $series_absensi['actual'][] = $act_abs;
            $series_absensi['target'][] = $tgt_abs;
            $series_absensi['pct'][]    = $pct_abs;

            $act_call = $call_act_map[$m] ?? 0;
            $tgt_call = $call_tgt_map[$m] ?? 0;
            $pct_call = $tgt_call > 0 ? round(($act_call / $tgt_call) * 100, 1) : 0;
            $series_call['actual'][] = $act_call;
            $series_call['target'][] = $tgt_call;
            $series_call['pct'][]    = $pct_call;

            $act_uw = $uw_act_map[$m] ?? 0;
            $tgt_uw = $uw_tgt_map[$m] ?? 0;
            $pct_uw = $tgt_uw > 0 ? round(($act_uw / $tgt_uw) * 100, 1) : 0;
            $series_user_wajib['actual'][] = $act_uw;
            $series_user_wajib['target'][] = $tgt_uw;
            $series_user_wajib['pct'][]    = $pct_uw;

            $act_det = $detailing_act_map[$m] ?? 0;
            $tgt_det = $detailing_tgt_map[$m] ?? 0;
            $pct_det = $tgt_det > 0 ? round(($act_det / $tgt_det) * 100, 1) : 0;
            $series_detailing['actual'][] = $act_det;
            $series_detailing['target'][] = $tgt_det;
            $series_detailing['pct'][]    = $pct_det;

            $act_sel = $selling_act_map[$m] ?? 0;
            $tgt_sel = $selling_tgt_map[$m] ?? 0;
            $pct_sel = $tgt_sel > 0 ? round(($act_sel / $tgt_sel) * 100, 1) : 0;
            $series_selling['actual'][] = $act_sel;
            $series_selling['target'][] = $tgt_sel;
            $series_selling['pct'][]    = $pct_sel;
        }

        return [
            'labels' => $month_labels,
            'series' => [
                'absensi'    => $series_absensi,
                'call'       => $series_call,
                'user_wajib' => $series_user_wajib,
                'detailing'  => $series_detailing,
                'selling'    => $series_selling,
            ]
        ];
    }

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
        $valid_roles = $this->get_role_sales();

        $this->db->select('s.salesmanid, s.nama_salesman, s.tipe_sales')
            ->from('m_sales_salesman s')
            ->where("s.nama_salesman NOT LIKE '%vacant%'")
            ->where('s.aktif', 1);

        if (!empty($valid_roles)) {
            $this->db->where_in('s.tipe_sales', $valid_roles);
        }

        if (!empty($data['subareaid'])) {
            $this->db->where("s.salesmanid IN (SELECT salesmanid FROM m_salesman_area WHERE subareaid = '" . $this->db->escape_str($data['subareaid']) . "')", NULL, FALSE);
        } elseif (!empty($data['areaid'])) {
            $this->db->where("s.salesmanid IN (SELECT salesmanid FROM m_salesman_area WHERE areaid = '" . $this->db->escape_str($data['areaid']) . "')", NULL, FALSE);
        } elseif (!empty($data['regionalid'])) {
            $this->db->where("s.salesmanid IN (SELECT salesmanid FROM m_salesman_area WHERE regionalid = '" . $this->db->escape_str($data['regionalid']) . "')", NULL, FALSE);
        }

        return $this->db->order_by('s.nama_salesman', 'ASC')->get()->result_array();
    }

    public function load_breakdown_absensi($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));
        $hk = get_hari_kerja($start_date, $end_date);

        $filter_salesman = $this->build_salesman_filter($data, 's', 'salesmanid');

        $sql = "
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                area.nama_regional,
                area.nama_area,
                area.nama_subarea,
                {$hk} AS hari_kerja,
                IFNULL(abs.hadir_fisik, 0) AS hadir,
                IFNULL(abs.total_izin, 0) AS izin,
                IFNULL(abs.total_sakit, 0) AS sakit,
                IFNULL(abs.total_cuti, 0) AS cuti,
                (IFNULL(abs.hadir_fisik, 0) + IFNULL(abs.total_izin, 0) + IFNULL(abs.total_sakit, 0) + IFNULL(abs.total_cuti, 0)) AS total_kehadiran,
                ROUND(CASE WHEN {$hk} > 0 THEN ((IFNULL(abs.hadir_fisik, 0) + IFNULL(abs.total_izin, 0) + IFNULL(abs.total_sakit, 0) + IFNULL(abs.total_cuti, 0)) / {$hk}) * 100 ELSE 0 END, 1) AS percentage
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT sub.nama_area ORDER BY sub.nama_area ASC SEPARATOR ', ') AS nama_subarea
                FROM m_salesman_area msa
                LEFT JOIN m_area_regional r ON r.regionalid = msa.regionalid
                LEFT JOIN m_area_areasite ar ON ar.areaid = msa.areaid
                LEFT JOIN m_area_subarea sub ON sub.subareaid = msa.subareaid
                GROUP BY msa.salesmanid
            ) area ON s.salesmanid = area.salesmanid
            LEFT JOIN (
                SELECT 
                    s_id AS salesmanid,
                    SUM(hadir_fisik) AS hadir_fisik,
                    SUM(izin) AS total_izin,
                    SUM(sakit) AS total_sakit,
                    SUM(cuti) AS total_cuti
                FROM (
                    SELECT 
                        ap.salesmanid AS s_id,
                        COUNT(DISTINCT ap.periode) AS hadir_fisik,
                        0 AS izin,
                        0 AS sakit,
                        0 AS cuti
                    FROM attendance_parma ap
                    LEFT JOIN (
                        SELECT salesmanid, periode, COUNT(1) AS total_detailing
                        FROM trx_visit_detailing
                        WHERE periode BETWEEN '{$start_date}' AND '{$end_date}'
                        GROUP BY salesmanid, periode
                    ) vd ON vd.salesmanid = ap.salesmanid AND vd.periode = ap.periode
                    WHERE ap.periode BETWEEN '{$start_date}' AND '{$end_date}'
                      AND ap.start_time IS NOT NULL
                      AND (
                          (DAYOFWEEK(ap.periode) BETWEEN 2 AND 6 AND IFNULL(vd.total_detailing, 0) >= 1)
                          OR (DAYOFWEEK(ap.periode) = 7)
                      )
                    GROUP BY ap.salesmanid

                    UNION ALL

                    SELECT 
                        tsa.salesmanid AS s_id,
                        0 AS hadir_fisik,
                        COUNT(CASE WHEN tsa.status = 'HF' THEN 1 END) AS izin,
                        COUNT(CASE WHEN tsa.status = 'S' THEN 1 END) AS sakit,
                        COUNT(CASE WHEN tsa.status = 'C' THEN 1 END) AS cuti
                    FROM t_sales_absensi tsa
                    WHERE tsa.periode BETWEEN '{$start_date}' AND '{$end_date}'
                    GROUP BY tsa.salesmanid
                ) abs_u
                GROUP BY s_id
            ) abs ON abs.salesmanid = s.salesmanid
            WHERE s.aktif = 1
            {$filter_salesman}
            ORDER BY area.nama_regional ASC, area.nama_area ASC, s.nama_salesman ASC
        ";

        return $this->db->query($sql)->result_array();
    }

    public function load_breakdown_call($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));
        $hk = get_hari_kerja($start_date, $end_date);

        $filter_salesman = $this->build_salesman_filter($data, 's', 'salesmanid');

        $sql = "
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                area.nama_regional,
                area.nama_area,
                area.nama_subarea,
                IFNULL(cl_tgt.target_call, 0) AS target_visit,
                IFNULL(cl.total_call, 0) AS call_act,
                IFNULL(cl.total_ec, 0) AS extra_call,
                (IFNULL(cl.total_call, 0) + IFNULL(cl.total_ec, 0)) AS total_call_all,
                ROUND(CASE WHEN IFNULL(cl_tgt.target_call, 0) > 0 THEN ((IFNULL(cl.total_call, 0) + IFNULL(cl.total_ec, 0)) / cl_tgt.target_call) * 100 ELSE 0 END, 1) AS percentage
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT sub.nama_area ORDER BY sub.nama_area ASC SEPARATOR ', ') AS nama_subarea
                FROM m_salesman_area msa
                LEFT JOIN m_area_regional r ON r.regionalid = msa.regionalid
                LEFT JOIN m_area_areasite ar ON ar.areaid = msa.areaid
                LEFT JOIN m_area_subarea sub ON sub.subareaid = msa.subareaid
                GROUP BY msa.salesmanid
            ) area ON s.salesmanid = area.salesmanid
            LEFT JOIN (
                SELECT 
                    tsrt.salesmanid,
                    COUNT(
                        DISTINCT CASE
                            WHEN tvd.salesmanid IS NOT NULL
                                AND rpdd.salesmanid IS NOT NULL
                            THEN CONCAT(tvd.customerid, '-', tvd.periode, '-', tvd.nourut)
                        END
                    ) AS total_call,
                    COUNT(
                        DISTINCT CASE
                            WHEN tvd.salesmanid IS NOT NULL
                                AND rpdd.salesmanid IS NULL
                            THEN CONCAT(tvd.customerid, '-', tvd.periode, '-', tvd.nourut)
                        END
                    ) AS total_ec
                FROM t_sales_rrk_trans tsrt
                LEFT JOIN (
                    SELECT DISTINCT customerid, salesmanid, periode
                    FROM req_pjp_daily_detail
                    WHERE periode BETWEEN '{$start_date}' AND '{$end_date}'
                ) rpdd
                    ON rpdd.salesmanid = tsrt.salesmanid
                    AND rpdd.customerid = tsrt.customerid
                    AND rpdd.periode = tsrt.periode
                LEFT JOIN trx_visit_detailing tvd
                    ON tvd.salesmanid = tsrt.salesmanid
                    AND tvd.customerid = tsrt.customerid
                    AND tvd.periode = tsrt.periode
                    AND tvd.periode BETWEEN '{$start_date}' AND '{$end_date}'
                WHERE tsrt.periode BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY tsrt.salesmanid
            ) cl ON cl.salesmanid = s.salesmanid
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_call_visit) AS target_call
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = '{$year}-{$month}'
                GROUP BY ar.role_name
            ) cl_tgt ON cl_tgt.role_name = s.tipe_sales
            WHERE s.aktif = 1
            {$filter_salesman}
            ORDER BY s.nama_salesman ASC
        ";

        return $this->db->query($sql)->result_array();
    }

    public function load_breakdown_kategori($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_tr = $this->build_salesman_filter($data, 'tr', 'salesmanid');

        $sql = "
            SELECT 
                COALESCE(NULLIF(TRIM(c.typeid), ''), 'Lainnya') AS typeid,
                COALESCE(mct.nama_type, NULLIF(TRIM(c.typeid), ''), 'Lainnya') AS nama_type,
                COUNT(DISTINCT tr.customerid) AS jumlah_outlet,
                COUNT(1) AS jumlah_visit
            FROM t_sales_rrk_trans tr
            LEFT JOIN m_customer c ON c.customerid = tr.customerid
            LEFT JOIN m_customer_type mct ON mct.typeid = c.typeid
            WHERE tr.periode BETWEEN ? AND ?
            {$filter_salesman_tr}
            GROUP BY COALESCE(NULLIF(TRIM(c.typeid), ''), 'Lainnya'), COALESCE(mct.nama_type, NULLIF(TRIM(c.typeid), ''), 'Lainnya')
            ORDER BY jumlah_outlet DESC, jumlah_visit DESC
        ";

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function load_breakdown_user_wajib($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_tvd = $this->build_salesman_filter($data, 'tvd', 'salesmanid');
        $filter_salesman_mss = $this->build_salesman_filter($data, 'mss', 'salesmanid');

        $sql_act = "
            SELECT 
                rp.spesialisasi_id,
                COALESCE(NULLIF(TRIM(rs.name), ''), 'Lainnya') AS nama_spesialisasi,
                COALESCE(NULLIF(TRIM(rp.type), ''), 'Dokter') AS tipe_spesialis,
                COUNT(1) AS total_actual
            FROM trx_visit_detailing tvd
            JOIN ref_professional rp ON rp.id = tvd.user_id
            LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
            WHERE tvd.periode BETWEEN ? AND ?
            {$filter_salesman_tvd}
            GROUP BY rp.spesialisasi_id, COALESCE(NULLIF(TRIM(rs.name), ''), 'Lainnya'), COALESCE(NULLIF(TRIM(rp.type), ''), 'Dokter')
        ";
        $res_act = $this->db->query($sql_act, [$start_date, $end_date])->result_array();

        $act_map = [];
        foreach ($res_act as $r) {
            $key = $r['spesialisasi_id'] ?? 0;
            if (!isset($act_map[$key])) {
                $act_map[$key] = [
                    'nama_spesialisasi' => $r['nama_spesialisasi'],
                    'tipe_spesialis' => $r['tipe_spesialis'],
                    'actual' => 0
                ];
            }
            $act_map[$key]['actual'] += (int)$r['total_actual'];
        }

        $sql_tgt = "
            SELECT 
                mst.spesialisasi_id,
                COALESCE(NULLIF(TRIM(mst.nama_spesialisasi), ''), rs.name, 'Lainnya') AS nama_spesialisasi,
                SUM(mst.target) AS total_target
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_spesialis_target mst ON mst.role_id = ar.role_id
                AND CONCAT(mst.tahun, '-', LPAD(mst.bulan, 2, '0')) = ?
            LEFT JOIN ref_spesialisasi rs ON rs.id = mst.spesialisasi_id
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY mst.spesialisasi_id, COALESCE(NULLIF(TRIM(mst.nama_spesialisasi), ''), rs.name, 'Lainnya')
        ";
        $res_tgt = $this->db->query($sql_tgt, ["{$year}-{$month}"])->result_array();

        $tgt_map = [];
        foreach ($res_tgt as $r) {
            $key = $r['spesialisasi_id'] ?? 0;
            $tgt_map[$key] = [
                'nama_spesialisasi' => $r['nama_spesialisasi'],
                'target' => (int)$r['total_target']
            ];
        }

        $all_ids = array_unique(array_merge(array_keys($act_map), array_keys($tgt_map)));
        $list = [];
        foreach ($all_ids as $spId) {
            $nama = $act_map[$spId]['nama_spesialisasi'] ?? $tgt_map[$spId]['nama_spesialisasi'] ?? 'Lainnya';
            $tipe = $act_map[$spId]['tipe_spesialis'] ?? 'Dokter';
            $act = $act_map[$spId]['actual'] ?? 0;
            $tgt = $tgt_map[$spId]['target'] ?? 0;
            $pct = $tgt > 0 ? round(($act / $tgt) * 100, 1) : 0;

            $list[] = [
                'spesialisasi_id' => $spId,
                'nama_spesialisasi' => $nama,
                'tipe_spesialis' => $tipe,
                'target_spesialis' => $tgt,
                'aktual' => $act,
                'percentage' => $pct
            ];
        }

        usort($list, function ($a, $b) {
            if ($b['aktual'] === $a['aktual']) {
                return $b['target_spesialis'] <=> $a['target_spesialis'];
            }
            return $b['aktual'] <=> $a['aktual'];
        });

        return $list;
    }

    public function load_breakdown_detailing($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_tvd = $this->build_salesman_filter($data, 'tvd', 'salesmanid');
        $filter_salesman_mss = $this->build_salesman_filter($data, 'mss', 'salesmanid');

        $sql_tvd_array = "
            SELECT tvd.array_product, COUNT(1) AS cnt
            FROM trx_visit_detailing tvd
            WHERE tvd.periode BETWEEN ? AND ?
              AND tvd.array_product IS NOT NULL
              AND TRIM(tvd.array_product) <> ''
            {$filter_salesman_tvd}
            GROUP BY tvd.array_product
        ";
        $res_tvd_array = $this->db->query($sql_tvd_array, [$start_date, $end_date])->result_array();

        $sql_all_products = "SELECT productid, COALESCE(NULLIF(nama_invoice, ''), productid) AS nama_invoice FROM m_product";
        $all_prod_rows = $this->db->query($sql_all_products)->result_array();
        $prod_name_map = [];
        foreach ($all_prod_rows as $pr) {
            $prod_name_map[trim($pr['productid'])] = $pr['nama_invoice'];
        }

        $act_prd_map = [];
        foreach ($res_tvd_array as $row) {
            $rawList = explode(',', $row['array_product']);
            $count = (int)$row['cnt'];
            foreach ($rawList as $pId) {
                $pId = trim($pId);
                if (empty($pId)) continue;
                if (!isset($act_prd_map[$pId])) {
                    $act_prd_map[$pId] = [
                        'nama_invoice' => $prod_name_map[$pId] ?? $pId,
                        'aktual' => 0
                    ];
                }
                $act_prd_map[$pId]['aktual'] += $count;
            }
        }

        $sql_produk_tgt = "
            SELECT 
                mpt.product_id,
                COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id) AS nama_invoice,
                SUM(mpt.target) AS total_target
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_produk_target mpt ON mpt.role_id = ar.role_id
                AND CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY mpt.product_id, COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id)
        ";
        $res_prd_tgt = $this->db->query($sql_produk_tgt, ["{$year}-{$month}"])->result_array();
        if (empty($res_prd_tgt)) {
            $sql_fb = "
                SELECT 
                    mpt.product_id,
                    COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id) AS nama_invoice,
                    SUM(mpt.target) AS total_target
                FROM m_sales_produk_target mpt
                WHERE CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
                GROUP BY mpt.product_id, COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id)
            ";
            $res_prd_tgt = $this->db->query($sql_fb, ["{$year}-{$month}"])->result_array();
        }

        $tgt_prd_map = [];
        foreach ($res_prd_tgt as $r) {
            $pId = $r['product_id'];
            $tgt_prd_map[$pId] = [
                'nama_invoice' => $r['nama_invoice'],
                'target' => (float)$r['total_target']
            ];
        }

        $all_ids = array_unique(array_merge(array_keys($act_prd_map), array_keys($tgt_prd_map)));
        $list = [];
        foreach ($all_ids as $pId) {
            $nama = $act_prd_map[$pId]['nama_invoice'] ?? $tgt_prd_map[$pId]['nama_invoice'] ?? ($prod_name_map[$pId] ?? $pId);
            $act = $act_prd_map[$pId]['aktual'] ?? 0;
            $tgt = $tgt_prd_map[$pId]['target'] ?? 0;
            $pct = $tgt > 0 ? round(($act / $tgt) * 100, 1) : 0;

            $list[] = [
                'productid' => $pId,
                'nama_invoice' => $nama,
                'target' => $tgt,
                'aktual' => $act,
                'percentage' => $pct
            ];
        }

        usort($list, function ($a, $b) {
            if ($b['aktual'] === $a['aktual']) {
                return $b['target'] <=> $a['target'];
            }
            return $b['aktual'] <=> $a['aktual'];
        });

        return $list;
    }

    public function load_breakdown_selling($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_tvd = $this->build_salesman_filter($data, 'tvd', 'salesmanid');
        $filter_salesman_mss = $this->build_salesman_filter($data, 'mss', 'salesmanid');

        $sql_tvd_array = "
            SELECT tvd.array_product, COUNT(1) AS cnt
            FROM trx_visit_detailing tvd
            WHERE tvd.periode BETWEEN ? AND ?
              AND tvd.array_product IS NOT NULL
              AND TRIM(tvd.array_product) <> ''
            {$filter_salesman_tvd}
            GROUP BY tvd.array_product
        ";
        $res_tvd_array = $this->db->query($sql_tvd_array, [$start_date, $end_date])->result_array();

        $sql_all_products = "SELECT productid, COALESCE(NULLIF(nama_invoice, ''), productid) AS nama_invoice FROM m_product";
        $all_prod_rows = $this->db->query($sql_all_products)->result_array();
        $prod_name_map = [];
        foreach ($all_prod_rows as $pr) {
            $prod_name_map[trim($pr['productid'])] = $pr['nama_invoice'];
        }

        $act_prd_map = [];
        foreach ($res_tvd_array as $row) {
            $rawList = explode(',', $row['array_product']);
            $count = (int)$row['cnt'];
            foreach ($rawList as $pId) {
                $pId = trim($pId);
                if (empty($pId)) continue;
                if (!isset($act_prd_map[$pId])) {
                    $act_prd_map[$pId] = [
                        'nama_invoice' => $prod_name_map[$pId] ?? $pId,
                        'aktual' => 0
                    ];
                }
                $act_prd_map[$pId]['aktual'] += $count;
            }
        }

        $sql_produk_tgt = "
            SELECT 
                mpt.product_id,
                COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id) AS nama_invoice,
                SUM(mpt.target_qty) AS total_target_qty
            FROM m_sales_salesman mss
            JOIN app_role ar ON ar.role_name = mss.tipe_sales
            JOIN m_sales_produk_target mpt ON mpt.role_id = ar.role_id
                AND CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
            WHERE mss.aktif = 1 AND LOWER(mss.nama_salesman) NOT LIKE '%vacant%'
            {$filter_salesman_mss}
            GROUP BY mpt.product_id, COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id)
        ";
        $res_prd_tgt = $this->db->query($sql_produk_tgt, ["{$year}-{$month}"])->result_array();
        if (empty($res_prd_tgt)) {
            $sql_fb = "
                SELECT 
                    mpt.product_id,
                    COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id) AS nama_invoice,
                    SUM(mpt.target_qty) AS total_target_qty
                FROM m_sales_produk_target mpt
                WHERE CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) = ?
                GROUP BY mpt.product_id, COALESCE(NULLIF(mpt.nama_invoice, ''), mpt.product_id)
            ";
            $res_prd_tgt = $this->db->query($sql_fb, ["{$year}-{$month}"])->result_array();
        }

        $tgt_prd_map = [];
        foreach ($res_prd_tgt as $r) {
            $pId = $r['product_id'];
            $tgt_prd_map[$pId] = [
                'nama_invoice' => $r['nama_invoice'],
                'target_qty' => (float)$r['total_target_qty']
            ];
        }

        $all_ids = array_unique(array_merge(array_keys($act_prd_map), array_keys($tgt_prd_map)));
        $list = [];
        foreach ($all_ids as $pId) {
            $nama = $act_prd_map[$pId]['nama_invoice'] ?? $tgt_prd_map[$pId]['nama_invoice'] ?? ($prod_name_map[$pId] ?? $pId);
            $act = $act_prd_map[$pId]['aktual'] ?? 0;
            $tgt = $tgt_prd_map[$pId]['target_qty'] ?? 0;
            $pct = $tgt > 0 ? round(($act / $tgt) * 100, 1) : 0;

            $list[] = [
                'productid' => $pId,
                'nama_invoice' => $nama,
                'target_qty' => $tgt,
                'aktual' => $act,
                'percentage' => $pct
            ];
        }

        usort($list, function ($a, $b) {
            if ($b['aktual'] === $a['aktual']) {
                return $b['target_qty'] <=> $a['target_qty'];
            }
            return $b['aktual'] <=> $a['aktual'];
        });

        return $list;
    }

    public function load_breakdown_join_visit($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_rvp = $this->build_salesman_filter($data, 'rvp', 'salesmanid');

        $sql = "
            SELECT 
                rvp.req_no,
                rvp.periode,
                rvp.salesmanid,
                COALESCE(NULLIF(rvp.nama_salesman, ''), s.nama_salesman, rvp.salesmanid) AS nama_salesman,
                rvp.customerid,
                COALESCE(NULLIF(rvp.nama_customer, ''), c.nama_customer, rvp.customerid) AS nama_outlet,
                rvp.user_id,
                rvp.nama_professional AS nama_user,
                rvp.status,
                CASE 
                    WHEN rvp.status = '1' THEN 'Pending'
                    WHEN rvp.status = '3' THEN 'Disetujui'
                    ELSE 'Reject'
                END AS status_desc,
                rvp.keterangan,
                rvp.visit_keterangan,
                rvp.reason,
                rvp.approved_by,
                rvp.approved_date,
                rvp.visit_doc
            FROM req_visit_professional rvp
            LEFT JOIN m_sales_salesman s ON s.salesmanid = rvp.salesmanid
            LEFT JOIN m_customer c ON c.customerid = rvp.customerid
            WHERE rvp.periode BETWEEN ? AND ?
            {$filter_salesman_rvp}
            ORDER BY rvp.periode DESC, rvp.req_no DESC
        ";

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }

    public function get_detail_join_visit($req_no)
    {
        $sql = "
            SELECT 
                a.req_no,
                a.siteid,
                a.periode,
                a.salesmanid,
                COALESCE(NULLIF(a.nama_salesman, ''), b.nama_salesman, a.salesmanid) AS nama_salesman,
                a.customerid,
                COALESCE(NULLIF(a.nama_customer, ''), c.nama_customer, a.customerid) AS nama_customer,
                a.user_id,
                a.nama_professional,
                a.keterangan,
                a.status,
                CASE
                    WHEN a.status = '1' THEN 'Pending'
                    WHEN a.status = '3' THEN 'Disetujui'
                    ELSE 'Reject'
                END AS status_desc,
                a.reason,
                a.visit_keterangan,
                a.visit_doc,
                a.approved_by,
                a.approved_date,
                a.created_by,
                a.created_date,
                a.modified_by,
                a.modified_date
            FROM req_visit_professional a
            LEFT JOIN m_sales_salesman b ON a.salesmanid = b.salesmanid
            LEFT JOIN m_customer c ON a.customerid = c.customerid
            WHERE a.req_no = ?
            LIMIT 1
        ";
        return $this->db->query($sql, [$req_no])->row_array();
    }

    public function load_breakdown_tpe_aktif($data)
    {
        $valid_roles = $this->get_role_sales();
        $filter_salesman_tpe = $this->build_salesman_filter($data, 's', 'salesmanid');
        $role_where = '';
        if (!empty($valid_roles)) {
            $escaped_roles = array_map(function($r) {
                return "'" . $this->db->escape_str($r) . "'";
            }, $valid_roles);
            $role_where = " AND s.tipe_sales IN (" . implode(',', $escaped_roles) . ") ";
        }

        $sql = "
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                area.nama_regional,
                area.nama_area,
                area.nama_subarea,
                s.supervisorid,
                COALESCE(spv.nama_salesman, s.supervisorid, '-') AS nama_supervisor
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT sub.nama_area ORDER BY sub.nama_area ASC SEPARATOR ', ') AS nama_subarea
                FROM m_salesman_area msa
                LEFT JOIN m_area_regional r ON r.regionalid = msa.regionalid
                LEFT JOIN m_area_areasite ar ON ar.areaid = msa.areaid
                LEFT JOIN m_area_subarea sub ON sub.subareaid = msa.subareaid
                GROUP BY msa.salesmanid
            ) area ON s.salesmanid = area.salesmanid
            LEFT JOIN m_sales_salesman spv ON spv.salesmanid = s.supervisorid
            WHERE s.aktif = 1 
              AND LOWER(s.nama_salesman) NOT LIKE '%vacant%'
              {$role_where}
              {$filter_salesman_tpe}
            ORDER BY area.nama_regional ASC, area.nama_area ASC, s.nama_salesman ASC
        ";

        return $this->db->query($sql)->result_array();
    }

    public function load_breakdown_area($data)
    {
        return $this->load_breakdown_absensi($data);
    }

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

        $sql_salesman = "
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                area.nama_regional,
                area.nama_area,
                area.nama_subarea
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT sub.nama_area ORDER BY sub.nama_area ASC SEPARATOR ', ') AS nama_subarea
                FROM m_salesman_area msa
                LEFT JOIN m_area_regional r ON r.regionalid = msa.regionalid
                LEFT JOIN m_area_areasite ar ON ar.areaid = msa.areaid
                LEFT JOIN m_area_subarea sub ON sub.subareaid = msa.subareaid
                WHERE msa.salesmanid = ?
                GROUP BY msa.salesmanid
            ) area ON s.salesmanid = area.salesmanid
            WHERE s.salesmanid = ?
            LIMIT 1
        ";
        $salesman_info = $this->db->query($sql_salesman, [$salesmanid, $salesmanid])->row_array();

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

        $sql_absensi = "
            SELECT 
                u.tanggal,
                CASE 
                    WHEN tsa.status = 'S' THEN 'Sakit'
                    WHEN tsa.status = 'C' THEN 'Cuti'
                    WHEN tsa.status = 'HF' THEN 'Half Day / Libur'
                    WHEN (
                        (DAYOFWEEK(u.tanggal) BETWEEN 2 AND 6 AND ap.start_time IS NOT NULL AND IFNULL(vd.total_detailing, 0) >= 1)
                        OR (DAYOFWEEK(u.tanggal) = 7 AND ap.start_time IS NOT NULL)
                    ) THEN 'Hadir'
                    ELSE 'Tidak Hadir'
                END AS status_label,
                CASE 
                    WHEN (
                        (DAYOFWEEK(u.tanggal) BETWEEN 2 AND 6 AND ap.start_time IS NOT NULL AND IFNULL(vd.total_detailing, 0) >= 1)
                        OR (DAYOFWEEK(u.tanggal) = 7 AND ap.start_time IS NOT NULL)
                    ) THEN 'H'
                    WHEN tsa.status IN ('S', 'C', 'HF') THEN tsa.status
                    ELSE 'A'
                END AS status_computed,
                IFNULL(vd.total_detailing, 0) AS total_detailing_hari,
                tsa.checkin,
                tsa.checkout,
                COALESCE(tsa.keterangan, ap.start_keterangan, '-') AS keterangan,
                ap.start_time AS parma_checkin,
                ap.end_time AS parma_checkout,
                ap.start_image AS foto_checkin,
                ap.end_image AS foto_checkout
            FROM (
                SELECT periode AS tanggal FROM attendance_parma WHERE salesmanid = ? AND periode BETWEEN ? AND ?
                UNION
                SELECT periode AS tanggal FROM t_sales_absensi WHERE salesmanid = ? AND periode BETWEEN ? AND ?
            ) u
            LEFT JOIN attendance_parma ap ON ap.salesmanid = ? AND ap.periode = u.tanggal
            LEFT JOIN t_sales_absensi tsa ON tsa.salesmanid = ? AND tsa.periode = u.tanggal
            LEFT JOIN (
                SELECT salesmanid, periode, COUNT(1) AS total_detailing
                FROM trx_visit_detailing
                WHERE salesmanid = ? AND periode BETWEEN ? AND ?
                GROUP BY salesmanid, periode
            ) vd ON vd.salesmanid = ? AND vd.periode = u.tanggal
            ORDER BY u.tanggal DESC
        ";
        $absensi_list = $this->db->query($sql_absensi, [
            $salesmanid, $start_date, $end_date, // attendance_parma dates
            $salesmanid, $start_date, $end_date, // t_sales_absensi dates
            $salesmanid,                         // join attendance_parma
            $salesmanid,                         // join t_sales_absensi
            $salesmanid, $start_date, $end_date, // vd subquery
            $salesmanid                          // join vd
        ])->result_array();

        return [
            'salesman' => $salesman_info,
            'kunjungan' => $kunjungan_list,
            'detailing' => $detailing_list,
            'absensi' => $absensi_list
        ];
    }

    // helper
    private function build_salesman_filter($data, $table_alias = 'a', $salesman_col = 'salesmanid', $include_role_check = true)
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
            if (!empty($data['restrict_level']) && !empty($data['usersession'])) {
                $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
                if ($restrict_query) {
                    $where .= " AND {$col} IN ({$restrict_query})";
                }
            }
        }

        if ($include_role_check) {
            $valid_roles = $this->get_role_sales();
            $role_in = "";
            if (!empty($valid_roles)) {
                $escaped_roles = array_map([$this->db, 'escape_str'], $valid_roles);
                $role_in = " AND tipe_sales IN ('" . implode("','", $escaped_roles) . "')";
            }
            $where .= " AND {$col} IN (SELECT salesmanid FROM m_sales_salesman WHERE aktif = 1 AND LOWER(nama_salesman) NOT LIKE '%vacant%' {$role_in})";
        }

        return $where;
    }
}
