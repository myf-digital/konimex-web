<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_gudang');
        if (!empty($id)) {
            $data['gudangid'] = $id;
        }
        return $this->db->insert('m_gudang', $data);
    }

    public function update($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('gudangid', $data['gudangid']);
        return $this->db->update('m_gudang', $data);
    }

    public function delete($data)
    {
        $this->db->where('gudangid', $data['gudangid']);
        $this->db->where('siteid', $data['siteid']);
        return $this->db->delete('m_gudang');
    }

    public function load($data)
    {
        $field = "a.*, case when a.status_aktif = 1 then 'Active' when a.status_aktif = 0 then 'Not Active' end aktifstatus  ";
        $table = 'm_gudang a';
        return easy_pagging($data, $field, $table);
    }

}
