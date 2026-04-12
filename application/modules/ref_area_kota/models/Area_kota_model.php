<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_kota_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_kota');
        if (!empty($id)) {
            $data['kotaid'] = $id;
        }
        return $this->db->insert('m_area_kota', $data);
    }

    public function update($data)
    {
        $this->db->where('kotaid', $data['kotaid']);
        return $this->db->update('m_area_kota', $data);
    }

    public function delete($data)
    {
        $this->db->where('kotaid', $data['kotaid']);
        return $this->db->delete('m_area_kota');
    }

    public function load($data)
    {
        $field = "a.*, b.propinsiid,b.nama_propinsi ";
        $table = " m_area_kota a ";
        $join = " left join m_area_propinsi b on a.propinsiid=b.propinsiid";
        return easy_pagging($data, $field, $table.$join);
    }

}
