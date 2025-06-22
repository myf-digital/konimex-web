<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_type_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer_type');
        if (!empty($id)) {
            $data['typeid'] = $id;
        }
        return $this->db->insert('m_customer_type', $data);
    }

    public function update($data)
    {
        $this->db->where('typeid', $data['typeid']);
        return $this->db->update('m_customer_type', $data);
    }

    public function delete($data)
    {
        $this->db->where('typeid', $data['typeid']);
        return $this->db->delete('m_customer_type');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_customer_type a';
        return easy_pagging($data, $field, $table);
    }

}
