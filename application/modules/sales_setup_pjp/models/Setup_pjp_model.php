<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_pjp_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('t_sales_setup_rrk');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        return $this->db->insert('t_sales_setup_rrk', $data);
    }

    public function update($data)
    {
        $this->db->where('siteid', $data['siteid']);
        return $this->db->update('t_sales_setup_rrk', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        return $this->db->delete('t_sales_setup_rrk');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 't_sales_setup_rrk a';
        return easy_pagging($data, $field, $table);
    }

}
