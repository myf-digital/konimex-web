<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setupsite_db_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_setupsite_db');
        if (!empty($id)) {
            $data['Id'] = $id;
        }
        return $this->db->insert('m_setupsite_db', $data);
    }

    public function update($data)
    {
        $this->db->where('Id', $data['Id']);
        return $this->db->update('m_setupsite_db', $data);
    }

    public function delete($data)
    {
        $this->db->where('Id', $data['Id']);
        return $this->db->delete('m_setupsite_db');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_setupsite_db a';
        return easy_pagging($data, $field, $table);
    }

}
