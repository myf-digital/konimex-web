<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_spot_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer_spot');
        if (!empty($id)) {
            $data[''] = $id;
        }
        return $this->db->insert('m_customer_spot', $data);
    }

    public function update($data)
    {
        $this->db->where('', $data['']);
        return $this->db->update('m_customer_spot', $data);
    }

    public function delete($data)
    {
        $this->db->where('', $data['']);
        return $this->db->delete('m_customer_spot');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_customer_spot a';
        return easy_pagging($data, $field, $table);
    }

}
