<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_product');
        if (!empty($id)) {
            $data['productid'] = $id;
        }

        $this->db->select("*");
		$this->db->from("ref_brand");
        $this->db->where("brandid", $data['brandid']);
        $vdata = $this->db->get()->row();

        $data['nama_brand'] = $vdata->brand;
        return $this->db->insert("m_product", $data);
    }

    public function update($data)
    {
        $this->db->select("*");
		$this->db->from("ref_brand");
        $this->db->where("brandid", $data['brandid']);
        $vdata = $this->db->get()->row();
        $data['nama_brand'] = $vdata->brand;
        $this->db->where('productid', $data['productid']);
        return $this->db->update('m_product', $data);
    }

    public function delete($data)
    {
        $this->db->where('productid', $data['productid']);
        return $this->db->delete('m_product');
    }

    public function load($data)
    {
        if (empty($data['sort'])) {
            $data['sort'] = 'a.modified_date desc, a.created_date';
            $data['order'] = 'desc';
        }
        $field = "a.* ";
        $table = " (
            select
                a.productid,
                a.barcode,
                a.nama_invoice,
                case when a.group_product = '0' then null
                else a.group_product
                end as group_product,
                case when a.category_product = '0' then null
                else a.category_product
                end as category_product,
                a.brandid,
                a.h_grosir,
                a.h_ritel,
                a.status,
                case when a.status='A' then 'ACTIVE' else 'DISCONTINUE' end status_desc,
                b.brand nama_brand,
                a.created_date,
                a.modified_date
            from m_product a
            left join ref_brand b on a.brandid=b.brandid
        ) a";
        return easy_pagging($data, $field, $table);
    }

    public function list()
    {
        $sql = "
            select
                a.productid,
                a.barcode,
                a.nama_invoice,
                case when a.group_product = '0' then null
                    else a.group_product
                end as group_product,
                case when a.category_product = '0' then null
                    else a.category_product
                end as category_product,
                a.brandid,
                a.h_grosir,
                a.h_ritel,
                a.status,
                case when a.status='A' then 'ACTIVE' else 'DISCONTINUE' end status_desc,
                b.brand nama_brand,
                a.created_date,
                a.modified_date
            from m_product a
            left join ref_brand b on a.brandid=b.brandid
            order by a.modified_date desc, a.created_date desc
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_all_brands()
    {
        return $this->db->select("brandid, brand")->from("ref_brand")->get()->result_array();
    }

    public function get_reference_data()
    {
        $groups = $this->db->query("
            SELECT DISTINCT `value` as `name` FROM ref_param_global WHERE key_param = 'key_group_prod' AND `value` IS NOT NULL AND `value` != '' AND `value` != '0'
            UNION
            SELECT DISTINCT group_product as `name` FROM m_product WHERE group_product IS NOT NULL AND group_product != '' AND group_product != '0'
            ORDER BY `name` ASC
        ")->result_array();

        $categories = $this->db->query("
            SELECT DISTINCT `value` as `name` FROM ref_param_global WHERE key_param = 'key_category_prod' AND `value` IS NOT NULL AND `value` != '' AND `value` != '0'
            UNION
            SELECT DISTINCT category_product as `name` FROM m_product WHERE category_product IS NOT NULL AND category_product != '' AND category_product != '0'
            ORDER BY `name` ASC
        ")->result_array();

        $brands = $this->db->query("
            SELECT brandid, brand as `name` FROM ref_brand
            WHERE brand IS NOT NULL AND brand != ''
            ORDER BY brandid ASC
        ")->result_array();

        return [
            'groups' => $groups,
            'categories' => $categories,
            'brands' => $brands
        ];
    }

    public function insert_batch_on_duplicate($table, $data, $update_fields = [])
    {
        if (empty($data)) return false;

        $CI =& get_instance();
        $columns = array_keys($data[0]);

        $escaped_columns = array_map(function($col) use ($CI) {
            return $CI->db->protect_identifiers($col);
        }, $columns);

        $values = [];
        foreach ($data as $row) {
            $row_values = [];
            foreach ($columns as $col) {
                $val = $row[$col] ?? null;
                $row_values[] = is_null($val) ? "NULL" : $CI->db->escape($val);
            }
            $values[] = "(" . implode(',', $row_values) . ")";
        }

        $sql = "INSERT INTO " . $CI->db->protect_identifiers($table) .
            " (" . implode(',', $escaped_columns) . ") VALUES " .
            implode(',', $values);

        if (empty($update_fields)) {
            $update_fields = $columns;
        }

        $updates = [];
        foreach ($update_fields as $field) {
            $updates[] = $CI->db->protect_identifiers($field) . " = VALUES(" . $CI->db->protect_identifiers($field) . ")";
        }

        $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updates);

        return $CI->db->query($sql);
    }
}
