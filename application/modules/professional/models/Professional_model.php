<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professional_model extends CI_Model
{
    public function load($data)
    {
        $field = "a.* ";
        $table = " (
                    SELECT 
                        d.cab, 
                        d.cabang, 
                        a.customerid, 
                        a.nama_customer, 
                        b.id_professional, 
                        b.nama_professional, 
                        mp.productid, 
                        mp.category_product, 
                        mp.nama_invoice, 
                        mp.sat_kecil, 
                        0 as qty,
                        0 as total
                    FROM m_customer a 
                    LEFT JOIN ref_professional_mapping b ON b.customerid = a.customerid
                    LEFT JOIN m_cabang_area c ON c.subareaid = a.subareaid
                    LEFT JOIN m_cabang d ON d.kode_cab = c.kode_cab
                    RIGHT OUTER JOIN m_product mp ON mp.status = 'A'
                    WHERE a.customerid IS NOT NULL
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_target($data)
    {
        $field = "a.* ";
        $table = " (
                    SELECT
                        a.*,
                        COALESCE(s.total_actual, 0) as total_actual
                    FROM target_professional a
                    LEFT JOIN (
                        SELECT 
                            tsm.customerid,
                            tsm.userid,
                            tsd.productid,
                            SUM(tsd.qty_kecil) as total_actual
                        FROM t_sales_detail tsd
                        JOIN t_sales_master tsm ON tsm.no_po = tsd.no_po
                        GROUP BY tsd.productid
                    ) s ON s.userid = a.id_professional AND s.customerid = a.customerid AND s.productid = a.productid
                    WHERE a.id_professional IS NOT NULL
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_history($data)
    {
        $field = "a.* ";
        $table = " (
                    select a.*
                    from target_uploads a
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_history_detail($data)
    {
        $uploadId = $data['id'] ? str_replace('/', '', $data['id']) : null;

        $field = "a.* ";
        $table = " (
                    select a.*
                    from target_professional_history a
                    where a.upload_id = '{$uploadId}'
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function insert_batch_on_duplicate($table, $data, $update_fields = [])
    {
        if (empty($data)) return false;

        $CI =& get_instance();

        // ambil kolom dari index pertama
        $columns = array_keys($data[0]);

        // escape nama kolom
        $escaped_columns = array_map(function($col) use ($CI) {
            return $CI->db->protect_identifiers($col);
        }, $columns);

        $values = [];

        foreach ($data as $row) {
            $row_values = [];

            foreach ($columns as $col) {
                $row_values[] = $CI->db->escape($row[$col]);
            }

            $values[] = "(" . implode(',', $row_values) . ")";
        }

        // build query utama
        $sql = "INSERT INTO " . $CI->db->protect_identifiers($table) .
            " (" . implode(',', $escaped_columns) . ") VALUES " .
            implode(',', $values);

        // kalau tidak ditentukan, update semua kolom kecuali primary key (opsional)
        if (empty($update_fields)) {
            $update_fields = $columns;
        }

        // build ON DUPLICATE KEY UPDATE
        $updates = [];
        foreach ($update_fields as $field) {
            $updates[] = $CI->db->protect_identifiers($field) . 
                        " = VALUES(" . $CI->db->protect_identifiers($field) . ")";
        }

        $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updates);

        return $CI->db->query($sql);
    }
}
