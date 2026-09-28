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

    public function load_summary_kpi($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $filter_salesman_ap  = $this->build_salesman_filter($data, 'ap', 'salesmanid');
        $filter_salesman_tsa = $this->build_salesman_filter($data, 'tsa', 'salesmanid');
        $filter_salesman_rrk = $this->build_salesman_filter($data, 'tr', 'salesmanid');
        $filter_salesman_dub = $this->build_salesman_filter($data, 'rdv', 'salesmanid');
        $filter_salesman_prd = $this->build_salesman_filter($data, 'rpv', 'salesmanid');
        $filter_salesman_spc = $this->build_salesman_filter($data, 'rsv', 'salesmanid');
        $filter_salesman_gen = $this->build_salesman_filter($data, 'a', 'salesmanid');

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
                IFNULL(SUM(rmt.target_call_visit * IFNULL(NULLIF(rmt.target_hk, 0), {$hk})), 0) AS total_target
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

        // User Wajib (Target dari m_sales_spesialis_target, Actual dari trx_visit_detailing -> ref_professional -> ref_spesialisasi)
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

        // Gabungkan semua spesialisasi unik
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

        // Urutkan berdasarkan actual terbanyak lalu target terbanyak
        usort($combined_specs, function ($a, $b) {
            if ($b['actual'] === $a['actual']) {
                return $b['target'] <=> $a['target'];
            }
            return $b['actual'] <=> $a['actual'];
        });

        // Ambil top 4 rincian saja untuk ditampilkan di card
        $top4_specs = array_slice($combined_specs, 0, 4);

        $top4_text_parts = [];
        foreach ($top4_specs as $ts) {
            $top4_text_parts[] = "{$ts['nama_spesialisasi']}: {$ts['actual']}/{$ts['target']}";
        }
        $top4_user_wajib_text = !empty($top4_text_parts) ? implode(' | ', $top4_text_parts) : '-';

        $act_user_wajib = $total_act_user_wajib;
        $tgt_user_wajib = $total_tgt_user_wajib;
        $pct_user_wajib = $tgt_user_wajib > 0 ? round(($act_user_wajib / $tgt_user_wajib) * 100, 1) : 0;

        $sql_dub = "
            SELECT 
                IFNULL(SUM(rdv.actual_call_planned), 0) AS actual_kat,
                IFNULL(SUM(t.target_dub), 0) AS target_kat
            FROM rekap_dub_visit rdv
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_dub * rmt.target_call_dub) AS target_dub
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = ?
                GROUP BY ar.role_name
            ) t ON t.role_name = rdv.tipe_sales
            WHERE rdv.tanggal BETWEEN ? AND ?
            {$filter_salesman_dub}
        ";
        $res_dub = $this->db->query($sql_dub, ["{$year}-{$month}", $start_date, $end_date])->row_array();

        // Produk (Detailing & Selling: Target dari m_sales_produk_target, Actual dari trx_visit_detailing.array_product = m_product.productid)
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

        // Master produk untuk nama invoice
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
            // Fallback jika tidak ada join role_id di m_sales_produk_target
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

        // Gabungkan semua produk unik
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

        // Urutkan Detailing berdasarkan actual terbanyak lalu target terbanyak
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

        // Urutkan Selling berdasarkan actual terbanyak lalu target terbanyak
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

        // Join Activity (Supervisi) dari req_visit_professional
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

        $filter_salesman_dub = $this->build_salesman_filter($data, 'rdv', 'salesmanid');
        $filter_salesman_prd = $this->build_salesman_filter($data, 'rpv', 'salesmanid');
        $filter_salesman_rrk = $this->build_salesman_filter($data, 'tr', 'salesmanid');

        $sql_trend_dub = "
            SELECT 
                DATE_FORMAT(rdv.tanggal, '%m') AS bulan,
                IFNULL(SUM(rdv.actual_call_planned), 0) AS act_kat,
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

        $series_kat = [];
        $series_user_wajib = [];
        $series_detailing = [];
        $series_selling = [];
        $series_call = [];

        foreach ($months as $m) {
            $series_kat[] = (int)($dub_map[$m]['act_kat'] ?? 0);
            $series_user_wajib[] = (int)($dub_map[$m]['act_user_wajib'] ?? 0);
            $series_detailing[] = (int)($prd_map[$m]['act_detailing'] ?? 0);
            $series_selling[] = (int)($prd_map[$m]['act_selling'] ?? 0);
            $series_call[] = (int)($call_map[$m]['act_call'] ?? 0);
        }

        return [
            'labels' => $month_labels,
            'series' => [
                'kat' => $series_kat,
                'user_wajib' => $series_user_wajib,
                'detailing' => $series_detailing,
                'selling' => $series_selling,
                'call' => $series_call,
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

    public function load_breakdown_area($data)
    {
        $year = !empty($data['tahun']) ? $data['tahun'] : date('Y');
        $month = !empty($data['bulan']) ? str_pad($data['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
        $start_date = "{$year}-{$month}-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $hk = get_hari_kerja($start_date, $end_date);

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
                {$hk} AS total_hari,
                ROUND(CASE WHEN {$hk} > 0 THEN (IFNULL(abs.total_hadir, 0) / {$hk}) * 100 ELSE 0 END, 1) AS pct_absensi,
                -- Call & Extra Call
                IFNULL(cl.total_call, 0) AS total_call,
                IFNULL(cl.total_ec, 0) AS total_ec,
                IFNULL(cl_tgt.target_call, 0) AS total_planned,
                ROUND(CASE WHEN IFNULL(cl_tgt.target_call, 0) > 0 THEN ((IFNULL(cl.total_call, 0) + IFNULL(cl.total_ec, 0)) / cl_tgt.target_call) * 100 ELSE 0 END, 1) AS pct_ec,
                -- Kat A & User Wajib
                IFNULL(dub.act_kat, 0) AS act_kat,
                IFNULL(dub_tgt.target_kat, 0) AS tgt_kat,
                ROUND(CASE WHEN IFNULL(dub_tgt.target_kat, 0) > 0 THEN (dub.act_kat / dub_tgt.target_kat) * 100 ELSE 0 END, 1) AS pct_kat,
                -- User Wajib
                IFNULL(uw.act_user_wajib, 0) AS act_user_wajib,
                IFNULL(uw_tgt.target_user_wajib, 0) AS tgt_user_wajib,
                ROUND(CASE WHEN IFNULL(uw_tgt.target_user_wajib, 0) > 0 THEN (IFNULL(uw.act_user_wajib, 0) / uw_tgt.target_user_wajib) * 100 ELSE 0 END, 1) AS pct_user_wajib,
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
            -- Subquery Absensi (Hadir Fisik + Sakit + Cuti + Izin)
            LEFT JOIN (
                SELECT 
                    s_id AS salesmanid,
                    SUM(hadir_count) AS total_hadir
                FROM (
                    -- Hadir Fisik
                    SELECT 
                        ap.salesmanid AS s_id,
                        COUNT(DISTINCT ap.periode) AS hadir_count
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

                    -- Sakit, Cuti, Izin (Paid Leave)
                    SELECT 
                        tsa.salesmanid AS s_id,
                        COUNT(1) AS hadir_count
                    FROM t_sales_absensi tsa
                    WHERE tsa.periode BETWEEN '{$start_date}' AND '{$end_date}'
                      AND tsa.status IN ('S', 'C', 'HF')
                    GROUP BY tsa.salesmanid
                ) abs_union
                GROUP BY s_id
            ) abs ON abs.salesmanid = s.salesmanid
            -- Subquery Call (Call & Extra Call dari trx_visit_detailing & req_pjp_daily_detail)
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
            -- Subquery Call Target
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_call_visit * IFNULL(NULLIF(rmt.target_hk, 0), {$hk})) AS target_call
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = '{$year}-{$month}'
                GROUP BY ar.role_name
            ) cl_tgt ON cl_tgt.role_name = s.tipe_sales
            -- Subquery DUB Actual
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    SUM(actual_call_planned) AS act_kat,
                    SUM(actual_call_visit) AS act_user_wajib
                FROM rekap_dub_visit
                WHERE tanggal BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY salesmanid
            ) dub ON dub.salesmanid = s.salesmanid
            -- Subquery DUB Target
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_dub * rmt.target_call_dub) AS target_kat
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) = '{$year}-{$month}'
                GROUP BY ar.role_name
            ) dub_tgt ON dub_tgt.role_name = s.tipe_sales
            -- Subquery User Wajib Actual
            LEFT JOIN (
                SELECT 
                    tvd.salesmanid,
                    COUNT(1) AS act_user_wajib
                FROM trx_visit_detailing tvd
                JOIN ref_professional rp ON rp.id = tvd.user_id
                WHERE tvd.periode BETWEEN '{$start_date}' AND '{$end_date}'
                GROUP BY tvd.salesmanid
            ) uw ON uw.salesmanid = s.salesmanid
            -- Subquery User Wajib Target
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(mst.target) AS target_user_wajib
                FROM m_sales_spesialis_target mst
                JOIN app_role ar ON ar.role_id = mst.role_id
                WHERE CONCAT(mst.tahun, '-', LPAD(mst.bulan, 2, '0')) = '{$year}-{$month}'
                GROUP BY ar.role_name
            ) uw_tgt ON uw_tgt.role_name = s.tipe_sales
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
}
