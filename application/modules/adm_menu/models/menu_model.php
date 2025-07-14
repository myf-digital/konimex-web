<?php 
class Menu_model extends CI_Model { 
	
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
                            $cond .= "where ($field like '%$value%')";
                        } else if ($opt == 'greater') {
                            $cond .= "where $field > '$value'";
                        } else if ($opt == 'less') {
                            $cond .= "where $field < '$value'";
                        } else if ($opt == 'notequal') {
                            $cond .= "where $field != '$value'";
                        } else if ($opt == 'equal') {
                            $cond .= "where $field = '$value'";
                        }
                        $loop++; // flag where
                    }
                } else {
                    // user and
                    if (!empty($value)) {
                        if ($opt == 'contains') {
                            $cond .= " and ($field like '%$value%')";
                        } else if ($opt == 'greater') {
                            $cond .= " and $field > '$value'";
                        } else if ($opt == 'less') {
                            $cond .= " and $field < '$value'";
                        } else if ($opt == 'notequal') {
                            $cond .= " and $field != '$value'";
                        } else if ($opt == 'equal') {
                            $cond .= " and $field = '$value'";
                        }
                    }
                }
            }
        }
		
		$response = array();		
		
        if (empty($param['sort'])) {
            if (empty($cond)) {
                $sql = "select * from (SELECT 
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
											  JOIN adm_menus hi ON hi.menu_id = ho.menu_id) val 
											  $cond limit ?, ?
							";
                $sqlcount = "select menu_id from adm_menus";
            } else {
                $sql = "select * from (
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
									  JOIN adm_menus hi ON hi.menu_id = ho.menu_id) val
									  $cond limit ?, ?";
                $sqlcount = "select menu_id from adm_menus $cond";
            }
            $result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
            $response['total'] = $this->db->query($sqlcount)->num_rows();
            $response['rows'] = $result_array->result();
        } else {
            $sort = $param['sort'];
            $order = $param['order'];
			
            if (empty($cond)) {
                $sql = "select * from (
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
							  JOIN adm_menus hi ON hi.menu_id = ho.menu_id ) val		
							  order by $sort $order limit ?, ?";
                $sqlcount = "select menu_id from adm_menus";
            } else {
                $sql = " select * from (
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
							  JOIN adm_menus hi ON hi.menu_id = ho.menu_id ) val 
							  $cond order by $sort $order limit ?, ?";
                $sqlcount = "select menu_id from adm_menus  $cond";
            }
            $result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
            $response['total'] = $this->db->query($sqlcount)->num_rows();
            $response['rows'] = $result_array->result();
        }
        return $response;
	}
	
	function get_value_edit($id) {
		
		$this->db->select('*');
		$this->db->from('adm_menus');
		$this->db->where('menu_id',$id);
		$data = $this->db->get()->row();
		return $data;
		
	}
	
	function get_parent_data($parent_id) {
		
		$type_parent = $parent_id - 1;
		$this->db->select("menu_name,menu_id,module_name");
		$this->db->from("adm_menus");
		$this->db->where("type_menu",$type_parent);
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	function insert_data() {
		
		$data['menu_name'] 		= $this->input->post('menu_name');
		$data['menu_icon']		= $this->input->post('menu_icon');
		$data['module_name']	= $this->input->post('module_name');
		$data['type_menu']		= $this->input->post('type_menu');
		$data['seq_number']		= $this->input->post('seq_number');
		$data['parent_id']		= $this->input->post('parent_id');
		$data['created_date']	= date("Y-m-d H:i:s");
		$data['created_by']		= $this->session->userdata('username');
		
		$q = $this->db->insert('adm_menus',$data);
		return $q;	
		
	}
	
	function update_data() {
		
		$id = $this->input->post('menu_id');
		
		$data['menu_name'] 		= $this->input->post('menu_name');
		$data['menu_icon']		= $this->input->post('menu_icon');
		$data['module_name']	= $this->input->post('module_name');
		$data['type_menu']		= $this->input->post('type_menu');
		$data['parent_id']		= $this->input->post('parent_id');
		$data['seq_number']		= $this->input->post('seq_number');
		$data['modified_date']	= date("Y-m-d H:i:s");
		$data['modified_by']	= $this->session->userdata('username');
		
		$this->db->where('menu_id',$id);
		$q = $this->db->update('adm_menus',$data);
		return $q;	
		
	}
	
	function delete_data() {
		$menu_id = $this->input->post('menu_id');
		if (!empty($menu_id)) {
			$this->db->where("menu_id",$menu_id);
			$del = $this->db->delete("adm_menus");
			if ($del) {
				$this->db->where("menu_id",$menu_id);
				$del = $this->db->delete("adm_roles_assignment");
			}			
		}		
		return true;
	}
	
	

}