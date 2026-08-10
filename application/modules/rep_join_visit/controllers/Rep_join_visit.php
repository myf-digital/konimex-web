<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_join_visit extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_join_visit_model', 'join_visit');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->join_visit->load($data));
    }
	
    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->join_visit->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->join_visit->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->join_visit->get_city($data));
    }

	function open_detail_answer() {
		$id_evaluation = $this->input->post("id_evaluation");
		
        $q = $this->db->query(" 
								select
									a.id_evaluation AS id_evaluation,
									a.periode AS periode,
									a.username AS review_by,
									a.salesmanid AS salesmanid,
									d.nama_salesman AS nama_salesman,
									d.tipe_sales AS tipe_sales,
									a.review AS review, 
									b.score, b.bobot,
									b.question,b.text_answer
								from
									(((evaluation_join_visit a
									join evaluation_join_visit_dtl b on
									(a.id_evaluation = b.id_evaluation))
									join join_visit_form c on
									(b.visit_form_id = c.visit_form_id))
									left join m_sales_salesman d on
									(a.salesmanid = d.salesmanid))
								where a.id_evaluation=".$id_evaluation.";
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
		$html = '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" colspan=2>Review By '.$data[0]["review_by"].' - MEDREP '.$data[0]["nama_salesman"].' ('.$data[0]["salesmanid"].')</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		$bgcolor="color: #5BB23A; text-shadow: 1px 1px 1px black, 0 0 10px #5BB23A, 0 0 2px darkgreen;";
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td rowspan=2>'.$i.'</td>';
			$html .= '<td style="white-space: word-wrap: break-word;">Question : '.$value['question'].'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
            $html .= '<td style="white-space: word-wrap: break-word;">Evaluation : '.$value['text_answer'].'</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table>';
				
		echo $html;

	}
    
	function savexls_join_visit_all() {
        $usersession = $this->uri->segment('3');
        $restrict_level = $this->uri->segment('4');
        $start = $this->uri->segment('5');
        $end = $this->uri->segment('6');
        $data = array("usersession" => $usersession, "restrict_level" => $restrict_level, "start_date" => $start, "end_date" => $end);
		//print_r($data);
		
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '360');
        
        $filename = "Join_Visit_MEDREP.xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Date')
                    ->setCellValue('C1', 'Review By')
                    ->setCellValue('D1', 'MEDREP Code')
                    ->setCellValue('E1', 'Name')
                    ->setCellValue('F1', 'Regional')
                    ->setCellValue('G1', 'Area')
                    ->setCellValue('H1', 'City')
                    ->setCellValue('I1', 'Score')
                    ->setCellValue('J1', 'Review')
                    ->setCellValue('K1', 'Preparation')
                    ->setCellValue('L1', 'Approach')
                    ->setCellValue('M1', 'Regular Shelf Merchandising')
                    ->setCellValue('N1', 'Advance Merchandising on Regular Shelves')
                    ->setCellValue('O1', 'Promo Implementation')
                    ->setCellValue('P1', 'Sell and Secure')
                    ->setCellValue('Q1', 'DRC Filling')
                    ;

        $datamcs = $this->join_visit->get_join_visit_gff_xls($data);
		//echo $this->db->last_query();
        $i = 1;
        $row = 2;
        foreach ($datamcs as $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $value['periode'])
                        ->setCellValue('C'.$row, $value['review_by'])
                        ->setCellValue('D'.$row, $value['salesmanid'])
                        ->setCellValue('E'.$row, $value['nama_salesman'])
                        ->setCellValue('F'.$row, $value['nama_regional'])
                        ->setCellValue('G'.$row, $value['nama_area'])
                        ->setCellValue('H'.$row, $value['city'])
                        ->setCellValue('I'.$row, $value['final_score'])
                        ->setCellValue('J'.$row, $value['review'])
                        ->setCellValue('K'.$row, $value['preparation'])
                        ->setCellValue('L'.$row, $value['approach'])
                        ->setCellValue('M'.$row, $value['regular_shelf_merchandising'])
                        ->setCellValue('N'.$row, $value['advance_merchandising_on_regular_shelves'])
                        ->setCellValue('O'.$row, $value['promo_implementation'])
                        ->setCellValue('P'.$row, $value['sell_and_secure'])
                        ->setCellValue('Q'.$row, $value['drc_filling'])
						;
			$i++;
            $row++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Join_Visit_MEDREP');
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
