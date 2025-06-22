<?php 
class Rep_target_model extends CI_Model { 
	
	//get list data
	function get_list_data() {
		
		$param = $this->input->post(array('page', 'rows', 'sort', 'order', 'filterRules'));
        $offset = intval(($param['page'] - 1) * $param['rows']);		
			
		$sales = "";
		$start = "";
		$finish = "";
		$sales = $this->input->post("sales");
		$start = $this->input->post("start");
		$finish = $this->input->post("finish");
		
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
		$this->db->select("DATE_FORMAT(periode, '%d-%m-%Y') periode,siteid,salesmanid,nama_salesman,prinsipalid,nama_prinsipal,target,sales,percent");	
		$this->db->from("t_target_prinsipal_salesman");		
		
		if ($sales != "") {
			$this->db->where("salesmanid",$sales);
		}
		
		if (($start != "") && ($finish != "")) {
			$this->db->where("periode between '".$start."' and '".$finish."'");
		}
		
		$this->db->limit($param['rows'], $offset);
		
		$data = $this->db->get();
		
		//echo $this->db->last_query(); die();
		
		$this->db->select("periode");	
		$this->db->from("t_target_prinsipal_salesman");		
		
		if ($sales != "") {
			$this->db->where("salesmanid",$sales);
		}
		
		if (($start != "") && ($finish != "")) {
			$this->db->where("periode between '".$start."' and '".$finish."'");
		}
		
		$total = $this->db->get()->num_rows();
		
		$response['total'] = $total;
		$response['rows']  = $data->result();
		return $response;
    } 
	
	function get_salesman() {
		
		$this->db->select("nama_salesman,salesmanid");
		$this->db->from("m_sales_salesman");
		$data = $this->db->get()->result_array();
		
		$return   ='<select name="salesmanid" id="salesmanid" class="chosen-select">';
		$return .='<option value="">--Salesman--</option>';
		foreach ($data as $value) {
			$return .='<option value="'.$value['salesmanid'].'">'.$value['nama_salesman'].'</option>';
		} 		
		$return .= '</select>';		
		return $return;
	}
	
	function get_data_export($sales,$start,$finish) {
		
		$this->db->select("DATE_FORMAT(periode, '%d-%m-%Y') periode,siteid,salesmanid,nama_salesman,prinsipalid,nama_prinsipal,target,sales,percent");	
		$this->db->from("t_target_prinsipal_salesman");		
		if ($sales != "") {
			$this->db->where("salesmanid",$sales);
		}
		
		if (($start != "") && ($finish != "")) {
			$this->db->where("periode between '".$start."' and '".$finish."'");
		}
		$data = $this->db->get()->result_array();
		
		return $data;
		
	}	
	
	
}