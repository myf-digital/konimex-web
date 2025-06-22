<?php 
class User_model extends CI_Model { 
	
	function get_list_data() {
	
		$param = $this->input->post(array('page', 'rows', 'sort', 'order', 'filterRules'));
        $offset = intval(($param['page'] - 1) * $param['rows']);
		
		$cond = '';
        if (count($param['filterRules']) > 0) {
            $filter = json_decode($param['filterRules']);
            $loop = 0;
            foreach ($filter as $json) {
                
				$rule = get_object_vars($json);
                $field = $rule['field'];
                $opt = $rule['op'];
                $value = $rule['value'];
                if ($loop == 0) {
                    if (!empty($value)) {
                        if ($opt == 'contains') {
                           $this->db->where("$field like '%$value%'");						   
                        } else if ($opt == 'greater') {
							$this->db->where("$field >",$value);                           
                        } else if ($opt == 'less') {
							$this->db->where("$field <", $value);                           
                        } else if ($opt == 'notequal') {
							$this->db->where("$field !=",$value);                           
                        } else if ($opt == 'equal') {
							$this->db->where($field,$value);                           
                        }
                        $loop++;
                    }
                } else {
                    if (!empty($value)) {
                         if ($opt == 'contains') {
                           $this->db->where("$field like '%$value%'");						   
                        } else if ($opt == 'greater') {
							$this->db->where("$field >",$value);                           
                        } else if ($opt == 'less') {
							$this->db->where("$field <",$value);                           
                        } else if ($opt == 'notequal') {
							$this->db->where("$field !=",$value);                           
                        } else if ($opt == 'equal') {
							$this->db->where($field,$value);                          
                        }
                    }
                }
            }
        }
		
		$response = array();		
		 
        if (empty($param['sort'])) {
            $this->db->order_by('userid', 'ASC');			
        } else {		
			$sort = $param['sort'];
            $order = $param['order'];
            $this->db->order_by($sort,$order);			
        }
		$this->db->select("*");
		$this->db->from("adm_user");
        $q = $this->db->get();
		$response['total'] = $q->num_rows();
        $response['rows']  = $q->result();
		return $response;
	}
	
	function get_role_options() {
		
		
		$this->db->select("role_id,role_name");
		$this->db->from("adm_roles");
		$data = $this->db->get()->result_array();
		
		return $data;
		
	}
	
	function get_value_edit($id) {
		
		$this->db->select("*");
		$this->db->from("adm_user");
		$this->db->where("userid",$id);
		$data = $this->db->get()->row();
		
		return $data;
	}
	
	function insert_data() {
		
		$pass = $this->get_password();
		$data['username'] 		= $this->input->post('username');
		$data['user_status'] 	= $this->input->post('user_status');
		$data['role_id'] 		= $this->input->post('role_id');
		$data['first_login'] 	= "Y";
		$data['password'] 		= md5($pass)	;
		$data['password_date']	= date("Y-m-d H:i:s");
		$data['created_date']	= date("Y-m-d H:i:s");
		$data['created_by']		= $this->session->userdata('username');
		
		$q = $this->db->insert('adm_user',$data);
		return $q;	
		
	}
	
	function get_password() {
		$this->db->select("value_config");
		$this->db->from("adm_config");
		$this->db->where("name_config","PASSDEF");
		
		$data = $this->db->get()->row()->value_config;
		return $data;
	}
	
	function update_data() {
		
		$id = $this->input->post('userid');
		
		$data['username'] 		= $this->input->post('username');
		$data['user_status'] 	= $this->input->post('user_status');
		$data['role_id'] 		= $this->input->post('role_id');
		$data['modified_date']	= date("Y-m-d H:i:s");
		$data['modified_by']	= $this->session->userdata('username');
		
		$this->db->where('userid',$id);
		$q = $this->db->update('adm_user',$data);
		return $q;	
		
	}
	
	function reset_password() {
		$pass = $this->generate_password(6);
		$id = $this->input->post('userid');
		
		$data['password'] = md5($pass);
		$this->db->where("userid",$id);
		$q = $this->db->update("adm_user",$data);
		if ($q) {
			//get email
			$q = $this->db->query("
				select b.email, a.username from adm_user a, pengajar b
				where a.userid = '".$id."'
				and a.id_pengajar = b.id_pengajar
			");
			
			$email 		= $q->row()->email;
			$username 	= $q->row()->username;
			
			//send email
			$mess = "
			Reset Password Berhasil
			User 		: ".$username." 
			Password	: ".$pass."
			
			
			Regards,
			
			SPEKTRO-BI";
			
			//send email
			$config['protocol']  = 'smtp';
			$config['smtp_host'] = 'ssl://srv27.niagahoster.com';
			$config['smtp_port'] = '465';
			$config['smtp_user'] = 'admin_support@spektro-bi.com';
			$config['smtp_pass'] = 'spektro123';
			$config['charset'] 	 = 'utf-8';
			$config['newline']   = "\r\n";
	
			// Loads the email library
			$this->load->library('email',$config);
			// FCPATH refers to the CodeIgniter install directory
			// Specifying a file to be attached with the email
			//$file = FCPATH . 'license.txt';
			// Defines the email details
			$this->email->from('admin_support@spektro-bi.com', 'PERPUSTAKAAN-BI');
			$this->email->to($email);
			//$this->email->cc('another@example.com');
			//$this->email->bcc('one-another@example.com');
			$this->email->subject('Reset Password');
			$this->email->message($mess);
			//$this->email->attach($file);
			// The email->send() statement will return a true or false
			// If true, the email will be sent
			if ($this->email->send()) {
			  return "OK";
			} else { 
			   echo $this->email->print_debugger();	
			   die();
			   //return "NOK";
			}
		}
		
		return $q;
	}
	


}