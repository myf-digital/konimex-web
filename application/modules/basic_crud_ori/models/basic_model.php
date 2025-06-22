<?php 
class Basic_model extends CI_Model { 
	
	function get_area() {
		$this->db->select("area_id,area_name");
		$this->db->from("table_area");
		
		$data = $this->db->get()->result_array();
		$options = "";
		
		foreach($data as $v_area) {
			$options .= '<option value="'.$v_area['area_id'].'">'.$v_area['area_name'].'</option>';
		}
		
		return $options;
		
	}
	
	function get_region($area) {
		$this->db->select("region_id,region_name");
		$this->db->from("table_region");
		$this->db->where("area_id",$area);
		
		$data = $this->db->get()->result_array();
		return $data;
	
	}
	
	function get_branch($region) {
		$this->db->select("branch_id,branch_name");
		$this->db->from("table_branch");
		$this->db->where("region_id",$region);
		
		$data = $this->db->get()->result_array();
		return $data;
	
	}
	
	
	//get list data
	function get_list_data() {
		
		$param = $this->input->post(array('page', 'rows', 'sort', 'order', 'filterRules'));
        $offset = intval(($param['page'] - 1) * $param['rows']);		
		
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
                    if (!empty($value)) {
                        if ($opt == 'contains') {
                          	$this->db->like($field,$value);	
                        } else if ($opt == 'greater') {
                            $this->db->where('$field > ',$value);
                        } else if ($opt == 'less') {
                           	$this->db->where('$field < ',$value);
                        } else if ($opt == 'notequal') {
                           	$this->db->where('$field != ',$value);
                        } else if ($opt == 'equal') {
                           	$this->db->where($field,$value);
                        }
                        $loop++; // flag where
                    }
                } else {
                    if (!empty($value)) {
                        if ($opt == 'contains') {                            
							$this->db->like($field,$value);	
                        } else if ($opt == 'greater') {
							$this->db->where('$field > ',$value);
                        } else if ($opt == 'less') {
                            $this->db->where('$field < ',$value);
                        } else if ($opt == 'notequal') {
                            $this->db->where('$field != ',$value);
                        } else if ($opt == 'equal') {
                            $this->db->where($field,$value);
                        }
                    }
                }
            }
        }
		
		$response = array();	
		
		//set order
		if (!empty($param['sort'])) {
			$sort = $param['sort'];
			$order = $param['order'];			
			$this->db->order_by($sort,$order); 
		}		
			
		//query data
		$this->db->select("basic_crud.id,basic_crud.input_text,DATE_FORMAT(basic_crud.startdate, '%d-%m-%Y') startdate,basic_crud.select_box,basic_crud.email,basic_crud.text_area,area_name,region_name,branch_name");	
		$this->db->from("basic_crud");
		$this->db->join("table_area", "basic_crud.area = table_area.area_id");
		$this->db->join("table_region", "basic_crud.region = table_region.region_id");
		$this->db->join("table_branch", "basic_crud.branch = table_branch.branch_id");
		$data = $this->db->get();
		
		//echo $this->db->last_query(); die();
		
		$response['total'] = $data->num_rows();
		$response['rows']  = $data->result();
		return $response;
    } 
	
	function get_data_export() {
		
		$this->db->select("basic_crud.id,basic_crud.input_text,DATE_FORMAT(basic_crud.startdate, '%d-%m-%Y') startdate,basic_crud.select_box,basic_crud.email,basic_crud.text_area,area_name,region_name,branch_name");	
		$this->db->from("basic_crud");
		$this->db->join("table_area", "basic_crud.area = table_area.area_id");
		$this->db->join("table_region", "basic_crud.region = table_region.region_id");
		$this->db->join("table_branch", "basic_crud.branch = table_branch.branch_id");
		$data = $this->db->get()->result_array();
		
		return $data;
		
	}
	
	function get_data_edit($id) {
		
		$this->db->select("id,input_text,DATE_FORMAT(startdate, '%d-%m-%Y') startdate, select_box,email,text_area,area,region,branch");
		$this->db->from("basic_crud");
		$this->db->where("id",$id);
		$data = $this->db->get()->row();
		return $data;
		
	}

	function insert_data() {
	
		$data['input_text'] 	= $this->input->post('input_text');
		$data['startdate']		= date("Y-m-d",strtotime($this->input->post('startdate')));
		$data['select_box']		= $this->input->post('select_box');
		$data['email']			= $this->input->post('email');
		$data['text_area']		= $this->input->post('text_area');
		$data['area']			= $this->input->post('area');
		$data['region']			= $this->input->post('region');
		$data['branch']			= $this->input->post('branch');
		
		$q = $this->db->insert('basic_crud',$data);
		return $q;	
	}	
	
	function update_data() {
		
		$id 					= $this->input->post('id');
		$data['input_text'] 	= $this->input->post('input_text');
		$data['startdate']		= date("Y-m-d",strtotime($this->input->post('startdate')));
		$data['select_box']		= $this->input->post('select_box');
		$data['email']			= $this->input->post('email');
		$data['text_area']		= $this->input->post('text_area');
		$data['area']			= $this->input->post('area');
		$data['region']			= $this->input->post('region');
		$data['branch']			= $this->input->post('branch');
		
		$this->db->where("id",$id);
		$q = $this->db->update('basic_crud',$data);
		return $q;
		
	}
	
	function delete_data() {
		
		$del = false;
		$id = $this->input->post('id');
		if (!empty($id)) {
			$this->db->where("id",$id);
			$del = $this->db->delete("basic_crud");
			
		}
		
		return $del;
	}
	
	function get_value_area($area) {
		
		$this->db->select("area_id,area_name");
		$this->db->from("table_area");
		$data = $this->db->get()->result_array();
		$op = "";
		foreach($data as $val_area) {
		
			if ($area == $val_area['area_id']) {
				$op .= '<option value="'.$val_area['area_id'].'" selected>'.$val_area['area_name'].'</option>';
			} else {
				$op .= '<option value="'.$val_area['area_id'].'">'.$val_area['area_name'].'</option>';
			}
			
		}
		
		return $op;
		
	}
	
	function get_value_region($area,$region) {
		
		$this->db->select("region_id,region_name");
		$this->db->from("table_region");
		$this->db->where("area_id",$area);
		$data = $this->db->get()->result_array();
		$op = "";
		foreach($data as $val_ter) {
		
			if ($region == $val_ter['region_id']) {
				$op .= '<option value="'.$val_ter['region_id'].'" selected>'.$val_ter['region_name'].'</option>';
			} else {
				$op .= '<option value="'.$val_ter['region_id'].'">'.$val_ter['region_name'].'</option>';
			}
			
		}
		
		return $op;
		
	}
	
	function get_value_branch($region,$branch) {
		
		$this->db->select("branch_id,branch_name");
		$this->db->from("table_branch");
		$this->db->where("region_id",$region);
		$data = $this->db->get()->result_array();
		$op = "";
		foreach($data as $val_ter) {
		
			if ($branch == $val_ter['branch_id']) {
				$op .= '<option value="'.$val_ter['branch_id'].'" selected>'.$val_ter['branch_name'].'</option>';
			} else {
				$op .= '<option value="'.$val_ter['branch_id'].'">'.$val_ter['branch_name'].'</option>';
			}
			
		}
		
		return $op;
	
	}
	
}