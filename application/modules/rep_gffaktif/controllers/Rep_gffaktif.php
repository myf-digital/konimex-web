<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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

	public function load_regional()
    {
        $data = param_input();
        responseJSON($this->report_gffaktif->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->report_gffaktif->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->report_gffaktif->get_city($data));
    }
	
	function namahari($tanggal) {
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

		$regionalid = $this->input->post("regionalid");
        $areaid = $this->input->post("areaid");
        $subareaid = $this->input->post("subareaid");

		$html = '<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= 'Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti';
		$html .= '<table id="activity_table" border="1" class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;width: 80px">No.</th>';
		$html .='<th rowspan="2" style="text-align:center;width: 200px">MEDREP</th>';
		$html .='<th rowspan="2" style="text-align:left;width: 300px">Nama MEDREP</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">Position</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">Regional</th>';
        $html .='<th rowspan="2" style="text-align:left;width: 200px">Area</th>';

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
		$html .='<th colspan="4" style="text-align:center;width: 215px">Total</th>';
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
					<th style="text-align:left;width: 65px">C</th>
					</tr>
					</tbody></table></div>';

		$html .= '<div class="container-table-content">';
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed fixed-table">';
        $html .= '<tbody>';

		/*Close Header*/
		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel, $regionalid, $areaid, $subareaid);
        $no = 1;
						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;width: 80px">'.$no.'</td>';
			$html .='<td style="text-align:center;width: 200px">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;width: 300px">'.$v_salesman['nama_salesman'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['tipe_sales'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['nama_regional'].'</td>';
			$html .='<td style="text-align:left;width: 200px">'.$v_salesman['nama_area'].'</td>';
			
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
		$html .='<td colspan="6" style="text-align:right;font-size:13px;">TOTAL</td>';
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

		$regionalid = $this->uri->segment('9');
        $areaid = $this->uri->segment('10');
        $subareaid = $this->uri->segment('11');
		
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
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">MEDREP</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama MEDREP</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Position</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Regional</th>';
        $html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Area</th>';

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
		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel,$regionalid,$areaid,$subareaid);
        $no = 1;
						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;white-space:nowrap;">'.$no.'</td>';
			$html .='<td style="text-align:center;white-space:nowrap;">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_salesman'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['tipe_sales'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_regional'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_area'].'</td>';
			
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
		$html .='<th colspan="6" style="text-align:right;font-size:13px;">TOTAL</th>';
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

		$filename = "Report_MEDREP_Aktif_".$periodemonth.".xls";

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
    	ini_set('memory_limit', '512M');

    	$periode = $this->uri->segment('3');
        $until = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
		$usersession = $this->uri->segment('6');
		$restrictlevel = $this->uri->segment('7');
		$position = $this->uri->segment('8');

		$regionalid = $this->uri->segment('9');
        $areaid = $this->uri->segment('10');
        $subareaid = $this->uri->segment('11');

        $start = date_create($periode);
		$end = date_create($until);
    	$filename = "Report_attendance_parma_".date_format($start,"M-Y");

		$spreadsheet = new Spreadsheet();
		$nb = ['Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti'];
		$header = ['No', 'Medrep', 'Medrep Name', 'Area'];

		$headerDate = [];
		$headerDay = [];
		$sumStatus = [];
		while($start <= $end)
		{
			$nameDay=date_format($start,"D");
			$day = date_format($start,"d") < 10 ? str_replace('0', '', date_format($start,"d")) : date_format($start,"d");
			$headerDate[] = $day;
			$headerDay[] = $nameDay;

			$vdate=date_format($start,"Y-m-d");
			$get_salesman_aktif = $this->report_gffaktif->get_salesman_sum_daily_new($vdate,$position,$restrictlevel,$usersession);

			if (!empty($get_salesman_aktif)){
				$sumStatus[] = $get_salesman_aktif->sumaktif;
			}

			$start->modify('+1 day');
		}

		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setTitle('Attendance');
		$sheet->fromArray($nb,NULL,'A1');
		$sheet->fromArray($header,NULL,'A2');
		$sheet->fromArray(array_merge($headerDate, ['Total']),NULL,'E2');
		$sheet->fromArray(array_merge($headerDay, ['H', 'HF', 'S', 'C']),NULL,'E3');

		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel,$regionalid,$areaid,$subareaid);

		$sids = [];
        $no = 1;
        $row = 4;		
		foreach ($get_salesman as $value) {

			$sids[] = $value['salesmanid'];

			$content = [
                $no, 
                $value['salesmanid'], 
                $value['nama_salesman'],
                $value['nama_area'],
            ];

            $sheet->fromArray($content,NULL,'A'.$row);
			
			$start = date_create($periode);
			$end = date_create($until);

			$status = [];
			while($start <= $end)
			{
				$vdate=date_format($start,"Y-m-d");
				$vsalesmanid=$value['salesmanid'];
				$get_salesman_aktif = $this->report_gffaktif->get_salesman_aktif_new($vsalesmanid,$vdate);

				if (!empty($get_salesman_aktif)){
					$status[] = $get_salesman_aktif->status;
				}else{
					$status[] = '-';
				}

				$start->modify('+1 day');
			}

			$get_salesman_aktif_sum = $this->report_gffaktif->get_salesman_aktif_sum_new($vsalesmanid,$periode,$until);

			$sumArr = [
				$get_salesman_aktif_sum->sumaktif,
				$get_salesman_aktif_sum->sumhf,
				$get_salesman_aktif_sum->sums,
				$get_salesman_aktif_sum->sumc
			];

            $sheet->fromArray(array_merge($status, $sumArr),NULL,'E'.$row);
			
			$no++;
			$row++;
		}

		$sheet->setCellValue('D'.$row, 'Total');
		$sheet->fromArray($sumStatus,NULL,'E'.$row);
		$sheet->mergeCells('A2:A3');
		$sheet->mergeCells('B2:B3');
		$sheet->mergeCells('C2:C3');
		$sheet->mergeCells('D2:D3');

		$sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center')->setVertical('center');

		//new tab sheet
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1);
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setTitle('Non Aktif');
		$header = ['No', 'Tanggal', 'Medrep', 'Medrep Name', 'Status', 'Keterangan', 'Photo'];
		$sheet->fromArray($nb,NULL,'A1');
		$sheet->fromArray($header,NULL,'A2');

		$gff_nonaktif = $this->report_gffaktif->get_salesman_non_aktif($sids, $periode, $until);
		
		$i = 1;
        $row = 3;
        foreach ($gff_nonaktif as $value) {

            $content = [
                $i, 
                $value['periode'], 
                $value['salesmanid'],
                $value['nama_salesman'],
                $value['status'],
                $value['keterangan']
            ];

            $sheet->fromArray($content,NULL,'A'.$row);

            if (!empty($value['image'])) {
                if (file_exists(DIR_IMAGE_PATH_ABSENCE.$value['image'])) {
                    $drawing = new Drawing();
                    $drawing->setPath(DIR_IMAGE_PATH_ABSENCE.$value['image']);
                    $drawing->setWidth(100); 
                    $drawing->setHeight(100);
                    $drawing->setCoordinates('H'.$row);                                                                      
                    $drawing->setWorksheet($sheet);
                    $sheet->getColumnDimension('H')->setWidth(20);
                    $sheet->getRowDimension($row)->setRowHeight(100);
                } else {
                    $sheet->setCellValue('H'.$row, '-');
                }
            } else {
                $sheet->setCellValue('H'.$row, '-');
            }

            $i++;
            $row++;
        }

		$spreadsheet->setActiveSheetIndex(0);
 
		$writer = new Xlsx($spreadsheet);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
    }

	function savexls_attendance_parma() {
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '0');
		$urlimage = URL_IMAGE;

        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $regionalid = $this->uri->segment('5');
        $areaid = $this->uri->segment('6');

        $usersession = $this->uri->segment('7');
        $restrict_level = $this->uri->segment('8');
        $idjabatan = $this->uri->segment('9');

        $params = [
        	'start_period' => $start,
        	'end_period' => $end,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid
        ];
        
        $filename = "Report-Attendance-".$start."_".$end.".xlsx";

        $this->load->library('excel');
    
        $objPHPExcel = new PHPExcel();
		$styleArray = array(
            'borders' => array(
                'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        // Attendance
        $date_interval = date_interval($params['start_period'], $params['end_period']);
        $objPHPExcel->createSheet(0);
        $sheetAttendance = $objPHPExcel->setActiveSheetIndex(0);
		$sheetAttendance->setTitle('Attendance');
        $sheetAttendance->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Medrep')
            ->setCellValue('C1', 'Medrep Name')
            ->setCellValue('D1', 'Area');
        $sheetAttendance->mergeCells('A1:A2');
        $sheetAttendance->mergeCells('B1:B2');
        $sheetAttendance->mergeCells('C1:C2');
        $sheetAttendance->mergeCells('D1:D2');

        $colIndex = 5;
        foreach ($date_interval as $dateObj) {
            $dateStr = format_date_id($dateObj, true, false);
            $colBase = number_to_alphabet($colIndex);

            $sheetAttendance->mergeCells("{$colBase}1:" . number_to_alphabet($colIndex+5) . '1');
            $sheetAttendance->setCellValue("{$colBase}1", $dateStr);

            $sheetAttendance->setCellValue(number_to_alphabet($colIndex)   . '2', 'Status');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+1) . '2', 'In');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+2) . '2', 'Image In');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+3) . '2', 'Out');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+4) . '2', 'Image Out');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+5) . '2', 'Duration');

            $colIndex += 6;
        }

        $attendanceMedrep = $this->report_gffaktif->get_attendance_parma($params);
        $salesman = [];
        foreach ($attendanceMedrep as $ap) {
            if (!in_array($ap['salesmanid'], array_column($salesman, 'salesmanid'))) {
                $filtered = array_filter($attendanceMedrep, function($row) use ($ap) {
                    return $row['salesmanid'] == $ap['salesmanid'];
                });

                $checkIn = [];
                $checkInImg = [];
                $checkOut = [];
                $checkOutImg = [];
                $status = [];

                foreach ($filtered as $row) {
                    $tgl = date('Y-m-d', strtotime($row['periode']));
                    $checkIn[$tgl] = $row['start_time'];
                    $checkInImg[$tgl] = $row['start_image'];
                    $checkOut[$tgl] = $row['end_time'];
                    $checkOutImg[$tgl] = $row['end_image'];
                    $status[$tgl] = $row['status'];
                }                
                $salesman[] = [
                    'salesmanid' => $ap['salesmanid'],
                    'nama_salesman' => $ap['nama_salesman'],
                    'nama_area' => $ap['nama_area'],
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_in_img' => $checkInImg,
                    'check_out' => $checkOut,
                    'check_out_img' => $checkOutImg,
                ];
            }
        }

        $i = 1;
        $row = 3;
        foreach ($salesman as $s) {
            $sheetAttendance->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $s['salesmanid'] ?? '')
                ->setCellValue('C'.$row, $s['nama_salesman'] ?? '')
                ->setCellValue('D'.$row, $s['nama_area'] ?? '');

            $colIdx = 5;
            foreach ($date_interval as $date_int) {
                $in = $s['check_in'][$date_int] ?? null;
                $inimg = $s['check_in_img'][$date_int] ?? null;
                if ($in) $in = date('H:i:s', strtotime($s['check_in'][$date_int]));

                $out = $s['check_out'][$date_int] ?? null;
                $outimg = $s['check_out_img'][$date_int] ?? null;
                if ($out) $out = date('H:i:s', strtotime($s['check_out'][$date_int]));

                $duration = cal_duration_date($s['check_in'][$date_int] ?? null, $s['check_out'][$date_int] ?? null);
                $status = $s['status'][$date_int] ?? null;
                
                $sheetAttendance->setCellValue(number_to_alphabet($colIdx)   . $row, $status);
                $sheetAttendance->setCellValue(number_to_alphabet($colIdx+1) . $row, $in);
                

				if($inimg!=null && $inimg!=''){$inimg=$urlimage.$inimg;}else{$inimg='';}
				if($outimg!=null && $outimg!=''){$outimg=$urlimage.$outimg;}else{$outimg='';}
				$sheetAttendance->setCellValue(number_to_alphabet($colIdx+2) . $row, $inimg);
                $sheetAttendance->setCellValue(number_to_alphabet($colIdx+3) . $row, $out);
                $sheetAttendance->setCellValue(number_to_alphabet($colIdx+4) . $row, $outimg);
                $sheetAttendance->setCellValue(number_to_alphabet($colIdx+5) . $row, $duration);

                $colIdx += 6;
            }
			$i++;
            $row++;
			//unlink($tempImage); // hapus file sementara setelah dimasukkan
        }
        
        // Hentikan output apa pun sebelum membuat file
        ob_end_clean();
        ob_start();
        error_reporting(0);

        // Bersihkan buffer output
        if (ob_get_length()) ob_end_clean();

		// Redirect output to a client's web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1'); // untuk IE9
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // tanggal kadaluwarsa
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
		
		unset($objPHPExcel);
		exit;
	}


}
