<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Rep_log extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_log_model', 'report_log');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    function open_detail() {
		
		$start = $this->input->post("start");
		$end = $this->input->post("end");

        $params = [
            'start' => $start,
            'end' => $end
        ];

		$data = $this->report_log->loadUserLog($params);
		
		$html ='<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th style="width: 50px">No</th>';
		$html .= '<th style="width: 80px">Tanggal</th>';
        $html .= '<th style="width: 100px">Username</th>';
		$html .= '<th style="width: 150px">Nama</th>';
		$html .= '<th style="width: 100px">Jabatan</th>';
        $html .= '<th style="width: 200px">Deskripsi</th>';
        $html .= '<th style="width: 100px">Waktu</th>';
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
			$html .= '<td style="width: 50px">'.$i.'</td>';
			$html .= '<td style="width: 80px">'.$value['periode'].'</td>';
			$html .= '<td style="width: 100px">'.$value['username'].'</td>';
            $html .= '<td style="width: 150px">'.$value['name'].'</td>';
			$html .= '<td style="width: 100px">'.$value['jabatan'].'</td>';
            $html .= '<td style="width: 200px">'.$value['description'].'</td>';
			$html .= '<td style="width: 92px">'.explode(' ', $value['log_date'])[1].'</td>';
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

        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');

        $params = [
            'start' => $start,
            'end' => $end
        ];

        $data = $this->report_log->loadUserLog($params);
                        
        $filename = "Report_history_login_".$start."_".$end;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Tanggal', 'Username', 'Nama', 'Jabatan', 'Deskripsi', 'Waktu'];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'History login '.$start.' - '.$end);

        $sheet->fromArray($header,NULL,'A2');

        $i = 1;
        $row = 3;
        foreach ($data as $value) {

            $content = [
                $i, 
                $value['periode'], 
                $value['username'], 
                $value['name'],
                $value['jabatan'],
                $value['description'],
                explode(' ', $value['log_date'])[1]
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
