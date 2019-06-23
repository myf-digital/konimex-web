<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Matrix_aspek_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_matrix_aspek');
        if (!empty($id)) {
            $data['id_matrix_aspek'] = $id;
        }
		
		$data_aspek = array();
		if(isset($data['id_aspek'])){ $data_aspek = $data['id_aspek']; unset($data['id_aspek']); }

			for($i=0;$i<count($data_aspek);$i++){
				$data_array = array(
					"id_matrix" => $data['id_matrix'],
					"id_aspek" => (int) $data_aspek[$i]
				);
				$this->db->insert("ref_matrix_aspek", $data_array);
			}

			if(count($data_aspek)>0){
				return true;
			}else{
				return false;
			}
    }

    public function update($data)
    {
        $this->db->where('id_matrix_aspek', $data['id_matrix_aspek']);
        return $this->db->update('ref_matrix_aspek', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_matrix_aspek', $data['id_matrix_aspek']);
        return $this->db->delete('ref_matrix_aspek');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'ref_matrix_aspek a';
        return easy_pagging($data, $field, $table);
    }

}
