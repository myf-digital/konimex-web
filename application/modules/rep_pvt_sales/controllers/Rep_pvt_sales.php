<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_pvt_sales extends CI_Controller {

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
		$this->load->model('rep_pvt_model', 'model');
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
			
			$btn_excell = '<a class="btn btn-success btn-xs" href="javascript:void(0);" onclick="return export_to_excel();return false;" ><i class="fa fa-download"></i> Save Scedule</a>';
			$btn_search = '<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="return search_data();return false;" ><i class="fa fa-search"></i> Search</a>';
			
			$btn_target = "<a style=\"margin:4px;\" class=\"btn btn-primary btn-xs\" onclick=\"open_target(\''+row.salesmanid+'\')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Detail\"><i class=\"fa fa-edit\"></i> Target\
								  </a>";
								  
			$btn_product = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"open_productivity(\''+row.salesmanid+'\')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Detail\"><i class=\"fa fa-edit\"></i> Order\
								  </a>";
			
			$target_excel = "<a class=\"btn btn-success btn-xs\" href=\"javascript:void(0);\" onclick=\"return export_target(\''+row.salesmanid+'\');return false;\" ><i class=\"fa fa-download\"></i> Save Target</a>";  
			//echo base_url();
			$data = array(
						'controller'  	=> $this->gparam['controller'],
						'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
						'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),						
						'excell'		=> $btn_excell,
						'search'		=> $btn_search,
						'target'		=> $btn_target,
						'product'		=> $btn_product,
						'target_excel'	=> $target_excel,
						'salesman'		=> $salesman
						);
			$this->load->view('pvt_sales_view',$data);
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
		
		$start 	= $this->uri->segment(3);
		$newDate = date("d-m-Y", strtotime($start));
		
		$filename = "Report_Scedule.xls";
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
					<th>Nama Salesman</th>					
					<th>Schedule</th>					
					<th>Eff Call</th>					
					<th>Ex Call</th>					
					<th>Call ID</th>					
					<th>Inv Call</th>					
					<th>Noo</th>					
					<th>Amount</th>									
				</tr>
			</thead>
			<tbody>';	
			
		$data = $this->model->get_data_export($start);
		$i = 1;
		if ($data !="") {
			foreach ($data as $value) {
				$html .='<tr>';		
				$html .='<td>'.$i.'</td>';		
				$html .='<td>'.$value['nama_salesman'].'</td>';		
				$html .='<td>'.$value['jadwal'].'</td>';		
				$html .='<td>'.$value['effectivecall'].'</td>';		
				$html .='<td>'.$value['ExtraCall'].'</td>';		
				$html .='<td>'.$value['cal'].'</td>';		
				$html .='<td>'.$value['InvalidCall'].'</td>';		
				$html .='<td>'.$value['noo'].'</td>';		
				$html .='<td>'.$value['amount'].'</td>';		
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
	
	function export_tagihan() {

		$sid = $this->uri->segment(3);
		$date = $this->uri->segment(4);
		
		$target 	= "";
		$sales 		= "";
		$percent 	= "";
		$ob 		= "";
		$oa			= "";
		$ec			= "";
		$oavsob		= "";
		$ecvsoa		= "";
		$salesvsec	= "";
		
		$q = $this->db->query(" select 
									periode, salesmanid,target,sales,percent,ob,oa,ec,oavsob,ecvsoa,salesvsec 
								from t_productivity
								where periode = '".$date."'
								and salesmanid = '".$sid."'");
		//echo $this->db->last_query(); die();
		$data = $q->result_array();
			
		foreach ($data as $value) { 
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			$percent 	= $value['percent'];
			$ob 		= $value['ob'];
			$oa			= $value['oa'];
			$ec			= $value['ec'];
			$oavsob		= $value['oavsob'];
			$ecvsoa		= $value['ecvsoa'];
			$salesvsec	= $value['salesvsec'];
		}
		

		$html ='<h3>Productivity Sales</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">Description</th>';
		$html .= '<th style="white-space: nowrap;">Value</th>';	
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';		
		$html .= '<tr>';
		$html .= '<td>Value Target</td>';
		$html .= '<td>'.$target.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Value Sales</td>';
		$html .= '<td>'.$sales.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Percent</td>';
		$html .= '<td>'.$percent.'%</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Outlet Binaan (OB)</td>';
		$html .= '<td>'.$ob.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Outlet Aktif (OA)</td>';
		$html .= '<td>'.$oa.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Effektif Call (EC)</td>';
		$html .= '<td>'.$ec.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>OA vs OB</td>';
		$html .= '<td>'.$oavsob.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Rata-Rata Transaksi(EC vs OA)</td>';
		$html .= '<td>'.$ecvsoa.'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Transaksi Per EC (Sales vs EC)</td>';
		$html .= '<td>'.$salesvsec.'</td>';		
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';

		$q = $this->db->query("
			select 
				salesmanid,
				periode,
				nama_prinsipal,
				target,
				sales,
				percent
			from t_target_prinsipal_salesman
			where periode = '".$date."'
			and salesmanid = '".$sid."'
		");
		
		/*$this->db->select("*");
		$this->db->from("t_target_prinsipal_salesman");
		$this->db->where("salesmanid",$sid);
		$this->db->where("periode",$periode);*/
		
		$data = $q->result_array();
		
		$html .= '<h3>Target Penjualan</h3><table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">Prinsipal</th>';
		$html .= '<th style="white-space: nowrap;">Target</th>';
		$html .= '<th style="white-space: nowrap;">Sales</th>';
		$html .= '<th style="white-space: nowrap;">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target 	= 0;
		$total_sales 	= 0;
		$total_percent 	= 0;
		
		foreach ($data as $value) {
			
			
			$prinsipal 	= $value['nama_prinsipal'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			$percent 	= $value['percent'];
			
			$total_target	= $total_target +  $target; 
			$total_sales 	= $total_target +  $sales; 	
			$total_percent  = $total_target +  $percent;			
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$prinsipal.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$target.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$sales.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$percent.'</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">Total</td>';
			$html .= '<td style="white-space: nowrap;">'.$total_target.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$total_sales.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$total_percent.'</td>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';

		$q = $this->db->query("
			select 
				salesmanid,
				periode,
				nama_group,
				target,
				sales,
				percent
			from t_target_group_salesman
			where periode = '".$date."'
			and salesmanid = '".$sid."'
		");
		
		/*$this->db->select("*");
		$this->db->from("t_target_prinsipal_salesman");
		$this->db->where("salesmanid",$sid);
		$this->db->where("periode",$periode);*/
		
		$data = $q->result_array();
		
		$html .= '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">Nama Group</th>';
		$html .= '<th style="white-space: nowrap;">Target</th>';
		$html .= '<th style="white-space: nowrap;">Sales</th>';
		$html .= '<th style="white-space: nowrap;">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target 	= 0;
		$total_sales 	= 0;
		$total_percent 	= 0;
		
		foreach ($data as $value) {
			
			
			$group 	= $value['nama_group'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			$percent 	= $value['percent'];
			
			$total_target	= $total_target +  $target; 
			$total_sales 	= $total_target +  $sales; 	
			$total_percent  = $total_target +  $percent;			
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$group.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$target.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$sales.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$percent.'</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">Total</td>';
			$html .= '<td style="white-space: nowrap;">'.$total_target.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$total_sales.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$total_percent.'</td>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		
		$filename = 'Target_sales.xls';

		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		

		echo $html;
	}

	function target_detail() {
		$sid = $this->input->post("sid");
		$date = $this->input->post("startdate");
		
		$q = $this->db->query(" select 
									periode, salesmanid,target,sales,percent,ob,oa,ec,oavsob,ecvsoa,salesvsec 
								from t_productivity
								where periode = '".$date."'
								and salesmanid = '".$sid."'");
		//echo $this->db->last_query(); die();
		$data = $q->result_array();
		if ($data){
		foreach ($data as $value) { 
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			$percent 	= $value['percent'];
			$ob 		= $value['ob'];
			$oa			= $value['oa'];
			$ec			= $value['ec'];
			$oavsob		= $value['oavsob'];
			$ecvsoa		= $value['ecvsoa'];
			$salesvsec	= $value['salesvsec'];
		}
		}else{
			$target 	= 0;
			$sales 		= 0;
			$percent 	= 0;
			$ob 		= 0;
			$oa			= 0;
			$ec			= 0;
			$oavsob		= 0;
			$ecvsoa		= 0;
			$salesvsec	= 0;
		}
		
		/*$this->db->select("*");
		$this->db->from("t_target_prinsipal_salesman");
		$this->db->where("salesmanid",$sid);
		$this->db->where("periode",$periode);*/
		$html ='<h3>Productivity Sales</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:400px;">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Description</th>';
		$html .= '<th style="white-space: nowrap;text-align:right; padding-right:15px;">Value</th>';	
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';		
		$html .= '<tr">';
		$html .= '<td text-align:left;padding-left:15px>Value Target</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($target, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Value Sales</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($sales, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Percent</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($percent, 2, '.', ',').'%</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Outlet Binaan (OB)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ob, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Outlet Aktif (OA)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($oa, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Effektif Call (EC)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ec, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>OA vs OB</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($oavsob, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Rata-Rata Transaksi(EC vs OA)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ecvsoa, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Transaksi Per EC (Sales vs EC)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($salesvsec, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';

		$q = $this->db->query("
			select 
				salesmanid,
				DATE_FORMAT(periode,'%d-%m-%Y') periode,
				nama_prinsipal,
				target,
				sales,
				percent
			from t_target_prinsipal_salesman
			where DATE_FORMAT(periode,'%Y%m') = DATE_FORMAT('".$date."','%Y%m')
			and salesmanid = '".$sid."'
		");

		$data = $q->result_array();
		foreach ($data as $vperiode) {
			$periode = $vperiode['periode'];
		}

		$html .= '<h3>Target Penjualan</h3><h7>Periode : '.$periode.'</h7>
				  <table class="table table-striped table-bordered table-condensed" style="width:700px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Prinsipal</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Target</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Sales</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target = 0; $total_sales = 0; $total_percent = 0;
		foreach ($data as $value) {
			
			
			$prinsipal 	= $value['nama_prinsipal'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			if ($value['sales']==0 || $value['target']==0){
			$percent = 0;
			} else {
			$percent = ($sales/$target)*100;
			}
			
			$total_target	= $total_target +  $target;
			$total_sales 	= $total_sales +  $sales;
			
			if ($total_target==0 || $total_sales==0){
			$percenttot = 0;
			} else {
			$percenttot = ($total_sales/$total_target)*100;
			}
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">'.$prinsipal.'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($target ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($sales ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percent ,2, '.', ',').' %</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Total</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_target ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_sales ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percenttot ,2, '.', ',').' %</th>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		
		//Target per group
		$q = $this->db->query("
			select 
				salesmanid,
				DATE_FORMAT(periode,'%d-%m-%Y') periode,
				nama_group,
				target,
				sales,
				percent
			from t_target_group_salesman
			where DATE_FORMAT(periode,'%Y%m') = DATE_FORMAT('".$date."','%Y%m')
			and salesmanid = '".$sid."'
		");

		$data = $q->result_array();
		foreach ($data as $vperiode) {
			$periode = $vperiode['periode'];
		}

		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:700px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Nama Group</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Target</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Sales</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target = 0; $total_sales = 0; $total_percent = 0;
		foreach ($data as $value) {
			
			
			$group 	= $value['nama_group'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			if ($value['sales']==0 || $value['target']==0){
			$percent = 0;
			} else {
			$percent = ($sales/$target)*100;
			}
			
			$total_target	= $total_target +  $target;
			$total_sales 	= $total_sales +  $sales;
			
			if ($total_target==0 || $total_sales==0){
			$percenttot = 0;
			} else {
			$percenttot = ($total_sales/$total_target)*100;
			}
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">'.$group.'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($target ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($sales ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percent ,2, '.', ',').' %</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Total</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_target ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_sales ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percenttot ,2, '.', ',').' %</th>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		
		echo $html;
	}

	function open_pvt() {
	
		$sid = $this->input->post("sid");
		$date = $this->input->post("startdate");
		
		$q = $this->db->query("
		
			select 
			   sls.customerid,
			   cst.nama_customer,
			   cst.alamat,
			   sum(dtl.netto) as total_netto
			from 
			t_sales_master sls left join
			t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales left JOIN
			m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid left JOIN
			m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
			m_product product on dtl.productid = product.productid 
			where sls.salesmanid = '".$sid."' AND
				  sls.tanggal >= '".$date."' AND
				  sls.tanggal <= '".$date."' 
			group by 
				   sls.customerid,
				   cst.nama_customer,
				   cst.alamat		
		");
		
		$i = 1;
		$data = $q->result_array();
		
		$html = '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">No</th>';
		$html .= '<th style="white-space: nowrap;">Action</th>';
		$html .= '<th style="white-space: nowrap;">Customer ID</th>';
		$html .= '<th style="white-space: nowrap;">Nama Customer</th>';
		$html .= '<th style="white-space: nowrap;">Alamat</th>';
		$html .= '<th style="white-space: nowrap;">Total Netto</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td>'.$i.'</td>';
			$html .= '<td style="white-space: nowrap;"><a class="btn btn-primary btn-xs" href="#" onclick="toggle_visibility(\'tr_detail_'.$i.'\'); return false;">Detail</a></td>';
			$html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
			$html .= '<td>'.$value['alamat'].'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			$html .= '</tr>';	
			$html .= '<tr id="tr_detail_'.$i.'" style="display: none;">';
			$html .= '<td colspan="6">';
				$customerid = $value['customerid'];
			$html .= '<div id="detail_product_"'.$i.' style="overflow-y: auto; max-height: 300px; max-width: 900px; white-space: nowrap; ">'; 
				$html .= '<table class="table table-striped table-bordered table-condensed">';
				$html .= '<thead>';
				$html .= '<tr>';
					$html .= '<th style="white-space: nowrap;">Product ID </th>';
					$html .= '<th style="white-space: nowrap;" >Nama Invoice</th>';
					$html .= '<th style="white-space: nowrap;" >QTY PCS</th>';
					$html .= '<th style="white-space: nowrap;" >Harga Jual</th>';
					$html .= '<th style="white-space: nowrap;" >Total Bruto</th>';
					$html .= '<th style="white-space: nowrap;" >Total Diskon</th>';
					$html .= '<th style="white-space: nowrap;" >Total Neto</th>';	
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';
				$q_detail = $this->db->query("		
					select 
					   sls.siteid, 
					   sls.salesmanid,
					   salesamn.nama_salesman,
					   sls.customerid,
					   cst.nama_customer,
					   cst.alamat,
					   dtl.productid,
					   product.nama_invoice,
					   sum(case when dtl.flag_bonus = 0 then 'JUAL' else 'BONUS' end) as statu_order,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
					   dtl.h_jual,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
					   dtl.disc_cabang,
					   dtl.disc_prinsipal,
					   dtl.disc_xtra,
					   dtl.disc_cod,
					   SUM(dtl.rp_cabang) AS rp_cabang,
					   SUM(dtl.rp_prinsipal) AS rp_prinsipal,
					   SUM(dtl.rp_xtra) AS rp_xtra,
					   sum(dtl.rp_cod) as rp_cod,
					   sum(case when dtl.flag_bonus = 0 then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
					   sum(case when dtl.flag_bonus = 0 then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
					from 
					t_sales_master sls left join
					t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales left JOIN
					m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid left JOIN
					m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
					m_product product on dtl.productid = product.productid 
					where sls.salesmanid = '".$sid."' AND
						  sls.tanggal >= '".$date."' AND
						  sls.tanggal <= '".$date."' AND
						  sls.customerid = '".$customerid."'
					group by sls.siteid, 
						   sls.salesmanid,
						   salesamn.nama_salesman,
						   sls.customerid,
						   cst.nama_customer,
						   cst.alamat,
						   dtl.productid,
						   product.nama_invoice,dtl.h_jual,
						   dtl.disc_cabang,
						   dtl.disc_prinsipal,
						   dtl.disc_xtra,
						   dtl.disc_cod		
				");
				
				$k_detail = $q_detail->result_array();
				foreach ($k_detail as $v_detail) {
					$html .= '<tr>';						
						$html .= '<td style="white-space: nowrap;">'.$v_detail['productid'].'</td>';
						$html .= '<td style="white-space: nowrap;">'.$v_detail['nama_invoice'].'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$v_detail['qty_jual_in_pcs'].'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_bruto'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_discount'], 0, '.', ',').'</td>';			
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 0, '.', ',').'</td>';
					$html .= '</tr>';	
				}
				$html .= '</tbody>';
				$html .= '</table>';
			$html .= '</div>';		
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '<script type="text/javascript">
							function toggle_visibility (id) {
							   var e = document.getElementById(id);
							   if(e.style.display == \'\') {
								  e.style.display = \'none\';
							   } else {
								  e.style.display = \'\';
							   }  
							}						
					  </script>';
			$i++;
		}
		$html .= '</tbody">';
		$html .= '</table">';
		
		echo $html;
		
		
	}
	
}
