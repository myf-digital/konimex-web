<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_kelurahan_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_area_kelurahan');
        if (!empty($id)) {
            $data['kelurahanid'] = $id;
        }
        return $this->db->insert('m_area_kelurahan', $data);
    }

    public function update($data)
    {
        $this->db->where('kelurahanid', $data['kelurahanid']);
        return $this->db->update('m_area_kelurahan', $data);
    }

    public function delete($data)
    {
        $this->db->where('kelurahanid', $data['kelurahanid']);
        return $this->db->delete('m_area_kelurahan');
    }

    public function load($data)
    {
        $field = " a.*,b.kotaid, b.nama_kota, c.propinsiid, c.nama_propinsi, d.kecamatanid, d.nama_kecamatan ";
        $table = " m_area_kelurahan a ";
        $join = " left join m_area_kota b on a.kotaid=b.kotaid";
        $join .= " left join m_area_propinsi c on a.propinsiid=c.propinsiid ";
        $join .= " left join m_area_kecamatan d on a.kecamatanid=d.kecamatanid ";
        return easy_pagging($data, $field, $table.$join);
    }

}
