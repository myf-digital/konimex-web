<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_att_sales extends CI_Controller {
	
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
		$this->load->model('rep_attsales_model', 'model');
		$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		//variable for restric message
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
		
    }
	
	
	public function index()	{
		
		if ($this->gparam['privilage']->privilage_view == 'Y') {

			$gridopt = $this->input->get(array('psize', 'pnumber'));
			header('Content-Type: text/html');
			//$act = base_url()."index.php/".$this->gparam['controller']."/load_data";
			
			$excell_download = '<a class="btn btn-success" id="save_excell" href="#" >
								<i class="fa fa-lg fa-fw fa-download"></i>Excell
							  </a>';		

			// pnumber, psize
			$data = array(
					'controller'  	=> $this->gparam['controller'],
					'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
					'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),
					'excell' 		=> $excell_download
					//'action'		=> $act
			);
			$this->load->view('rep_attsales_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}
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

	function load_data() {
	
		/* $periode = $this->input->post("periode");
		$until = $this->input->post("until");
	    $get_salesman = $this->model->get_salesman($peride,$until);
		 */
		
		$periode = $this->input->post("periode");	
		$until = $this->input->post("until");
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
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">Salesman ID</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama Salesman</th>';

		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;white-space:nowrap;color:red;" colspan="2">'.date_format($start,"d-M-Y").'</th>';
			}else{
			$html .='<th style="text-align:center;white-space:nowrap;" colspan="2">'.date_format($start,"d-M-Y").'</th>';
			}
			$start->modify('+1 day');
		}
		$html .= '</tr><tr>';
		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;color:red;">Masuk</th>';
			}else{
			$html .='<th style="text-align:center;">Masuk</th>';
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
				foreach ($get_salesman_aktif as $val_aktif) {
					$namahari=date_format($start,"D");
					if ($namahari=='Sun'){
					$html .='<th style="text-align:center;color:red;font-size:11px;">'.$val_aktif['aktif'].'</th>';
					}else{
					$html .='<th style="text-align:center;font-size:11px;">'.$val_aktif['aktif'].'</th>';
					}
				}

				$start->modify('+1 day');
			}			
			
			/******************/
			
		}
			$html .='</tr';
		
		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .= '<script>
					var name = "Report_Salesman_Aktif.xls";
					$("#save_excell").on("click", function () {
						var uri = $("#activity_table").btechco_excelexport({
						containerid: "activity_table"
						, datatype: $datatype.Table
						, returnUri: true
						});
				$(this).attr("download", name).attr("href", uri).attr("target", "_blank");
			});
		</script>';
		echo $html;
	}
	
	
}
