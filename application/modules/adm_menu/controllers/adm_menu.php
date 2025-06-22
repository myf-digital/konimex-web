<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adm_menu extends CI_Controller { 
	
	var $gparam = array();
	public function __construct() {
        parent::__construct();
		
		$this->load->library('authlib');
		
		$username = "";		
	
		$cek = $this->authlib->cek_timeout();
		if ($cek == 1) {
			$username = $this->session->userdata('username');
		}
		
		//echo $cek."-".$username; die(); 
		
		//echo $username;
		if ($username == "") {
			//redirect them to the login page
			echo '<h4 style="margin-top:10px; display:block; text-align:left"><i class="fa fa-warning txt-color-orangeDark"></i> Your login is expired, click <a href="'.base_url().'">here</a> to relogin.</h4>';
			die;
		}
		
		$this->gparam['controller'] = $this->router->fetch_class();
		$this->load->model('menu_model','model');
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
				$update_button = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"edit_view('+row.menu_id+')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Update\"><i class=\"fa fa-edit\"></i> Update\
								  </a>";
			}

			if ($this->gparam['privilage']->privilage_delete == 'Y') {	
				$delete_button = "<a style=\"margin:4px;\" class=\"btn btn-danger btn-xs\" onclick=\"delete_data('+row.menu_id+')\" href=\"javascript:void(0)\" group=\"\" data-toggle=\"tooltip\" title=\"Delete\">\
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
			
			$this->load->view('menu_view',$data);
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
	
		$data['controller'] = $this->gparam['controller'];				
		$data['action'] = base_url()."index.php/".$this->gparam['controller']."/crud";
		$id = $this->uri->segment(3);	
		
		if (!empty($id)) {
			//get value for edit
			$edit = $this->model->get_value_edit($id);
			$type_menu_val = $edit->type_menu;
			$parent_id = '';
			
			$type_menu = '<label class="select">
							<select name="type_menu" class="chosen-select" onchange="get_parent(this.value),get_icon(this.value)">
								<option value="">--Select--</option>';

			if ($type_menu_val == '0') {
				$type_menu .= '<option value="0" selected>Parent</option>
								<option value="1">First Child</option>									
								<option value="2">Second Child</option>	'; 
				$menu_icon = '<input placeholder="Click For Choose Icon" id="menu_icon" type="text" name="menu_icon" class="icp icp-auto" value="'.$edit->menu_icon.'" />
				<script>
					$(function() {
						$(\'.icp-auto\').iconpicker();
					});	
				</script>
				';				
			} else if ($type_menu_val == '1') {
				$type_menu .= '<option value="0">Parent</option>
								<option value="1" selected>First Child</option>									
								<option value="2">Second Child</option>	';	
				$menu_icon = '<input placeholder="Disabled" id="menu_icon" type="text" name="menu_icon" value="" disabled />';
				$parent_val = $edit->parent_id;
				$data_parent = $this->model->get_parent_data($type_menu_val);
				foreach ($data_parent as $parent) {
					if ($parent_val == $parent['menu_id']) {
						$parent_id .='<option value="'.$parent['menu_id'].'" selected>'.$parent['menu_name'].' - '.$parent['module_name'].'</option>';				
					} else {
						$parent_id .='<option value="'.$parent['menu_id'].'">'.$parent['menu_name'].' - '.$parent['module_name'].'</option>';				
					}
				}
				
			} else {
				$type_menu .='<option value="0">Parent</option>
								<option value="1">First Child</option>									
								<option value="2" selected>Second Child</option>';
				$menu_icon = '<input placeholder="Disabled" id="menu_icon" type="text" name="menu_icon" value="" disabled />';
				$parent_val = $edit->parent_id;
				$data_parent = $this->model->get_parent_data($type_menu_val);
				foreach ($data_parent as $parent) {
					if ($parent_val == $parent['menu_id']) {
						$parent_id .='<option value="'.$parent['menu_id'].'" selected>'.$parent['menu_name'].' - '.$parent['module_name'].'</option>';				
					} else {
						$parent_id .='<option value="'.$parent['menu_id'].'">'.$parent['menu_name'].' - '.$parent['module_name'].'</option>';				
					}
				}	
			}			
			$type_menu .='</select></label>';
			
			$data['parent_id'] = $parent_id;
			$data['menu_id'] = $id;
			$data['menu_name'] = $edit->menu_name;
			$data['module_name'] = $edit->module_name;
			$data['seq_number'] = $edit->seq_number;
			$data['header'] = 'Edit Menu';
			$data['submit'] = 'update';	
		} else {
		
			$type_menu = '<label class="select">
							<select name="type_menu" class="chosen-select" onchange="get_parent(this.value),get_icon(this.value)">
								<option value="">--Select--</option>
								<option value="0">Parent</option>
								<option value="1">First Child</option>									
								<option value="2">Second Child</option>									
							</select>
						</label>';
			$menu_icon = '<input placeholder="Disabled" id="menu_icon" type="text" name="menu_icon" value="" disabled />';			
			$data['header'] = 'Add Menu';
			$data['submit'] = 'create';
		}
		
		$data['menu_icon'] = $menu_icon;
		$data['type_menu'] = $type_menu;
		$this->load->view('crud_menu_view',$data);
	
	}
	
	function get_parent () {
		$type_menu = $this->input->post('type_menu');
		
		$select  = "<label class=\"select\">";
		$select .= "<select name=\"parent_id\" class=\"chosen-select\">";
		$select .= "<option value=\"\">--Select--</option>";
		if ($type_menu != '0') {
			$data = $this->model->get_parent_data($type_menu);
			
			foreach ($data as $parent_val) {
				$select .= "<option value=\"".$parent_val['menu_id']."\">".$parent_val['menu_name']." - ".$parent_val['module_name']."</option>";
			}
			
		}
		
		$select .= "</select></label>";
		$select .= "<script>
			$(function() {		
				$(\".chosen-select\").chosen();		
			});	

		</script>";
		echo $select;
	}
	
	function get_icon () {
		$type_menu = $this->input->post('type_menu');
		
		if ($type_menu == '0') {
			echo '<label class="input"><input placeholder="Click For Choose Icon" id="menu_icon" type="text" name="menu_icon" class="icp icp-auto" value="" /></label>';
			echo '<script>
					$(function() {
						$(\'.icp-auto\').iconpicker();
					});	
				</script>';
		} else {
			echo '<label class="input"><input placeholder="Disable" id="menu_icon" type="text" name="menu_icon" value="" disabled /></label>';
		
		}
	}
	
	public function delete() {
		
		$delete = $this->model->delete_data();
		if ($delete) {
			$json = true;
		}
		echo json_encode($json);	
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