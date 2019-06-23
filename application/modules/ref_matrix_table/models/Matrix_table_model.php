<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Matrix_table_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_matrix_table');
        if (!empty($id)) {
            $data['id_matrix'] = $id;
        }

			$data_aspek = array();
			$data_grafik = array();
			if(isset($data['id_aspek'])){ $data_aspek = $data['id_aspek']; unset($data['id_aspek']); }
			if(isset($data['id_aspek_grafik'])){ $data_grafik = $data['id_aspek_grafik']; unset($data['id_aspek_grafik']); }

			$this->db->insert('ref_matrix_table', $data);
			$idmatrix = $this->db->insert_id();
			for($i=0;$i<count($data_aspek);$i++){
				$data_array = array(
					"id_matrix" => (int) $idmatrix,
					"id_aspek" => (int) $data_aspek[$i]
				);
				$this->db->insert("ref_matrix_aspek", $data_array);
			}

			for($i=0;$i<count($data_grafik);$i++){
				$data_array = array(
					"id_matrix" => (int) $idmatrix,
					"id_aspek" => (int) $data_grafik[$i]
				);
				$this->db->insert("ref_matrix_grafik", $data_array);
			}
			
			if(!$idmatrix){
				return false;
			}else{
				return true;
			}
    }

    public function update($data)
    {
		$idmatrix = $data['id_matrix'];
		if (isset($data["id_aspek"])){

			$data_aspek = array();
			$data_aspek = $data['id_aspek'];
			unset($data["id_aspek"]);

			$this->db->where("id_matrix", $idmatrix);
			$this->db->delete("ref_matrix_aspek");
			
			for($i=0;$i<count($data_aspek);$i++){
				$data_array = array(
					"id_matrix" => (int) $idmatrix,
					"id_aspek" => (int) $data_aspek[$i],
				);
				$this->db->insert("ref_matrix_aspek", $data_array);
			}

		}
		
		if (isset($data["id_aspek_grafik"])){
			
			$data_grafik = array();
			$data_grafik = $data['id_aspek_grafik'];
			unset($data["id_aspek_grafik"]);

			$this->db->where("id_matrix", $idmatrix);
			$this->db->delete("ref_matrix_grafik");
			
			for($i=0;$i<count($data_grafik);$i++){
				$data_array = array(
					"id_matrix" => (int) $idmatrix,
					"id_aspek" => (int) $data_grafik[$i],
				);
				$this->db->insert("ref_matrix_grafik", $data_array);
			}
		
		}

		
        $this->db->where('id_matrix', $idmatrix);
        return $this->db->update('ref_matrix_table', $data);
    }

    public function delete($data)
    {
		$this->db->where("id_matrix", $data['id_matrix']);
		$this->db->delete("ref_matrix_aspek");

		$this->db->where("id_matrix", $data['id_matrix']);
		$this->db->delete("ref_matrix_grafik");
		
        $this->db->where('id_matrix', $data['id_matrix']);
        return $this->db->delete('ref_matrix_table');
    }

    public function load($data)
    {
        $field = "a.*,(select count(1) from ref_matrix_aspek where id_matrix=a.id_matrix) as jml_aspek ";
        $table = 'ref_matrix_table a';
        return easy_pagging($data, $field, $table);
    }

}
