<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_config');
        if (!empty($id)) {
            $data['Id'] = $id;
        }
        return $this->db->insert('m_config', $data);
    }

    public function update($data)
    {
        $this->db->where('Id', $data['Id']);
        return $this->db->update('m_config', $data);
    }

    public function delete($data)
    {
        $this->db->where('Id', $data['Id']);
        return $this->db->delete('m_config');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_config a';
        return easy_pagging($data, $field, $table);
    }

}
