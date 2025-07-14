<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Basic_reporting extends CI_Controller {

	var $gparam = array();
	
	public function __construct() {
        parent::__construct();
		$this->load->library('authlib');
		$this->load->library('fpdf');
		
		/* Cek Time Out*/
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
		/* *** */
		
		//get class name
		$this->gparam['controller'] = $this->router->fetch_class();
		//initiate mode
		$this->load->model('basic_model', 'basic');
		$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		//variable for restric message
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
    }
	
	public function index()	{
		
		if ($this->gparam['privilage']->privilage_view == 'Y') {
			
			$gridopt = $this->input->get(array('psize', 'pnumber'));
			header('Content-Type: text/html');
			$create_button = '""';			
			$update_button = '';
			$delete_button = '';
			
			$search_button = '<a href="javascript:void(0)"  class="btn btn-primary btn-xs" onclick="search_data();" >
									<i class="fa fa-search"></i> Search
								  </a>';
			
			//get area
			$options = '<option value="">--Select--</option>';
			$options .= $this->basic->get_area();
			
			$area_dropdown = '<select name="area" class="chosen-select" style="width:150px;">
								'.$options.'									
							  </select>';
			
			if ($this->gparam['privilage']->privilage_create == 'Y') {
				$btn_create = '<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="create_view();"><i class="fa fa-plus"></i> Create</a>';			
			}
			
			if ($this->gparam['privilage']->privilage_update == 'Y') {
				$btn_update = "<a class=\"btn btn-success  btn-xs\" href=\"javascript:void(0);\" onclick=\"update_view('+row.id+');\"><i class=\"fa fa-edit\"></i> Update</a>";			
			}
			
			if ($this->gparam['privilage']->privilage_delete == 'Y') {
				$btn_delete = "<a class=\"btn btn-danger btn-xs\" href=\"javascript:void(0);\" onclick=\"delete_data('+row.id+');\"><i class=\"fa fa-trash-o\"></i> Delete</a>";			
			}
			
			$image_button = "<a class=\"btn btn-primary btn-xs\" onclick=\"preview_image('+row.id+')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Preview\"><i class=\"fa fa-camera-retro fa-lg\"> image</i>\
								  </a>";
			
			$btn_excell = '<a class="btn btn-success btn-xs" href="javascript:void(0);" onclick="return export_to_excel();return false;" ><i class="fa fa-download"></i> Save Excell</a>';
			$btn_pdf = '<a class="btn btn-danger btn-xs" href="javascript:void(0);" onclick="return export_to_pdf();return false;" ><i class="fa fa-download"></i> Save PDF</a>';

			//echo base_url();
			$data = array(
						'controller'  	=> $this->gparam['controller'],
						'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
						'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),
						'create'		=> $btn_create,
						'update'		=> $btn_update,
						'delete'		=> $btn_delete,
						'excell'		=> $btn_excell,
						'pdf'			=> $btn_pdf,
						'preview'		=> $image_button,
						'search_button'	=> $search_button,
						'area_dropdown'	=> $area_dropdown,
				);
			$this->load->view('basic_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}		
		
	}
	
	function load_data() { //load list data
		header('Content-Type: application/jsonp');
        $list = $this->basic->get_list_data();
		echo json_encode($list);
	}
	
	function form() { //initiate form value
		
		if ($this->gparam['privilage']->privilage_view == 'Y') {
			
			//declare controller name
			$data['controller'] = $this->gparam['controller'];
			//declare action post
			$data['action'] = base_url()."index.php/".$this->gparam['controller']."/crud";
			//get uri segment for cek process insert/update
			$id = $this->uri->segment(3);
			
			if (!empty($id)) {
				//if update process
				
				//edit value
				$edit = $this->basic->get_data_edit($id); //get data edit			
				$data['id'] 		= $id;
				$data['input_text'] = $edit->input_text;
				$data['startdate'] 	= $edit->startdate;
				$data['email'] 		= $edit->email;
				$data['text_area'] 	= $edit->text_area;
				
				$area	= ""; 	
				$region	= "";
				$branch	= "";
				
				$area 	= $edit->area;
				$region = $edit->region;
				$branch = $edit->branch;
				
				if ($area != "") {
					$data['area'] = $this->basic->get_value_area($area);
					
					if ($region != "") {
						$data['region'] = $this->basic->get_value_region($area,$region);
						
						if ($branch !="") {
							$data['branch'] = $this->basic->get_value_branch($region,$branch);
						}
					}
				}
				//initiate select box
				if ($edit->select_box == "valid") {
					$options = '<option value="valid" selected>Valid</option>
							<option value="invalid">Invalid</option>';
				} else {
					$options = '<option value="valid">Valid</option>
							<option value="invalid" selected>Invalid</option>';
				}			
				
				$select = '
					<section class="col col-3">
						<label class="label">Input Dropdown</label>
						<label class="select">									
							<select name="select_box" class="chosen-select">
								<option value="" selected="" disabled=""> -Choose- </option>
								'.$options.'
							</select></label>
					</section>
				';
				//end initiate select box			
				$get = $this->basic->get_image($id);
					if ($get == "NOF") {
						$fimage = '$(\'#fileupload\').filer({
									limit: 1, 
									maxSize : 25,
									showThumbs: true,
									templates: {
										box: \'<ul class="jFiler-item-list"></ul>\',
										item: \'<li class="jFiler-item">\
													<div class="jFiler-item-container">\
														<div class="jFiler-item-inner">\
															<div class="jFiler-item-thumb">\
																<div class="jFiler-item-status"></div>\
																<div class="jFiler-item-info">\
																	<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
																</div>\
																{{fi-image}}\
															</div>\
															<div class="jFiler-item-assets jFiler-row">\
																<ul class="list-inline pull-left">\
																	<li><span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span></li>\
																</ul>\
																<ul class="list-inline pull-right">\
																	<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
																</ul>\
															</div>\
														</div>\
													</div>\
												</li>\',
										itemAppend: \'<li class="jFiler-item">\
													<div class="jFiler-item-container">\
														<div class="jFiler-item-inner">\
															<div class="jFiler-item-thumb">\
																<div class="jFiler-item-status"></div>\
																<div class="jFiler-item-info">\
																	<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
																</div>\
																{{fi-image}}\
															</div>\
															<div class="jFiler-item-assets jFiler-row">\
																<ul class="list-inline pull-left">\
																	<span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
																</ul>\
																<ul class="list-inline pull-right">\
																	<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
																</ul>\
															</div>\
														</div>\
													</div>\
												</li>\',
										progressBar: \'<div class="bar"></div>\',
										itemAppendToEnd: true,
										removeConfirmation: true,
										_selectors: {
											list: \'.jFiler-item-list\',
											item: \'.jFiler-item\',
											progressBar: \'.bar\',
											remove: \'.jFiler-item-trash-action\',
										}
									},			
									addMore: true    
								});';
					} else {
						
						$list = '[';	
						foreach ($get as $value_image) {
							$list .='{
									name: "'.$value_image['image_name'].'",
									size: '.$value_image['size'].',
									type: "image/jpg",
									file: "'.base_url().'/uploads/'.$value_image['image_name'].'"
							}';
						}
						$list .= ']';
						$fimage = '$(\'#fileupload\').filer({
									limit: 1, 
									maxSize : 25,
									showThumbs: true,
									templates: {
										box: \'<ul class="jFiler-item-list"></ul>\',
										item: \'<li class="jFiler-item">\
													<div class="jFiler-item-container">\
														<div class="jFiler-item-inner">\
															<div class="jFiler-item-thumb">\
																<div class="jFiler-item-status"></div>\
																<div class="jFiler-item-info">\
																	<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
																</div>\
																{{fi-image}}\
															</div>\
															<div class="jFiler-item-assets jFiler-row">\
																<ul class="list-inline pull-left">\
																	<li><span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span></li>\
																</ul>\
																<ul class="list-inline pull-right">\
																	<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
																</ul>\
															</div>\
														</div>\
													</div>\
												</li>\',
										itemAppend: \'<li class="jFiler-item">\
													<div class="jFiler-item-container">\
														<div class="jFiler-item-inner">\
															<div class="jFiler-item-thumb">\
																<div class="jFiler-item-status"></div>\
																<div class="jFiler-item-info">\
																	<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
																</div>\
																{{fi-image}}\
															</div>\
															<div class="jFiler-item-assets jFiler-row">\
																<ul class="list-inline pull-left">\
																	<span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
																</ul>\
																<ul class="list-inline pull-right">\
																	<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
																</ul>\
															</div>\
														</div>\
													</div>\
												</li>\',
										progressBar: \'<div class="bar"></div>\',
										itemAppendToEnd: true,
										removeConfirmation: true,
										_selectors: {
											list: \'.jFiler-item-list\',
											item: \'.jFiler-item\',
											progressBar: \'.bar\',
											remove: \'.jFiler-item-trash-action\',
										}
									},			
									addMore: true,
									files: '.$list.'
								});';
					}

				//header form
				$data['header'] = 'Form Update';
				//submit button value
				$data['submit'] = 'update';
				
			} else {
			
				/* Upload Image JS*/
					$fimage = '$(\'#fileupload\').filer({
									limit: 1, 
									maxSize : 25,
									showThumbs: true,
									templates: {
										box: \'<ul class="jFiler-item-list"></ul>\',
										item: \'<li class="jFiler-item">\
													<div class="jFiler-item-container">\
														<div class="jFiler-item-inner">\
															<div class="jFiler-item-thumb">\
																<div class="jFiler-item-status"></div>\
																<div class="jFiler-item-info">\
																	<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
																</div>\
																{{fi-image}}\
															</div>\
															<div class="jFiler-item-assets jFiler-row">\
																<ul class="list-inline pull-left">\
																	<li><span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span></li>\
																</ul>\
																<ul class="list-inline pull-right">\
																	<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
																</ul>\
															</div>\
														</div>\
													</div>\
												</li>\',
										itemAppend: \'<li class="jFiler-item">\
													<div class="jFiler-item-container">\
														<div class="jFiler-item-inner">\
															<div class="jFiler-item-thumb">\
																<div class="jFiler-item-status"></div>\
																<div class="jFiler-item-info">\
																	<span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
																</div>\
																{{fi-image}}\
															</div>\
															<div class="jFiler-item-assets jFiler-row">\
																<ul class="list-inline pull-left">\
																	<span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
																</ul>\
																<ul class="list-inline pull-right">\
																	<li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
																</ul>\
															</div>\
														</div>\
													</div>\
												</li>\',
										progressBar: \'<div class="bar"></div>\',
										itemAppendToEnd: true,
										removeConfirmation: true,
										_selectors: {
											list: \'.jFiler-item-list\',
											item: \'.jFiler-item\',
											progressBar: \'.bar\',
											remove: \'.jFiler-item-trash-action\',
										}
									},			
									addMore: true    
								});';
					/* ************** */
				
				//if insert process form
				$select = '
					<section class="col col-3">
						<label class="label">Input Dropdown</label>
						<label class="select">									
							<select name="select_box" class="chosen-select">
								<option value="" selected="" disabled=""> -Choose- </option>
								<option value="valid">Valid</option>
								<option value="invalid">Invalid</option>
							</select></label>
					</section>
				';
				$area = $this->basic->get_area();
				$data['area'] 		= $area;	
				//header form 
				$data['header'] = 'Form Insert';
				//submit button value
				$data['submit'] = 'create';
			}
			
			
			$data['fimage'] = $fimage;
			$data['select_box'] = $select;
			$this->load->view('basic_crud_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}
		
	
	}
	
	function crud() {
		
		$crud = $this->input->post('submit');
		 
		if ($crud == 'create') {
			
			$insert = $this->basic->insert_data();
			if ($insert) {
				$json = true;
			}
			
		} else if ($crud == 'update') {
			$update = $this->basic->update_data();
			if ($update) {
				$json = true;			
			}
				
		}		
		echo json_encode($json);
		
	}
	
	public function delete() {
		
		$delete = $this->basic->delete_data();
		if ($delete) {
			$json = true;
		}
		echo json_encode($json);	
	}
	
	function get_region() {
	
		$area = $this->input->post('area');
		$select  = "<label class=\"select\">";
		$select .= "<select name=\"region\" class=\"chosen-select\" onchange=\"get_branch(this.value)\">";
		$select .= "<option value=\"\">--Select--</option>";
		if ($area != '0') {
			$data = $this->basic->get_region($area);
			
			foreach ($data as $parent_val) {
				$select .= "<option value=\"".$parent_val['region_id']."\">".$parent_val['region_name']."</option>";
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
	
	function get_branch() {
		$region = $this->input->post('region');
		$select  = "<label class=\"select\">";
		$select .= "<select name=\"branch\" class=\"chosen-select\">";
		$select .= "<option value=\"\">--Select--</option>";
		if ($region != '0') {
			$data = $this->basic->get_branch($region);
			
			foreach ($data as $parent_val) {
				$select .= "<option value=\"".$parent_val['branch_id']."\">".$parent_val['branch_name']."</option>";
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
	
	function export_excel() {
		
		$filename = "Basic_CRUD.xls";
		$html = '
			<style>
				table,th,td
				{
				border:1px solid black;
				border-collapse:collapse;
				}
			</style>
		<table>
			<thead>
				<tr>
					<th>No</th>
					<th>Input Example</th>
					<th>Input Date</th>
					<th>E-Mail</th>
					<th>Select Box</th>
					<th>Text Area</th>
					<th>Area</th>
					<th>Region</th>
					<th>Branch</th>
				</tr>
			</thead>
			<tbody>';
		
		$data = $this->basic->get_data_export();
		$i = 1;
		if ($data !="") {
			foreach ($data as $value) {
				$html .='<tr>';		
				$html .='<td>'.$i.'</td>';		
				$html .='<td>'.$value['input_text'].'</td>';		
				$html .='<td>'.$value['startdate'].'</td>';		
				$html .='<td>'.$value['email'].'</td>';		
				$html .='<td>'.$value['select_box'].'</td>';		
				$html .='<td>'.$value['text_area'].'</td>';		
				$html .='<td>'.$value['area_name'].'</td>';		
				$html .='<td>'.$value['region_name'].'</td>';		
				$html .='<td>'.$value['branch_name'].'</td>';		
				$html .='</tr>';
				$i++;	
			}
		
		}
		
		
		$html .= '	</tbody>
					</table>';
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		 
		echo $html;		
		
		
	}
	
	function export_pdf() {
	
		define('FPDF_FONTPATH',$this->config->item('fonts_path'));
		$data['data_detail'] = $this->basic->get_data_export();		
		$this->load->view("pdf_view",$data);
		
	}
	
	function get_fancy_image() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->basic->get_fancy();
        echo json_encode($list);
	}
}
