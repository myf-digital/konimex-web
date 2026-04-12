<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_propinsi_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_propinsi');
        if (!empty($id)) {
            $data['propinsiid'] = $id;
        }
        return $this->db->insert('m_area_propinsi', $data);
    }

    public function update($data)
    {
        $this->db->where('propinsiid', $data['propinsiid']);
        return $this->db->update('m_area_propinsi', $data);
    }

    public function delete($data)
    {
        $this->db->where('propinsiid', $data['propinsiid']);
        return $this->db->delete('m_area_propinsi');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_area_propinsi a';
        return easy_pagging($data, $field, $table);
    }

}
