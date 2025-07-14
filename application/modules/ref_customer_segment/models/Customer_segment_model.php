<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_segment_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer_segment');
        if (!empty($id)) {
            $data['segmentid'] = $id;
        }
        return $this->db->insert('m_customer_segment', $data);
    }

    public function update($data)
    {
        $this->db->where('segmentid', $data['segmentid']);
        return $this->db->update('m_customer_segment', $data);
    }

    public function delete($data)
    {
        $this->db->where('segmentid', $data['segmentid']);
        return $this->db->delete('m_customer_segment');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_customer_segment a';
        return easy_pagging($data, $field, $table);
    }

}
