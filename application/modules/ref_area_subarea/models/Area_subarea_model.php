<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_subarea_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_subarea');
        if (!empty($id)) {
            $data['subareaid'] = $id;
        }
        return $this->db->insert('m_area_subarea', $data);
    }

    public function update($data)
    {
        $this->db->where('subareaid', $data['subareaid']);
        return $this->db->update('m_area_subarea', $data);
    }

    public function delete($data)
    {
        $this->db->where('subareaid', $data['subareaid']);
        return $this->db->delete('m_area_subarea');
    }

    public function load($data)
    {
        $field = "a.*, b.nama_regional, c.nama_area ";
        $table = 'm_area_subarea a';
        $join = " left join m_area_regional b on a.regionalid=b.regionalid";
        $join .= " left join m_area_areasite c on a.areaid=c.areaid";
        return easy_pagging($data, $field, $table.$join);
    }

}
