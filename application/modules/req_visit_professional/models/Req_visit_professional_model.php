<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Req_visit_professional_model extends CI_Model
{
    public function load($data)
    {
        $usersession = $data["usersession"] ?? '';
        $restrict_level = $data["restrict_level"] ?? '';

        $strquery = "";
        if (!empty($usersession) && !empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $date_query = "";
        $start_date = !empty($data['start_date']) ? $data['start_date'] : (!empty($data['get_date1']) ? $data['get_date1'] : '');
        $end_date = !empty($data['end_date']) ? $data['end_date'] : (!empty($data['get_date2']) ? $data['get_date2'] : '');

        if (!empty($start_date) && !empty($end_date)) {
            $date_query = " AND a.periode BETWEEN '" . $this->db->escape_str($start_date) . "' AND '" . $this->db->escape_str($end_date) . "'";
        } elseif (!empty($start_date)) {
            $date_query = " AND a.periode >= '" . $this->db->escape_str($start_date) . "'";
        } elseif (!empty($end_date)) {
            $date_query = " AND a.periode <= '" . $this->db->escape_str($end_date) . "'";
        }

        $status_query = "";
        if (isset($data['filter_status']) && $data['filter_status'] !== '' && $data['filter_status'] !== null) {
            $status_val = $this->db->escape_str($data['filter_status']);
            if ($status_val === '1' || $status_val === '3') {
                $status_query = " AND a.status = '" . $status_val . "'";
            } else if ($status_val === '2' || strtolower($status_val) === 'reject') {
                $status_query = " AND (a.status NOT IN ('1', '3') OR a.status IS NULL)";
            }
        }

        $salesman_query = "";
        if (!empty($data['salesmanid']) && $data['salesmanid'] !== 'all') {
            $salesman_query = " AND a.salesmanid = '" . $this->db->escape_str($data['salesmanid']) . "'";
        }

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
                a.salesmanid,
                COALESCE(NULLIF(a.nama_salesman, ''), b.nama_salesman, a.salesmanid) AS nama_salesman,
                CONCAT_WS(' - ', NULLIF(a.salesmanid, ''), NULLIF(a.nama_salesman, '')) as salesman,
                a.customerid,
                COALESCE(NULLIF(a.nama_customer, ''), c.nama_customer, a.customerid) AS nama_customer,
                CONCAT_WS(' - ', NULLIF(a.customerid, ''), NULLIF(a.nama_customer, '')) as outlet,
                a.user_id,
                a.nama_professional,
                CONCAT_WS(' - ', NULLIF(a.user_id, ''), NULLIF(a.nama_professional, '')) as professional,
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
            WHERE 1=1 {$date_query} {$status_query} {$salesman_query} {$strquery}
        ) a";

        return easy_pagging($data, $field, $table);
    }

    public function get_salesman($data)
    {
        $usersession = $data["usersession"] ?? '';
        $restrict_level = $data["restrict_level"] ?? '';

        $strquery = "";
        if (!empty($usersession) && !empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $search = $data['q'] ?? '';
        $search_query = "";
        if (!empty($search)) {
            $search_query = " AND (a.salesmanid LIKE '%" . $this->db->escape_str($search) . "%' OR a.nama_salesman LIKE '%" . $this->db->escape_str($search) . "%')";
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
        ";

        return $this->db->query($sql, [$req_no])->row_array();
    }

    public function get_export_data($data)
    {
        $usersession = $data["usersession"] ?? '';
        $restrict_level = $data["restrict_level"] ?? '';

        $strquery = "";
        if (!empty($usersession) && !empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $date_query = "";
        $start_date = !empty($data['start_date']) ? $data['start_date'] : '';
        $end_date = !empty($data['end_date']) ? $data['end_date'] : '';

        if (!empty($start_date) && !empty($end_date)) {
            $date_query = " AND a.periode BETWEEN '" . $this->db->escape_str($start_date) . "' AND '" . $this->db->escape_str($end_date) . "'";
        } elseif (!empty($start_date)) {
            $date_query = " AND a.periode >= '" . $this->db->escape_str($start_date) . "'";
        } elseif (!empty($end_date)) {
            $date_query = " AND a.periode <= '" . $this->db->escape_str($end_date) . "'";
        }

        $status_query = "";
        if (isset($data['filter_status']) && $data['filter_status'] !== '' && $data['filter_status'] !== null) {
            $status_val = $this->db->escape_str($data['filter_status']);
            if ($status_val === '1' || $status_val === '3') {
                $status_query = " AND a.status = '" . $status_val . "'";
            } else if ($status_val === '2' || strtolower($status_val) === 'reject') {
                $status_query = " AND (a.status NOT IN ('1', '3') OR a.status IS NULL)";
            }
        }

        $salesman_query = "";
        if (!empty($data['salesmanid']) && $data['salesmanid'] !== 'all') {
            $salesman_query = " AND a.salesmanid = '" . $this->db->escape_str($data['salesmanid']) . "'";
        }

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
                a.approved_by,
                a.approved_date,
                a.created_date
            FROM req_visit_professional a
            LEFT JOIN m_sales_salesman b ON a.salesmanid = b.salesmanid
            LEFT JOIN m_customer c ON a.customerid = c.customerid
            WHERE 1=1 {$date_query} {$status_query} {$salesman_query} {$strquery}
            ORDER BY a.periode DESC, a.created_date DESC
        ";

        return $this->db->query($sql)->result_array();
    }
}
