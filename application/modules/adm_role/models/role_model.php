<?php 
class Role_model extends CI_Model {
	
	function cekRoleName($role) {
		
		$this->db->select("role_id");
		$this->db->from("adm_roles");
		$this->db->where("upper(role_name)",strtoupper($role));
		$num = $this->db->get()->num_rows();
		return $num;
		
	}
	
	function cekRoleNameUpdate($role,$id) {
		
		//get old role 
		$this->db->select("role_name");
		$this->db->from("adm_roles");
		$this->db->where("role_id",$id);
		$oldRole = $this->db->get()->row()->role_name;
		if (strtoupper($oldRole) == strtoupper($role)) {
			$num = 0;
		} else {
			$this->db->select("role_id");
			$this->db->from("adm_roles");
			$this->db->where("upper(role_name)",strtoupper($role));
			$num = $this->db->get()->num_rows();
			
		}		
		return $num;
	}
	
	function get_value_edit($id) {
		$this->db->select('*');
		$this->db->from('adm_roles');
		$this->db->where('role_id',$id);
		$data = $this->db->get()->row();
		return $data;
	}
	
	function get_list_data() {
	
		$param = $this->input->post(array('page', 'rows', 'sort', 'order', 'filterRules'));
        $offset = intval(($param['page'] - 1) * $param['rows']);
		
		// clause filter, array not null
        $cond = '';
        if (count($param['filterRules']) > 0) {
            $filter = json_decode($param['filterRules']);
            $loop = 0;
            foreach ($filter as $json) {
                // convert to array
                $rule = get_object_vars($json);
                // declare variable
                $field = $rule['field'];
                $opt = $rule['op'];
                $value = $rule['value'];
                if ($loop == 0) {
                    // user where
                    if (!empty($value)) {
                        if ($opt == 'contains') {
                           $this->db->where("$field like '%$value%'");
						   // $cond .= "where ($field like '%$value%')";
                        } else if ($opt == 'greater') {
							$this->db->where("$field >",$value);
                           // $cond .= "where $field > '$value'";
                        } else if ($opt == 'less') {
							$this->db->where("$field <", $value);
                           // $cond .= "where $field < '$value'";
                        } else if ($opt == 'notequal') {
							$this->db->where("$field !=",$value);
                           // $cond .= "where $field != '$value'";
                        } else if ($opt == 'equal') {
							$this->db->where($field,$value);
                           // $cond .= "where $field = '$value'";
                        }
                        $loop++; // flag where
                    }
                } else {
                    // user and
                    if (!empty($value)) {
                         if ($opt == 'contains') {
                           $this->db->where("$field like '%$value%'");
						   // $cond .= "where ($field like '%$value%')";
                        } else if ($opt == 'greater') {
							$this->db->where("$field >",$value);
                           // $cond .= "where $field > '$value'";
                        } else if ($opt == 'less') {
							$this->db->where("$field <",$value);
                           // $cond .= "where $field < '$value'";
                        } else if ($opt == 'notequal') {
							$this->db->where("$field !=",$value);
                           // $cond .= "where $field != '$value'";
                        } else if ($opt == 'equal') {
							$this->db->where($field,$value);
                           // $cond .= "where $field = '$value'";
                        }
                    }
                }
            }
        }
		
		$response = array();
		//$table = 'adm_roles'; 
		 
        if (empty($param['sort'])) {
            $this->db->order_by('role_id', 'ASC');
			/* if (empty($cond)) {
                $sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table  order by role_id limit ?, ?";
                $sqlcount = "select * from $table";
            } else {
                $sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table  $cond order by role_id limit ?, ?";
                $sqlcount = "select * from $table $cond";
            }
            $result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
            $response['total'] = $this->db->query($sqlcount)->num_rows();
            $response['rows'] = $result_array->result(); */
        } else {
		
			$sort = $param['sort'];
            $order = $param['order'];
            $this->db->order_by($sort,$order);			
			/* $sort = $param['sort'];
            $order = $param['order'];
            if (empty($cond)) {
                $sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table order by $sort $order limit ?, ?";
                $sqlcount = "select role_id from $table";
            } else {
                $sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table  $cond order by $sort $order limit ?, ?";
                $sqlcount = "select role_id from $table  $cond";
            }
            $result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
            $response['total'] = $this->db->query($sqlcount)->num_rows();
            $response['rows'] = $result_array->result(); */
        }
		$this->db->select("*");
		$this->db->from("adm_roles");
        $q = $this->db->get();
		$response['total'] = $q->num_rows();
        $response['rows']  = $q->result();
		//print_r($response); die();
		return $response;
	}
	
