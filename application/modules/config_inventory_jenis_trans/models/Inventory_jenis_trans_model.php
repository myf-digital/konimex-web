<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_jenis_trans_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_config_inventory_jenis_trans');
        if (!empty($id)) {
            $data['jenis_trans'] = $id;
        }
        return $this->db->insert('m_config_inventory_jenis_trans', $data);
    }

    public function update($data)
    {
        $this->db->where('jenis_trans', $data['jenis_trans']);
        return $this->db->update('m_config_inventory_jenis_trans', $data);
    }

    public function delete($data)
    {
        $this->db->where('jenis_trans', $data['jenis_trans']);
        return $this->db->delete('m_config_inventory_jenis_trans');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_config_inventory_jenis_trans a';
        return easy_pagging($data, $field, $table);
    }

}
