<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resource_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_resource');
        if (!empty($id)) {
            $data['resource_id'] = $id;
        }
		$data["password"] = md5($data["password"]);
        return $this->db->insert('app_resource', $data);
    }

    public function update($data)
    {
        $this->db->where('resource_id', $data['resource_id']);
        return $this->db->update('app_resource', $data);
    }

    public function delete($data)
    {
        $this->db->where('resource_id', $data['resource_id']);
        return $this->db->delete('app_resource');
    }

    public function load($data)
    {
        $field = "a.*, b.role_name, concat(c.satker,'(',c.kode_satker,')') satker ";
        $table = 'app_resource a';
        $joins = ' left join app_role b on a.role_id = b.role_id left join ref_satuan_kerja c on a.id_satker=c.id_satker ';
        return easy_pagging($data, $field, $table.$joins);
    }

}
