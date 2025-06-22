<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_kirim_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_kirim');
        if (!empty($id)) {
            $data['areakirimid'] = $id;
        }
        return $this->db->insert('m_area_kirim', $data);
    }

    public function update($data)
    {
        $this->db->where('areakirimid', $data['areakirimid']);
        $this->db->where('siteid', $data['siteid']);
        return $this->db->update('m_area_kirim', $data);
    }

    public function delete($data)
    {
        $this->db->where('areakirimid', $data['areakirimid']);
        $this->db->where('siteid', $data['siteid']);
        return $this->db->delete('m_area_kirim');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_area_kirim a';
        return easy_pagging($data, $field, $table);
    }

}
