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
		
		$area = "";
		$area = $this->input->post("area");
		
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
		$this->db->where("area",$area);
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
		//insert to table image
		if ($q) {
			$id = $this->db->insert_id();
			$this->upload_image($id);
		}
		
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
		if ($q) {
			
			//cek image exsis
			$cek = $this->image_cek($id);
			if ($cek < 1) {
				$this->upload_image($id);
			} else {
				$this->upload_image_update($id);
			}
			
			
		}
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
	
	function upload_image($id) {
		
		if (isset($_FILES['myfile'])) {
			$this->load->library('uploaderfiler');
			$data = $this->uploaderfiler->upload($_FILES['myfile'], $this->uploaderfiler->getConfigAdmin(DIR_IMAGE));
			// get success upload
			if ($data['isComplete']) {
				$files = $data['data'];
				// loop upload image data, temp image
				$imagesMeta = array();				
				foreach ($files['metas'] as $key => $value) {
					//array_push($imagesMeta, array('id_siswa' => $id_daftar, 'seq_number' => $i, 'image_name' => $value['name'], 'size' => $value['size']));
					$insert_image = array (
						'id' => $id, 
						'image_name' => $value['name'], 
						'size' => $value['size']
					);
						
					$this->db->insert('upload_image',$insert_image);
					
				}
				
				//print_r($imagesMeta); die();
				//$this->db->insert_batch('image_siswa', $imagesMeta);
			}
			if ($data['hasErrors']) {
				$errors = $data['errors'];
				print_r($errors);
			}
		}
		
	}
	
	function upload_image_update($id) {
		
		if (isset($_FILES['myfile'])) {
			
			//get file name
			$this->db->select("image_name");
			$this->db->from("upload_image");
			$this->db->where("id",$id);
			$name = $this->db->get()->row()->image_name;
			
			//delete old image
			$del_url = $_SERVER['DOCUMENT_ROOT'].'/kepegawaian/uploads/'.$name;
			unlink($del_url);			
			
			$this->load->library('uploaderfiler');
			$data = $this->uploaderfiler->upload($_FILES['myfile'], $this->uploaderfiler->getConfigAdmin(DIR_IMAGE));
			// get success upload
			if ($data['isComplete']) {
				$files = $data['data'];
				// loop upload image data, temp image
				$imagesMeta = array();				
				foreach ($files['metas'] as $key => $value) {
					//array_push($imagesMeta, array('id_siswa' => $id_daftar, 'seq_number' => $i, 'image_name' => $value['name'], 'size' => $value['size']));
					$insert_image = array (
						'image_name' => $value['name'], 
						'size' => $value['size']
					);
					$this->db->where('id',$id);	
					$this->db->update('upload_image',$insert_image);
					
				}
				
				//print_r($imagesMeta); die();
				//$this->db->insert_batch('image_siswa', $imagesMeta);
			}
			if ($data['hasErrors']) {
				$errors = $data['errors'];
				print_r($errors);
			}
		}
		
	}
	
	function image_cek($id) {
		$this->db->select("*");
		$this->db->from("upload_image");
		$this->db->where("id",$id);
		$cn = $this->db->get()->num_rows();
		return $cn;
	}
	
	function get_image($id) {
		$this->db->select("*");
		$this->db->from("upload_image");
		$this->db->where("id",$id);
		$data = $this->db->get();
		$num = $data->num_rows();
		//echo $this->db->last_query(); die();
		if ($num < 0) {
			return "NOF";
		}  else  {
			return $data->result_array();	
		}
		
	}
	
	function get_fancy() {
		
		$param = $this->input->post(array('id'));
        $sql = 'select id, image_name, size from upload_image where id =?';
        $result_array = $this->db->query($sql, array($param['id']));
        $response['images'] = $result_array->result();
        return $response;		
		
	}
	
}