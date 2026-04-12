<?php 
class Change_pass_model extends CI_Model {
	
	function cekOldPass($old_pass) {
		$user = $this->session->userdata('username');
		
		$this->db->select("userid");
		$this->db->from("adm_user");
		$this->db->where(
			array(
				'username' => $user,
				'password' => md5(trim($old_pass))
			)
		);
		$num = $this->db->get()->num_rows();		
		return $num;
	}
	
	function insert_data($user) {
		
		$new_pass = md5($this->input->post("new_pass"));
		$data = array("password" => $new_pass);		
		$this->db->where("username",$user);
		$q = $this->db->update("adm_user",$data);
		
		return $q;
	
	}
	
}