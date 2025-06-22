<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_brand');
        if (!empty($id)) {
            $data['brandid'] = $id;
        }
        return $this->db->insert('ref_brand', $data);
    }

    public function update($data)
    {
        $this->db->where('brandid', $data['brandid']);
        return $this->db->update('ref_brand', $data);
    }

    public function delete($data)
    {
        $this->db->where('brandid', $data['brandid']);
        return $this->db->delete('ref_brand');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_brand a';
        return easy_pagging($data, $field, $table);
    }

}
