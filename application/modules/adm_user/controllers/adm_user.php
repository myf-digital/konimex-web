<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adm_user extends CI_Controller { 
	
	var $gparam = array();
	public function __construct() {
        parent::__construct();
		
		$this->load->library('authlib');
		
		$username = "";		
		
		$cek = $this->authlib->cek_timeout();
		if ($cek == 1) {
			$username = $this->session->userdata('username');
		}
		
		//echo $username;
		if ($username == "") {
			//redirect them to the login page
			echo '<h4 style="margin-top:10px; display:block; text-align:left"><i class="fa fa-warning txt-color-orangeDark"></i> Your login is expired, click <a href="'.base_url().'">here</a> to relogin.</h4>';
			die;
		}
		
		$this->gparam['controller'] = $this->router->fetch_class();
		$this->load->model('user_model','model');
		$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
		
    }

	public function index()	{
		
		if ($this->gparam['privilage']->privilage_view == 'Y') {
		
			$gridopt = $this->input->get(array('psize', 'pnumber'));
			header('Content-Type: text/html');
			$create_button = '""';			
			$update_button = '';
			$delete_button = '';		
			
			if ($this->gparam['privilage']->privilage_create == 'Y') {
				
				//create button
				$create_button = '<a href="javascript:void(0)"  class="btn btn-primary btn-xs" onclick="create_view();" >
									<i class="fa fa-plus"></i> Create
								  </a>';
			}	
			
			if ($this->gparam['privilage']->privilage_update == 'Y') {
				$update_button = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"edit_view('+row.userid+')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Update\"><i class=\"fa fa-edit\"></i> Update\
								  </a>";
				}

			if ($this->gparam['privilage']->privilage_delete == 'Y') {	
				$delete_button = "<a style=\"margin:4px;\" class=\"btn btn-danger btn-xs\" onclick=\"delete_data('+row.userid+')\" href=\"javascript:void(0)\" group=\"\" data-toggle=\"tooltip\" title=\"Delete\">\
									<i class=\"fa fa-trash-o\"></i> Delete\
								</a>"; 
			}				
			
			// pnumber, psize
			$data = array(
					'controller'  	=> $this->gparam['controller'],
					'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
					'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),
					'create' 		=> $create_button,
					'update' 		=> $update_button,
					'delete'		=> $delete_button
			);
			
			$this->load->view('user_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}
	}	
	
	function load_data() {
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data();
		
        echo json_encode($list);
	}
	
	function form() {
		
		if ($this->gparam['privilage']->privilage_view == 'Y') {
			$data['controller'] = $this->gparam['controller'];				
			$data['action'] = base_url()."index.php/".$this->gparam['controller']."/crud";
			$id = $this->uri->segment(3);	
			
			if (!empty($id)) {
				if ($this->gparam['privilage']->privilage_update == 'Y') {				
					//get value for edit
					$edit = $this->model->get_value_edit($id);
					$user_status 	= $edit->user_status;
					$role 			= $edit->role_id;
					
					if ($user_status == 'Y') {
						$value = '<option value="Y" selected>Valid</option>
								  <option value="N">Invalid</option>';
					} else if ($user_status == 'N') {
						$value = '<option value="Y">Valid</option>
								  <option value="N" selected>Invalid</option>';	
					} else {
						$value = '<option value="Y">Valid</option>
								  <option value="N">Invalid</option>';
					}
					
					$data['username'] = $edit->username;			
					$data['userid'] = $id;
					$status = '<label class="select">
									<select name="user_status" class="chosen-select">
										<option value="">--Select--</option>
										'.$value.'									
									</select> 
								</label>';
								
					$role_options = '';
					$get_role = $this->model->get_role_options();			
					foreach ($get_role as $value_role) {
						if ($role == $value_role['role_id']) {
							$role_options .= '<option value="'.$value_role['role_id'].'" selected>'.$value_role['role_name'].'</option>';					
						} else {
							$role_options .= '<option value="'.$value_role['role_id'].'">'.$value_role['role_name'].'</option>';					
						}
					}
					
					$role= '<label class="select">
									<select name="role_id" class="chosen-select">
										<option value="">--Select--</option>
										'.$role_options.'	
									</select> 
								</label>';						
					
					$data['header'] 		= 'Edit User';
					$data['submit'] 		= 'update';	
				} else {
					echo $this->gparam['restrict']; die();
				}
			} else {
				if ($this->gparam['privilage']->privilage_create == 'Y') {	
					$status = '<label class="select">
									<select name="user_status" class="chosen-select">
										<option value="">--Select--</option>
										<option value="Y">Valid</option>
										<option value="N">Invalid</option>									
									</select> 
								</label>';
					$role_options = '';
					$get_role = $this->model->get_role_options();			
					foreach ($get_role as $value_role) {
						$role_options .= '<option value="'.$value_role['role_id'].'">'.$value_role['role_name'].'</option>';
					}
					
					$role= '<label class="select">
									<select name="role_id" class="chosen-select">
										<option value="">--Select--</option>
										'.$role_options.'	
									</select> 
								</label>';			
					$data['header'] = 'Add New User';
					$data['submit'] = 'create';
				} else {
					echo $this->gparam['restrict']; die();
				}
			}
			
			$data['status'] = $status;	
			$data['role'] 	= $role;	
			$this->load->view('crud_user_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}
	}

	public function crud() {
		
		$crud = $this->input->post('submit');
		if ($crud == 'create') {
			
			$insert = $this->model->insert_data();
			if ($insert) {
				$json = true;
			}
			
		} else if ($crud == 'update') {
			$update = $this->model->update_data();
			if ($update) {
				$json = true;			
			}
				
		}
		
		echo json_encode($json);
		
	}	

}