<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;

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

    public function load_subarea()
    {
        $data = param_input();
        responseJSON($this->report_productivity->get_subarea($data));
    }

    function open_detail() {
		$start = $this->input->post("start_period");
		$end = $this->input->post("end_period");

		$idjabatan = $this->input->post("idjabatan");
		$restrict_level = $this->input->post("restrict_level");
		$usersession = $this->input->post("usersession");

        $regionalid = $this->input->post("regionalid");
        $areaid = $this->input->post("areaid");
        $subareaid = $this->input->post("subareaid");

        $params = [
        	'start_period' => $start,
        	'end_period' => $end,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        	'subareaid' => $subareaid,
        ];

        $data = $this->report_productivity->getProductivity($params);
		
		$html ='<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 50px">No</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Area</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">ID MEDREP</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 300px;">Nama MEDREP</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Position</th>';
		$html .= '<th colspan="13" style="vertical-align : middle;text-align:center;width: 2600px;">Kuantitatif</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="vertical-align : middle;text-align:center;">HK</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Absensi</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%Kehadiran</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Keterangan Absensi</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Call DUB</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Target DUB</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Efektif Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Call Visit</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Target Visit</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Rata-Rata Visit</th>';
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

			$html .= '<td style="text-align:center;width: 200px">'.number_format($value['MEDREP Aktif'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:center;width: 200px">'.number_format($value['MEDREP Hadir'], 0, '.', ',').' </td>';
			$kehadiran_pct = $value['MEDREP Aktif'] > 0 ? ($value['MEDREP Hadir'] / $value['MEDREP Aktif'] * 100) : 0;
			$html .= '<td style="text-align:center;width: 200px">'.number_format($kehadiran_pct, 2, '.', ',').' %</td>';
			$html .= '<td style="text-align:center;width: 200px">Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').') </td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:center;width: 200px;">'.number_format($value['call_dub'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:center;width: 200px;">'.number_format($value['target_dub'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:center;width: 200px;">'.number_format($value['efektif_dub'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:center;width: 200px;">'.number_format($value['call_visit'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:center;width: 200px;">'.number_format($value['target_visit'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:center;width: 200px;">'.number_format($value['rata_rata_visit'], 2, '.', ',').'</td>';
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
        $subareaid = $this->uri->segment('7');

        $usersession = $this->uri->segment('8');
        $restrict_level = $this->uri->segment('9');
        $idjabatan = $this->uri->segment('10');

        $params = [
        	'start_period' => $start,
        	'end_period' => $end,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        	'subareaid' => $subareaid,
        ];

        $data = $this->report_productivity->getProductivity($params);

        $filename = "Report_Productivity_".$start."_".$end;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Area', 'ID MEDREP', 'Nama MEDREP', 'Position', 'HK', 'Absensi', '%Kehadiran', 'Keterangan Absensi', 'Call DUB', 'Target DUB', 'Efektif Call', 'Call Visi', 'Target Visit', 'Rata-Rata Visit', 'Keterangan', 'Detailing'];

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('F1', 'Kuantitatif')->mergeCells('F1:Q1');

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
				number_format($value['MEDREP Aktif'], 0, '.', ','),
				number_format($value['MEDREP Hadir'], 0, '.', ','),
				$value['MEDREP Aktif'] > 0 ? number_format($value['MEDREP Hadir']/$value['MEDREP Aktif']*100, 2, '.', ',') : '0.00',
				'Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').')',
				number_format($value['call_dub'], 0, '.', ','),
				number_format($value['target_dub'], 0, '.', ','),
				number_format($value['efektif_dub'], 0, '.', ','),
				number_format($value['call_visit'], 0, '.', ','),
				number_format($value['target_visit'], 0, '.', ','),
				number_format($value['rata_rata_visit'], 2, '.', ','),
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
        ini_set('max_execution_time', '300'); // atau set ke 300 (5 menit)
        ini_set('memory_limit', '1G'); // tambahkan memori jika datanya besar		
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $regionalid = $this->uri->segment('5');
        $areaid = $this->uri->segment('6');
        $subareaid = $this->uri->segment('7');

        $usersession = $this->uri->segment('8');
        $restrict_level = $this->uri->segment('9');
        $idjabatan = $this->uri->segment('10');

        $params = [
        	'start_period' => $start,
        	'end_period' => $end,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        	'subareaid' => $subareaid,
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
            ->setCellValue('B1', 'Area')
            ->setCellValue('C1', 'MEDREP')
            ->setCellValue('D1', 'MEDREP Name')
            ->setCellValue('E1', 'Position')
            ->setCellValue('F1', 'HK')
            ->setCellValue('G1', 'Absensi')
            ->setCellValue('H1', '%Kehadiran')
            ->setCellValue('I1', 'Keterangan Absensi')
            ->setCellValue('J1', 'Call DUB')
            ->setCellValue('K1', 'Target DUB')
            ->setCellValue('L1', 'Efektif Call')
            ->setCellValue('M1', 'Call Visit')
            ->setCellValue('N1', 'Target Visit')
            ->setCellValue('O1', 'Rata-Rata Visit')
            ->setCellValue('P1', 'Outlet Order')
            ->setCellValue('Q1', 'Total Order')
            ->setCellValue('R1', 'Keterangan')
            ->setCellValue('S1', 'Detailing');
		
        $data = $this->report_productivity->getProductivity($params);
        $i = 1;
        $row = 2;
        foreach ($data as $value) {
			$sheetProductivity->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $value['nama_area'] ?? '')
                ->setCellValue('C'.$row, $value['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $value['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $value['tipe_sales'] ?? '')
                ->setCellValue('F'.$row, $value['MEDREP Aktif'] ?? '')
                ->setCellValue('G'.$row, $value['MEDREP Hadir'] ?? '')
                ->setCellValue('H'.$row, '=IF(F'.$row.'>0, G'.$row.'/F'.$row.', 0)')
                ->setCellValue('I'.$row, 'Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').')')
                ->setCellValue('J'.$row, $value['call_dub'] ?? '')
                ->setCellValue('K'.$row, $value['target_dub'] ?? '')
                ->setCellValue('L'.$row, $value['efektif_dub'] ?? '')
                ->setCellValue('M'.$row, $value['call_visit'] ?? '')
                ->setCellValue('N'.$row, $value['target_visit'] ?? '')
                ->setCellValue('O'.$row, $value['rata_rata_visit'] ?? '')
                ->setCellValue('P'.$row, $value['jumlah_customer'] ?? '')
                ->setCellValue('Q'.$row, $value['total_penjualan'] ?? '')
                ->setCellValue('R'.$row, $value['rrk_keterangan'] ?? '')
                ->setCellValue('S'.$row, $value['rrk_detailing'] ?? '');
			
			$objPHPExcel->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->applyFromArray(array('code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE));
			$objPHPExcel->getActiveSheet()->getStyle('Q'.$row)->getNumberFormat()->setFormatCode('"Rp. "#,##0.00');
			$i++;
            $row++;
        }
		
        // Visit MEDREP
        $objPHPExcel->createSheet(1);
        $sheetVisit = $objPHPExcel->setActiveSheetIndex(1);
        $sheetVisit->setTitle('Visit MEDREP');
        $sheetVisit->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Period')
            ->setCellValue('C1', 'MEDREP')
            ->setCellValue('D1', 'MEDREP Name')
            ->setCellValue('E1', 'MEDREP ID Outlet')
            ->setCellValue('F1', 'MEDREP Nama Outlet')
            ->setCellValue('G1', 'Channel')
            ->setCellValue('H1', 'Regional')
            ->setCellValue('I1', 'Area')
            ->setCellValue('J1', 'CheckIn')
            ->setCellValue('K1', 'CheckOut')
            ->setCellValue('L1', 'Time Visit')
            ->setCellValue('M1', 'Reason')
            ->setCellValue('N1', 'Description')
            ->setCellValue('O1', 'Flag');

        $datavisit = $this->report_productivity->getVisit_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($datavisit as $valuevisit) {
            $sheetVisit->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $valuevisit['periode'] ?? '')
                ->setCellValue('C'.$row, $valuevisit['salesmanid'] ?? '')
                ->setCellValue('D'.$row, $valuevisit['nama_salesman'] ?? '')
                ->setCellValue('E'.$row, $valuevisit['customerid'] ?? '')
                ->setCellValue('F'.$row, $valuevisit['nama_customer'] ?? '')
                ->setCellValue('G'.$row, $valuevisit['typeid'] ?? '')
                ->setCellValue('H'.$row, $valuevisit['nama_regional'] ?? '')
                ->setCellValue('I'.$row, $valuevisit['nama_area'] ?? '')
                ->setCellValue('J'.$row, $valuevisit['check_in'] ?? '')
                ->setCellValue('K'.$row, $valuevisit['check_out'] ?? '')
                ->setCellValue('L'.$row, $valuevisit['lama_kunjungan'] ?? '')
                ->setCellValue('M'.$row, $valuevisit['alasan'] ?? '')
                ->setCellValue('N'.$row, $valuevisit['keterangan'] ?? '')
                ->setCellValue('O'.$row, $valuevisit['flag'] ?? '');
			$i++;
            $row++;
        }
		
        // Order MEDREP
        $objPHPExcel->createSheet(2);
        $sheetOrder = $objPHPExcel->setActiveSheetIndex(2);
        $sheetOrder->setTitle('Order MEDREP');
		$sheetOrder->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'User MEDREP')
            ->setCellValue('D1', 'MEDREP ID Outlet')
            ->setCellValue('E1', 'ID Outlet Distributor')
            ->setCellValue('F1', 'MEDREP Nama Outlet')
            ->setCellValue('G1', 'Channel')
            ->setCellValue('H1', 'No SP')
            ->setCellValue('I1', 'No Sales Order')
            ->setCellValue('J1', 'ProductID')
            ->setCellValue('K1', 'Product Name')
            ->setCellValue('L1', 'Qty (MEDREP Apps)')
            ->setCellValue('M1', 'Price')
            ->setCellValue('N1', 'Total GTS (MEDREP Apps)')
            ->setCellValue('O1', 'Status')
            ->setCellValue('P1', 'JJID + Product Name')
            ->setCellValue('Q1', 'Qty Actual (Tableau Himalaya)')
            ->setCellValue('R1', 'Total GTS (Tableau Himalaya)')
            ->setCellValue('S1', 'Gap Qty')
            ->setCellValue('T1', 'Gap Total GTS');

        $dataorder = $this->report_productivity->getOrder_salesman($params);
        $i = 1;
        $row = 2;
        foreach ($dataorder as $valueorder) {
            $sheetOrder->setCellValue('A'.$row, $i)
                ->setCellValue('B'.$row, $valueorder['period'] ?? '')
                ->setCellValue('C'.$row, $valueorder['nama_salesman']." (".$valueorder['salesmanid'].")")
                ->setCellValue('D'.$row, $valueorder['customerid'] ?? '')
                ->setCellValue('E'.$row, $valueorder['cust_id_map'] ?? '')
                ->setCellValue('F'.$row, $valueorder['nama_customer'] ?? '')
                ->setCellValue('G'.$row, $valueorder['typeid'] ?? '')
                ->setCellValue('H'.$row, $valueorder['no_po'] ?? '')
                ->setCellValue('I'.$row, $valueorder['no_sales'] ?? '')
                ->setCellValue('J'.$row, $valueorder['productid'] ?? '')
                ->setCellValue('K'.$row, $valueorder['nama_invoice'] ?? '')
                ->setCellValue('L'.$row, $valueorder['qty_jual_in_pcs'] ?? '')
                ->setCellValue('M'.$row, $valueorder['h_jual'] ?? '')
                ->setCellValue('N'.$row, '=L'.$row.'*M'.$row)
                ->setCellValue('O'.$row, $valueorder['status'] ?? '')
                ->setCellValue('P'.$row, ($valueorder['nama_invoice'] ?? ''))
                ->setCellValue('Q'.$row, '')
                ->setCellValue('R'.$row, '')
                ->setCellValue('S'.$row, '=L'.$row.'-Q'.$row)
                ->setCellValue('T'.$row, '=N'.$row.'-R'.$row);
			$i++;
            $row++;
        }

        // Detailing
        $objPHPExcel->createSheet(3);
        $sheetDetailing = $objPHPExcel->setActiveSheetIndex(3);
		$sheetDetailing->setTitle('Detailing');
        $sheetDetailing->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Period')
            ->setCellValue('C1', 'MEDREP')
            ->setCellValue('D1', 'MEDREP Name')
            ->setCellValue('E1', 'MEDREP ID Outlet')
            ->setCellValue('F1', 'MEDREP Nama Outlet')
            ->setCellValue('G1', 'Channel')
            ->setCellValue('H1', 'Regional')
            ->setCellValue('I1', 'Area')
            ->setCellValue('J1', 'Specialist')
            ->setCellValue('K1', 'Spesialisasi')
            ->setCellValue('L1', 'Product Detailing')
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
                ->setCellValue('F'.$row, $valuevisit['nama_customer'] ?? '')
                ->setCellValue('G'.$row, $valuevisit['typeid'] ?? '')
                ->setCellValue('H'.$row, $valuevisit['nama_regional'] ?? '')
                ->setCellValue('I'.$row, $valuevisit['nama_area'] ?? '')
                ->setCellValue('J'.$row, $valuevisit['professional_name'] ?? '')
                ->setCellValue('K'.$row, $valuevisit['spesialisasi'] ?? '')
                ->setCellValue('L'.$row, $valuevisit['products'] ?? '')
                ->setCellValue('M'.$row, $valuevisit['reason'] ?? '')
                ->setCellValue('N'.$row, $valuevisit['keterangan'] ?? '');
			$i++;
            $row++;
        }
        
        // Progress Listing
        $objPHPExcel->createSheet(4);
        $sheetProgressListing = $objPHPExcel->setActiveSheetIndex(4);
		$sheetProgressListing->setTitle('Progress Listing');
        $sheetProgressListing->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Periode')
            ->setCellValue('C1', 'MEDREP')
            ->setCellValue('D1', 'MEDREP Name')
            ->setCellValue('E1', 'MEDREP ID Outlet')
            ->setCellValue('F1', 'MEDREP Nama Outlet')
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
            ->setCellValue('Q1', 'Dokumen Registrasi Lengkap');

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
                ->setCellValue('Q'.$row, $this->signFormat($value['dok_registrasi_lengkap']));
			$i++;
            $row++;
        }

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
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+2) . '2', 'Image Check-In');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+3) . '2', 'Out');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+4) . '2', 'Image Check-Out');
            $sheetAttendance->setCellValue(number_to_alphabet($colIndex+5) . '2', 'Duration');

            $colIndex += 6;
        }

        $attendanceMedrep = $this->report_productivity->get_attendance_parma($params);
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
                    'nama_area' => $ap['city'] ?? $ap['nama_area'],
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
                
				if($inimg!=null && $inimg!=''){$inimg='=HYPERLINK("'.$urlimage.$inimg.'", "Show Foto Check-In")';}else{$inimg='';}
				if($outimg!=null && $outimg!=''){$outimg='=HYPERLINK("'.$urlimage.$outimg.'", "Show Foto Check-Out")';}else{$outimg='';}
				$sheetAttendance->setCellValue(number_to_alphabet($colIdx+2) . $row, $inimg);
                $sheetAttendance->getStyle(number_to_alphabet($colIdx+2) . $row)
                                ->getFont()
                                ->setSize(11)                             // Mengatur ukuran font menjadi 12
                                ->setItalic(true)
                                ->setUnderline(Font::UNDERLINE_SINGLE);
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
        $objPHPExcel->createSheet(6);
        $sheetcall = $objPHPExcel->setActiveSheetIndex(6);
		$sheetcall->setTitle('Call Daily');
        $sheetcall->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Medrep')
            ->setCellValue('C1', 'Medrep Name')
            ->setCellValue('D1', 'Area');
        $sheetcall->mergeCells('A1:A2');
        $sheetcall->mergeCells('B1:B2');
        $sheetcall->mergeCells('C1:C2');
        $sheetcall->mergeCells('D1:D2');

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

        $callMedrep = $this->report_productivity->get_daily_target_call($params);
        $parmalist = [];
        foreach ($callMedrep as $ap) {
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
