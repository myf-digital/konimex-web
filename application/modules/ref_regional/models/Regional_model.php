<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Regional_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_regional');
        if (!empty($id)) {
            $data['idregional'] = $id;
        }
        return $this->db->insert('ref_regional', $data);
    }

    public function update($data)
    {
        $this->db->where('idregional', $data['idregional']);
        return $this->db->update('ref_regional', $data);
    }

    public function delete($data)
    {
        $this->db->where('idregional', $data['idregional']);
        return $this->db->delete('ref_regional');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_regional a';
        return easy_pagging($data, $field, $table);
    }

}
