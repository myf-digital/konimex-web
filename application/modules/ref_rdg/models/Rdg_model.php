<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rdg_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_rdg');
        if (!empty($id)) {
            $data['id_rdg'] = $id;
        }
			$data_aspek = array();
			$data_satker = array();
			if(isset($data['id_aspek'])){ $data_aspek = $data['id_aspek']; unset($data['id_aspek']); }
			if(isset($data['id_satker'])){ $data_satker = $data['id_satker']; unset($data['id_satker']); }
			
			$this->db->insert('ref_rdg', $data);
			$idrdg = $this->db->insert_id();
			for($i=0;$i<count($data_aspek);$i++){
				$data_array = array(
					"id_rdg" => (int) $idrdg,
					"id_aspek" => (int) $data_aspek[$i]
				);
				$this->db->insert("ref_map_rdg", $data_array);
			}

			for($i=0;$i<count($data_satker);$i++){
				$data_array = array(
					"id_rdg" => (int) $idrdg,
					"id_satker" => (int) $data_satker[$i]
				);
				$this->db->insert("ref_rdg_satker", $data_array);
			}
			
			if(!$idrdg){
				return false;
			}else{
				return true;
			}
			 
    }

    public function update($data)
    {
		$idrdg = $data["id_rdg"];
		if (isset($data["id_aspek"])){

			$data_aspek = array();
			$data_aspek = $data['id_aspek'];
			unset($data["id_aspek"]);
			$this->db->where("id_rdg", $idrdg);
			$this->db->delete("ref_map_rdg");
			
			for($i=0;$i<count($data_aspek);$i++){
				$data_array = array(
					"id_aspek" => (int) $data_aspek[$i],
					"id_rdg" => (int) $idrdg
				);
				$this->db->insert("ref_map_rdg", $data_array);
			}
		}
		
		if (isset($data["id_satker"])){

			$data_satker = array();
			$data_satker = $data['id_satker'];
			unset($data["id_satker"]);
			$this->db->where("id_rdg", $idrdg);
			$this->db->delete("ref_rdg_satker");
			
			for($i=0;$i<count($data_satker);$i++){
				$data_array = array(
					"id_rdg" => (int) $idrdg,
					"id_satker" => (int) $data_satker[$i]
				);
				$this->db->insert("ref_rdg_satker", $data_array);
			}
		}
		
        $this->db->where('id_rdg', $data['id_rdg']);
        return $this->db->update('ref_rdg', $data);
				
    }

    public function delete($data)
    {
        $this->db->where('id_rdg', $data['id_rdg']);
        $this->db->delete('ref_map_rdg');

        $this->db->where('id_rdg', $data['id_rdg']);
        $this->db->delete('ref_rdg_satker');

        $this->db->where('id_rdg', $data['id_rdg']);
        return $this->db->delete('ref_rdg');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = "(select a.*,  date_format(tanggal, '%d %M %Y') reformat_tanggal,
				  (SELECT GROUP_CONCAT(y.kode_satker) AS kode_satker FROM ref_rdg_satker x left join ref_satuan_kerja y on x.id_satker=y.id_satker 
				  where x.id_rdg=a.id_rdg GROUP BY id_rdg) kode_satker ";
        $table .= " from ref_rdg a ) as a";
        return easy_pagging($data, $field, $table);
    }

}
