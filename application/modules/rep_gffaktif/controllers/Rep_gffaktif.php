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

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->report_gffaktif->get_city($data));
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

		$regionalid = $this->input->post("regionalid");
        $areaid = $this->input->post("areaid");

		$html = '<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= 'Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti';
		$html .= '<table id="activity_table" border="1" class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;width: 80px">No.</th>';
		$html .='<th rowspan="2" style="text-align:center;width: 200px">GFF</th>';
		$html .='<th rowspan="2" style="text-align:left;width: 300px">Nama GFF</th>';
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
		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel, $regionalid, $areaid);
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

		$regionalid = $this->uri->segment('9');
        $areaid = $this->uri->segment('10');
		
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
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">GFF</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama GFF</th>';
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
		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel,$regionalid,$areaid);
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

		$filename = "Report_GFF_Aktif_".$periodemonth.".xls";
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
    	ini_set('memory_limit', '512M');

    	$periode = $this->uri->segment('3');
        $until = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
		$usersession = $this->uri->segment('6');
		$restrictlevel = $this->uri->segment('7');
		$position = $this->uri->segment('8');

		$regionalid = $this->uri->segment('9');
        $areaid = $this->uri->segment('10');

        $start = date_create($periode);
		$end = date_create($until);
    	$filename = "Report_gff_aktif_".date_format($start,"M-Y");

		$spreadsheet = new Spreadsheet();
		$nb = ['Keterangan : H -> Hadir , HF -> Hari Off, S -> Sakit, C -> Izin Cuti'];
		$header = ['No', 'GFF', 'Nama GFF', 'Position', 'Regional', 'Area FC', 'City'];

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

		$sheet->setTitle('Absen');
		$sheet->fromArray($nb,NULL,'A1');
		$sheet->fromArray($header,NULL,'A2');
		$sheet->fromArray(array_merge($headerDate, ['Total']),NULL,'H2');
		$sheet->fromArray(array_merge($headerDay, ['H', 'HF', 'S', 'C']),NULL,'H3');

		$get_salesman = $this->report_gffaktif->get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel,$regionalid,$areaid);

		$sids = [];
        $no = 1;
        $row = 4;		
		foreach ($get_salesman as $value) {

			$sids[] = $value['salesmanid'];

			$content = [
                $no, 
                $value['salesmanid'], 
                $value['nama_salesman'],
                $value['tipe_sales'],
                $value['nama_regional'],
                $value['nama_area'],
                $value['city']
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

            $sheet->fromArray(array_merge($status, $sumArr),NULL,'H'.$row);
			
			$no++;
			$row++;
		}

		$sheet->setCellValue('G'.$row, 'Total');
		$sheet->fromArray($sumStatus,NULL,'H'.$row);
		$sheet->mergeCells('A2:A3');
		$sheet->mergeCells('B2:B3');
		$sheet->mergeCells('C2:C3');
		$sheet->mergeCells('D2:D3');
		$sheet->mergeCells('E2:E3');
		$sheet->mergeCells('F2:F3');
		$sheet->mergeCells('G2:G3');
		$sheet->getStyle('A2:G2')->getAlignment()->setHorizontal('center')->setVertical('center');

		//new tab sheet
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1);
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setTitle('Non Aktif');
		$header = ['No', 'Tanggal', 'GFF', 'Nama GFF', 'Position', 'Status', 'Keterangan', 'Photo'];
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
                $value['tipe_sales'],
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


}
