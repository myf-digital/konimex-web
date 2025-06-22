<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_city_model extends CI_Model
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
        $field = "a.* ";
        $table = " (select a.subareaid, a.nama_area, a.regionalid, b.nama_regional, c.areaid, c.nama_area nama_aream from m_area_subarea a 
					left join m_area_regional b on a.regionalid=b.regionalid 
					left join m_area_areasite c on a.areaid=c.areaid ) a ";
        return easy_pagging($data, $field, $table);
    }

}
