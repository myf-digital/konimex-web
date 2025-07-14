<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_role');
        if (!empty($id)) {
            $data['role_id'] = $id;
        }
        return $this->db->insert('app_role', $data);
    }

    public function update($data)
    {
        $this->db->where('role_id', $data['role_id']);
        return $this->db->update('app_role', $data);
    }

    public function delete($data)
    {
        $this->db->where('role_id', $data['role_id']);
        return $this->db->delete('app_role');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_role a';
        return easy_pagging($data, $field, $table);
    }

}
