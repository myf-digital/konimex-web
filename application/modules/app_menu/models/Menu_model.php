<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_menu');
        if (!empty($id)) {
            $data['menu_id'] = $id;
        }
        if (!empty($data['status'])) {
            $data['status'] = ($data['status'] == 'active' ? null : 'RD');
        }
        return $this->db->insert('app_menu', $data);
    }

    public function update($data)
    {
        if (!empty($data['status'])) {
            $data['status'] = ($data['status'] == 'active' ? null : 'RD');
        }
        $this->db->where('menu_id', $data['menu_id']);
        return $this->db->update('app_menu', $data);
    }

    public function delete($data)
    {
        $this->db->where('menu_id', $data['menu_id']);
        return $this->db->delete('app_menu');
    }

    public function load($data)
    {
        $field = "
            a.*, 
            b.menu_name as menu_parent,
            (CASE
                WHEN a.status IS NULL THEN 'Active'
                WHEN a.status = 'RD' THEN 'Non Active'
                WHEN a.status = 'RA' THEN 'Non Active'
            END) as status_label
        ";
        $table = 'app_menu a';
        $join = " left join app_menu b on a.parent_id=b.menu_id";
        return easy_pagging($data, $field, $table . $join);
    }

}
