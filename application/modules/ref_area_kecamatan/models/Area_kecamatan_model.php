<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_kecamatan_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_kecamatan');
        if (!empty($id)) {
            $data['kecamatanid'] = $id;
        }
        return $this->db->insert('m_area_kecamatan', $data);
    }

    public function update($data)
    {
        $this->db->where('kecamatanid', $data['kecamatanid']);
        return $this->db->update('m_area_kecamatan', $data);
    }

    public function delete($data)
    {
        $this->db->where('kecamatanid', $data['kecamatanid']);
        return $this->db->delete('m_area_kecamatan');
    }

    public function load($data)
    {
        $field = " a.*,b.kotaid, b.nama_kota, c.propinsiid, c.nama_propinsi ";
        $table = " m_area_kecamatan a ";
        $join = " left join m_area_kota b on a.kotaid=b.kotaid";
        $join .= " left join m_area_propinsi c on a.propinsiid=c.propinsiid ";
        return easy_pagging($data, $field, $table.$join);
    }

}
