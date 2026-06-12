<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_sales_planned extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_sales_planned_model', 'rep_sales_planned');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    function load_view_planned() {
    	$salesmanid = $this->input->post("salesmanid");

    	if ($salesmanid == 'null' || !$salesmanid) {
    		echo json_encode(['status' => false, 'message' => 'Salesman ID is empty']);
    		return;
    	}	

    	$plannedDetail = $this->rep_sales_planned->getSalesPlannedDetail($salesmanid);
    	$salesSales = $this->rep_sales_planned->getSales($salesmanid);

    	if (!$salesSales) {
    		echo json_encode(['status' => false, 'message' => 'Salesman not found']);
    		return;
    	}

    	$salesActive = $salesSales->aktif == 1 ? 'Active' : 'Non Active';

		$supervisor = '';
		if ($salesSales->supervisorid) {
			$supervisor = $salesSales->salesmanid. ' - '.$salesSales->supervisor;
		}

    	$infoHtml = '
    	<table class="table table-bordered table-condensed report-table">
			<thead>
				<tr>
					<th style="white-space: nowrap;" colspan="2">MEDREP</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="white-space: nowrap;">Nama</td>
					<td style="white-space: nowrap;">'.$salesSales->salesmanid. ' - '.$salesSales->nama_salesman.'</td>
				</tr>
				<tr>
					<td style="white-space: nowrap;">Posisi</td>
					<td style="white-space: nowrap;">'.($salesSales->posisi ?? '-').'</td>
				</tr>
				<tr>
					<td style="white-space: nowrap;">Supervisor</td>
					<td style="white-space: nowrap;">'.$supervisor.'</td>
				</tr>
				<tr>
					<td style="white-space: nowrap;">Regional</td>
					<td style="white-space: nowrap;">'.($salesSales->nama_regional ?? '-').'</td>
				</tr>
				<tr>
					<td style="white-space: nowrap;">Area</td>
					<td style="white-space: nowrap;">'.($salesSales->nama_area ?? '-').'</td>
				</tr>
				<tr>
					<td style="white-space: nowrap;">Sub Area</td>
					<td style="white-space: nowrap;">'.($salesSales->nama_subarea ?? '-').'</td>
				</tr>
				<tr>
					<td style="white-space: nowrap;">Aktif</td>
					<td style="white-space: nowrap;">'.$salesActive.'</td>
				</tr>
			</tbody>
		</table>';

    	echo json_encode([
    		'status' => true,
    		'info_html' => $infoHtml,
    		'events' => $plannedDetail
    	]);
    }

    function download_to_excel() {
    	$salesmanid = $this->input->get("salesmanid");

    	if ($salesmanid == 'null') return false;

    	$salesmanid = str_replace('/', '', trim($salesmanid));	

    	$salesRekap = $this->rep_sales_planned->getSalesPlannedDetail($salesmanid);
    	$salesSales = $this->rep_sales_planned->getSales($salesmanid);

    	$salesActive = $salesSales->aktif == 1 ? 'Active' : 'Non Active';

		$supervisor = '';
		if ($salesSales->supervisorid) {
			$supervisor = $salesSales->salesmanid. ' - '.$salesSales->supervisor;
		}

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
    	$html .= '<div class="col-md-4 margin-top-less">';
    	$html .= '<table class="table table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;" colspan="2">MEDREP</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Nama</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->salesmanid. ' - '.$salesSales->nama_salesman.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Posisi</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->posisi.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Supervisor</td>';
		$html .= '<td style="white-space: nowrap;">'.$supervisor.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Regional</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->nama_regional.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Area</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->nama_area.'</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;">Sub Area</td>';
		$html .= '<td style="white-space: nowrap;">'.$salesSales->nama_subarea.'</td>';
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
        $html .= '<th style="white-space: nowrap;">No</th>';
		$html .= '<th style="white-space: nowrap;">Tanggal</th>';
		$html .= '<th style="white-space: nowrap;">ID Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Nama Professional</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$no = 1;
		foreach ($salesRekap as $value) {
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$no++.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['periode'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['outlet'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['user_name'].'</td>';
			$html .= '</tr>';
		}
		
		$html .= '</tbody>';
		$html .= '</table></div></div></div>';

		$filename = "Report_sales_planned_".$salesmanid."_".date('YmdHis').".xls";

		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $filename);
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

    	$salesRekap = $this->rep_sales_planned->getSalesPlannedDetail($salesmanid);
    	$salesSales = $this->rep_sales_planned->getSales($salesmanid);

    	$salesActive = $salesSales->aktif == 1 ? 'Active' : 'Non Active';

		$supervisor = '';
		if ($salesSales->supervisorid) {
			$supervisor = $salesSales->salesmanid. ' - '.$salesSales->supervisor;
		}

		$filename = "Report_sales_planned_".$salesmanid."_".date('YmdHis');

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Report Planned');
		$row1 = ['MEDREP'];
		$row2 = ['Nama', $salesSales->salesmanid. ' - '.$salesSales->nama_salesman];
		$row3 = ['Posisi', $salesSales->posisi];
		$row4 = ['Supervisor', $supervisor];
		$row5 = ['Regional', $salesSales->nama_regional];
		$row6 = ['Area', $salesSales->nama_area];
		$row7 = ['Sub Area', $salesSales->nama_subarea];
		$row8 = ['Aktif', $salesActive];

		$row10 = ['No', 'Tanggal', 'ID Outlet', 'Nama Outlet', 'Nama Professional'];

		$spreadsheet->getActiveSheet()
		    ->fromArray($row1,NULL,'A1');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row2,NULL,'A2');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row3,NULL,'A3');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row4,NULL,'A4');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row5,NULL,'A5');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row6,NULL,'A6');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row7,NULL,'A7');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row8,NULL,'A8');
		$spreadsheet->getActiveSheet()
		    ->fromArray($row10,NULL,'A10');

		$rowNum = 11;
		$no = 1;
		foreach ($salesRekap as $value) {
			$rowData = [
				$no++,
				$value['periode'],
				$value['customerid'],
				$value['outlet'],
				$value['user_name']
			];
			$spreadsheet->getActiveSheet()->fromArray($rowData, NULL, 'A' . $rowNum);
			$rowNum++;
		}

		$headerStyle = [
			'font' => [
				'bold' => true,
				'color' => ['rgb' => 'FFFFFF'],
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'D50017'],
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			]
		];
		$spreadsheet->getActiveSheet()->getStyle('A10:E10')->applyFromArray($headerStyle);

		// Style MEDREP header cell A1:B1
		$spreadsheet->getActiveSheet()->mergeCells('A1:B1');
		$spreadsheet->getActiveSheet()->getStyle('A1:B1')->applyFromArray($headerStyle);

		// Apply borders to the table cells (header + data rows)
		$borderStyle = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['rgb' => 'CCCCCC'],
				],
			],
		];
		$lastRow = $rowNum - 1;
		if ($lastRow >= 10) {
			$spreadsheet->getActiveSheet()->getStyle('A10:E' . $lastRow)->applyFromArray($borderStyle);
		}

		// Apply borders to the MEDREP Info box (A1:B8)
		$spreadsheet->getActiveSheet()->getStyle('A1:B8')->applyFromArray($borderStyle);

		// Style Info Label columns (A2:A8) with soft pink background and bold font
		$infoLabelStyle = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'FFF5F5'],
			],
			'font' => [
				'bold' => true,
				'color' => ['rgb' => '333333'],
			]
		];
		$spreadsheet->getActiveSheet()->getStyle('A2:A8')->applyFromArray($infoLabelStyle);

		// Auto-size columns A to E based on content width
		foreach (range('A', 'E') as $col) {
			$spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}
        
		$writer = new Xlsx($spreadsheet);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
    }
}