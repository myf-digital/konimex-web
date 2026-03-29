<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_product_model extends CI_Model
{
    public function load($data)
    {
        $field = "a.* ";
        $table = " (
                    select
                        a.*,
                        b.cabang
                    from stock_product a
                    left join m_cabang b on b.cab = a.nama_cabang
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_history($data)
    {
        $field = "a.* ";
        $table = " (
                    select a.*
                    from stock_uploads a
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_history_detail($data)
    {
        $uploadId = $data['id'] ? str_replace('/', '', $data['id']) : null;

        $field = "a.* ";
        $table = " (
                    select
                        a.*,
                        b.cabang
                    from stock_product_history a
                    left join m_cabang b on b.cab = a.nama_cabang
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
