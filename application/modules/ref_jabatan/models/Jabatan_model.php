<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jabatan_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_jabatan');
        if (!empty($id)) {
            $data['idjabatan'] = $id;
        }
        return $this->db->insert('ref_jabatan', $data);
    }

    public function update($data)
    {
        $this->db->where('idjabatan', $data['idjabatan']);
        return $this->db->update('ref_jabatan', $data);
    }

    public function delete($data)
    {
        $this->db->where('idjabatan', $data['idjabatan']);
        return $this->db->delete('ref_jabatan');
    }

    public function load($data)
    {
        $field = "a.*, b.desc restrict_level_desc ";
        $table = 'ref_jabatan a left join ref_param_global b on b.key_param="key_restrict_level" and a.restrict_level=b.value';
        return easy_pagging($data, $field, $table);
    }

}
