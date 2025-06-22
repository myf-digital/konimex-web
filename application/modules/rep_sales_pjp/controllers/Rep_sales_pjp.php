<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_sales_pjp extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_sales_pjp_model', 'rep_sales');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    function load_view_salespjp() {
    	$salesmanid = $this->input->post("salesmanid");

    	if ($salesmanid == 'null') return false;	

    	$salesRekap = $this->rep_sales->getSalesRekapPjp($salesmanid);
    	$salesSales = $this->rep_sales->getSales($salesmanid);

    	$salesActive = $salesSales->aktif == 1 ? 'Active' : 'Non Active';

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
    	$html .= '<div class="col-md-4 margin-top-less">';
    	$html .= '<table class="table table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;" colspan="2">Sales</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Nama</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->nama.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Posisi</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->posisi.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Aktif</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesActive.'</td>';
		$html .= '</tr>';
		$html .= '</tbody">';
		$html .= '</table>';
		$html .= '</div>';

		$html .= '<div class="col-md-12">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Week</th>';
		$html .= '<th style="white-space: nowrap;">Senin</th>';
		$html .= '<th style="white-space: nowrap;">Selasa</th>';
		$html .= '<th style="white-space: nowrap;">Rabu</th>';
		$html .= '<th style="white-space: nowrap;">Kamis</th>';
		$html .= '<th style="white-space: nowrap;">Jumat</th>';
		$html .= '<th style="white-space: nowrap;">Sabtu</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$formatWeek1 = [];
		$formatWeek2 = [];
		$formatWeek3 = [];
		$formatWeek4 = [];

		foreach ($salesRekap as $value) {
			if ($value['minggu'] == '1') {
				$formatWeek1[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '2') {
				$formatWeek2[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '3') {
				$formatWeek3[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '4') {
				$formatWeek4[$value['hari']] = $value['jml'];
			}
		}

		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 1</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[6].'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 2</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[6].'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 3</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[6].'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 4</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[6].'</td>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table></div></div></div>';

		echo $html;
    }

    function download_to_excel() {
    	$salesmanid = $this->input->get("salesmanid");

    	if ($salesmanid == 'null') return false;

    	$salesmanid = str_replace('/', '', trim($salesmanid));	

    	$salesRekap = $this->rep_sales->getSalesRekapPjp($salesmanid);
    	$salesSales = $this->rep_sales->getSales($salesmanid);

    	$salesActive = $salesSales->aktif == 1 ? 'Active' : 'Non Active';

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
    	$html .= '<div class="col-md-4 margin-top-less">';
    	$html .= '<table class="table table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;" colspan="2">Sales</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Nama</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->nama.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Posisi</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->posisi.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Aktif</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesActive.'</td>';
		$html .= '</tr>';
		$html .= '</tbody">';
		$html .= '</table>';
		$html .= '</div>';

		$html .= '<div class="col-md-12">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Week</th>';
		$html .= '<th style="white-space: nowrap;">Senin</th>';
		$html .= '<th style="white-space: nowrap;">Selasa</th>';
		$html .= '<th style="white-space: nowrap;">Rabu</th>';
		$html .= '<th style="white-space: nowrap;">Kamis</th>';
		$html .= '<th style="white-space: nowrap;">Jumat</th>';
		$html .= '<th style="white-space: nowrap;">Sabtu</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$formatWeek1 = [];
		$formatWeek2 = [];
		$formatWeek3 = [];
		$formatWeek4 = [];

		foreach ($salesRekap as $value) {
			if ($value['minggu'] == '1') {
				$formatWeek1[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '2') {
				$formatWeek2[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '3') {
				$formatWeek3[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '4') {
				$formatWeek4[$value['hari']] = $value['jml'];
			}
		}

		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 1</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek1[6].'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 2</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek2[6].'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 3</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek3[6].'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Week 4</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[1].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[2].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[3].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[4].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[5].'</td>';
		$html .= '<td style="white-space: nowrap;">'.@$formatWeek4[6].'</td>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table></div></div></div>';

		$filename = "Report_sales_pjp_".$salesmanid."_".date('YmdHis').".xls";

		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header('Cache-Control: max-age=0');
        header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);

		echo $html;
    }

    function download_to_excel_spreadsheet() {
    	ini_set('memory_limit', '128M');
    	$salesmanid = $this->input->get("salesmanid");

    	if ($salesmanid == 'null') return false;

    	$salesmanid = str_replace('/', '', trim($salesmanid));	

    	$salesRekap = $this->rep_sales->getSalesRekapPjp($salesmanid);
    	$salesSales = $this->rep_sales->getSales($salesmanid);

    	$salesActive = $salesSales->aktif == 1 ? 'Active' : 'Non Active';

		$formatWeek1 = [];
		$formatWeek2 = [];
		$formatWeek3 = [];
		$formatWeek4 = [];

		foreach ($salesRekap as $value) {
			if ($value['minggu'] == '1') {
				$formatWeek1[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '2') {
				$formatWeek2[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '3') {
				$formatWeek3[$value['hari']] = $value['jml'];
			}

			if ($value['minggu'] == '4') {
				$formatWeek4[$value['hari']] = $value['jml'];
			}
		}

		$filename = "Report_sales_pjp_".$salesmanid."_".date('YmdHis');

		$spreadsheet = new Spreadsheet();
		$row1 = ['Sales'];
		$row2 = ['Nama', $salesSales->nama];
		$row3 = ['Posisi', $salesSales->posisi];
		$row4 = ['Aktif', $salesActive];

		$row5 = ['Week', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		$row6 = array_merge(['Week 1'], $formatWeek1);
		$row7 = array_merge(['Week 2'], $formatWeek2);
		$row8 = array_merge(['Week 3'], $formatWeek3);
		$row9 = array_merge(['Week 4'], $formatWeek4);

		$spreadsheet->getActiveSheet()
		    ->fromArray($row1,NULL,'A1');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row2,NULL,'A2');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row3,NULL,'A3');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row4,NULL,'A4');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row5,NULL,'A6');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row6,NULL,'A7');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row7,NULL,'A8');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row8,NULL,'A9');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row9,NULL,'A10');
        
		$writer = new Xlsx($spreadsheet);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
    }
}