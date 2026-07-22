<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_att_sales extends CI_Controller {

	var $gparam = array();
	
	public function __construct() {
        parent::__construct();
		//$this->load->library('authlib');
		$this->load->library('fpdf');
		
		//get class name
		//$this->gparam['controller'] = $this->router->fetch_class();
		//initiate mode
		//$this->load->model('Rep_attsales_model', 'model');
		//$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		//variable for restric message
		//$this->gparam['restrict'] = 'You Cannot Access This Menu';
    }
	
	public function index()	{
		
		//if ($this->gparam['privilage']->privilage_view == 'Y') {
			
			$gridopt = $this->input->get(array('psize', 'pnumber'));
			header('Content-Type: text/html');
			
			$salesman = $this->model->get_salesman();
			
			$btn_excell = '<a class="btn btn-success btn-xs" href="javascript:void(0);" onclick="return export_to_excel();return false;" ><i class="fa fa-download"></i> Save Schedule</a>';
			$btn_search = '<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="return open_att_sales();return false;" ><i class="fa fa-search"></i> Search</a>';
			
			
			//echo base_url();
			$data = array(
						'controller'  	=> $this->gparam['controller'],
						'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
						'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),						
						'excell'		=> $btn_excell,
						'search'		=> $btn_search,
						'salesman'		=> $salesman
						);
			$this->load->view('rep_attsales_view',$data);
		//} else {
		//	echo $this->gparam['restrict'];
		//}		
		
	}
	
	function crc_export() {
		$sid 	= $this->uri->segment(3);
		$cid 	= $this->uri->segment(4);
		$date 	= $this->uri->segment(5);
		
		$dateTime = explode('-',$date);
		$month = $dateTime[1];
		$year = $dateTime[0];
		
		$siteid = $this->model->get_siteid();
		
		$q = $this->db->query("
							select a.periode,a.siteid,a.salesmanid,a.customerid,a.productid,b.isi_besar,concat(b.nama_invoice,' - ',b.nama_brand) as product_desc,
								   a.qty_rata qty_rata,a.qty_akhir qty_akhir,a.qty_saran_order qty_saran_order,a.qty_fix_order qty_fix_order
							from t_sales_crc a left join m_product b on a.productid=b.productid
							where a.siteid = '".$siteid."' and a.periode='".$date."' and 
								  a.salesmanid='".$sid."' and a.customerid='".$cid."'
							order by a.productid asc
						");
		$data = $q->result_array();
		
		$filename = "Report_CRC.xls";
		
		$html ='<h3>List CRC Current Date </h3>';
		$html .= '
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
					<th>Product Desk</th>					
					<th>Rata-Rata</th>					
					<th>Stock Akhir</th>					
					<th>Saran Order</th>					
					<th>Fix Order</th>														
				</tr>
			</thead>
			<tbody>';	
			
		$i = 1;
		if ($data !="") {
			foreach ($data as $value) {
				$html .='<tr>';		
				$html .='<td>'.$i.'</td>';		
				$html .='<td>'.$value['product_desc'].'</td>';		
				$html .='<td>'.$value['qty_rata'].'</td>';	
				$html .='<td>'.$value['qty_akhir'].'</td>';	
				$html .='<td>'.$value['qty_saran_order'].'</td>';	
				$html .='<td>'.$value['qty_fix_order'].'</td>';	
				$html .= '</tr>';	
				$i++;	
			}
		
		}
		
		
		$html .= '	</tbody>
					</table>';
		
		
		$html .= $this->model->get_record_month($cid,$sid,$date);
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		
		
		echo $html;
	}

	function month_crc() {
		
		$sid 	= $this->input->post("sid");
		$cid 	= $this->input->post("cid");
		$date 	= $this->input->post("startdate");
		
		$dateTime = explode('-',$date);
		$month = $dateTime[1];
		$year = $dateTime[0];
		
		
		
		$html = $this->model->get_record_month($cid,$sid,$date);
		
		echo $html;
		
	}
	
	function load_data() { //load list data
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data();
		echo json_encode($list);
	}		

	function load_data_outlet() { //load list data outlet
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data_outlet();
		echo json_encode($list);
	}		

	function load_data_crc() { //load list data crc
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data_crc();
		echo json_encode($list);
	}		

	function load_data_crc_month() { //load list data crc
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data_crc_month();
		echo json_encode($list);
	}	

	function export_excel() {
		
		$periode 	= $this->uri->segment(3);
		$until 	= $this->uri->segment(4);
		
		$filename = "Report_TPE_Aktif.xls";
		$html ='<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:300px;
					overflow:auto;  
					margin-top:20px;
				}

				#table-wrapper table {
					width:100%;
				}

				#table-wrapper table thead th .text {
					position:absolute;   
					top:-20px;
					z-index:2;
					height:20px;
					width:35%;
					border:1px solid;
				}
				</style>';
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">TPE ID</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama TPE</th>';

		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;white-space:nowrap;color:red;">'.date_format($start,"d").'</th>';
			}else{
			$html .='<th style="text-align:center;white-space:nowrap;">'.date_format($start,"d").'</th>';
			}
			$start->modify('+1 day');
		}
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Total</th>';
		$html .= '</tr><tr>';
		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;color:red;">'.$namahari.'</th>';
			}else{
			$html .='<th style="text-align:center;">'.$namahari.'</th>';
			}
			$start->modify('+1 day');
		}

		$html .= '</tr></thead>';

		$html .= '<tbody>';
		/*Close Header*/
		$get_salesman = $this->model->get_salesman();

						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;white-space:nowrap;">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_salesman'].'</td>';
			
			/******************/
			$start = date_create($periode);
			$end = date_create($until);
			while($start <= $end)
			{
				$vdate=date_format($start,"Y-m-d");
				$vsalesmanid=$v_salesman['salesmanid'];
				$get_salesman_aktif = $this->model->get_salesman_aktif($vsalesmanid,$vdate);
				if (!empty($get_salesman_aktif)){
					foreach ($get_salesman_aktif as $val_aktif) {
						$namahari=date_format($start,"D");
						if ($namahari=='Sun'){
						$html .='<th style="text-align:center;color:red;font-size:11px;">'.$val_aktif['aktif'].'</th>';
						}else{
						$html .='<th style="text-align:center;font-size:11px;">'.$val_aktif['aktif'].'</th>';
						}
					}
				}else{
					$namahari=date_format($start,"D");
					if ($namahari=='Sun'){
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
					}else{
					$html .='<th style="text-align:center;font-size:11px;">0</th>';
					}
				}

				$start->modify('+1 day');
			}
			
			$get_salesman_aktif_sum = $this->model->get_salesman_aktif_sum($vsalesmanid,$periode,$until);
			if (!empty($get_salesman_aktif_sum)){
				foreach ($get_salesman_aktif_sum as $val_sumaktif) {
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumaktif'].'</th>';
				}
			}else{
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
			}
			
			
			/******************/
			
		}
			$html .='</tr>';
		
		$html .= '</tbody>';
		$html .= '</table>';
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		 
		echo $html;		
		
		
	}		

	function namahari($tanggal){
    
    //fungsi mencari namahari
    //format $tgl YYYY-MM-DD
    //harviacode.com
    
    $tgl=substr($tanggal,8,2);
    $bln=substr($tanggal,5,2);
    $thn=substr($tanggal,0,4);
 
    $info=date('w', mktime(0,0,0,$bln,$tgl,$thn));
    
		switch($info){
			case '0': return "Minggu"; break;
			case '1': return "Senin"; break;
			case '2': return "Selasa"; break;
			case '3': return "Rabu"; break;
			case '4': return "Kamis"; break;
			case '5': return "Jumat"; break;
			case '6': return "Sabtu"; break;
		};
    
	}

	function load_data_att() {
	
		/* $periode = $this->input->post("periode");
		$until = $this->input->post("until");
	    $get_salesman = $this->model->get_salesman($peride,$until);
		 */
		
		$periode = $this->input->post("startdate");	
		$until = $this->input->post("enddate");
		/*Header*/
		$html ='<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:300px;
					overflow:auto;  
					margin-top:20px;
				}

				#table-wrapper table {
					width:100%;
				}

				#table-wrapper table thead th .text {
					position:absolute;   
					top:-20px;
					z-index:2;
					height:20px;
					width:35%;
					border:1px solid;
				}
				</style>';
		$html .= '<div id="table-wrapper"><div id="table-scroll"><table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">TPE ID</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama TPE</th>';

		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;white-space:nowrap;color:red;">'.date_format($start,"d").'</th>';
			}else{
			$html .='<th style="text-align:center;white-space:nowrap;">'.date_format($start,"d").'</th>';
			}
			$start->modify('+1 day');
		}
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Total</th>';
		$html .= '</tr><tr>';
		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;color:red;">'.$namahari.'</th>';
			}else{
			$html .='<th style="text-align:center;">'.$namahari.'</th>';
			}
			$start->modify('+1 day');
		}

		$html .= '</tr></thead>';

		$html .= '<tbody>';
		/*Close Header*/
		$get_salesman = $this->model->get_salesman();

						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;white-space:nowrap;">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_salesman'].'</td>';
			
			/******************/
			$start = date_create($periode);
			$end = date_create($until);
			while($start <= $end)
			{
				$vdate=date_format($start,"Y-m-d");
				$vsalesmanid=$v_salesman['salesmanid'];
				$get_salesman_aktif = $this->model->get_salesman_aktif($vsalesmanid,$vdate);
				if (!empty($get_salesman_aktif)){
					foreach ($get_salesman_aktif as $val_aktif) {
						$namahari=date_format($start,"D");
						if ($namahari=='Sun'){
						$html .='<th style="text-align:center;color:red;font-size:11px;">'.$val_aktif['aktif'].'</th>';
						}else{
						$html .='<th style="text-align:center;font-size:11px;">'.$val_aktif['aktif'].'</th>';
						}
					}
				}else{
					$namahari=date_format($start,"D");
					if ($namahari=='Sun'){
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
					}else{
					$html .='<th style="text-align:center;font-size:11px;">0</th>';
					}
				}

				$start->modify('+1 day');
			}
			
			$get_salesman_aktif_sum = $this->model->get_salesman_aktif_sum($vsalesmanid,$periode,$until);
			if (!empty($get_salesman_aktif_sum)){
				foreach ($get_salesman_aktif_sum as $val_sumaktif) {
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumaktif'].'</th>';
				}
			}else{
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
			}
			
			
			/******************/
			
		}
			$html .='</tr>';
		
		$html .= '</tbody>';
		$html .= '</table></div></div>';
		
		echo $html;
	}
	

	
}
