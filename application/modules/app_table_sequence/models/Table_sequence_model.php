<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Table_sequence_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_table_sequence');
        if (!empty($id)) {
            $data['id'] = $id;
        }
        return $this->db->insert('app_table_sequence', $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('app_table_sequence', $data);
    }

    public function delete($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->delete('app_table_sequence');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_table_sequence a';
        return easy_pagging($data, $field, $table);
    }

}
