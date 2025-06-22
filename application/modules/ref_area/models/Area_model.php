<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_area');
        if (!empty($id)) {
            $data['idarea'] = $id;
        }
        return $this->db->insert('ref_area', $data);
    }

    public function update($data)
    {
        $this->db->where('idarea', $data['idarea']);
        return $this->db->update('ref_area', $data);
    }

    public function delete($data)
    {
        $this->db->where('idarea', $data['idarea']);
        return $this->db->delete('ref_area');
    }

    public function load($data)
    {
        $field = "a.*, b.regional ";
        $table = 'ref_area a left join ref_regional b on a.idregional=b.idregional';
        return easy_pagging($data, $field, $table);
    }

}
