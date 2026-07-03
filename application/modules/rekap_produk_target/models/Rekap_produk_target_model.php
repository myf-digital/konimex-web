<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_produk_target_model extends CI_Model
{
    public function load_summary($start_date, $end_date, $product_ids = [])
    {
        $start_month = date('Y-m', strtotime($start_date));
        $end_month = date('Y-m', strtotime($end_date));

        $where_prod = "";
        $params = [$start_month, $end_month, $start_date, $end_date];

        if (!empty($product_ids)) {
            $ids = [];
            foreach ($product_ids as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_prod = " AND rpv.product_id IN (" . implode(',', $ids) . ") ";
            }
        }

        $sql = "
            SELECT 
                rpv.product_id,
                rpv.nama_invoice,
                SUM(rpv.actual_visit) AS actual_visit,
                SUM(rpv.actual_qty) AS actual_qty,
                IFNULL(t.target, 0) AS target,
                IFNULL(t.target_qty, 0) AS target_qty
            FROM rekap_produk_visit rpv
            LEFT JOIN (
                SELECT 
                    mpt.product_id,
                    SUM(mpt.target) AS target,
                    SUM(mpt.target_qty) AS target_qty
                FROM m_sales_produk_target mpt
                WHERE CONCAT(mpt.tahun, '-', LPAD(mpt.bulan, 2, '0')) BETWEEN ? AND ?
                GROUP BY mpt.product_id
            ) t ON t.product_id COLLATE utf8mb4_general_ci = rpv.product_id COLLATE utf8mb4_general_ci
            WHERE rpv.tanggal BETWEEN ? AND ?
              {$where_prod}
            GROUP BY rpv.product_id, rpv.nama_invoice, t.target, t.target_qty
            ORDER BY rpv.nama_invoice ASC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    public function load_detail_visit($product_id, $start_date, $end_date)
    {
        $sql = "
            SELECT 
                rpv.salesmanid,
                rpv.nama_salesman,
                rpv.customerid,
                rpv.nama_customer,
                rpv.check_in,
                rpv.check_out,
                rpv.duration,
                rpv.keterangan
            FROM rekap_produk_visit rpv
            WHERE rpv.product_id = ?
              AND rpv.tanggal BETWEEN ? AND ?
              AND rpv.actual_visit = 1
              AND rpv.customerid IS NOT NULL
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, [$product_id, $start_date, $end_date])->result_array();
    }

    public function load_detail_sales($product_id, $start_date, $end_date)
    {
        $sql = "
            SELECT 
                rpv.check_in AS tanggal,
                rpv.salesmanid,
                rpv.nama_salesman,
                REPLACE(rpv.keterangan, 'Sales PO: ', '') AS no_po,
                rpv.actual_qty AS qty_kecil,
                tsd.h_jual,
                (rpv.actual_qty * tsd.h_jual) AS total_price
            FROM rekap_produk_visit rpv
            LEFT JOIN t_sales_detail tsd ON tsd.no_po = REPLACE(rpv.keterangan, 'Sales PO: ', '') AND tsd.productid = rpv.product_id
            WHERE rpv.product_id = ?
              AND rpv.tanggal BETWEEN ? AND ?
              AND rpv.actual_qty > 0
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, [$product_id, $start_date, $end_date])->result_array();
    }

    public function load_all_detail_visit($start_date, $end_date, $product_ids = [])
    {
        $where_prod = "";
        $params = [$start_date, $end_date];

        if (!empty($product_ids)) {
            $ids = [];
            foreach ($product_ids as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_prod = " AND rpv.product_id IN (" . implode(',', $ids) . ") ";
            }
        }

        $sql = "
            SELECT 
                rpv.tanggal,
                rpv.salesmanid,
                rpv.nama_salesman,
                rpv.product_id,
                rpv.nama_invoice,
                rpv.customerid,
                rpv.nama_customer,
                rpv.check_in,
                rpv.check_out,
                rpv.duration,
                rpv.keterangan
            FROM rekap_produk_visit rpv
            WHERE rpv.tanggal BETWEEN ? AND ?
              AND rpv.actual_visit = 1
              AND rpv.customerid IS NOT NULL
              {$where_prod}
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    public function load_all_detail_sales($start_date, $end_date, $product_ids = [])
    {
        $where_prod = "";
        $params = [$start_date, $end_date];

        if (!empty($product_ids)) {
            $ids = [];
            foreach ($product_ids as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_prod = " AND rpv.product_id IN (" . implode(',', $ids) . ") ";
            }
        }

        $sql = "
            SELECT 
                rpv.check_in AS tanggal,
                rpv.salesmanid,
                rpv.nama_salesman,
                rpv.product_id,
                rpv.nama_invoice,
                REPLACE(rpv.keterangan, 'Sales PO: ', '') AS no_po,
                rpv.actual_qty AS qty_kecil,
                tsd.h_jual,
                (rpv.actual_qty * tsd.h_jual) AS total_price
            FROM rekap_produk_visit rpv
            LEFT JOIN t_sales_detail tsd ON tsd.no_po = REPLACE(rpv.keterangan, 'Sales PO: ', '') AND tsd.productid = rpv.product_id
            WHERE rpv.tanggal BETWEEN ? AND ?
              AND rpv.actual_qty > 0
              {$where_prod}
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, $params)->result_array();
    }
}
