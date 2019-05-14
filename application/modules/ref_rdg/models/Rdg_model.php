<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rdg_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_rdg');
        if (!empty($id)) {
            $data['id_rdg'] = $id;
        }
        return $this->db->insert('ref_rdg', $data);
    }

    public function update($data)
    {
        $this->db->where('id_rdg', $data['id_rdg']);
        return $this->db->update('ref_rdg', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_rdg', $data['id_rdg']);
        return $this->db->delete('ref_rdg');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_rdg a';
        return easy_pagging($data, $field, $table);
    }

}
