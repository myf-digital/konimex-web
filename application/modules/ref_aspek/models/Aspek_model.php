<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aspek_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_aspek');
        if (!empty($id)) {
            $data['id_aspek'] = $id;
        }
        return $this->db->insert('ref_aspek', $data);
    }

    public function update($data)
    {
        $this->db->where('id_aspek', $data['id_aspek']);
        return $this->db->update('ref_aspek', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_aspek', $data['id_aspek']);
        return $this->db->delete('ref_aspek');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_aspek a';
        return easy_pagging($data, $field, $table);
    }

}
