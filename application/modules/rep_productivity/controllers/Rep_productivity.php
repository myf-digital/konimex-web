<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Rep_productivity extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_productivity_model', 'report_productivity');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->report_productivity->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->report_productivity->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->report_productivity->get_city($data));
    }

    function open_detail() {
		$start = $this->input->post("start_period");
		$end = $this->input->post("end_period");
        //$position = $this->input->post("position");

		$idjabatan = $this->input->post("idjabatan");
		$restrict_level = $this->input->post("restrict_level");
		$usersession = $this->input->post("usersession");

        $regionalid = $this->input->post("regionalid");
        $areaid = $this->input->post("areaid");

		/*if ($year.'-'.$month==date("Y-m")){
			$periodedate= date("Y-m-d");
			//$periodedate= date("Y-m-d",strtotime($periode));
		}else{
			$periode = $year.'-'.$month.'-01';
			$periodedate=date("Y-m-t",strtotime($periode));
		}*/

        $params = [
        	'start_period' => $start,
        	'end_period' => $end,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        ];

        $data = $this->report_productivity->getProductivity($params);
        //echo $this->db->last_query();
		//die();
		
		$html ='<div class="box-body">';
		$html .= '<div class="container-table">';
		//$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 50px">No</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Area</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">User PAR-MA</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 300px;">Nama PAR-MA</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Position</th>';
		$html .= '<th colspan="13" style="vertical-align : middle;text-align:center;width: 2600px;">Kuantitatif</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="vertical-align : middle;text-align:center;">HK</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Absensi</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%Kehadiran</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Keterangan</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Target Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Call on FJP</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Effective Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Extra Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Actual Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%FJP Compliance</th>';
		$html .= '<th style="vertical-align : middle;text-align:center; width: 200px;">Keterangan</th>';
		$html .= '<th style="vertical-align : middle;text-align:center; width: 200px;">Detailing</th>';
		$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		$html .= '<div class="container-table-content">';
		$html .= '<table class="table table-striped table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td style="width: 50px;">'.$i.'</td>';
			$html .= '<td style="width:150px;">'.$value['nama_area'].'</td>';
			$html .= '<td style="width:150px;">'.$value['salesmanid'].'</td>';
			$html .= '<td style="width:300px;">'.$value['nama_salesman'].'</td>';
			$html .= '<td style="width:150px;">'.$value['tipe_sales'].'</td>';

			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['PAR-MA Aktif'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['PAR-MA Hadir'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['PAR-MA Hadir']/$value['PAR-MA Aktif']*100, 2, '.', ',').' %</td>';
			$html .= '<td style="text-align:right;width: 200px">Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').') </td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['pjp'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['effective_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['extra_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call']+$value['extra_call'], 0, '.', ',').'</td>';
            if ($value['pjp']) {
			    $html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call']/$value['pjp']*100, 2, '.', ',').' %</td>';
            } else {
			    $html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format(0, 2, '.', ',').' %</td>';
            }
			$html .= '<td style="text-align:right; width: 200px;">'.$value['rrk_keterangan'].'</td>';
			$html .= '<td style="text-align:right; width: 200px;">'.$value['rrk_detailing'].'</td>';
			$html .= '</tr>';
			$i++;
		}
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

    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '0');
        ini_set('max_execution_time', '0');
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $regionalid = $this->uri->segment('5');
        $areaid = $this->uri->segment('6');

        $usersession = $this->uri->segment('7');
        $restrict_level = $this->uri->segment('8');
        $idjabatan = $this->uri->segment('9');

		/*if ($year.'-'.$month==date("Y-m")){
			$periodedate= date("Y-m-d");
		}else{
			$periode = $year.'-'.$month.'-01';
			$periodedate=date("Y-m-t",strtotime($periode));
		}*/

        $params = [
        	'start_period' => $start,
        	'end_period' => $end,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        ];

        $data = $this->report_productivity->getProductivity($params);

        $filename = "Report_Productivity_".$start."_".$end;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Area', 'User PAR-MA', 'Nama PAR-MA', 'Position', 'HK', 'Absensi', '%Kehadiran', 'Keterangan', 'Target Call', 'Call on FJP', 'Extra Call', 'Actual Call', '%FJP Compliance', 'Keterangan', 'Detailing'];

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('F1', 'Kuantitatif')->mergeCells('F1:P1');

        $sheet->fromArray($header,NULL,'A2');

        $i = 1;
        $row = 3;
        foreach ($data as $value) {

            $content = [
                $i, 
				$value['nama_area'],
				$value['salesmanid'],
				$value['nama_salesman'],
				$value['tipe_sales'],
				number_format($value['PAR-MA Aktif'], 0, '.', ','),
				number_format($value['PAR-MA Hadir'], 0, '.', ','),
				number_format($value['PAR-MA Hadir']/$value['PAR-MA Aktif']*100, 2, '.', ','),
				'Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').')',
				number_format($value['pjp'], 0, '.', ','),
				number_format($value['call'], 0, '.', ','),
				number_format($value['extra_call'], 0, '.', ','),
				number_format($value['call']+$value['extra_call'], 0, '.', ','),
				number_format($value['call']/$value['pjp']*100, 2, '.', ',').' %',
				$value['rrk_keterangan'],
				$value['rrk_detailing']
				];

            $sheet->fromArray($content,NULL,'A'.$row);

            $i++;
            $row++;
        }
 
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
   }

	function savexls_visit_and_order() {
		//ini_set('memory_limit', '0MB');
        ini_set('max_execution_time', '0');
		
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
        
        $filename = "Report-Productivity-".$start."_".$end.".xlsx";

        $this->load->library('excel');
    
        $objPHPExcel = new PHPExcel();
		$styleArray = array(
            'borders' => array(
                'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        // Productivity
        $objPHPExcel->createSheet(0);
        $sheetProductivity = $objPHPExcel->setActiveSheetIndex(0);
        $sheetProductivity->setTitle('Productivity');
        $sheetProductivity
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'City/Area')
            ->setCellValue('C1', 'PAR-MA')
            ->setCellValue('D1', 'PAR-MA Name')
            ->setCellValue('E1', 'Position')
            ->setCellValue('F1', 'HK')
            ->setCellValue('G1', 'Absensi')
            ->setCellValue('H1', '%Kehadiran')
            ->setCellValue('I1', 'Keterangan Absensi')
            ->setCellValue('J1', 'Target Call')
            ->setCellValue('K1', 'Effective Call')
            ->setCellValue('L1', 'Call')
            ->setCellValue('M1', 'Extra Call')
            ->setCellValue('N1', 'Actual Call')
            ->setCellValue('O1', '%FJP Compliance')
            ->setCellValue('P1', 'Outlet Order')
            ->setCellValue('Q1', 'Total Order')
            ->setCellValue('R1', 'Keterangan')
            ->setCellValue('S1', 'Detailing');
		
        $data = $this->report_productivity->getProductivity($params);
        $i = 1;
        $row = 2;
        foreach ($data as $value) {
			$sheetProductivity->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $value['city'] ?? '')
                ->setCellValue('C'.$row, $value['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $value['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $value['tipe_sales'] ?? '')
                ->setCellValue('F'.$row, $value['PAR-MA Aktif'] ?? '')
                ->setCellValue('G'.$row, $value['PAR-MA Hadir'] ?? '')
                ->setCellValue('H'.$row, '=G'.$row.'/F'.$row)
                ->setCellValue('I'.$row, 'Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').')')
                ->setCellValue('J'.$row, $value['pjp'] ?? '')
                ->setCellValue('K'.$row, $value['effective_call'] ?? '')
                ->setCellValue('L'.$row, $value['call'] ?? '')
                ->setCellValue('M'.$row, $value['extra_call'] ?? '')
                ->setCellValue('N'.$row, '=K'.$row.'+L'.$row.'+M'.$row)
                ->setCellValue('O'.$row, '=(K'.$row.'+L'.$row.')/J'.$row)
                ->setCellValue('P'.$row, $value['jumlah_customer'] ?? '')
                ->setCellValue('Q'.$row, $value['total_penjualan'] ?? '')
                ->setCellValue('R'.$row, $value['rrk_keterangan'] ?? '')
                ->setCellValue('S'.$row, $value['rrk_detailing'] ?? '');
			
			$objPHPExcel->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));
			$objPHPExcel->getActiveSheet()->getStyle('P'.$row)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));
			$objPHPExcel->getActiveSheet()->getStyle('R'.$row)->getNumberFormat()->setFormatCode('"Rp. "#,##0.00');
			$i++;
            $row++;
        }
		
        // Visit PAR-MA
        $objPHPExcel->createSheet(1);
        $sheetVisit = $objPHPExcel->setActiveSheetIndex(1);
        $sheetVisit->setTitle('Visit PAR-MA');
        $sheetVisit->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Period')
            ->setCellValue('C1', 'PAR-MA')
            ->setCellValue('D1', 'PAR-MA Name')
            ->setCellValue('E1', 'PAR-MA ID Outlet')
            ->setCellValue('F1', 'Latest JJid')
            ->setCellValue('G1', 'PAR-MA Nama Outlet')
            ->setCellValue('H1', 'Cluster')
            ->setCellValue('I1', 'Tier')
            ->setCellValue('J1', 'Area')
            ->setCellValue('K1', 'CheckIn')
            ->setCellValue('L1', 'CheckOut')
            ->setCellValue('M1', 'Time Visit')
            ->setCellValue('N1', 'Reason')
            ->setCellValue('O1', 'Description')
            ->setCellValue('P1', 'Flag');

        $datavisit = $this->report_productivity->getVisit_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($datavisit as $valuevisit) {
            $sheetVisit->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $valuevisit['periode'] ?? '')
                ->setCellValue('C'.$row, $valuevisit['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $valuevisit['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $valuevisit['customerid'] ?? '')
                ->setCellValue('F'.$row, $valuevisit['latest_jjid'] ?? '')
                ->setCellValue('G'.$row, $valuevisit['nama_customer'] ?? '')
                ->setCellValue('H'.$row, $valuevisit['channel'] ?? '')
                ->setCellValue('I'.$row, $valuevisit['account'] ?? '')
                ->setCellValue('J'.$row, $valuevisit['nama_area'] ?? '')
                ->setCellValue('K'.$row, $valuevisit['check_in'] ?? '')
                ->setCellValue('L'.$row, $valuevisit['check_out'] ?? '')
                ->setCellValue('M'.$row, $valuevisit['lama_kunjungan'] ?? '')
                ->setCellValue('N'.$row, $valuevisit['alasan'] ?? '')
                ->setCellValue('O'.$row, $valuevisit['keterangan'] ?? '')
                ->setCellValue('P'.$row, $valuevisit['flag'] ?? '');
			$i++;
            $row++;
        }
		
        // Order PAR-MA
        $objPHPExcel->createSheet(2);
        $sheetOrder = $objPHPExcel->setActiveSheetIndex(2);
        $sheetOrder->setTitle('Order PAR-MA');
		$sheetOrder->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'User PAR-MA')
            ->setCellValue('D1', 'PAR-MA ID Outlet')
            ->setCellValue('E1', 'Latest JJid')
            ->setCellValue('F1', 'ID Outlet Distributor')
            ->setCellValue('G1', 'PAR-MA Nama Outlet')
            ->setCellValue('H1', 'Tier')
            ->setCellValue('I1', 'No SP')
            ->setCellValue('J1', 'No Sales Order')
            ->setCellValue('K1', 'ProductID')
            ->setCellValue('L1', 'Product Name')
            ->setCellValue('M1', 'Qty (PAR-MA Apps)')
            ->setCellValue('N1', 'Price')
            ->setCellValue('O1', 'Total GTS (PAR-MA Apps)')
            ->setCellValue('P1', 'Status')
            ->setCellValue('Q1', 'JJID + Product Name')
            ->setCellValue('R1', 'Qty Actual (Tableau Kenvue)')
            ->setCellValue('S1', 'Total GTS (Tableau Kenvue)')
            ->setCellValue('T1', 'Gap Qty')
            ->setCellValue('U1', 'Gap Total GTS');

        $dataorder = $this->report_productivity->getOrder_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($dataorder as $valueorder) {
            $sheetOrder->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $valueorder['period'] ?? '')
                ->setCellValue('C'.$row, $valueorder['nama_salesman']." (".$valueorder['salesmanid'].")")
                ->setCellValue('D'.$row, $valueorder['customerid'] ?? '')
                ->setCellValue('E'.$row, $valueorder['latest_jjid'] ?? '')
                ->setCellValue('F'.$row, $valueorder['cust_id_map'] ?? '')
                ->setCellValue('G'.$row, $valueorder['nama_customer'] ?? '')
                ->setCellValue('H'.$row, $valueorder['account'] ?? '')
                ->setCellValue('I'.$row, $valueorder['no_po'] ?? '')
                ->setCellValue('J'.$row, $valueorder['no_sales'] ?? '')
                ->setCellValue('K'.$row, $valueorder['productid'] ?? '')
                ->setCellValue('L'.$row, $valueorder['nama_invoice'] ?? '')
                ->setCellValue('M'.$row, $valueorder['qty_jual_in_pcs'] ?? '')
                ->setCellValue('N'.$row, $valueorder['h_jual'] ?? '')
                ->setCellValue('O'.$row, '=M'.$row.'*N'.$row)
                ->setCellValue('P'.$row, $valueorder['status'] ?? '')
                ->setCellValue('Q'.$row, '=E'.$row.'&L'.$row)
                ->setCellValue('R'.$row, '')
                ->setCellValue('S'.$row, '')
                ->setCellValue('T'.$row, '=M'.$row.'-R'.$row)
                ->setCellValue('U'.$row, '=O'.$row.'-S'.$row);
			$i++;
            $row++;
        }

        // CRC
        $objPHPExcel->createSheet(3);
        $sheetCRC = $objPHPExcel->setActiveSheetIndex(3);
        $sheetCRC->setTitle('CRC');
		$sheetCRC->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Period')
            ->setCellValue('C1', 'User PAR-MA')
            ->setCellValue('D1', 'PAR-MA Name')
            ->setCellValue('E1', 'PAR-MA ID Outlet')
            ->setCellValue('F1', 'Latest JJid')
            ->setCellValue('G1', 'PAR-MA Nama Outlet')
            ->setCellValue('H1', 'Tier')
            ->setCellValue('I1', 'ProductID')
            ->setCellValue('J1', 'Product Name')
            ->setCellValue('K1', 'Brand')
            ->setCellValue('L1', 'Qty Stock');

        $datacrc = $this->report_productivity->getcrc_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($datacrc as $valuecrc) {
            $sheetCRC->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $valuecrc['periode'] ?? '')
                ->setCellValue('C'.$row, $valuecrc['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $valuecrc['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $valuecrc['customerid'] ?? '')
                ->setCellValue('F'.$row, $valuecrc['latest_jjid'] ?? '')
                ->setCellValue('G'.$row, $valuecrc['nama_customer'] ?? '')
                ->setCellValue('H'.$row, $valuecrc['account'] ?? '')
                ->setCellValue('I'.$row, $valuecrc['productid'] ?? '')
                ->setCellValue('J'.$row, $valuecrc['nama_invoice'] ?? '')
                ->setCellValue('K'.$row, $valuecrc['nama_brand'] ?? '')
                ->setCellValue('L'.$row, $valuecrc['qty_akhir'] ?? '');
			$i++;
            $row++;
        }

        // Detailing
        $objPHPExcel->createSheet(4);
        $sheetDetailing = $objPHPExcel->setActiveSheetIndex(4);
		$sheetDetailing->setTitle('Detailing');
        $sheetDetailing->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Period')
            ->setCellValue('C1', 'PAR-MA')
            ->setCellValue('D1', 'PAR-MA Name')
            ->setCellValue('E1', 'PAR-MA ID Outlet')
            ->setCellValue('F1', 'Latest JJid')
            ->setCellValue('G1', 'PAR-MA Nama Outlet')
            ->setCellValue('H1', 'Cluster')
            ->setCellValue('I1', 'Tier')
            ->setCellValue('J1', 'Area')
            ->setCellValue('K1', 'PIC Name')
            ->setCellValue('L1', 'Brand Detailing')
            ->setCellValue('M1', 'Reason')
            ->setCellValue('N1', 'Description');

        $datavisit = $this->report_productivity->get_detailing_parma($params);
        $i = 1;
        $row = 2;
        foreach ($datavisit as $valuevisit) {
            $sheetDetailing->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $valuevisit['periode'] ?? '')
                ->setCellValue('C'.$row, $valuevisit['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $valuevisit['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $valuevisit['customerid'] ?? '')
                ->setCellValue('F'.$row, $valuevisit['latest_jjid'] ?? '')
                ->setCellValue('G'.$row, $valuevisit['nama_customer'] ?? '')
                ->setCellValue('H'.$row, $valuevisit['channel'] ?? '')
                ->setCellValue('I'.$row, $valuevisit['account'] ?? '')
                ->setCellValue('J'.$row, $valuevisit['nama_area'] ?? '')
                ->setCellValue('K'.$row, $valuevisit['professional_name'] ?? '')
                ->setCellValue('L'.$row, $valuevisit['brands'] ?? '')
                //->setCellValue('M'.$row, $valuevisit['start_detailing'] ?? '')
                ->setCellValue('M'.$row, $valuevisit['reason'] ?? '')
                ->setCellValue('N'.$row, $valuevisit['keterangan'] ?? '');
			$i++;
            $row++;
        }
        
        // Progress Listing
        $objPHPExcel->createSheet(5);
        $sheetProgressListing = $objPHPExcel->setActiveSheetIndex(5);
		$sheetProgressListing->setTitle('Progress Listing');
        $sheetProgressListing->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Periode')
            ->setCellValue('C1', 'PAR-MA')
            ->setCellValue('D1', 'PAR-MA Name')
            ->setCellValue('E1', 'PAR-MA ID Outlet')
            ->setCellValue('F1', 'PAR-MA Nama Outlet')
            ->setCellValue('G1', 'Brand ID')
            ->setCellValue('H1', 'Brand Name')
            ->setCellValue('I1', 'Progress')
            ->setCellValue('J1', 'Ambil Dokumen Register')
            ->setCellValue('K1', 'Melengkapi Dokumen Register')
            ->setCellValue('L1', 'Sign Dokter 1')
            ->setCellValue('M1', 'Sign Dokter 2')
            ->setCellValue('N1', 'Sign Dokter 3')
            ->setCellValue('O1', 'Sign Dokter 4')
            ->setCellValue('P1', 'Sign Dokter 5')
            ->setCellValue('Q1', 'Dokumen Registrasi Lengkap')
            ->setCellValue('R1', 'Estimasi PO')
            ->setCellValue('S1', 'PO Release');

        $progressListing = $this->report_productivity->get_progress_listing($params);
        $i = 1;
        $row = 2;
        foreach ($progressListing as $value) {
            $sheetProgressListing->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $value['periode'] ?? '')
                ->setCellValue('C'.$row, $value['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $value['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $value['customerid'] ?? '')
                ->setCellValue('F'.$row, $value['nama_customer'] ?? '')
                ->setCellValue('G'.$row, $value['brandid'] ?? '')
                ->setCellValue('H'.$row, $value['brand'] ?? '')
                ->setCellValue('I'.$row, $value['progress'] ?? '')
                ->setCellValue('J'.$row, $this->signFormat($value['ambil_dok_registrasi']))
                ->setCellValue('K'.$row, $this->signFormat($value['melengkapi_dok_registrasi']))
                ->setCellValue('L'.$row, $this->signFormat($value['sign_dokter_1']))
                ->setCellValue('M'.$row, $this->signFormat($value['sign_dokter_2']))
                ->setCellValue('N'.$row, $this->signFormat($value['sign_dokter_3']))
                ->setCellValue('O'.$row, $this->signFormat($value['sign_dokter_4']))
                ->setCellValue('P'.$row, $this->signFormat($value['sign_dokter_5']))
                ->setCellValue('Q'.$row, $this->signFormat($value['dok_registrasi_lengkap']))
                ->setCellValue('R'.$row, $this->signFormat($value['estimasi_po']))
                ->setCellValue('S'.$row, $this->signFormat($value['po_release']));
			$i++;
            $row++;
        }

        // Attendance
        $date_interval = date_interval($params['start_period'], $params['end_period']);
        $objPHPExcel->createSheet(0);
        $sheetAttendance = $objPHPExcel->setActiveSheetIndex(0);
		$sheetAttendance->setTitle('Attendance');
        $sheetAttendance->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Parma')
            ->setCellValue('C1', 'Parma Name')
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

        $attendanceParma = $this->report_productivity->get_attendance_parma($params);
        $salesman = [];
        foreach ($attendanceParma as $ap) {
            if (!in_array($ap['salesmanid'], array_column($salesman, 'salesmanid'))) {
                $filtered = array_filter($attendanceParma, function($row) use ($ap) {
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
        $urlimage = URL_IMAGE;

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
        }
        
        // target call daily
        $date_interval = date_interval($params['start_period'], $params['end_period']);
        $objPHPExcel->createSheet(7);
        $sheetcall = $objPHPExcel->setActiveSheetIndex(7);
		$sheetcall->setTitle('Call Daily');
        $sheetcall->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Parma')
            ->setCellValue('C1', 'Parma Name')
            ->setCellValue('D1', 'Area');
        $sheetcall->mergeCells('A1:A2');
        $sheetcall->mergeCells('B1:B2');
        $sheetcall->mergeCells('C1:C2');
        $sheetcall->mergeCells('D1:D2');
        //$sheetAttendance->getStyle('A1')->getAlignment()->setHorizontal('center');
        //$sheetAttendance->getStyle('B1')->getAlignment()->setHorizontal('center');
        //$sheetAttendance->getStyle('C1')->getAlignment()->setHorizontal('center');

        $colIndex = 5;
        foreach ($date_interval as $dateObj) {
            $dateStr = format_date_id($dateObj, true, false);
            $colBase = number_to_alphabet($colIndex);

            $sheetcall->mergeCells("{$colBase}1:" . number_to_alphabet($colIndex+3) . '1');
            $sheetcall->setCellValue("{$colBase}1", $dateStr);

            $sheetcall->setCellValue(number_to_alphabet($colIndex)   . '2', 'Target Call');
            $sheetcall->setCellValue(number_to_alphabet($colIndex+1) . '2', 'Call');
            $sheetcall->setCellValue(number_to_alphabet($colIndex+2) . '2', 'Extra Call');
            $sheetcall->setCellValue(number_to_alphabet($colIndex+3) . '2', 'Actual Call');
            $colIndex += 4;
        }

        $callParma = $this->report_productivity->get_daily_target_call($params);
        $parmalist = [];
        foreach ($callParma as $ap) {
            $parmaKey = $ap['parma'];

            if (!isset($parmalist[$parmaKey])) {
                $parmalist[$parmaKey] = [
                    'parma' => $ap['parma'],
                    'parma_name' => $ap['nama_parma'],
                    'area' => $ap['nama_area'],
                    'daily' => [] // simpan per tanggal
                ];
            }

            $parmalist[$parmaKey]['daily'][$ap['periode']] = [
                'target_call' => $ap['target_call'],
                'Call' => $ap['Call'],
                'ExtraCall' => $ap['ExtraCall'],
                'actual_call' => $ap['actual_call']
            ];
        }

        $i = 1;
        $row = 3;
        foreach ($parmalist as $s) {
            $sheetcall->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $s['parma'])
                ->setCellValue('C'.$row, $s['parma_name'])
                ->setCellValue('D'.$row, $s['area']);

            $colIndex = 5;
            foreach ($date_interval as $date_int) {
                $dateStr = $date_int; // pastikan format sama dengan kunci di $s['daily']
                $daily = $s['daily'][$dateStr] ?? ['target_call' => 0, 'Call' => 0, 'ExtraCall' => 0, 'actual_call' => 0];

                $sheetcall->setCellValue(number_to_alphabet($colIndex)   . $row, $daily['target_call']);
                $sheetcall->setCellValue(number_to_alphabet($colIndex+1) . $row, $daily['Call']);
                $sheetcall->setCellValue(number_to_alphabet($colIndex+2) . $row, $daily['ExtraCall']);
                $sheetcall->setCellValue(number_to_alphabet($colIndex+3) . $row, $daily['actual_call']);

                $colIndex += 4;
            }

            $i++;
            $row++;
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

    function signFormat($value) {
        if ($value && $value == 1) return 'Sudah';
        return '';
    }
}
