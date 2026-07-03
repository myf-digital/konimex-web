<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_dub_target_model extends CI_Model
{
    public function load_summary($start_date, $end_date, $salesman_ids = [])
    {
        $start_month = date('Y-m', strtotime($start_date));
        $end_month = date('Y-m', strtotime($end_date));

        $where_salesman = "";
        $params = [$start_month, $end_month, $start_date, $end_date];

        if (!empty($salesman_ids)) {
            $ids = [];
            foreach ($salesman_ids as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_salesman = " AND rdv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        }

        $sql = "
            SELECT 
                rdv.salesmanid,
                rdv.nama_salesman,
                rdv.tipe_sales,
                SUM(rdv.actual_call_planned) AS actual_call_planned,
                SUM(rdv.actual_call_visit) AS actual_call_visit,
                IFNULL(t.target_dub, 0) AS target_dub,
                IFNULL(t.target_call_visit, 0) AS target_call_visit
            FROM rekap_dub_visit rdv
            LEFT JOIN (
                SELECT 
                    ar.role_name,
                    SUM(rmt.target_dub) AS target_dub,
                    SUM(rmt.target_call_visit) AS target_call_visit
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE CONCAT(rmt.tahun, '-', LPAD(rmt.bulan, 2, '0')) BETWEEN ? AND ?
                GROUP BY ar.role_name
            ) t ON t.role_name = rdv.tipe_sales
            WHERE rdv.tanggal BETWEEN ? AND ?
              {$where_salesman}
            GROUP BY rdv.salesmanid, rdv.nama_salesman, rdv.tipe_sales, t.target_dub, t.target_call_visit
            ORDER BY rdv.tipe_sales ASC, rdv.nama_salesman ASC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    public function load_detail($salesmanid, $start_date, $end_date)
    {
        $sql = "
            SELECT 
                rdv.customerid,
                rdv.nama_customer,
                mc.typeid,
                rdv.check_in,
                rdv.check_out,
                rdv.keterangan,
                rdv.array_product,
                rdv.professional_id AS user_id,
                rdv.nama_professional AS nama_dokter,
                rdv.actual_call_planned AS is_planned
            FROM rekap_dub_visit rdv
            LEFT JOIN m_customer mc ON mc.customerid = rdv.customerid
            WHERE rdv.salesmanid = ?
              AND rdv.tanggal BETWEEN ? AND ?
              AND rdv.customerid IS NOT NULL
            ORDER BY rdv.check_in DESC
        ";
        return $this->db->query($sql, [$salesmanid, $start_date, $end_date])->result_array();
    }

    public function load_all_detail($start_date, $end_date, $salesman_ids = [])
    {
        $where_salesman = "";
        $params = [$start_date, $end_date];

        if (!empty($salesman_ids)) {
            $ids = [];
            foreach ($salesman_ids as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_salesman = " AND rdv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        }

        $sql = "
            SELECT 
                rdv.tanggal,
                rdv.salesmanid,
                rdv.nama_salesman,
                rdv.tipe_sales,
                rdv.customerid,
                rdv.nama_customer,
                mc.typeid,
                rdv.check_in,
                rdv.check_out,
                rdv.duration,
                rdv.keterangan,
                rdv.array_product,
                rdv.professional_id AS user_id,
                rdv.nama_professional AS nama_dokter,
                rdv.actual_call_planned AS is_planned
            FROM rekap_dub_visit rdv
            LEFT JOIN m_customer mc ON mc.customerid = rdv.customerid
            WHERE rdv.tanggal BETWEEN ? AND ?
              AND rdv.customerid IS NOT NULL
              {$where_salesman}
            ORDER BY rdv.check_in DESC
        ";
        return $this->db->query($sql, $params)->result_array();
    }
}
