<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_product_knowledge extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_product_knowledge_model', 'product_knowledge');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->product_knowledge->load($data));
    }

    public function load_event()
    {
        $data = param_input();
        responseJSON($this->product_knowledge->get_event($data));
    }
	
    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->product_knowledge->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->product_knowledge->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->product_knowledge->get_city($data));
    }

	function open_detail_answer() {
		$eventid = $this->input->post("id_event");
		$username = $this->input->post("username");
		
        $q = $this->db->query(" 
								select a.id_event, c.event, a.username, e.nama_salesman,e.tipe_sales gff_tipe, a.final_score, b.question_id, b.question, b.answer_id, b.text_answer, 
										case when b.score =1 then 'Benar' else 'Salah' end hasil_nilai
								FROM evaluation_product_knowledge a left join evaluation_product_knowledge_dtl b on a.id_evaluation = b.id_evaluation
								left join product_knowledge_event c on a.id_event = c.id_event
								left join v_gff_info e on a.username = e.salesmanid
								where a.id_event = ".$eventid." and a.username = '".$username."'
								;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
		$html = '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" colspan=3>GFF '.$data[0]["nama_salesman"].' ('.$username.')</th>';
		$html .= '</tr>';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Pertanyaan</th>';
		$html .= '<th style="white-space: nowrap;">Jawaban</th>';
		$html .= '<th style="white-space: nowrap;">Penilaian</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			if ($value['hasil_nilai']=='Salah'){
				$bgcolor="color: #FF0000; text-shadow: 1px 1px 1px black, 0 0 10px #FF0000, 0 0 2px darkred;";
			}else{$bgcolor="color: #5BB23A; text-shadow: 1px 1px 1px black, 0 0 10px #5BB23A, 0 0 2px darkgreen;";}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$value['question'].'</td>';
            $html .= '<td style="white-space: nowrap;">'.$value['text_answer'].'</td>';
			$html .= '<td style="white-space: nowrap;'.$bgcolor.'">'.$value['hasil_nilai'].'</td>';
			$html .= '</tr>';
			$i++;
			$bgcolor="";
		}
		$html .= '</tbody>';
		$html .= '</table>';
				
		echo $html;

	}
    
	function savexls_product_knowledge_all() {
        $usersession = $this->uri->segment('3');
        $restrict_level = $this->uri->segment('4');
        $eventid = $this->uri->segment('5');
        $data = array("usersession" => $usersession, "restrict_level" => $restrict_level, "id_event" => $eventid);
		//print_r($data);
		
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '360');
        
        $filename = "Product_Knowledge_Event.xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Event')
                    ->setCellValue('C1', 'Kode GFF')
                    ->setCellValue('D1', 'Nama GFF')
                    ->setCellValue('E1', 'Regional')
                    ->setCellValue('F1', 'Area')
                    ->setCellValue('G1', 'City')
                    ->setCellValue('H1', 'Score')
                    ;

        $datamcs = $this->product_knowledge->get_product_knowledge_xls($data);
        $i = 1;
        $row = 2;
        foreach ($datamcs as $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $value['event'])
                        ->setCellValue('C'.$row, $value['username'])
                        ->setCellValue('D'.$row, $value['nama_salesman'])
                        ->setCellValue('E'.$row, $value['nama_regional'])
                        ->setCellValue('F'.$row, $value['nama_area'])
                        ->setCellValue('G'.$row, $value['city'])
                        ->setCellValue('H'.$row, $value['final_score'])
						;
			$i++;
            $row++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Product Knowledge');
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
