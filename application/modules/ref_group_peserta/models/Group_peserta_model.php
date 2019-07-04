<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group_peserta_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_group_peserta');
        if (!empty($id)) {
            $data['id_group'] = $id;
        }

			$data_karyawan = array();
			if(isset($data['id_karyawan'])){ $data_karyawan = $data['id_karyawan']; unset($data['id_karyawan']); }

			$this->db->insert('ref_group_peserta', $data);
			$idgroup = $this->db->insert_id();
			for($i=0;$i<count($data_karyawan);$i++){
				$data_array = array(
					"id_group" => (int) $idgroup,
					"id_karyawan" => (int) $data_karyawan[$i]
				);
				$this->db->insert("ref_group_mapping", $data_array);
			}

			if(!$idgroup){
				return false;
			}else{
				return true;
			}
    }

    public function update($data)
    {
		$idgroup = $data['id_group'];
		if (isset($data["id_karyawan"])){

			$data_karyawan = array();
			$data_karyawan = $data['id_karyawan'];
			unset($data["id_karyawan"]);

			$this->db->where("id_group", $idgroup);
			$this->db->delete("ref_group_mapping");
			
			for($i=0;$i<count($data_karyawan);$i++){
				$data_array = array(
					"id_group" => (int) $idgroup,
					"id_karyawan" => (int) $data_karyawan[$i]
				);
				$this->db->insert("ref_group_mapping", $data_array);
			}

		}
		
        $this->db->where('id_group', $idgroup);
        return $this->db->update('ref_group_peserta', $data);
    }

    public function delete($data)
    {
		$this->db->where("id_group", $data['id_group']);
		$this->db->delete("ref_group_mapping");

        $this->db->where('id_group', $data['id_group']);
        return $this->db->delete('ref_group_peserta');
    }

    public function load($data)
    {
        $field = "a.*,(select count(1) from ref_group_mapping where id_group=a.id_group) as jml_peserta ";
        $table = 'ref_group_peserta a';
        return easy_pagging($data, $field, $table);
    }

}
