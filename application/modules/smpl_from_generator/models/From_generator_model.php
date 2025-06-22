<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class From_generator_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('test_sample_generator');
        if (!empty($id)) {
            $data['id'] = $id;
        }
        return $this->db->insert('test_sample_generator', $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('test_sample_generator', $data);
    }

    public function delete($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->delete('test_sample_generator');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'test_sample_generator a';
        return easy_pagging($data, $field, $table);
    }

}
