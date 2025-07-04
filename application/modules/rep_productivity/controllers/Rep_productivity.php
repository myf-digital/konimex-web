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
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Code GFF</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 300px;">Nama GFF</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px;">Position</th>';
		$html .= '<th colspan=16" style="vertical-align : middle;text-align:center;width: 3200px;">Kuantitatif</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="vertical-align : middle;text-align:center;">HK</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Absensi</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%Kehadiran</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Keterangan</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Target Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Call on PJP</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Extra Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">Actual Call</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">%PJP Compliance</th>';
		$html .= '<th style="vertical-align : middle;text-align:center; width: 200px;">Keterangan</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">SOS HYPERMARKET</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">SOS MTI</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">SOS SUPERMARKET</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">SOS MINIMARKET</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">SOS MODERN PHARMA</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;">SOS TOTAL</th>';
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

			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['GFF Aktif'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['GFF Hadir'], 0, '.', ',').' </td>';
			$html .= '<td style="text-align:right;width: 200px">'.number_format($value['GFF Hadir']/$value['GFF Aktif']*100, 2, '.', ',').' %</td>';
			$html .= '<td style="text-align:right;width: 200px">Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').') </td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['pjp'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['extra_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call']+$value['extra_call'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['call']/$value['pjp']*100, 2, '.', ',').' %</td>';
			$html .= '<td style="text-align:right; width: 200px;">'.$value['rrk_keterangan'].'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['sos_gsk_hyp'], 2, '.', ',').' %</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['sos_gsk_mti'], 2, '.', ',').' %</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['sos_gsk_spm'], 2, '.', ',').' %</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['sos_gsk_mini'], 2, '.', ',').' %</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['sos_gsk_mph'], 2, '.', ',').' %</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;width: 200px;">'.number_format($value['sos_gsk_total'], 2, '.', ',').' %</td>';
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

        $filename = "report_productivity_".$year."_".$month;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Area', 'Code GFF', 'Nama GFF', 'Position', 'HK', 'Absensi', '%Kehadiran', 'Keterangan', 'Target Call', 'Call on PJP', 'Extra Call', 'Actual Call', '%PJP Compliance', 'Keterangan', 'SOS HYPERMARKET', 'SOS MTI', 'SOS SUPERMARKET', 'SOS MINIMARKET', 'SOS MODERN PHARMA', 'SOS TOTAL'];

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
				number_format($value['GFF Aktif'], 0, '.', ','),
				number_format($value['GFF Hadir'], 0, '.', ','),
				number_format($value['GFF Hadir']/$value['GFF Aktif']*100, 2, '.', ','),
				'Cuti('.number_format($value['cuti'], 0, '.', ',').'), Sakit('.number_format($value['sakit'], 0, '.', ',').')',
				number_format($value['pjp'], 0, '.', ','),
				number_format($value['call'], 0, '.', ','),
				number_format($value['extra_call'], 0, '.', ','),
				number_format($value['call']+$value['extra_call'], 0, '.', ','),
				number_format($value['call']/$value['pjp']*100, 2, '.', ',').' %',
				$value['rrk_keterangan'],
				number_format($value['sos_gsk_hyp'], 2, '.', ',').' %',
				number_format($value['sos_gsk_mti'], 2, '.', ',').' %',
				number_format($value['sos_gsk_spm'], 2, '.', ',').' %',
				number_format($value['sos_gsk_mini'], 2, '.', ',').' %',
				number_format($value['sos_gsk_mph'], 2, '.', ',').' %',
				number_format($value['sos_gsk_total'], 2, '.', ',').' %',
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

}
