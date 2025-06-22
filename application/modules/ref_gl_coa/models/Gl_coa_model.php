<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gl_coa_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_gl_coa');
        if (!empty($id)) {
            $data['coa_id'] = $id;
        }
        return $this->db->insert('m_gl_coa', $data);
    }

    public function update($data)
    {
        $this->db->where('coa_id', $data['coa_id']);
        return $this->db->update('m_gl_coa', $data);
    }

    public function delete($data)
    {
        $this->db->where('coa_id', $data['coa_id']);
        return $this->db->delete('m_gl_coa');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_gl_coa a';
        return easy_pagging($data, $field, $table);
    }

}
