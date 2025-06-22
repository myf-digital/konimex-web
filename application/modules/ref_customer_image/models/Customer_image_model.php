<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_image_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer_image');
        if (!empty($id)) {
            $data['id'] = $id;
        }
        return $this->db->insert('m_customer_image', $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('m_customer_image', $data);
    }

    public function delete($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->delete('m_customer_image');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_customer_image a';
        return easy_pagging($data, $field, $table);
    }

}
