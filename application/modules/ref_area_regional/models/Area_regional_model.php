<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_regional_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_regional');
        if (!empty($id)) {
            $data['regionalid'] = $id;
        }
        return $this->db->insert('m_area_regional', $data);
    }

    public function update($data)
    {
        $this->db->where('regionalid', $data['regionalid']);
        return $this->db->update('m_area_regional', $data);
    }

    public function delete($data)
    {
        $this->db->where('regionalid', $data['regionalid']);
        return $this->db->delete('m_area_regional');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = "m_area_regional a";
        return easy_pagging($data, $field, $table);
    }

}
