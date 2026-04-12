<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_city');
        if (!empty($id)) {
            $data['id'] = $id;
        }
        return $this->db->insert('ref_city', $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('ref_city', $data);
    }

    public function delete($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->delete('ref_city');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_city a';
        return easy_pagging($data, $field, $table);
    }

}
