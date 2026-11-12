<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Req_join_visit_model extends CI_Model
{
    private function build_where_clauses($data)
    {
        $usersession = $data['usersession'] ?? '';
        $restrict_level = $data['restrict_level'] ?? '';

        $restrict_clause = '';
        if (!empty($usersession) && !empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $restrict_clause = " AND (
                    a.created_by IN ({$restrict_query})
                    OR EXISTS (
                        SELECT 1
                        FROM req_join_visit_detail rjvd_r
                        WHERE rjvd_r.req_no = a.req_no
                          AND rjvd_r.salesmanid IN ({$restrict_query})
                    )
                )";
            }
        }

        $date_clause = '';
        $start_date = !empty($data['start_date']) ? $data['start_date'] : (!empty($data['get_date1']) ? $data['get_date1'] : '');
        $end_date = !empty($data['end_date']) ? $data['end_date'] : (!empty($data['get_date2']) ? $data['get_date2'] : '');

        if (!empty($start_date) && !empty($end_date)) {
            $date_clause = " AND a.periode BETWEEN '" . $this->db->escape_str($start_date) . "' AND '" . $this->db->escape_str($end_date) . "'";
        } elseif (!empty($start_date)) {
            $date_clause = " AND a.periode >= '" . $this->db->escape_str($start_date) . "'";
        } elseif (!empty($end_date)) {
            $date_clause = " AND a.periode <= '" . $this->db->escape_str($end_date) . "'";
        }

        $status_clause = '';
        if (isset($data['filter_status']) && $data['filter_status'] !== '' && $data['filter_status'] !== null) {
            $status_val = $this->db->escape_str($data['filter_status']);
            if ($status_val === '1' || $status_val === '3') {
                $status_clause = " AND a.status = '" . $status_val . "'";
            } elseif ($status_val === '2' || $status_val === '5' || strtolower($status_val) === 'reject') {
                $status_clause = " AND (a.status NOT IN ('1', '3') OR a.status IS NULL)";
            }
        }

        $salesman_clause = '';
        if (!empty($data['salesmanid']) && $data['salesmanid'] !== 'all') {
            $salesman_esc = $this->db->escape_str($data['salesmanid']);
            $salesman_clause = " AND (
                a.created_by = '{$salesman_esc}'
                OR EXISTS (
                    SELECT 1
                    FROM req_join_visit_detail rjvd_s
                    WHERE rjvd_s.req_no = a.req_no
                      AND rjvd_s.salesmanid = '{$salesman_esc}'
                )
            )";
        }

        return [
            'restrict' => $restrict_clause,
            'date'     => $date_clause,
            'status'   => $status_clause,
            'salesman' => $salesman_clause,
        ];
    }

    public function load($data)
    {
        $clauses = $this->build_where_clauses($data);

        if (empty($data['sort'])) {
            $data['sort'] = 'a.periode DESC, a.created_date';
            $data['order'] = 'DESC';
        }

        $field = "a.*";
        $table = " (
            SELECT
                a.req_no,
                a.siteid,
                a.periode,
                a.created_by,
                COALESCE(NULLIF(b.nama_salesman, ''), a.created_by) AS nama_pengaju,
                CONCAT_WS(' - ', NULLIF(a.created_by, ''), NULLIF(b.nama_salesman, '')) AS pengaju,
                COALESCE(dtl.peserta_tpe, '-') AS peserta_tpe,
                COALESCE(dtl.outlet, '-') AS outlet,
                COALESCE(dtl.professional, '-') AS professional,
                a.keterangan,
                a.status,
                CASE
                    WHEN a.status = 1 THEN 'Pending'
                    WHEN a.status = 3 THEN 'Disetujui'
                    ELSE 'Reject'
                END AS status_desc,
                a.reason,
                a.modified_by,
                COALESCE(NULLIF(m.nama_salesman, ''), a.modified_by) AS nama_approver,
                a.modified_date,
                a.created_date,
                IFNULL(dtl.total_target, 0) AS total_target,
                IFNULL(dtl.total_realisasi, 0) AS total_realisasi,
                CASE
                    WHEN IFNULL(dtl.total_realisasi, 0) > 0
                    THEN CONCAT('Sudah Detailing (', IFNULL(dtl.total_realisasi, 0), '/', IFNULL(dtl.total_target, 0), ')')
                    ELSE 'Belum Detailing'
                END AS realisasi_desc
            FROM req_join_visit a
            LEFT JOIN m_sales_salesman b ON a.created_by = b.salesmanid
            LEFT JOIN m_sales_salesman m ON a.modified_by = m.salesmanid
            LEFT JOIN (
                SELECT
                    d.req_no,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(' - ', NULLIF(d.salesmanid, ''), NULLIF(ds.nama_salesman, '')) ORDER BY ds.nama_salesman SEPARATOR '||') AS peserta_tpe,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(' - ', NULLIF(d.customerid, ''), NULLIF(dc.nama_customer, '')) ORDER BY dc.nama_customer SEPARATOR ', ') AS outlet,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(' - ', NULLIF(d.user_id, ''), NULLIF(dp.nama_professional, '')) ORDER BY dp.nama_professional SEPARATOR '||') AS professional,
                    COUNT(DISTINCT CONCAT(d.periode, '|', d.customerid, '|', d.user_id)) AS total_target,
                    COUNT(DISTINCT CASE WHEN tvd.salesmanid IS NOT NULL THEN CONCAT(d.periode, '|', d.customerid, '|', d.user_id) END) AS total_realisasi
                FROM req_join_visit_detail d
                LEFT JOIN m_sales_salesman ds ON d.salesmanid = ds.salesmanid
                LEFT JOIN m_customer dc ON d.customerid = dc.customerid AND (dc.customerid_m = '' OR dc.customerid_m IS NULL)
                LEFT JOIN ref_professional dp ON d.user_id = dp.id
                LEFT JOIN trx_visit_detailing tvd ON tvd.periode = d.periode
                    AND tvd.salesmanid = d.salesmanid
                    AND tvd.customerid = d.customerid
                    AND tvd.user_id = d.user_id
                GROUP BY d.req_no
            ) dtl ON dtl.req_no = a.req_no
            WHERE 1=1 {$clauses['date']} {$clauses['status']} {$clauses['salesman']} {$clauses['restrict']}
        ) a";

        return easy_pagging($data, $field, $table);
    }

    public function get_salesman($data)
    {
        $usersession = $data['usersession'] ?? '';
        $restrict_level = $data['restrict_level'] ?? '';

        $strquery = '';
        if (!empty($usersession) && !empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $search = $data['q'] ?? '';
        $search_query = '';
        if (!empty($search)) {
            $search_esc = $this->db->escape_str($search);
            $search_query = " AND (a.salesmanid LIKE '%{$search_esc}%' OR a.nama_salesman LIKE '%{$search_esc}%')";
        }

        $sql = "
            SELECT DISTINCT a.salesmanid, a.nama_salesman
            FROM m_sales_salesman a
            WHERE 1=1 AND LOWER(a.nama_salesman) <> 'vacant' AND a.aktif = '1' {$strquery} {$search_query}
            ORDER BY a.nama_salesman ASC
        ";

        return $this->db->query($sql)->result_array();
    }

    public function get_detail($req_no)
    {
        $sqlHeader = "
            SELECT
                a.req_no,
                a.siteid,
                a.periode,
                a.keterangan,
                a.status,
                CASE
                    WHEN a.status = 1 THEN 'Pending'
                    WHEN a.status = 3 THEN 'Disetujui'
                    ELSE 'Reject'
                END AS status_desc,
                a.reason,
                a.created_by,
                COALESCE(NULLIF(b.nama_salesman, ''), a.created_by) AS nama_pengaju,
                a.created_date,
                a.modified_by,
                COALESCE(NULLIF(m.nama_salesman, ''), a.modified_by) AS nama_approver,
                a.modified_date
            FROM req_join_visit a
            LEFT JOIN m_sales_salesman b ON a.created_by = b.salesmanid
            LEFT JOIN m_sales_salesman m ON a.modified_by = m.salesmanid
            WHERE a.req_no = ?
        ";
        $header = $this->db->query($sqlHeader, [$req_no])->row_array();
        if (!$header) {
            return null;
        }

        $sqlDetails = "
            SELECT
                d.req_no,
                d.periode,
                d.salesmanid,
                COALESCE(NULLIF(s.nama_salesman, ''), d.salesmanid) AS nama_salesman,
                d.customerid,
                COALESCE(NULLIF(c.nama_customer, ''), d.customerid) AS nama_customer,
                d.user_id,
                COALESCE(NULLIF(rp.nama_professional, ''), tvd.professional_name, CAST(d.user_id AS CHAR)) AS nama_professional,
                COALESCE(NULLIF(tvd.tipe_pic, ''), rp.type, '-') AS tipe_pic,
                tsrt.check_in,
                tsrt.check_out,
                tsrt.img_checkin,
                tvd.start_detailing,
                tvd.end_detailing,
                tvd.array_product,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR ', ')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, tvd.array_product) > 0
                    ),
                    tvd.array_product
                ) AS products,
                tvd.keterangan AS visit_keterangan,
                tvd.reason AS visit_reason,
                tvd.url_img_detailing,
                tvd.url_file_signature,
                tvd.url_file_serahterima
            FROM req_join_visit_detail d
            LEFT JOIN m_sales_salesman s ON s.salesmanid = d.salesmanid
            LEFT JOIN m_customer c ON c.customerid = d.customerid AND (c.customerid_m = '' OR c.customerid_m IS NULL)
            LEFT JOIN ref_professional rp ON rp.id = d.user_id
            LEFT JOIN trx_visit_detailing tvd ON tvd.periode = d.periode
                AND tvd.salesmanid = d.salesmanid
                AND tvd.customerid = d.customerid
                AND tvd.user_id = d.user_id
            LEFT JOIN t_sales_rrk_trans tsrt ON tsrt.periode = d.periode
                AND tsrt.salesmanid = d.salesmanid
                AND tsrt.customerid = d.customerid
            WHERE d.req_no = ?
            ORDER BY d.periode ASC, c.nama_customer ASC, rp.nama_professional ASC, s.nama_salesman ASC
        ";
        $rawDetails = $this->db->query($sqlDetails, [$req_no])->result_array();

        $participantsMap = [];
        $groupedItems = [];
        $imagesMap = [];

        $buildUrl = function ($rawPath) {
            if (empty($rawPath)) {
                return '';
            }
            return (strpos($rawPath, 'http://') === 0 || strpos($rawPath, 'https://') === 0)
                ? $rawPath
                : rtrim(URL_IMAGE, '/') . '/' . ltrim($rawPath, '/');
        };

        foreach ($rawDetails as $row) {
            $smId = $row['salesmanid'];
            if (!empty($smId)) {
                $participantsMap[$smId] = $smId . ' - ' . $row['nama_salesman'];
            }

            $itemKey = $row['periode'] . '|' . $row['customerid'] . '|' . $row['user_id'];
            if (!isset($groupedItems[$itemKey])) {
                $groupedItems[$itemKey] = [
                    'periode'           => $row['periode'],
                    'customerid'        => $row['customerid'],
                    'nama_customer'     => $row['nama_customer'],
                    'user_id'           => $row['user_id'],
                    'nama_professional' => $row['nama_professional'],
                    'tipe_pic'          => $row['tipe_pic'],
                    'participants'      => [],
                    'check_in'          => $row['check_in'],
                    'check_out'         => $row['check_out'],
                    'start_detailing'   => $row['start_detailing'],
                    'end_detailing'     => $row['end_detailing'],
                    'products'          => $row['products'],
                    'visit_keterangan'  => $row['visit_keterangan'],
                    'is_realized'       => !empty($row['start_detailing']) || !empty($row['products']) || !empty($row['url_img_detailing']) || !empty($row['url_file_signature']),
                ];
            } else {
                if (empty($groupedItems[$itemKey]['check_in']) && !empty($row['check_in'])) {
                    $groupedItems[$itemKey]['check_in'] = $row['check_in'];
                }
                if (empty($groupedItems[$itemKey]['check_out']) && !empty($row['check_out'])) {
                    $groupedItems[$itemKey]['check_out'] = $row['check_out'];
                }
                if (empty($groupedItems[$itemKey]['start_detailing']) && !empty($row['start_detailing'])) {
                    $groupedItems[$itemKey]['start_detailing'] = $row['start_detailing'];
                }
                if (empty($groupedItems[$itemKey]['end_detailing']) && !empty($row['end_detailing'])) {
                    $groupedItems[$itemKey]['end_detailing'] = $row['end_detailing'];
                }
                if (empty($groupedItems[$itemKey]['products']) && !empty($row['products'])) {
                    $groupedItems[$itemKey]['products'] = $row['products'];
                }
                if (empty($groupedItems[$itemKey]['visit_keterangan']) && !empty($row['visit_keterangan'])) {
                    $groupedItems[$itemKey]['visit_keterangan'] = $row['visit_keterangan'];
                }
                if (!empty($row['start_detailing']) || !empty($row['products']) || !empty($row['url_img_detailing']) || !empty($row['url_file_signature'])) {
                    $groupedItems[$itemKey]['is_realized'] = true;
                }
            }

            if (!empty($smId)) {
                $groupedItems[$itemKey]['participants'][$smId] = $smId . ' - ' . $row['nama_salesman'];
            }

            $photoCandidates = [
                ['path' => $row['img_checkin'] ?? '',         'label' => 'Check-In: ' . $row['nama_customer']],
                ['path' => $row['url_img_detailing'] ?? '',   'label' => 'Detailing: ' . $row['nama_professional']],
                ['path' => $row['url_file_signature'] ?? '',  'label' => 'Signature: ' . $row['nama_professional']],
                ['path' => $row['url_file_serahterima'] ?? '', 'label' => 'Serah Terima: ' . $row['nama_professional']],
            ];

            foreach ($photoCandidates as $cand) {
                $rawP = trim((string)$cand['path']);
                if ($rawP !== '' && !isset($imagesMap[$rawP])) {
                    $imagesMap[$rawP] = [
                        'path'  => $rawP,
                        'url'   => $buildUrl($rawP),
                        'label' => $cand['label'],
                    ];
                }
            }
        }

        $itemsList = [];
        foreach ($groupedItems as $gi) {
            $gi['participants'] = array_values($gi['participants']);
            $itemsList[] = $gi;
        }

        $header['participants'] = array_values($participantsMap);
        $header['items']        = $itemsList;
        $header['images']       = array_values($imagesMap);

        return $header;
    }

    public function get_export_data($data)
    {
        $clauses = $this->build_where_clauses($data);

        $sql = "
            SELECT
                a.req_no,
                a.periode,
                a.created_by,
                COALESCE(NULLIF(b.nama_salesman, ''), a.created_by) AS nama_pengaju,
                d.salesmanid,
                COALESCE(NULLIF(s.nama_salesman, ''), d.salesmanid) AS nama_salesman,
                d.customerid,
                COALESCE(NULLIF(c.nama_customer, ''), d.customerid) AS nama_customer,
                d.user_id,
                COALESCE(NULLIF(rp.nama_professional, ''), tvd.professional_name, CAST(d.user_id AS CHAR)) AS nama_professional,
                a.keterangan,
                a.status,
                CASE
                    WHEN a.status = 1 THEN 'Pending'
                    WHEN a.status = 3 THEN 'Disetujui'
                    ELSE 'Reject'
                END AS status_desc,
                a.reason,
                CASE
                    WHEN tvd.salesmanid IS NOT NULL THEN 'Sudah Detailing'
                    ELSE 'Belum Detailing'
                END AS status_realisasi,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR ', ')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, tvd.array_product) > 0
                    ),
                    tvd.array_product
                ) AS products,
                tvd.start_detailing,
                tvd.end_detailing,
                tvd.keterangan AS visit_keterangan,
                a.modified_by,
                COALESCE(NULLIF(m.nama_salesman, ''), a.modified_by) AS nama_approver,
                a.modified_date,
                a.created_date
            FROM req_join_visit a
            LEFT JOIN req_join_visit_detail d ON d.req_no = a.req_no
            LEFT JOIN m_sales_salesman b ON a.created_by = b.salesmanid
            LEFT JOIN m_sales_salesman m ON a.modified_by = m.salesmanid
            LEFT JOIN m_sales_salesman s ON d.salesmanid = s.salesmanid
            LEFT JOIN m_customer c ON d.customerid = c.customerid AND (c.customerid_m = '' OR c.customerid_m IS NULL)
            LEFT JOIN ref_professional rp ON d.user_id = rp.id
            LEFT JOIN trx_visit_detailing tvd ON tvd.periode = d.periode
                AND tvd.salesmanid = d.salesmanid
                AND tvd.customerid = d.customerid
                AND tvd.user_id = d.user_id
            WHERE 1=1 {$clauses['date']} {$clauses['status']} {$clauses['salesman']} {$clauses['restrict']}
            ORDER BY a.periode DESC, a.req_no DESC, s.nama_salesman ASC, rp.nama_professional ASC
        ";

        return $this->db->query($sql)->result_array();
    }
}

