<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_competitor_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_product_competitor');
        if (!empty($id)) {
            $data['productid'] = $id;
        }
        $data['nama_invoice'] = str_replace("'","`",str_replace('"','`', $data['nama_invoice']));
        return $this->db->insert('m_product_competitor', $data);
    }

    public function update($data)
    {
        $data['nama_invoice'] = str_replace("'","`",str_replace('"','`', $data['nama_invoice']));
        $this->db->where('productid', $data['productid']);
        return $this->db->update('m_product_competitor', $data);
    }

    public function delete($data)
    {
        $this->db->where('productid', $data['productid']);
        return $this->db->delete('m_product_competitor');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table =  " ( select productid, nama_invoice, category, status, case when status='A' then 'ACTIVE' else 'DISCONTINUE' end status_desc from m_product_competitor ) a " ;
        return easy_pagging($data, $field, $table);
    }

}
