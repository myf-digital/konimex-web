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
		$year = $this->input->post("year");
		$month = $this->input->post("month");
        $position = $this->input->post("position");

		$idjabatan = $this->input->post("idjabatan");
		$restrict_level = $this->input->post("restrict_level");
		$usersession = $this->input->post("usersession");

        $regionalid = $this->input->post("regionalid");
        $areaid = $this->input->post("areaid");

		if ($year.'-'.$month==date("Y-m")){
			$periodedate= date("Y-m-d");
			//$periodedate= date("Y-m-d",strtotime($periode));
		}else{
			$periode = $year.'-'.$month.'-01';
			$periodedate=date("Y-m-t",strtotime($periode));
		}

        $params = [
        	'year' => $year,
        	'month' => $month,
        	'periode' => $periodedate,
			'tipe_sales' => $position,
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
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">User PARMA</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 300px;">Nama PARMA</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Position</th>';
		$html .= '<th colspan="13" style="vertical-align : middle;text-align:center;width: 2600px;">Kuantitatif</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="vertical-align : middle;text-align:center;">HK</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Absensi</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%Kehadiran</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Keterangan</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Target Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Call on PJP</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Effective Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Extra Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Actual Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%PJP Compliance</th>';
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
		foreach ($data as $value) 
		{
			$html .= '<tr>';
			$html .= '<td style="width: 50px;">'.$i.'</td>';
			$html .= '<td style="width:150px;">'.$value['nama_area'].'</td>';
			$html .= '<td style="width:150px;">'.$value['salesmanid'].'</td>';
			$html .= '<td style="width:300px;">'.$value['nama_salesman'].'</td>';
			$html .= '<td style="width:150px;">'.$value['tipe_sales'].'</td>';

			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['PARMA Aktif'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['PARMA Hadir'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['PARMA Hadir']/$value['PARMA Aktif']*100, 2, '.', ',').' %</td>';
			$html .= '<td style="text-align:right;width: 200px">Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').') </td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['pjp'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['effective_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['extra_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call']+$value['extra_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call']/$value['pjp']*100, 2, '.', ',').' %</td>';
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
		ini_set('memory_limit', '512M');
        $year = $this->uri->segment('3');
        $month = $this->uri->segment('4');
        $position = $this->uri->segment('5');
        $regionalid = $this->uri->segment('6');
        $areaid = $this->uri->segment('7');

        $usersession = $this->uri->segment('8');
        $restrict_level = $this->uri->segment('9');
        $idjabatan = $this->uri->segment('10');

		if ($year.'-'.$month==date("Y-m")){
			$periodedate= date("Y-m-d");
		}else{
			$periode = $year.'-'.$month.'-01';
			$periodedate=date("Y-m-t",strtotime($periode));
		}

        $params = [
        	'year' => $year,
        	'month' => $month,
        	'periode' => $periodedate,
        	'tipe_sales' => $position,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        ];

        $data = $this->report_productivity->getProductivity($params);

        $filename = "Report_Productivity_".$year."-".$month;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Area', 'User PARMA', 'Nama PARMA', 'Position', 'HK', 'Absensi', '%Kehadiran', 'Keterangan', 'Target Call', 'Call on PJP', 'Extra Call', 'Actual Call', '%PJP Compliance', 'Keterangan', 'Detailing'];

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
				number_format($value['PARMA Aktif'], 0, '.', ','),
				number_format($value['PARMA Hadir'], 0, '.', ','),
				number_format($value['PARMA Hadir']/$value['PARMA Aktif']*100, 2, '.', ','),
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
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '0');
		
        $year = $this->uri->segment('3');
        $month = $this->uri->segment('4');
        $position = $this->uri->segment('5');
        $regionalid = $this->uri->segment('6');
        $areaid = $this->uri->segment('7');

        $usersession = $this->uri->segment('8');
        $restrict_level = $this->uri->segment('9');
        $idjabatan = $this->uri->segment('10');

		if ($year.'-'.$month==date("Y-m")){
			$periodedate= date("Y-m-d");
		}else{
			$periode = $year.'-'.$month.'-01';
			$periodedate=date("Y-m-t",strtotime($periode));
		}

        $params = [
        	'year' => $year,
        	'month' => $month,
        	'periode' => $periodedate,
        	'tipe_sales' => $position,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid
        ];
        
        $filename = "Report-Productivity-".$year."-".$month.".xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
		$styleArray = array(
								'borders' => array(
									'allborders' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
									)
								)
							);

		$objWorkSheet = $objPHPExcel->createSheet(0);
		$objPHPExcel->setActiveSheetIndex(0)->setTitle('Productivity');
		//$objPhpExcel->setActiveSheetIndex(0)->setShowGridlines(false);
        $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No')
					->setCellValue('B1', 'City/Area')
                    ->setCellValue('C1', 'Code PARMA')
                    ->setCellValue('D1', 'PARMA Name')
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
                    ->setCellValue('O1', '%PJP Compliance')
                    ->setCellValue('P1', 'Outlet Order')
                    ->setCellValue('Q1', 'Total Order')
                    ->setCellValue('R1', 'Keterangan')
                    ->setCellValue('S1', 'Detailing')
					;
		
        $data = $this->report_productivity->getProductivity($params);
        $i = 1;
        $row = 2;
        foreach ($data as $value) {
			$objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $value['city'])
                        ->setCellValue('C'.$row, $value['salesmanid'])
                        ->setCellValue('D'.$row, $value['nama_salesman'])
                        ->setCellValue('E'.$row, $value['tipe_sales'])
                        ->setCellValue('F'.$row, $value['PARMA Aktif'])
                        ->setCellValue('G'.$row, $value['PARMA Hadir'])
                        ->setCellValue('H'.$row, '=G'.$row.'/F'.$row)
                        ->setCellValue('I'.$row, 'Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').')')
                        ->setCellValue('J'.$row, $value['pjp'])
                        ->setCellValue('K'.$row, $value['effective_call'])
                        ->setCellValue('L'.$row, $value['call'])
                        ->setCellValue('M'.$row, $value['extra_call'])
                        ->setCellValue('N'.$row, '=K'.$row.'+L'.$row.'+M'.$row)
                        ->setCellValue('O'.$row, '=(K'.$row.'+L'.$row.')/J'.$row)
                        ->setCellValue('P'.$row, $value['jumlah_customer'])
                        ->setCellValue('Q'.$row, $value['total_penjualan'])
                        ->setCellValue('R'.$row, $value['rrk_keterangan'])
                        ->setCellValue('S'.$row, $value['rrk_detailing'])
						;
			
			$objPHPExcel->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));
			$objPHPExcel->getActiveSheet()->getStyle('P'.$row)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));
			$objPHPExcel->getActiveSheet()->getStyle('R'.$row)->getNumberFormat()->setFormatCode('"Rp. "#,##0.00');
			$i++;
            $row++;
        }

		
		$objWorkSheet = $objPHPExcel->createSheet(1);
		$objPHPExcel->setActiveSheetIndex(1)->setTitle('Visit PARMA');
        $objPHPExcel->setActiveSheetIndex(1)
					->setCellValue('A1', 'No')
					->setCellValue('B1', 'Period')
                    ->setCellValue('C1', 'PARMA')
                    ->setCellValue('D1', 'PARMA Name')
                    ->setCellValue('E1', 'OutletID')
                    ->setCellValue('F1', 'Latest JJid')
                    ->setCellValue('G1', 'Outlet Name')
                    ->setCellValue('H1', 'Channel')
                    ->setCellValue('I1', 'Account')
                    ->setCellValue('J1', 'City')
                    ->setCellValue('K1', 'CheckIn')
                    ->setCellValue('L1', 'CheckOut')
                    ->setCellValue('M1', 'Time Visit')
                    ->setCellValue('N1', 'Reason')
                    ->setCellValue('O1', 'Description')
                    ->setCellValue('P1', 'Flag')
					;

        $datavisit = $this->report_productivity->getVisit_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($datavisit as $valuevisit) {
            $objPHPExcel->setActiveSheetIndex(1)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $valuevisit['periode'])
                        ->setCellValue('C'.$row, $valuevisit['salesmanid'])
                        ->setCellValue('D'.$row, $valuevisit['nama_salesman'])
                        ->setCellValue('E'.$row, $valuevisit['customerid'])
                        ->setCellValue('F'.$row, $valuevisit['latest_jjid'])
                        ->setCellValue('G'.$row, $valuevisit['nama_customer'])
                        ->setCellValue('H'.$row, $valuevisit['channel'])
                        ->setCellValue('I'.$row, $valuevisit['account'])
                        ->setCellValue('J'.$row, $valuevisit['nama_area'])
                        ->setCellValue('K'.$row, $valuevisit['check_in'])
                        ->setCellValue('L'.$row, $valuevisit['check_out'])
                        ->setCellValue('M'.$row, $valuevisit['lama_kunjungan'])
                        ->setCellValue('N'.$row, $valuevisit['alasan'])
                        ->setCellValue('O'.$row, $valuevisit['keterangan'])
                        ->setCellValue('P'.$row, $valuevisit['flag'])
						;
			$i++;
            $row++;
        }
		
		$objWorkSheet = $objPHPExcel->createSheet(2);
        $objPHPExcel->setActiveSheetIndex(2)->setTitle('Order PARMA');
		$objPHPExcel->setActiveSheetIndex(2)
					->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Tanggal')
                    ->setCellValue('C1', 'User PARMA')
                    ->setCellValue('D1', 'OutletID')
                    ->setCellValue('E1', 'Latest JJid')
                    ->setCellValue('F1', 'ID Dist   ')
                    ->setCellValue('G1', 'Outlet Name')
                    ->setCellValue('H1', 'Account')
                    ->setCellValue('I1', 'No SP')
                    ->setCellValue('J1', 'No Sales Order')
                    ->setCellValue('K1', 'ProductID')
                    ->setCellValue('L1', 'Product Name')
                    ->setCellValue('M1', 'Qty (PARMA Apps)')
                    ->setCellValue('N1', 'Price')
                    ->setCellValue('O1', 'Total GTS (PARMA Apps)')
                    ->setCellValue('P1', 'Status')
                    ->setCellValue('Q1', 'JJID + Product Name')
                    ->setCellValue('R1', 'Qty Actual (Tableau Kenvue)')
                    ->setCellValue('S1', 'Total GTS (Tableau Kenvue)')
                    ->setCellValue('T1', 'Gap Qty')
                    ->setCellValue('U1', 'Gap Total GTS')
                    ;
        $dataorder = $this->report_productivity->getOrder_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($dataorder as $valueorder) {
            $objPHPExcel->setActiveSheetIndex(2)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $valueorder['period'])
                        ->setCellValue('C'.$row, $valueorder['nama_salesman']." (".$valueorder['salesmanid'].")")
                        ->setCellValue('D'.$row, $valueorder['customerid'])
                        ->setCellValue('E'.$row, $valueorder['latest_jjid'])
                        ->setCellValue('F'.$row, $valueorder['cust_id_map'])
                        ->setCellValue('G'.$row, $valueorder['nama_customer'])
                        ->setCellValue('H'.$row, $valueorder['account'])
                        ->setCellValue('I'.$row, $valueorder['no_po'])
                        ->setCellValue('J'.$row, $valueorder['no_sales'])
                        ->setCellValue('K'.$row, $valueorder['productid'])
                        ->setCellValue('L'.$row, $valueorder['nama_invoice'])
                        ->setCellValue('M'.$row, $valueorder['qty_jual_in_pcs'])
                        ->setCellValue('N'.$row, $valueorder['h_jual'])
                        ->setCellValue('O'.$row, '=M'.$row.'*N'.$row)
                        ->setCellValue('P'.$row, $valueorder['status'])
                        ->setCellValue('Q'.$row, '=E'.$row.'&L'.$row)
                        ->setCellValue('R'.$row, '')
                        ->setCellValue('S'.$row, '')
                        ->setCellValue('T'.$row, '=M'.$row.'-R'.$row)
                        ->setCellValue('U'.$row, '=O'.$row.'-S'.$row)
						;
			$i++;
            $row++;
        }        
		/*$objWorkSheet = $objPHPExcel->createSheet(2);
        $objPHPExcel->setActiveSheetIndex(2)->setTitle('Order PARMA');
		$objPHPExcel->setActiveSheetIndex(2)
					->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Period')
                    ->setCellValue('C1', 'User PARMA')
                    ->setCellValue('D1', 'PARMA Name')
                    ->setCellValue('E1', 'OutletID')
                    ->setCellValue('F1', 'Latest JJid')
                    ->setCellValue('G1', 'ID Dist')
                    ->setCellValue('H1', 'Outlet Name')
                    ->setCellValue('I1', 'Account')
                    ->setCellValue('J1', 'No SP')
                    ->setCellValue('K1', 'No Sales Order')
                    ->setCellValue('L1', 'ProductID')
                    ->setCellValue('M1', 'Product Name')
                    ->setCellValue('N1', 'Qty')
                    ->setCellValue('O1', 'Price')
                    ->setCellValue('P1', 'Total')
                    ->setCellValue('Q1', 'Status')
                    ;
        $dataorder = $this->report_productivity->getOrder_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($dataorder as $valueorder) {
            $objPHPExcel->setActiveSheetIndex(2)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $valueorder['period'])
                        ->setCellValue('C'.$row, $valueorder['salesmanid'])
                        ->setCellValue('D'.$row, $valueorder['nama_salesman'])
                        ->setCellValue('E'.$row, $valueorder['customerid'])
                        ->setCellValue('F'.$row, $valueorder['latest_jjid'])
                        ->setCellValue('G'.$row, $valueorder['cust_id_map'])
                        ->setCellValue('H'.$row, $valueorder['nama_customer'])
                        ->setCellValue('I'.$row, $valueorder['account'])
                        ->setCellValue('J'.$row, $valueorder['no_po'])
                        ->setCellValue('K'.$row, $valueorder['no_sales'])
                        ->setCellValue('L'.$row, $valueorder['productid'])
                        ->setCellValue('M'.$row, $valueorder['nama_invoice'])
                        ->setCellValue('N'.$row, $valueorder['qty_jual_in_pcs'])
                        ->setCellValue('O'.$row, $valueorder['h_jual'])
                        ->setCellValue('P'.$row, '=N'.$row.'*O'.$row)
                        ->setCellValue('Q'.$row, $valueorder['status'])
						;
			$i++;
            $row++;
        }*/

		$objWorkSheet = $objPHPExcel->createSheet(3);
        $objPHPExcel->setActiveSheetIndex(3)->setTitle('CRC');
		$objPHPExcel->setActiveSheetIndex(3)
					->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Period')
                    ->setCellValue('C1', 'User PARMA')
                    ->setCellValue('D1', 'PARMA Name')
                    ->setCellValue('E1', 'OutletID')
                    ->setCellValue('F1', 'Latest JJid')
                    ->setCellValue('G1', 'Outlet Name')
                    ->setCellValue('H1', 'Account')
                    ->setCellValue('I1', 'ProductID')
                    ->setCellValue('J1', 'Product Name')
                    ->setCellValue('K1', 'Brand')
                    ->setCellValue('L1', 'Qty Stock')
                    ;
        $datacrc = $this->report_productivity->getcrc_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($datacrc as $valuecrc) {
            $objPHPExcel->setActiveSheetIndex(3)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $valuecrc['periode'])
                        ->setCellValue('C'.$row, $valuecrc['salesmanid'])
                        ->setCellValue('D'.$row, $valuecrc['nama_salesman'])
                        ->setCellValue('E'.$row, $valuecrc['customerid'])
                        ->setCellValue('F'.$row, $valuecrc['latest_jjid'])
                        ->setCellValue('G'.$row, $valuecrc['nama_customer'])
                        ->setCellValue('H'.$row, $valuecrc['account'])
                        ->setCellValue('I'.$row, $valuecrc['productid'])
                        ->setCellValue('J'.$row, $valuecrc['nama_invoice'])
                        ->setCellValue('K'.$row, $valuecrc['nama_brand'])
                        ->setCellValue('L'.$row, $valuecrc['qty_akhir'])
						;
			$i++;
            $row++;
        }

        $objWorkSheet = $objPHPExcel->createSheet(4);
		$objPHPExcel->setActiveSheetIndex(4)->setTitle('Detailing');
        $objPHPExcel->setActiveSheetIndex(4)
					->setCellValue('A1', 'No')
					->setCellValue('B1', 'Period')
                    ->setCellValue('C1', 'PARMA')
                    ->setCellValue('D1', 'PARMA Name')
                    ->setCellValue('E1', 'OutletID')
                    ->setCellValue('F1', 'Latest JJid')
                    ->setCellValue('G1', 'Outlet Name')
                    ->setCellValue('H1', 'Channel')
                    ->setCellValue('I1', 'Account')
                    ->setCellValue('J1', 'City')
                    ->setCellValue('K1', 'PIC Name')
                    ->setCellValue('L1', 'Brand Detailing')
                    ->setCellValue('M1', 'Time Detailing')
                    ->setCellValue('N1', 'Reason')
                    ->setCellValue('O1', 'Description')
					;

        $datavisit = $this->report_productivity->get_detailing_parma($params);
        $i = 1;
        $row = 2;
        foreach ($datavisit as $valuevisit) {
            $objPHPExcel->setActiveSheetIndex(4)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $valuevisit['periode'])
                        ->setCellValue('C'.$row, $valuevisit['salesmanid'])
                        ->setCellValue('D'.$row, $valuevisit['nama_salesman'])
                        ->setCellValue('E'.$row, $valuevisit['customerid'])
                        ->setCellValue('F'.$row, $valuevisit['latest_jjid'])
                        ->setCellValue('G'.$row, $valuevisit['nama_customer'])
                        ->setCellValue('H'.$row, $valuevisit['channel'])
                        ->setCellValue('I'.$row, $valuevisit['account'])
                        ->setCellValue('J'.$row, $valuevisit['nama_area'])
                        ->setCellValue('K'.$row, $valuevisit['professional_name'])
                        ->setCellValue('L'.$row, $valuevisit['brands'])
                        ->setCellValue('M'.$row, $valuevisit['start_detailing'])
                        ->setCellValue('N'.$row, $valuevisit['reason'])
                        ->setCellValue('O'.$row, $valuevisit['keterangan'])
						;
			$i++;
            $row++;
        }

        $objPHPExcel->createSheet(5);
		$objPHPExcel->setActiveSheetIndex(5)->setTitle('Progress Listing');
        $objPHPExcel->setActiveSheetIndex(5)
					->setCellValue('A1', 'No')
					->setCellValue('B1', 'Periode')
					->setCellValue('C1', 'Salesman ID')
					->setCellValue('D1', 'Salesman Name')
					->setCellValue('E1', 'Customer ID')
					->setCellValue('F1', 'Customer Name')
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
					->setCellValue('S1', 'PO Release')
					;

        $progressListing = $this->report_productivity->get_progress_listing($params);
        $i = 1;
        $row = 2;
        foreach ($progressListing as $value) {
            $objPHPExcel->setActiveSheetIndex(5)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $value['periode'])
                        ->setCellValue('C'.$row, $value['salesmanid'])
                        ->setCellValue('D'.$row, $value['nama_salesman'])
                        ->setCellValue('E'.$row, $value['customerid'])
                        ->setCellValue('F'.$row, $value['nama_customer'])
                        ->setCellValue('G'.$row, $value['brandid'])
                        ->setCellValue('H'.$row, $value['brand'])
                        ->setCellValue('I'.$row, $value['progress'])
                        ->setCellValue('J'.$row, $this->signFormat($value['ambil_dok_registrasi']))
                        ->setCellValue('K'.$row, $this->signFormat($value['melengkapi_dok_registrasi']))
                        ->setCellValue('L'.$row, $this->signFormat($value['sign_dokter_1']))
                        ->setCellValue('M'.$row, $this->signFormat($value['sign_dokter_2']))
                        ->setCellValue('N'.$row, $this->signFormat($value['sign_dokter_3']))
                        ->setCellValue('O'.$row, $this->signFormat($value['sign_dokter_4']))
                        ->setCellValue('P'.$row, $this->signFormat($value['sign_dokter_5']))
                        ->setCellValue('Q'.$row, $this->signFormat($value['dok_registrasi_lengkap']))
                        ->setCellValue('R'.$row, $this->signFormat($value['estimasi_po']))
                        ->setCellValue('S'.$row, $this->signFormat($value['po_release']))
						;
			$i++;
            $row++;
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

    function signFormat($value) {
        if ($value && $value == 1) return 'Sudah';
        return '';
    }
}
