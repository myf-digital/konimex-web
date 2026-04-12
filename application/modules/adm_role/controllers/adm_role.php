<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adm_role extends CI_Controller {
	
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
		$this->load->model('role_model','model');
		$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
		
    }
	
	public function index()
	{
		
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
				$update_button = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"edit_view('+row.role_id+')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Update\"><i class=\"fa fa-edit\"></i> Update\
								  </a>";
				$assigment_button = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"assigment_view('+row.role_id+')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Update\"><i class=\"fa fa-plus\"></i> Assigment Menu\
								  </a>";					
			}

			if ($this->gparam['privilage']->privilage_delete == 'Y') {	
				$delete_button = "<a style=\"margin:4px;\" class=\"btn btn-danger btn-xs\" onclick=\"delete_data('+row.role_id+')\" href=\"javascript:void(0)\" group=\"\" data-toggle=\"tooltip\" title=\"Delete\">\
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
					'delete'		=> $delete_button,
					'assigment'		=> $assigment_button
			);
			
			$this->load->view('role_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}
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
					$role_status = $edit->role_status;
					
					if ($role_status == 'Y') {
						$value = '<option value="Y" selected>Valid</option>
								  <option value="N">Invalid</option>';
					} else if ($role_status == 'N') {
						$value = '<option value="Y">Valid</option>
								  <option value="N" selected>Invalid</option>';	
					} else {
						$value = '<option value="Y">Valid</option>
								  <option value="N">Invalid</option>';
					}
					
					$data['role_name'] = $edit->role_name;			
					$data['role_id'] = $id;
					$status = '<label class="select">
									<select name="role_status" class="chosen-select">
										<option value="">--Select--</option>
										'.$value.'									
									</select> 
								</label>';
					
					$data['header'] 		= 'Edit Role';
					$data['submit'] 		= 'update';	
				} else {
					echo $this->gparam['restrict']; die();
				}
			} else {
				if ($this->gparam['privilage']->privilage_create == 'Y') {	
					$status = '<label class="select">
									<select name="role_status" class="chosen-select">
										<option value="">--Select--</option>
										<option value="Y">Valid</option>
										<option value="N">Invalid</option>									
									</select> 
								</label>';
					$data['header'] = 'Add New Role';
					$data['submit'] = 'create';
				} else {
					echo $this->gparam['restrict']; die();
				}
			}
			
			$data['status'] = $status;	
			$this->load->view('crud_role_view',$data);
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
	
	function cek_role() {
		
		$id = "";
		$role 	= $this->input->post('role_name');
		$id 	= $this->input->post('role_id');
		
		//echo $role."--".$id; die(); 
		if ($id == "") {
			$cek = $this->model->cekRoleName($role);			
			if ($cek > 0) {
				$json = false;
			} else {
				$json = true;
			}
		} else {
			$cek = $this->model->cekRoleNameUpdate($role,$id);			
			if ($cek > 0) {
				$json = false;
			} else {
				$json = true;
			}
		}
		
		echo json_encode($json);		
	}
	
	public function delete() {
		
		$delete = $this->model->delete_data();
		if ($delete) {
			$json = true;
		}
		echo json_encode($json);	
	}
	
	function load_data() {
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data();
		
        echo json_encode($list);
	}
	
	function role_assigment() {
		
		$role_id = $this->uri->segment(3);	
		$role_name = $this->model->get_role_name($role_id);		
		
		$html = '<input type="hidden" name="role_id" value="'.$role_id.'" />';
		$html .= '<table class="table table-striped table-bordered table-hover table-condensed">';
		$html .= '
			<thead>
				<tr>
					<th>No</th>
					<th>Menu Name</th>
					<th>Module Name</th>
					<th>Menu Icon</th>
					<th>View</th>
					<th>Create</th>
					<th>Update</th>
					<th>Delete</th>
				</tr>	
			</thead>';
		if ($this->gparam['privilage']->privilage_view == 'Y') { 
			if ($this->gparam['privilage']->privilage_update == 'Y') { 
				$html .= '<tbody>';
				$menu_ass = $this->model->get_assigment();
				$i= 1;
				foreach ($menu_ass as $v_ass) {
					
					$cek_view 	= $this->model->cek_role_ass($role_id,$v_ass['menu_id'],'privilage_view');
					$cek_create = $this->model->cek_role_ass($role_id,$v_ass['menu_id'],'privilage_create');
					$cek_update = $this->model->cek_role_ass($role_id,$v_ass['menu_id'],'privilage_update');
					$cek_delete = $this->model->cek_role_ass($role_id,$v_ass['menu_id'],'privilage_delete');
					
					$html .='<tr>';
					$html .='<td>'.$i.'<input type="hidden" name="menu_id_'.$i.'" value="'.$v_ass['menu_id'].'" /></td>';
					$html .='<td>'.$v_ass['menu_name'].'</td>';
					$html .='<td>'.$v_ass['module_name'].'</td>';
					
					if ($v_ass['menu_icon'] == "") {
						$html .= '<td></td>';
					} else {
						$html .= '<td align="center" valign="middle"><i class="fa '.$v_ass['menu_icon'].' fa-lg"></i></td>';
					}
					
					
					if ($cek_view == 'Y') {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_view_'.$i.'" class="onoffswitch-checkbox" id="privilage_view_'.$i.'" value="Y" checked>
										<label class="onoffswitch-label" for="privilage_view_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';					
					} else {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_view_'.$i.'" id="privilage_view_'.$i.'" class="onoffswitch-checkbox" value="Y">
										<label class="onoffswitch-label" for="privilage_view_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					}
					
					if ($cek_create == 'Y') {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_create_'.$i.'" id="privilage_create_'.$i.'" class="onoffswitch-checkbox" value="Y" checked>
										<label class="onoffswitch-label" for="privilage_create_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					} else {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_create_'.$i.'" id="privilage_create_'.$i.'" class="onoffswitch-checkbox" value="Y">
										<label class="onoffswitch-label" for="privilage_create_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					}
					if ($cek_update == 'Y') {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_update_'.$i.'" id="privilage_update_'.$i.'" class="onoffswitch-checkbox" value="Y" checked>
										<label class="onoffswitch-label" for="privilage_update_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					} else {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_update_'.$i.'" id="privilage_update_'.$i.'" class="onoffswitch-checkbox" value="Y">
										<label class="onoffswitch-label" for="privilage_update_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					}
					if ($cek_delete == 'Y') {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_delete_'.$i.'" id="privilage_delete_'.$i.'" class="onoffswitch-checkbox" value="Y" checked>
										<label class="onoffswitch-label" for="privilage_delete_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					} else {
						$html .='<td>
									<span class="onoffswitch">
										<input type="checkbox" name="privilage_delete_'.$i.'" id="privilage_delete_'.$i.'" class="onoffswitch-checkbox" value="Y">
										<label class="onoffswitch-label" for="privilage_delete_'.$i.'"> 
											<div class="onoffswitch-inner" data-swchon-text="Yes" data-swchoff-text="No"></div> 
											<div class="onoffswitch-switch"></div>
										</label> 
									</span>	
								</td>';			
					}
					
					$html .='</tr>';
					$i++;
					
				}
				$html .= '</tbody>';
			}
		}
		$html .='</table>';
		$html .= '<input type="hidden" name="total_menu" value="'.$i.'" />';
		$data['html'] = $html;
		$data['controller'] 	= $this->gparam['controller'];				
		$data['action'] 		= base_url()."index.php/".$this->gparam['controller']."/save_assigment";
		$data['header'] 		= 'Role Assigment - '.$role_name;
		$data['submit'] 		= 'assigment';
		$this->load->view('role_assigment_view',$data);
	}
	
	function save_assigment() {
		
		$save = $this->input->post("submit");
		if ($save == "assigment") {
			
			$save_ass = $this->model->save_assigment();
			if ($save_ass) {
				$json = true;
			}
		}		
		echo json_encode($json);
	}
	
	
}
