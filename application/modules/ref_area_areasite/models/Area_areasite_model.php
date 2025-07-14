<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_areasite_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_areasite');
        if (!empty($id)) {
            $data['areaid'] = $id;
        }
        return $this->db->insert('m_area_areasite', $data);
    }

    public function update($data)
    {
        $this->db->where('areaid', $data['areaid']);
        return $this->db->update('m_area_areasite', $data);
    }

    public function delete($data)
    {
        $this->db->where('areaid', $data['areaid']);
        return $this->db->delete('m_area_areasite');
    }

    public function load($data)
    {
        $field = "a.*,b.nama_regional ";
        $table = 'm_area_areasite a';
        $join = " left join m_area_regional b on a.regionalid=b.regionalid ";
        return easy_pagging($data, $field, $table.$join);
    }

}
