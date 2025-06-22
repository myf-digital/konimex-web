<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_bank');
        if (!empty($id)) {
            $data['bankid'] = $id;
        }
        return $this->db->insert('m_bank', $data);
    }

    public function update($data)
    {
        $this->db->where('bankid', $data['bankid']);
        return $this->db->update('m_bank', $data);
    }

    public function delete($data)
    {
        $this->db->where('bankid', $data['bankid']);
        return $this->db->delete('m_bank');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_bank a';
        return easy_pagging($data, $field, $table);
    }

}
