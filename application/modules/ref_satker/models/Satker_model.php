<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Satker_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_satuan_kerja');
        if (!empty($id)) {
            $data['id_satker'] = $id;
        }
        return $this->db->insert('ref_satuan_kerja', $data);
    }

    public function update($data)
    {
        $this->db->where('id_satker', $data['id_satker']);
        return $this->db->update('ref_satuan_kerja', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_satker', $data['id_satker']);
        return $this->db->delete('ref_satuan_kerja');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_satuan_kerja a';
        return easy_pagging($data, $field, $table);
    }

}