	function insert_data() {
		
		$data['role_name'] 	= $this->input->post('role_name');
		$data['created_date']	= date("Y-m-d H:i:s");
		$data['created_by']		= $this->session->userdata('username');
		
		$q = $this->db->insert('adm_roles',$data);
		return $q;	
		
	}
	
	function update_data() {
		
		$id = $this->input->post('role_id');
		
		$data['role_name'] 	= $this->input->post('role_name');
		$data['modified_date']	= date("Y-m-d H:i:s");
		$data['modified_by']	= $this->session->userdata('username');
		
		$this->db->where('role_id',$id);
		$q = $this->db->update('adm_roles',$data);
		return $q;	
		
	}
	
	function delete_data() {
		
		$del = false;
		$role_id = $this->input->post('role_id');
		if (!empty($role_id)) {
			$this->db->where("role_id",$role_id);
			$del = $this->db->delete("adm_roles");
			
		}
		
		return $del;
	}
	
	function get_role_name($role_id) {
		$this->db->select("role_name");
		$this->db->from("adm_roles");
		$this->db->where("role_id",$role_id);
		$role_name = $this->db->get()->row()->role_name;
		
		return $role_name;
	}
	
	function get_assigment() {
		$q = $this->db->query("
				SELECT 
				CONCAT(REPEAT('&nbsp;&nbsp;&nbsp;|-&nbsp;&nbsp;&nbsp;', level - 1), 
				CAST(hi.menu_name AS CHAR)) AS menu_name, 
				parent_id, 
				menu_icon,
				module_name,
				type_menu,
				seq_number,
				created_by,
				created_date, 
				modified_by, 
				modified_date, 
				hi.menu_id,
				level 
				FROM (  SELECT 
						hierarchy_connect_by_parent_eq_prior_id(menu_id) AS menu_id, 
						@level AS level 
							FROM ( 
									SELECT @start_with := 0, 
									@id := @start_with, 
									@level := 0 
								) vars, 
							adm_menus WHERE @id IS NOT NULL
							) ho 
				JOIN adm_menus hi ON hi.menu_id = ho.menu_id
			");
			
		$data = $q->result_array();

		return $data;
	}
	
	function cek_role_ass($role_id,$menu_id,$field) {
		
		$this->db->select($field);
		$this->db->from("adm_roles_assignment");
		$this->db->where(
			array(
				"role_id" => $role_id,
				"menu_id" => $menu_id
			)			
		);
		
		$get = $this->db->get();
		$count = $get->num_rows();
		if ($count < 1) {
			$data = "N";
		} else {
			$data = $get->row()->$field;		
		}
		return $data;
		
	}
	
	function save_assigment() {
		
		$role_id = $this->input->post("role_id");
		$total_menu = $this->input->post("total_menu");
		
		//print_r($this->input->post()); die();
		//delete role assigment existing
		$this->db->where("role_id",$role_id);
		$del = $this->db->delete("adm_roles_assignment");
		$del = true;
		if ($del) {
			for($i=1; $i<$total_menu; $i++) {						
				
				$view 	= "";
				$create = "";
				$update = "";
				$delete = "";
				
				$view 	= $this->input->post('privilage_view_'.$i);
				$create = $this->input->post('privilage_create_'.$i);
				$update = $this->input->post('privilage_update_'.$i);
				$delete = $this->input->post('privilage_delete_'.$i);
				
				if ($view == "") {
					$data['privilage_view']	= "N";				
				} else {
					$data['privilage_view']	= $this->input->post('privilage_view_'.$i);				
				}
				
				if ($create == "") {
					$data['privilage_create']	= "N";				
				} else {
					$data['privilage_create']	= $this->input->post('privilage_create_'.$i);				
				}
				
				if ($update == "") {
					$data['privilage_update']	= "N";				
				} else {
					$data['privilage_update']	= $this->input->post('privilage_update_'.$i);				
				}
				
				if ($delete == "") {
					$data['privilage_delete']	= "N";				
				} else {
					$data['privilage_delete']	= $this->input->post('privilage_delete_'.$i);				
				}
				
				$data['menu_id'] 		= $this->input->post('menu_id_'.$i);
				$data['role_id'] 		= $role_id;
				$data['created_date']	= date("Y-m-d H:i:s");
				$data['created_by']		= $this->session->userdata('username');
				//print_r($data); die(); 
				$this->db->insert("adm_roles_assignment",$data);
			}
		}
		
		return true;
			
	}
}