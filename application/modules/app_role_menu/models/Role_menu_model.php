<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_menu_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_role_menu');
        if (!empty($id)) {
            $data['role_menu_id'] = $id;
        }
        return $this->db->insert('app_role_menu', $data);
    }

    public function update($data)
    {
        $this->db->where('role_menu_id', $data['role_menu_id']);
        return $this->db->update('app_role_menu', $data);
    }

    public function delete($data)
    {
        $this->db->where('role_menu_id', $data['role_menu_id']);
        return $this->db->delete('app_role_menu');
    }

    public function load($data)
    {
        $field = "a.*, b.menu_name, c.role_name";
        $table = 'app_role_menu a';
        $joins = ' left join app_menu b on a.menu_id = b.menu_id';
        $joins .= ' left join app_role c on a.role_id = c.role_id';
        return easy_pagging($data, $field, $table.$joins);
    }

}
