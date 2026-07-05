<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_spesialis_target_model extends CI_Model
{
    public function load_summary($data)
    {
        $start_month = date('Y-m', strtotime($data['start_date']));
        $end_month = date('Y-m', strtotime($data['end_date']));

        $where_spec = "";
        $where_salesman = "";
        $params = [$start_month, $end_month, $data['start_date'], $data['end_date']];

        if (!empty($data['spesialisasi_ids'])) {
            $ids = [];
            foreach ($data['spesialisasi_ids'] as $id) {
                if (!is_null($id) && $id !== '') {
                    $ids[] = (int)$id;
                }
            }
            if (!empty($ids)) {
                $where_spec = " AND rsv.spesialisasi_id IN (" . implode(',', $ids) . ") ";
            }
        }

        if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rsv.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $sql = "
            SELECT 
                rsv.spesialisasi_id,
                rsv.nama_spesialisasi,
                SUM(rsv.actual) AS actual,
                IFNULL(t.target, 0) AS target
            FROM rekap_spesialis_visit rsv
            LEFT JOIN (
                SELECT 
                    mst.spesialisasi_id,
                    SUM(mst.target) AS target
                FROM m_sales_spesialis_target mst
                WHERE CONCAT(mst.tahun, '-', LPAD(mst.bulan, 2, '0')) BETWEEN ? AND ?
                GROUP BY mst.spesialisasi_id
            ) t ON t.spesialisasi_id = rsv.spesialisasi_id
            WHERE rsv.tanggal BETWEEN ? AND ?
              {$where_spec}
              {$where_salesman}
            GROUP BY rsv.spesialisasi_id, rsv.nama_spesialisasi, t.target
            ORDER BY rsv.nama_spesialisasi ASC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    public function load_detail($data)
    {
        $where_salesman = "";
        if (!empty($data['salesman_ids'])) {
            $ids = [];
            foreach ($data['salesman_ids'] as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_salesman = " AND rsv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        } else if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rsv.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $sql = "
            SELECT 
                rsv.salesmanid,
                rsv.nama_salesman,
                rsv.customerid,
                rsv.nama_customer,
                rsv.check_in,
                rsv.check_out,
                rsv.duration,
                rsv.keterangan,
                rsv.nama_professional AS nama_dokter,
                rsv.nama_spesialisasi
            FROM rekap_spesialis_visit rsv
            WHERE rsv.spesialisasi_id = ?
              AND rsv.tanggal BETWEEN ? AND ?
              AND rsv.customerid IS NOT NULL
              {$where_salesman}
            ORDER BY rsv.check_in DESC
        ";
        return $this->db->query($sql, [$data['spesialisasi_id'], $data['start_date'], $data['end_date']])->result_array();
    }

    public function load_all_detail($data)
    {
        $where_spec = "";
        $where_salesman = "";
        $params = [$data['start_date'], $data['end_date']];

        if (!empty($data['spesialisasi_ids'])) {
            $ids = [];
            foreach ($data['spesialisasi_ids'] as $id) {
                if (!is_null($id) && $id !== '') {
                    $ids[] = (int)$id;
                }
            }
            if (!empty($ids)) {
                $where_spec = " AND rsv.spesialisasi_id IN (" . implode(',', $ids) . ") ";
            }
        }

        if (!empty($data['salesman_ids'])) {
            $ids = [];
            foreach ($data['salesman_ids'] as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_salesman = " AND rsv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        } else if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rsv.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $sql = "
            SELECT 
                rsv.tanggal,
                rsv.salesmanid,
                rsv.nama_salesman,
                rsv.spesialisasi_id,
                rsv.nama_spesialisasi,
                rsv.customerid,
                rsv.nama_customer,
                rsv.check_in,
                rsv.check_out,
                rsv.duration,
                rsv.keterangan,
                rsv.nama_professional AS nama_dokter
            FROM rekap_spesialis_visit rsv
            WHERE rsv.tanggal BETWEEN ? AND ?
              AND rsv.customerid IS NOT NULL
              {$where_spec}
              {$where_salesman}
            ORDER BY rsv.check_in DESC
        ";
        return $this->db->query($sql, $params)->result_array();
    }
}
