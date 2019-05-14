<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_karyawan');
        if (!empty($id)) {
            $data['id_karyawan'] = $id;
        }
        return $this->db->insert('ref_karyawan', $data);
    }

    public function update($data)
    {
        $this->db->where('id_karyawan', $data['id_karyawan']);
        return $this->db->update('ref_karyawan', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_karyawan', $data['id_karyawan']);
        return $this->db->delete('ref_karyawan');
    }

    public function load($data)
    {
        $field = "a.*, b.kode_satker, concat(b.kode_satker,'(',b.satker,')') satker ";
        $table = 'ref_karyawan a left join ref_satuan_kerja b on a.id_satker=b.id_satker';
        return easy_pagging($data, $field, $table);
    }

}
