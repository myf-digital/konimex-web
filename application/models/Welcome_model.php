<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome_model extends CI_Model {
	
	public function create($data){
		$id = IDGenerator::getInstance()->nextID('ex_example');
		if (!empty($id)) {$data['example_id'] = $id;}
		return $this->db->insert('ex_example', $data);
	}
	
	public function update($data){
		$this->db->where('example_id', $data['example_id']);
		return $this->db->update('ex_example', $data);
	}
	
	public function delete($data){
		$this->db->where('example_id', $data['example_id']);
		return $this->db->delete('ex_example');
	}
	
	public function load($data){
		$field = "a.*";
		$table = 'ex_example a';
		return easy_pagging($data, $field, $table);
	}
	
}
