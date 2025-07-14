<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_join_visit_outlet extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_join_visit_outlet_model', 'join_visit');
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

    public function load_event()
    {
        $data = param_input();
        responseJSON($this->join_visit->get_event($data));
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
		$id_evaluation_outlet = $this->input->post("id_evaluation_outlet");
		$customerid = $this->input->post("customerid");
		$kode_outlet = $this->input->post("kode_outlet");
		$nama_customer = $this->input->post("nama_customer");
		$username = $this->input->post("username");
		
        $q = $this->db->query(" 
								select a.id_evaluation_outlet,a.customerid,a.periode,a.username as review_by,c.category,
										(select category_eng from outlet_form_category where cat_form_id=b.cat_form_id) category_q, 
										(select question_eng from outlet_visit_form where question_id=(select parent_id from outlet_visit_form where question_id=b.question_id)) header_q,
										b.question_id,b.question,b.text_answer from outlet_visit_evaluation a 
								left join outlet_visit_evaluation_dtl b on a.id_evaluation_outlet=b.id_evaluation_outlet
								left join outlet_visit_form c on b.question_id = c.question_id and b.cat_form_id =c.cat_form_id 
								where a.id_evaluation_outlet =".$id_evaluation_outlet." order by c.category, c.nourut;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
		$html = '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" colspan=3>Review By '.$username.' - Outlet :'.$customerid.'-'.$nama_customer.' ('.$kode_outlet.')</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		$bgcolor="color: #5BB23A; text-shadow: 1px 1px 1px black, 0 0 10px #5BB23A, 0 0 2px darkgreen;";
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td rowspan=2>'.$i.'</td>';
			$html .= '<td rowspan=2>'.$value['category'].'</td>';
			$html .= '<td style="white-space: word-wrap: break-word;">Question : '.$value['header_q'].' - '.$value['question'].'</td>';
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
    
	function savexls_join_visit_outlet_all() {
        $start_period = $this->uri->segment('3');
        $end_period = $this->uri->segment('4');
        //$data = array("start_period" => $start_period, "end_period" => $end_period);
		//print_r($data);
		
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '360');
        
        $filename = "review_outlet.xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
		$objWorkSheet = $objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0)->setTitle('Review Outlet');
        $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No')
					->setCellValue('B1', 'Period')
                    ->setCellValue('C1', 'OutletID')
                    ->setCellValue('D1', 'Kode Outelt')
                    ->setCellValue('E1', 'Outlet Name')
                    ->setCellValue('F1', 'Review By')
                    ->setCellValue('G1', 'Review Date')
                    ->setCellValue('H1', 'Category')
                    ->setCellValue('I1', 'Header')
                    ->setCellValue('J1', 'Question')
                    ->setCellValue('K1', 'Answer')
					;
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Arial')
		->setSize(12)
		->setBold(true)
		->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE)
		->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        $q = $this->db->query(" 
								select a.id_evaluation_outlet,a.periode,a.customerid,c.kode_outlet,c.nama_customer,a.username,a.created_date, d.category,
										(select category_eng from outlet_form_category where cat_form_id=b.cat_form_id) category_q, 
										(select question_eng from outlet_visit_form where question_id=(select parent_id from outlet_visit_form where question_id=b.question_id)) header_q,
										b.question_id,b.question,b.text_answer from outlet_visit_evaluation a 
								left join outlet_visit_evaluation_dtl b on a.id_evaluation_outlet=b.id_evaluation_outlet
								left join m_customer c on a.customerid = c.customerid 
								left join outlet_visit_form d on b.question_id = d.question_id and b.cat_form_id =d.cat_form_id 
								where a.periode between '$start_period' and '$end_period'
								order by a.id_evaluation_outlet, d.category, d.nourut;
                            ");
		$data = $q->result_array();
        $i = 1;
        $row = 2;
        foreach ($data as $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $value['periode'])
                        ->setCellValue('C'.$row, $value['customerid'])
                        ->setCellValue('D'.$row, $value['kode_outlet'])
                        ->setCellValue('E'.$row, $value['nama_customer'])
                        ->setCellValue('F'.$row, $value['username'])
                        ->setCellValue('G'.$row, $value['created_date'])
                        ->setCellValue('H'.$row, $value['category'])
                        ->setCellValue('I'.$row, $value['header_q'])
                        ->setCellValue('J'.$row, $value['question'])
                        ->setCellValue('K'.$row, $value['text_answer'])
						;
			$i++;
            $row++;
        }
		
        $objPHPExcel->setActiveSheetIndex(1)->setTitle('Summary Review');
		$objPHPExcel->setActiveSheetIndex(1)
					->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Category')
                    ->setCellValue('C1', 'Pertanyaan')
                    ->setCellValue('D1', 'Question')
                    ->setCellValue('E1', 'Jawaban')
                    ;
        $qsum = $this->db->query(" 
									select a.question_id,a.`category`,a.question_idn,a.question_eng,a.`type`, 
											(select GROUP_CONCAT(z.text_answer) from outlet_visit_evaluation_dtl z
											 WHERE z.id_evaluation_outlet in (select x.id_evaluation_outlet from outlet_visit_evaluation x where x.periode between '$start_period' and '$end_period') 
													and z.question_id=a.question_id order by z.text_answer) as summary_answer  
									from outlet_visit_form a 
									where a.is_active=1
									order by a.category asc, a.nourut asc;
								");
		$datasum = $qsum->result_array();
        $i = 1;
        $row = 2;
        foreach ($datasum as $valuesum) {
			if ($valuesum['summary_answer']!='' and $valuesum['type']!='Header'){
				if($valuesum['type']=='MultipleChoice')
				{
				   $arr = explode(',', $valuesum['summary_answer']);
				   $spar = '';
				   $result_count = '';
				   foreach(array_count_values($arr) as $k => $v){
					 if($v > 0){
					   $result_count .= $spar.$k."(".$v.")";
					 }
					 $spar = ',';
				   }
				}else if($valuesum['type']=='Freetext')
				{
					$result_count=$valuesum['summary_answer'];
				}else if($valuesum['type']=='MultipleChoice-Sub')
				{
				   $strvalue = str_replace("|",",",$valuesum['summary_answer']);
				   $arr = explode(',', $strvalue);
				   $spar = '';
				   $arr_sub = array();
				   $result_count = '';
				   foreach(array_count_values($arr) as $k => $v){
					 if (substr_count( $v, '|' )>0)
					 {
						$arr_sub = explode('|', $v);
						
					 }else{
						 if($v > 0){
						   $result_count .= $spar.$k."(".$v.")";
						 }
					 }
					 $spar = ',';
				   }
				}else
				{$result_count='';}
			}else{$result_count='';}


            $objPHPExcel->setActiveSheetIndex(1)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $valuesum['category'])
                        ->setCellValue('C'.$row, $valuesum['question_idn'])
                        ->setCellValue('D'.$row, $valuesum['question_eng'])
                        ->setCellValue('E'.$row, $result_count)
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


}
