<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Constant_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_constant');
        if (!empty($id)) {
            $data['app_variable_id'] = $id;
        }
        return $this->db->insert('app_constant', $data);
    }

    public function update($data)
    {
        $this->db->where('app_variable_id', $data['app_variable_id']);
        return $this->db->update('app_constant', $data);
    }

    public function delete($data)
    {
        $this->db->where('app_variable_id', $data['app_variable_id']);
        return $this->db->delete('app_constant');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_constant a';
        return easy_pagging($data, $field, $table);
    }

}
