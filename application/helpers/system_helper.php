<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');



function GetMenuByRole($role_id,$type_menu,$parent_id = "") {
	
	$ci =& get_instance();
	// start query
	
	if ($parent_id == "") {
		$sql = '
			select a.role_id, a.menu_id,b.type_menu, b.menu_name, b.menu_icon, b.module_name, b.seq_number, 
			b.parent_id, a.privilage_view, a.privilage_create, a.privilage_update, a.privilage_delete 
			from adm_roles_assignment a 
			left join adm_menus b on a.menu_id = b.menu_id where a.role_id = '.$role_id.' and b.type_menu = '.$type_menu.' order by b.seq_number
		';
	} else {
		$sql = '
			select a.role_id, a.menu_id,b.type_menu, b.menu_name, b.menu_icon, b.module_name, b.seq_number, 
			b.parent_id, a.privilage_view, a.privilage_create, a.privilage_update, a.privilage_delete 
			from adm_roles_assignment a 
			left join adm_menus b on a.menu_id = b.menu_id where a.role_id = '.$role_id.' and b.type_menu = '.$type_menu.' and b.parent_id = '.$parent_id.' order by b.seq_number
		';
		
		//echo $sql;
	}
	$result_array = $ci->db->query($sql);
	
	return $result_array->result_array();
}
// FIX OK, use requsive
function GenerateMenuToHTML() {
	$ci =& get_instance();
	$role_id = $ci->session->userdata('role_id');
	$list_menu = '';
	
	//generate parent
	$parent = GetMenuByRole($role_id,0);
	
	foreach ($parent as $parent_menu) {
		if ($parent_menu['privilage_view'] == 'Y') {
		
			$list_menu .= '<li>';
			$list_menu .= '<a href="'.$parent_menu['module_name'].'"><i class="fa fa-lg fa-fw '.$parent_menu['menu_icon'].'"></i> 
			<span class="menu-item-parent">'.$parent_menu['menu_name'].'</span></a>';
			$list_menu .= generateChild($role_id,1,$parent_menu['menu_id']);
			$list_menu .= '</li>';
		
		}
	}
	
	return $list_menu;
}

function generateChild($role_id,$type_menu,$parent_id) {
	
	$child = GetMenuByRole($role_id,$type_menu,$parent_id);
	$count = count($child); 
	//print_r($child);
	$list_menu = '';
	
	if ($count > 0) {
		$list_menu .= '<ul>';
		//first child
		foreach ($child as $child_menu) {
			if ($child_menu['privilage_view'] == 'Y') {	
				$list_menu .= '<li>';
				$list_menu .= '<a href="'.$child_menu['module_name'].'">'.$child_menu['menu_name'].'</a>';
				if ($type_menu == 1) {
					$list_menu .= generateChild($role_id,2,$child_menu['menu_id']);
				}
				$list_menu .= '</li>';
			}
		}
		$list_menu .= '</ul>'; 
	}
	return $list_menu;
}

function getPrivilage($module_name) {
	
	$ci =& get_instance();
	$role_id = $ci->session->userdata('role_id');
	// start query
	
		$sql = "
			select a.privilage_view, a.privilage_create, a.privilage_update, a.privilage_delete, a.privilage_approve 
			from adm_roles_assignment a 
			left join adm_menus b on a.menu_id = b.menu_id 
			where a.role_id = ".$role_id." 
			and b.module_name = '".$module_name."'
		";
	
	$result_array = $ci->db->query($sql);	
	return $result_array->row();
	
}

function UpdateHelper($table,$update_field,$where) {
	
	$ci =& get_instance();
	$ci->db->where($where);
	$update = $ci->db->update($table,$update_field);
	return $update;
	
}

function getOneValue($table, $field, $where) {
	$ci =& get_instance();
	// =============================== start here
	$ci->db->select("$field as VAL");
	$ci->db->from($table);
	$ci->db->where($where);
	$value = $ci->db->get()->row()->VAL;
	return $value;
}

function GetValue($table, $field, $where) {
	$ci =& get_instance();
	// =============================== start here
	$ci->db->select("$field as VAL");
	$ci->db->from($table);
	$ci->db->where($where);
	$value = $ci->db->get()->row()->VAL;
	return $value;
}


/*
	@return count from table
*/
function countQuery($table, $field, $where = '') {
	$ci =& get_instance();
	// =============================== start here
	$ci->db->select($field);
	$ci->db->from($table);
	if ($where != "") {
		$ci->db->where($where);
	}	
	$count = $ci->db->get()->num_rows();
	
	return $count;
}

function insertHelper($table, $data) {
	$ci =& get_instance();
	
	$insert = $ci->db->insert($table, $data);
	return $insert;
}

function getOptionList($table,$field_name,$field_value,$empty_value="Y",$option_name,$order_by='',$where='') {
	$ci =& get_instance();
	
	if ($order_by != "") {
        $ci->db->order_by($order_by);
    }
	
	if ($where != "") {
		$ci->db->where($where);
	}
	
	$field_value = strtoupper($field_value);
	$field_name = strtoupper($field_name);
	
	$ci->db->select("$field_value a_val,$field_name b_val");
	$q = $ci->db->get($table);        
	$r = $q->result_array();
	//print_r($r); die();
	$html = "";
	$html .='<select class="form-control chosen" name="'.$option_name.'">';
	if ($empty_value=="Y") {
        $html .= '<option value="">--Select--</option>';
    }
	
	foreach($r as $data) {
        $html .= '<option value="'.$data['a_val'].'">'.$data['b_val'].'</option>';
    }
	
	$html .= '</select>';
    return $html;    
}

