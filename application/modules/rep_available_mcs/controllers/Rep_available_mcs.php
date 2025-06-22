<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_available_mcs extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_available_mcs_model', 'available_mcs');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->available_mcs->load($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->available_mcs->get_account($data));
    }
	
    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->available_mcs->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->available_mcs->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->available_mcs->get_city($data));
    }

	function open_productmcs_activesku() {
		$customerid = $this->input->post("customerid");
		
        $q = $this->db->query(" 
								select a.customerid,a.kode_outlet, a.nama_customer, a.typeid, b.productid, mp.nama_invoice product_name, 
								case when c.productid is null then 'Not Available' else 'Available' end statusmcs
								from m_customer a 
								left join mapping_sku_active_data_mcs b on a.typeid = b.typeid and productid in (select bb.productid from mapping_sku_active bb where bb.idaccount=a.classid)
								left join m_product mp on mp.productid = b.productid 
								left join mapping_sku_active_last3months c on c.customerid =a.customerid and c.productid =b.productid 
								where a.customerid <> '' and a.typeid not in ('TOKO PANEL') and a.customerid='".$customerid."'
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
		$html = '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Product ID</th>';
		$html .= '<th style="white-space: nowrap;">Product Name</th>';
		$html .= '<th style="white-space: nowrap;">Status MCS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			if ($value['statusmcs']=='Not Available'){
				$bgcolor="background-color: red;";
			}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$value['productid'].'</td>';
            $html .= '<td style="white-space: nowrap;">'.$value['product_name'].'</td>';
			$html .= '<td style="white-space: nowrap;'.$bgcolor.'">'.$value['statusmcs'].'</td>';
			$html .= '</tr>';
			$i++;
			$bgcolor="";
		}
		$html .= '</tbody>';
		$html .= '</table>';
				
		echo $html;

	}
    
	function savexls_available_mcs_all() {
        $usersession = $this->uri->segment('3');
        $restrict_level = $this->uri->segment('4');
        $account = $this->uri->segment('5');
        $regional = $this->uri->segment('6');
        $area = $this->uri->segment('7');
        $city = $this->uri->segment('8');
		//echo $usersession."/".$restrict_level."/".$account."/".$regional."/".$area."/".$city;
        $data = array("usersession" => $usersession, "restrict_level" => $restrict_level, "account" => $account , "regionalid" => $regional, "areaid" => $area, "city" => $city);
		//print_r($data);
		
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '360');
        
        $filename = "Active_SKU_MCS_All.xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Outlet ID')
                    ->setCellValue('C1', 'Kode Outlet')
                    ->setCellValue('D1', 'Outlet Name')
                    ->setCellValue('E1', 'Channel')
                    ->setCellValue('F1', 'Account')
                    ->setCellValue('G1', 'Regional')
                    ->setCellValue('H1', 'Area')
                    ->setCellValue('I1', 'City')
                    ->setCellValue('J1', 'DC')
                    ->setCellValue('K1', 'MCS')
                    ->setCellValue('L1', 'Last3Months')
                    ->setCellValue('M1', '%')
                    ;

        $datamcs = $this->available_mcs->get_available_mcs_xls($data);
        $i = 1;
        $row = 2;
        foreach ($datamcs as $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row, $i)
                        ->setCellValue('B'.$row, $value['customerid'])
                        ->setCellValue('C'.$row, $value['kode_outlet'])
                        ->setCellValue('D'.$row, $value['nama_customer'])
                        ->setCellValue('E'.$row, $value['typeid'])
                        ->setCellValue('F'.$row, $value['nama_class'])
                        ->setCellValue('G'.$row, $value['nama_regional'])
                        ->setCellValue('H'.$row, $value['nama_area'])
                        ->setCellValue('I'.$row, $value['city'])
                        ->setCellValue('J'.$row, $value['dc'])
                        ->setCellValue('K'.$row, $value['active_mcs'])
                        ->setCellValue('L'.$row, $value['active_sku'])
                        ->setCellValue('M'.$row, $value['_percentage'])
						;
			$i++;
            $row++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('MCS');
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
