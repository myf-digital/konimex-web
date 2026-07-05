<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_produk_target_model extends CI_Model
{
    public function load_summary($data)
    {
        $start_month = date('Y-m', strtotime($data['start_date']));
        $end_month = date('Y-m', strtotime($data['end_date']));

        $where_prod = "";
        $where_salesman = "";
        $params = [$start_month, $end_month, $data['start_date'], $data['end_date']];

        if (!empty($data['product_ids'])) {
            $ids = [];
            foreach ($data['product_ids'] as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_prod = " AND rpv.product_id IN (" . implode(',', $ids) . ") ";
            }
        }

        if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rpv.salesmanid IN (" . $restrict_query . ")";
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
              {$where_salesman}
            GROUP BY rpv.product_id, rpv.nama_invoice, t.target, t.target_qty
            ORDER BY rpv.nama_invoice ASC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    public function load_detail_visit($data)
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
                $where_salesman = " AND rpv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        } else if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rpv.salesmanid IN (" . $restrict_query . ") ";
            }
        }

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
              {$where_salesman}
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, [$data['product_id'], $data['start_date'], $data['end_date']])->result_array();
    }

    public function load_detail_sales($data)
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
                $where_salesman = " AND rpv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        } else if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rpv.salesmanid IN (" . $restrict_query . ") ";
            }
        }

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
              {$where_salesman}
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, [$data['product_id'], $data['start_date'], $data['end_date']])->result_array();
    }

    public function load_all_detail_visit($data)
    {
        $where_prod = "";
        $where_salesman = "";
        $params = [$data['start_date'], $data['end_date']];

        if (!empty($data['product_ids'])) {
            $ids = [];
            foreach ($data['product_ids'] as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_prod = " AND rpv.product_id IN (" . implode(',', $ids) . ") ";
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
                $where_salesman = " AND rpv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        } else if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rpv.salesmanid IN (" . $restrict_query . ") ";
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
              {$where_salesman}
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, $params)->result_array();
    }

    public function load_all_detail_sales($data)
    {
        $where_prod = "";
        $where_salesman = "";
        $params = [$data['start_date'], $data['end_date']];

        if (!empty($data['product_ids'])) {
            $ids = [];
            foreach ($data['product_ids'] as $id) {
                if (!empty($id)) {
                    $ids[] = $this->db->escape($id);
                }
            }
            if (!empty($ids)) {
                $where_prod = " AND rpv.product_id IN (" . implode(',', $ids) . ") ";
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
                $where_salesman = " AND rpv.salesmanid IN (" . implode(',', $ids) . ") ";
            }
        } else if (!empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $where_salesman = " AND rpv.salesmanid IN (" . $restrict_query . ") ";
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
              {$where_salesman}
            ORDER BY rpv.check_in DESC
        ";
        return $this->db->query($sql, $params)->result_array();
    }
}
