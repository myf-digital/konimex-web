<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_site_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_setup_site');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        return $this->db->insert('m_setup_site', $data);
    }

    public function update($data)
    {
        $this->db->where('siteid', $data['siteid']);
        return $this->db->update('m_setup_site', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        return $this->db->delete('m_setup_site');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_setup_site a';
        return easy_pagging($data, $field, $table);
    }

}
