<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restrict_location_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_restrict_location');
        if (!empty($id)) {
            $data['id'] = $id;
        }
        return $this->db->insert('app_restrict_location', $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('app_restrict_location', $data);
    }

    public function delete($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->delete('app_restrict_location');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_restrict_location a';
        return easy_pagging($data, $field, $table);
    }

}
