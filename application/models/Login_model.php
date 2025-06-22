<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model { 

	function cek_user_valid($username) {
		
		$this->db->select("userid");
		$this->db->from("adm_user");
		$this->db->where("username",$username);
		
		$cek = $this->db->get()->num_rows();
		return $cek;
	}
	
	//cek user has login from other computer
	function cek_session_exsis($username) {
	
		$this->db->select("userlogin");
		$this->db->from("adm_user_session");
		$this->db->where("userlogin = '".$username."' and logout_status is null");
		$cek_user_exist = $this->db->get()->num_rows();
		//echo $this->db->last_query();
		//echo $cek_user_exist; die();
		$return = 'User '.$username.' Already Login';		
		if ($cek_user_exist == 0) {
			$return = true;
		} else {
			//cek timeout
			$this->db->select('date_expired');
			$this->db->from('adm_user_session');
			$this->db->where('userlogin',$username);
			$this->db->where('logout_status is null');
			$get_expired = $this->db->get()->row()->date_expired;
			
			//$expired = explode(" ",$get_expired);
			$now = date("Y-m-d H:i:s");
			//echo $get_expired." -- ".$now;
			
			$nw = new DateTime($now);
			$ex = new DateTime($get_expired);
			
			if ($nw > $ex) {
				
				//time out update adm_user_session
				$update['date_logout'] = date("Y-m-d H:i:s"); 
				$update['logout_status'] = "Timeout";				
				//$this->session_destroy();
				
				//update timeout
				$this->db->where('userlogin',$username);
				$this->db->where('logout_status is null');
				$this->db->update('adm_user_session',$update);
				
				//
				$return = true;
				
			} 
		}
		return $return;		
	}
	
	//cek lock user
	function cek_user_status($username) {
		
		$this->db->select('user_status');
		$this->db->from('adm_user');
		$this->db->where('username',$username);
		$user_status = $this->db->get()->row()->user_status;
		
		$return = 'User '.$username.' Has Been Lock';
		if ($user_status == 'Y') {
			$return = true;
		}
		
		$return = true;
		return $return;		
		
	}
	
	//cek valid user/pass	
	function cek_user_pass($username,$password) {
		
		//max try variable
		$max_try = 3;
		
		//cek count lock
		$this->db->select('username');
		$this->db->from('adm_lock');
		$this->db->where('username',$username);
		$cek_lock = $this->db->get()->num_rows();
		
		if ($cek_lock >= $max_try) {
			
			//lock user
			$this->db->set("user_status","'N'",false);
			$this->db->where("username",$username);
			$update = $this->db->update("adm_user");
			
			if ($update) {
				$return = 'user '.$username.' Has Been Lock';
			} else {
				echo "Failed Lock User"; die();
			}
			
			
		} else {
			
			//cek user password
			$encript = md5($password);
			$this->db->select("userid");
			$this->db->from("adm_user");
			$this->db->where(
				array(
					'username'	=> $username,
					'password'	=> $encript
				)
			);
			$cek_user = $this->db->get()->num_rows();
			if ($cek_user < 1) {			
				//wrong password insert to adm_lock
				$data_insert['username'] = $username;
				$data_insert['log_date'] = date("Y-m-d H:i:s");
				$insert = $this->db->insert('adm_lock',$data_insert);
				
				if ($insert) {
					
					//return total try err message
					$cnt = $cek_lock + 1;					
					$return = 'Wrong Password '.$cnt.' Time of '.$max_try;				
				
				} else {
					echo 'Failed Insert table '.$table_lock; die();
				}	
				
				//$return = true;
			} else {
				//insert adm_user_session
				$return = true;
				
			}
		}	
		
		return $return;
	
	}
	
	//create session 
	function create_sess($username) {
		//echo "create session <br>";
		//delete adm lock
		$this->db->where('username',$username);
		$del_process = $this->db->delete('adm_lock');
		//echo $this->db->last_query();
		if ($del_process) {
			
			//insert user_session
			$data = array(
				"userlogin"		=> $username,
				"date_login"	=> date("Y-m-d H:i:s"),
				"date_expired" 	=> date("Y-m-d H:i:s",strtotime("+120 minutes"))
			);
			
			$q = $this->db->insert('adm_user_session',$data);
			//echo $this->db->last_query(); die();
			if ($q) {
				//echo "Masuk $q";
				//generate session
				$this->db->select('userid,username,role_id');
				$this->db->from('adm_user');
				//$this->db->join('ref_karyawan b', 'a.id_karyawan = b.id', 'left');
				$this->db->where('username',$username);
				$data = $this->db->get()->row();
				//echo $this->db->last_query(); die();
				$session_data = array(
					'userid'	=> $data->userid,
					'username'	=> $data->username,
					'role_id'	=> $data->role_id
				);
				
				//delete lock
				$this->db->where("username",$username);
				$this->db->delete("adm_lock");
				
				$this->session->set_userdata($session_data);	
				$username = $this->session->userdata('username');
				//echo "username - ".$username; die();
				$return = true;
			} else {
				echo "Failed Create Session"; die();
			}
			
			
		} else {
			$return = false;
		}
		
		return $return;	
		
	}
	
	//cek timeout
	function cek_timeout_session($user) {
		
		//cek if user session is null
		if ($user != "") {
		
			//get expired from adm_user_session
			$this->db->select('date_expired');
			$this->db->from('adm_user_session');
			$this->db->where('userlogin',$user);
			$this->db->where('logout_status is null');
			$get_expired = $this->db->get()->row()->date_expired;
			
			//$expired = explode(" ",$get_expired);
			$now = date("Y-m-d H:i:s");
			//echo $get_expired." -- ".$now;
			
			$nw = new DateTime($now);
			$ex = new DateTime($get_expired);
			
			if ($nw > $ex) {
				
				//time out update adm_user_session
				$update['date_logout'] = date("Y-m-d H:i:s"); 
				$update['logout_status'] = "Timeout";			
				$return = false;
				$this->session_destroy();
				
			} else {
				//session still running update expired_date 60 minute  
				$update['date_expired'] = date("Y-m-d H:i:s",strtotime("+60 minutes"));
				$return = true;
			}
			
			$this->db->where('userlogin',$user);
			$this->db->where('logout_status is null');
			$this->db->update('adm_user_session',$update);
			//echo $this->db->last_query(); die();
			
			return $return;
			
		} else {
			return false;
		}
	}
	
	function session_destroy() {
		$this->session->sess_destroy();
	}
	
	function logout_process($username) {
		$data = array();
		$data['date_logout'] = date("Y-m-d H:i:s");
		$data['logout_status'] = "Normal Logout";
		
		$this->db->where("userlogin",$username);
		$this->db->where('logout_status is null');
		$update = $this->db->update('adm_user_session',$data);
		
		if ($update) {
			$this->session_destroy();
		}
		
		return true;
	}
}