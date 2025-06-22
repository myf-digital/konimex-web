<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_salesman_category_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_sales_salesman_category');
        if (!empty($id)) {
            $data[''] = $id;
        }
        return $this->db->insert('m_sales_salesman_category', $data);
    }

    public function update($data)
    {
        $this->db->where('', $data['']);
        return $this->db->update('m_sales_salesman_category', $data);
    }

    public function delete($data)
    {
        $this->db->where('', $data['']);
        return $this->db->delete('m_sales_salesman_category');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_sales_salesman_category a';
        return easy_pagging($data, $field, $table);
    }

}
