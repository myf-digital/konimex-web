<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_gffaktif extends BaseController
{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Rep_gffaktif_model', 'report_gffaktif');

    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function delete()
    {
        $data = param_input();
        response($this->report_gffaktif->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->report_gffaktif->load($data));
    }

    public function load_promo()
    {
        $data = param_input();
        responseJSON($this->report_gffaktif->load_promo($data));
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
	
		$periode = $this->input->post("start");	
		$until = $this->input->post("end");
        $idjabatan = $this->input->post("idjabatan");
        $position = $this->input->post("position");
		$usersession = $this->input->post("usersession");
		$restrictlevel = $this->input->post("restrict_level");
		$qexecabsensi = $this->db->query("replace into t_sales_absensi (periode, salesmanid, status, checkin, checkout, keterangan, pjp, `call`, extra_call, crc, promo, competitor, `order`, sos)
										select a.date, a.salesman_id, 
												case when a.type='IST' then 'S' 
												when a.type='ICT' then 'C' 
												when a.type='AHR' then 'H' 
												else 'HF' end tipe, check_in checkin, check_out checkout, 
												concat(a.description_in,'-', a.description_out) keterangan, 0 _pjp, 0 _call, 0 _extra_call, 0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos  
										from s_absensi a 
										where a.date between DATE_ADD((select tanggal from m_setup_site), INTERVAL -2 DAY) and DATE_ADD((select tanggal from m_setup_site), INTERVAL -1 DAY)
										union
										select a.periode,a.salesmanid,'H' status, min(a.check_in) checkin, max(a.check_out) checkout, '' keterangan, 
										(select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) _pjp, 
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)) _call,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) ) _extra_call,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and crc_time is not null) _crc,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and promo_time is not null) _promo,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and competitor_time is not null) _competitor,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and order_time is not null) _order,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and sos_time is not null) _sos
										from t_sales_rrk_trans a where a.periode between DATE_ADD((select tanggal from m_setup_site), INTERVAL -2 DAY) and DATE_ADD((select tanggal from m_setup_site), INTERVAL -1 DAY)
										group by a.periode,a.salesmanid
										;");

		$html = '<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= 'Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti';
		$html .= '<table id="activity_table" border="1" class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;width: 80px">No.</th>';
		$html .='<th rowspan="2" style="text-align:center;width: 200px">TPE</th>';
		$html .='<th rowspan="2" style="text-align:left;width: 300px">Nama TPE</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">Position</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">Regional</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">Area FC</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">City</th>';

		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;width: 50px;color:white;background-color:red;">'.date_format($start,"d").'</th>';
			}else{
			$html .='<th style="text-align:center;width: 50px;">'.date_format($start,"d").'</th>';
			}
			$start->modify('+1 day');
		}
		$html .='<th colspan="4" style="text-align:center;width: 200px">Total</th>';
		$html .= '</tr><tr>';
		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;color:white;background-color:red;width: 50px">'.$namahari.'</th>';
			}else{
			$html .='<th style="text-align:center;width: 50px">'.$namahari.'</th>';
			}
			$start->modify('+1 day');
		}

		$html .= '	<th style="text-align:left;width: 50px">H</th>
					<th style="text-align:left;width: 50px">HF</th>
					<th style="text-align:left;width: 50px">S</th>
					<th style="text-align:left;width: 50px">C</th>
					</tr>
					</tbody></table></div>';

		$html .= '<div class="container-table-content">';
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed fixed-table">';
        $html .= '<tbody>';

		/*Close Header*/
		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel);
        $no = 1;
						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;width: 80px">'.$no.'</td>';
			$html .='<td style="text-align:center;width: 200px">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;width: 300px">'.$v_salesman['nama_salesman'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['tipe_sales'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['nama_regional'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['nama_area'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['city'].'</td>';
			
			/******************/
			$start = date_create($periode);
			$end = date_create($until);
			while($start <= $end)
			{
				$vdate=date_format($start,"Y-m-d");
				$vsalesmanid=$v_salesman['salesmanid'];
				$get_salesman_aktif = $this->report_gffaktif->get_salesman_aktif($vsalesmanid,$vdate);
				if (!empty($get_salesman_aktif)){
					foreach ($get_salesman_aktif as $val_aktif) {
						$namahari=date_format($start,"D");
						if ($namahari=='Sun'){
						$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">'.$val_aktif['status'].'</td>';
						}else{
						$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_aktif['status'].'</td>';
                        }
					}
				}else{
					$namahari=date_format($start,"D");
					if ($namahari=='Sun'){
					$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">-</td>';
					}else{
					$html .='<td style="text-align:center;font-size:11px;width: 50px">-</td>';
					}
				}

				$start->modify('+1 day');
			}
			
			$get_salesman_aktif_sum = $this->report_gffaktif->get_salesman_aktif_sum($vsalesmanid,$periode,$until);
			if (!empty($get_salesman_aktif_sum)){
				foreach ($get_salesman_aktif_sum as $val_sumaktif) {
					$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sumaktif'].'</td>';
					$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sumhf'].'</td>';
					$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sums'].'</td>';
					$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sumc'].'</td>';
				}
			}else{
				$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
				$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
				$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
				$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
			}
			
			/******************/
			$no++;
		}
		$html .='</tr>';
		$html .='<tr>';
		$html .='<td colspan="7" style="text-align:right;font-size:13px;">TOTAL</td>';
				$start = date_create($periode);
				$end = date_create($until);
				while($start <= $end)
				{
					$vdate=date_format($start,"Y-m-d");
					$get_salesman_aktif = $this->report_gffaktif->get_salesman_sum_daily($vdate,$position,$restrictlevel,$usersession);
					if (!empty($get_salesman_aktif)){
						foreach ($get_salesman_aktif as $val_aktif) {
							$namahari=date_format($start,"D");
							if ($namahari=='Sun'){
								$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">'.$val_aktif['sumaktif'].'</td>';
							}else{
								$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_aktif['sumaktif'].'</td>';
							}
						}
					}

					$start->modify('+1 day');
				}
				$get_aktif_sum_periode = $this->report_gffaktif->get_salesman_sum_periode($periode,$until,$position,$restrictlevel,$usersession);
				if (!empty($get_aktif_sum_periode)){
					foreach ($get_aktif_sum_periode as $val_sumaktif) {
						$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sumaktif'].'</td>';
						$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sumhf'].'</td>';
						$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sums'].'</td>';
						$html .='<td style="text-align:center;font-size:11px;width: 50px">'.$val_sumaktif['sumc'].'</td>';
					}
				}else{
						$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
						$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
						$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
						$html .='<td style="text-align:center;color:red;font-size:11px;width: 50px">0</td>';
				}
		$html .='</tr>';
		
		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .= '<script type="text/javascript">
                $(".container-table-content").on("scroll", function() {
                    $(".container-table").scrollLeft($(this).scrollLeft());
                });
                $(".container-table").on("scroll", function() {
                    $(".container-table-content").scrollLeft($(this).scrollLeft());
                });
            </script>';
		
		echo $html;
	}

	function savetoxls() {

		ini_set('memory_limit', '256M');
        $periode = $this->uri->segment('3');
        $until = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
		$usersession = $this->uri->segment('6');
		$restrictlevel = $this->uri->segment('7');
		$position = $this->uri->segment('8');
		
        /*Header*/
		$html ='<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:600px;
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
		$html .= '<div id="table-wrapper">
					<div id="table-scroll">
					Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti
					<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">No.</th>';
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">TPE</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama TPE</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Position</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Regional</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Area FC</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">City</th>';

		$start = date_create($periode);
		$end = date_create($until);
		$periodemonth = date_format($start,"M-Y");
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
		$html .='<th colspan="4" style="text-align:center;white-space:nowrap;">Total</th>';
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

		$html .= '	<th style="text-align:left;white-space:nowrap;">H</th>
					<th style="text-align:left;white-space:nowrap;">HF</th>
					<th style="text-align:left;white-space:nowrap;">S</th>
					<th style="text-align:left;white-space:nowrap;">C</th>
					</tr>
					</thead>';

        $html .= '<tbody>';

		/*Close Header*/
		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel);
        $no = 1;
						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;white-space:nowrap;">'.$no.'</td>';
			$html .='<td style="text-align:center;white-space:nowrap;">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_salesman'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['tipe_sales'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_regional'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_area'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['city'].'</td>';
			
			/******************/
			$start = date_create($periode);
			$end = date_create($until);
			while($start <= $end)
			{
				$vdate=date_format($start,"Y-m-d");
				$vsalesmanid=$v_salesman['salesmanid'];
				$get_salesman_aktif = $this->report_gffaktif->get_salesman_aktif($vsalesmanid,$vdate);
				if (!empty($get_salesman_aktif)){
					foreach ($get_salesman_aktif as $val_aktif) {
						$namahari=date_format($start,"D");
						if ($namahari=='Sun'){
						$html .='<th style="text-align:center;color:red;font-size:11px;">'.$val_aktif['status'].'</th>';
						}else{
						$html .='<th style="text-align:center;font-size:11px;">'.$val_aktif['status'].'</th>';
                        }
					}
				}else{
					$namahari=date_format($start,"D");
					if ($namahari=='Sun'){
					$html .='<th style="text-align:center;color:red;font-size:11px;">-</th>';
					}else{
					$html .='<th style="text-align:center;font-size:11px;">-</th>';
					}
				}

				$start->modify('+1 day');
			}
			
			$get_salesman_aktif_sum = $this->report_gffaktif->get_salesman_aktif_sum($vsalesmanid,$periode,$until);
			if (!empty($get_salesman_aktif_sum)){
				foreach ($get_salesman_aktif_sum as $val_sumaktif) {
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumaktif'].'</th>';
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumhf'].'</th>';
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sums'].'</th>';
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumc'].'</th>';
				}
			}else{
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
            }
			
			/******************/
			$no++;
		}
		$html .='</tr>';
		$html .='<tr>';
		$html .='<th colspan="7" style="text-align:right;font-size:13px;">TOTAL</th>';
				$start = date_create($periode);
				$end = date_create($until);
				while($start <= $end)
				{
					$vdate=date_format($start,"Y-m-d");
					$get_salesman_aktif = $this->report_gffaktif->get_salesman_sum_daily($vdate,$position,$restrictlevel,$usersession);
					if (!empty($get_salesman_aktif)){
						foreach ($get_salesman_aktif as $val_aktif) {
							$namahari=date_format($start,"D");
							if ($namahari=='Sun'){
								$html .='<th style="text-align:center;color:red;font-size:11px;">'.$val_aktif['sumaktif'].'</th>';
							}else{
								$html .='<th style="text-align:center;font-size:11px;">'.$val_aktif['sumaktif'].'</th>';
							}
						}
					}

					$start->modify('+1 day');
				}
				

		$get_aktif_sum_periode = $this->report_gffaktif->get_salesman_sum_periode($periode,$until,$position,$restrictlevel,$usersession);
		if (!empty($get_aktif_sum_periode)){
			foreach ($get_aktif_sum_periode as $val_sumaktif) {
				$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumaktif'].'</th>';
				$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumhf'].'</th>';
				$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sums'].'</th>';
				$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumc'].'</th>';
			}
		}else{
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
		}

		$html .='</tr>';
		
		$html .= '</tbody>';
		$html .= '</table></div></div>';

		$filename = "Report_TPE_Aktif_".$periodemonth.".xls";
        /*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
		*/

		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header('Cache-Control: max-age=0');
        header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
        
		echo $html;
	}

	
    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '256M');
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');

		$filename = "Report_TPE_Aktif_".$start.".xlsx";
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'TPE')
                    ->setCellValue('C2', 'Nama TPE')
                    ->setCellValue('D2', 'Position')
                    ->setCellValue('E2', 'Regional')
                    ->setCellValue('F2', 'Area FC')
					->setCellValue('G2', 'City')
                    ;

					$limit = 10;
					$value='test';
					for($i=0,$j='H';$i<$limit;$i++,$j++) {
						$objPHPExcel->setActiveSheetIndex(0)
					  ->setCellValue($j.'2', $value);
					}					

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

    }


}
