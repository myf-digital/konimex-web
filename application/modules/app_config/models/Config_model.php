<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_config');
        if (!empty($id)) {
            $data['configuration_id'] = $id;
        }
        return $this->db->insert('app_config', $data);
    }

    public function update($data)
    {
        $this->db->where('configuration_id', $data['configuration_id']);
        return $this->db->update('app_config', $data);
    }

    public function delete($data)
    {
        $this->db->where('configuration_id', $data['configuration_id']);
        return $this->db->delete('app_config');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_config a';
        return easy_pagging($data, $field, $table);
    }

}
