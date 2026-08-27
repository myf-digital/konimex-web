<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_tagihan extends CI_Controller {

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
		$this->load->model('rep_tagihan_model', 'model');
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
			
			$btn_order = "<a style=\"margin:4px;\" class=\"btn btn-primary btn-xs\" onclick=\"open_order(\''+row.salesmanid+'\')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Detail\"><i class=\"fa fa-edit\"></i> Order\
								  </a>";
								  
			$btn_tagihan = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"return load_tagihan(\''+row.salesmanid+'\',\''+row.nama_salesman+'\'); return false;\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Detail\"><i class=\"fa fa-edit\"></i> Tagihan\
								  </a>";
			
			$tagihan_excel = "<a class=\"btn btn-success btn-xs\" href=\"javascript:void(0);\" onclick=\"return export_tagihan(\''+row.salesmanid+'\',\''+row.nama_salesman+'\');return false;\" ><i class=\"fa fa-download\"></i> Save Tagihan</a>";
			
			//echo base_url();
			$data = array(
						'controller'  	=> $this->gparam['controller'],
						'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
						'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),						
						'excell'		=> $btn_excell,
						'order'			=> $btn_order,
						'tagihan'		=> $btn_tagihan,
						'search'		=> $btn_search,
						'tagihan_excel'		=> $tagihan_excel,
						'salesman'		=> $salesman
						);
			$this->load->view('rep_tagihan_view',$data);
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
					<th>Nama TPE</th>					
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
	
	function open_order() {
		$sid = $this->input->post("sid");
		$date = $this->input->post("startdate");
		
		$q = $this->db->query("
		
			select 
			   sls.customerid,
			   cst.nama_customer,
			   cst.alamat
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
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		foreach ($data as $value) {
			$html .= '<tr>';		
			/*$html .= '<td>'.$value['siteid'].'</td>';
			$html .= '<td>'.$value['salesmanid'].'</td>';
			$html .= '<td>'.$value['nama_salesman'].'</td>';*/
			$html .= '<td>'.$i.'</td>';
			$html .= '<td style="white-space: nowrap;"><a class="btn btn-primary btn-xs" href="#" onclick="toggle_visibility(\'tr_detail_'.$i.'\'); return false;">Detail</a></td>';
			$html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
			$html .= '<td>'.$value['alamat'].'</td>';
			$html .= '</tr>';	
			$html .= '<tr id="tr_detail_'.$i.'" style="display: none;">';
			$html .= '<td colspan="5">';
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
						$html .= '<td style="white-space: nowrap;">'.$v_detail['qty_jual_in_pcs'].'</td>';
						$html .= '<td style="white-space: nowrap;">'.$v_detail['h_jual'].'</td>';
						$html .= '<td style="white-space: nowrap;">'.$v_detail['total_bruto'].'</td>';
						$html .= '<td style="white-space: nowrap;">'.$v_detail['total_discount'].'</td>';			
						$html .= '<td style="white-space: nowrap;">'.$v_detail['total_netto'].'</td>';
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
	
	function excel_tagihan() {
		$sid 		= urldecode($this->uri->segment(3));
		$namasls 	= urldecode($this->uri->segment(4));
		$date 		= urldecode($this->uri->segment(5));
		
		//echo $sid."-",$date."-"$nama
		$this->db->select("customerid,salesmanid, no_sales, no_ink, bayar_tunai,bayar_transfer,bayar_giro, bayar");	
		$this->db->from("t_ar_ink_detail");
		$this->db->where("tgl_ink",$date);	
		$this->db->where("salesmanid",$sid);	
		
		$data = $this->db->get()->result_array();
		
		
		$newDate = date("d-m-Y", strtotime($date));
		$html = '<h3>List Tagihan TPE '.$namasls.' '.$newDate.'</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead style="display: block; width:1135px;">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;" width="200px">Customer</th>';
		$html .= '<th style="white-space: nowrap;" width="160px">No Tagihan</th>';
		$html .= '<th style="white-space: nowrap;" width="160px">No Sales</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Nilai Tagihan</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Tunai</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Transfer</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Giro</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Total</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody style="overflow-y: auto;height: 160px;width: 1155px;display: block;">';
		
		$total_bayar = 0;
		$total_tunai = 0;
		$total_transfer = 0;
		$total_giro = 0;
		$total_all = 0; 	
		foreach ($data as $value) {
		
			$total = $value['bayar_tunai'] + $value['bayar_transfer'] + $value['bayar_giro'];
			
			$total_bayar = $total_bayar + $value['bayar'];
			$total_tunai = $total_tunai + $value['bayar_tunai'];
			$total_transfer = $total_transfer + $value['bayar_transfer'];
			$total_giro = $total_giro + $value['bayar_giro'];
			
			$total_all = $total_all + $total; 
			
			// get customer
			$this->db->select("nama_customer");
			$this->db->from("m_customer");
			$this->db->where("customerid",$value['customerid']);
			$get = $this->db->get();
			
			$num = $get->num_rows();
			if ($num < 1) {
				$cus = "";
			} else {
				$cus = $get->row()->nama_customer;
			}
			
			
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;" width="200px">'.$cus.'</td>';
			$html .= '<td style="white-space: nowrap;" width="160px">'.$value['no_ink'].'</td>';
			$html .= '<td style="white-space: nowrap;" width="160px">'.$value['no_sales'].'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar_tunai'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar_transfer'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar_giro'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total,2).'</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '<thead style="display: block; width:1135px;">';
		$html .= '<tr style="font-weight: bold; font-size: 14px;">';
		$html .= '<td style="white-space: nowrap;" width="200px">Total</td>';
		$html .= '<td style="white-space: nowrap;" width="160px"></td>';
		$html .= '<td style="white-space: nowrap;" width="160px"></td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_bayar,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_tunai,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_transfer,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_giro,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_all,2).'</td>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '</table>';
		
		$filename = "Report_Tagihan.xls";
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		
		
		echo $html;
	}
	
	function show_tagihan() {
		$sid = $this->input->post("sid");
		$date = $this->input->post("startdate");
		$namasls = $this->input->post("nama_salesman");
		
		$this->db->select("customerid,salesmanid, no_sales, no_ink, bayar_tunai,bayar_transfer,bayar_giro, bayar");	
		$this->db->from("t_ar_ink_detail");
		$this->db->where("tgl_ink",$date);	
		$this->db->where("salesmanid",$sid);	
		
		$data = $this->db->get()->result_array();
		
		$html = '<h3>List Tagihan TPE '.$namasls.'</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead style="display: block; width:1000px;">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;" width="180px">Customer</th>';
		$html .= '<th style="white-space: nowrap;" width="160px">No Tagihan</th>';
		$html .= '<th style="white-space: nowrap;" width="160px">No Sales</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Nilai Tagihan</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Tunai</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Transfer</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Giro</th>';
		$html .= '<th style="white-space: nowrap; text-align: right;" width="100px">Bayar Total</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody style="overflow-y: auto;height: 190px;width: 1020px;display: block;">';
		
		$total_bayar = 0;
		$total_tunai = 0;
		$total_transfer = 0;
		$total_giro = 0;
		$total_all = 0; 	
		foreach ($data as $value) {
		
			$total = $value['bayar_tunai'] + $value['bayar_transfer'] + $value['bayar_giro'];
			
			$total_bayar = $total_bayar + $value['bayar'];
			$total_tunai = $total_tunai + $value['bayar_tunai'];
			$total_transfer = $total_transfer + $value['bayar_transfer'];
			$total_giro = $total_giro + $value['bayar_giro'];
			
			$total_all = $total_all + $total; 
			
			// get customer
			$this->db->select("nama_customer");
			$this->db->from("m_customer");
			$this->db->where("customerid",$value['customerid']);
			$get = $this->db->get();
			
			$num = $get->num_rows();
			if ($num < 1) {
				$cus = "";
			} else {
				$cus = $get->row()->nama_customer;
			}
			
			
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;" width="180px">'.$cus.'</td>';
			$html .= '<td style="white-space: nowrap;" width="160px">'.$value['no_ink'].'</td>';
			$html .= '<td style="white-space: nowrap;" width="160px">'.$value['no_sales'].'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar_tunai'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar_transfer'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($value['bayar_giro'],2).'</td>';
			$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total,2).'</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '<thead style="display: block; width:1000px;">';
		$html .= '<tr style="font-weight: bold; font-size: 14px;">';
		$html .= '<td style="white-space: nowrap;" width="180px">Total</td>';
		$html .= '<td style="white-space: nowrap;" width="160px"></td>';
		$html .= '<td style="white-space: nowrap;" width="160px"></td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_bayar,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_tunai,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_transfer,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_giro,2).'</td>';
		$html .= '<td style="white-space: nowrap; text-align: right;" width="100px">'.number_format($total_all,2).'</td>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '</table>';
		
		echo $html;
	}
	
}
