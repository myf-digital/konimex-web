<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_target_prinsipal extends CI_Controller {

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
		$this->load->model('rep_target_model', 'model');
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
			/* 
			$search_button = '<a href="javascript:void(0)"  class="btn btn-primary btn-xs" onclick="search_data();" >
									<i class="fa fa-search"></i> Search
								  </a>';
			
			//get area
			$options = '<option value="">--Select--</option>';
			$options .= $this->basic->get_area();
			 */
			/* $area_dropdown = '<select name="area" class="chosen-select" style="width:150px;">
								'.$options.'									
							  </select>'; */
			
			
			$salesman = $this->model->get_salesman();
			
			$btn_excell = '<a class="btn btn-success btn-xs" href="javascript:void(0);" onclick="return export_to_excel();return false;" ><i class="fa fa-download"></i> Save Excell</a>';
			$btn_search = '<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="return search_data();return false;" ><i class="fa fa-search"></i> Search</a>';
			
			//echo base_url();
			$data = array(
						'controller'  	=> $this->gparam['controller'],
						'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
						'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),						
						'excell'		=> $btn_excell,
						'search'		=> $btn_search,
						'salesman'		=> $salesman
						);
			$this->load->view('pvt_target_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}		
		
	}
	
	function load_data() { //load list data
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data();
		echo json_encode($list);
	}		
	
	function export_excel() {
		
		$sales 	= "";
		$start 	= "";
		$finish = "";
		
		$sales 	= $this->uri->segment(3);
		$start 	= $this->uri->segment(4);
		$finish = $this->uri->segment(5);
		
		$filename = "Target_Prinsipal.xls";
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
					<th>Periode</th>
					<th>Salesmanid</th>
					<th>Nama Medrep</th>
					<th>Prinsipal ID</th>
					<th>Nama Prinsipal</th>
					<th>Target</th>
					<th>Sales</th>
					<th>Pesentase Target</th>					
				</tr>
			</thead>
			<tbody>';			
			
		$data = $this->basic->get_data_export($sales,$start,$finish);
		$i = 1;
		
		if ($data !="") {
			foreach ($data as $value) {
				$html .='<tr>';		
				$html .='<td>'.$i.'</td>';		
				$html .='<td>'.$value['periode'].'</td>';		
				$html .='<td>'.$value['salesmanid'].'</td>';		
				$html .='<td>'.$value['nama_salesman'].'</td>';		
				$html .='<td>'.$value['prinsipalid'].'</td>';		
				$html .='<td>'.$value['nama_prinsipal'].'</td>';		
				$html .='<td>'.$value['target'].'</td>';		
				$html .='<td>'.$value['sales'].'</td>';		
				$html .='<td>'.$value['percent'].'</td>';				
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
	
}
