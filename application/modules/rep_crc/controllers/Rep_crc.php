<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_crc extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_crc_model', 'rep_crc');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function load_outlet()
    {
        $data = param_input();
        responseJSON($this->rep_crc->load_outlet($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->rep_crc->load_account($data));
    }

    function load_view_crc() {
    	$customerid = $this->input->post("customerid");
    	$accountid = $this->input->post("accountid");
    	$start = $this->input->post("start");
		$end = $this->input->post("end");

    	$data = [
    		'customerid' => $customerid,
    		'start' => $start,
    		'end' => $end
    	];

    	$periode = $this->rep_crc->getPeriodeRekap($data);
    	$customerStockRekap = $this->rep_crc->getCustomerStockRekap($data);
    	$productMapping = $this->rep_crc->getMappingProduct($data);

        $html ='<div class="box-header">';
    	$html .='<div class="row">';
		$html .= '<div class="col-md-12">';
		$html .= '<p style="margin-bottom: -20px; margin-left: 5px"><span style="font-weight: bold;">Q</span> : Qty Akhir, <span style="font-weight: bold;">E</span> : Total Qty Expired, <span style="font-weight: bold;">P</span> : Price</p>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th colspan="2">Tanggal</th>';

        foreach ($periode as $key => $value) {
        	$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");
        	//print_r($bulan);
			$perArr = explode('-', $value['periode']);
        	$bul = intval($perArr[1]);
        	$html .= '<th colspan="3">'.$perArr[2].' '.$bulan[$bul].'</th>';
        }

		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$html .= '<tr>';
		$html .= '<td>Product ID</td>';
		$html .= '<td>Product Name</td>';

		foreach ($periode as $key => $value) {
			$html .= '<td>Q</td>';
			$html .= '<td>E</td>';
			$html .= '<td>P</td>';
		}

		$html .= '</tr>';

		foreach ($productMapping as $product) {

			$customer = [];
			foreach ($customerStockRekap as $cust) {
				if ($product['productid'] == $cust['productid']) {
					$customer[$cust['periode']] = $cust;
				}
			}

			$html .= '<tr>';
			$html .= '<td>'.$product['productid'].'</td>';
			$html .= '<td>'.$product['product_name'].'</td>';

			foreach ($periode as $per) {

				if (isset($customer[$per['periode']])) {
					$qtyAkhir = $customer[$per['periode']]['qty_akhir'] != null ? $customer[$per['periode']]['qty_akhir'] : '-';
					$totalQtyExp = $customer[$per['periode']]['total_qty_exp'] != null ? $customer[$per['periode']]['total_qty_exp'] : '-';
					$price = $customer[$per['periode']]['price'] != null ? number_format($customer[$per['periode']]['price']) : '-';

					$html .= '<td>'.$qtyAkhir.'</td>';
					$html .= '<td>'.$totalQtyExp.'</td>';
					$html .= '<td>'.$price.'</td>';
				} else {
					$html .= '<td>-</td>';
					$html .= '<td>-</td>';
					$html .= '<td>-</td>';
				}
			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table></div></div></div>';

		echo $html;
    }

    function download_to_excel_spreadsheet() {
    	ini_set('memory_limit', '128M');

    	$customerid = $this->input->get("customerid");
    	$accountid = $this->input->get("accountid");
    	$start = $this->input->get("start");
		$end = $this->input->get("end");

    	if ($customerid == 'null') return false;

    	$customerid = str_replace('/', '', trim($customerid));	
    	$accountid = str_replace('/', '', trim($accountid));	
    	$start = str_replace('/', '', trim($start));	
    	$end = str_replace('/', '', trim($end));

    	$data = [
    		'customerid' => $customerid,
    		'start' => $start,
    		'end' => $end
    	];

    	$periode = $this->rep_crc->getPeriodeRekap($data);
    	$customerStockRekap = $this->rep_crc->getCustomerStockRekap($data);
    	$productMapping = $this->rep_crc->getMappingProduct($data);	

		$filename = "Report_crc_".$customerid."_".date('YmdHis');

		$headerDay = [];

		foreach ($periode as $key => $value) {
        	$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");
        	$perArr = explode('-', $value['periode']);
        	$bul = intval($perArr[1]);
        	$headerDay[] = $perArr[2].' '.$bulan[$bul];
        }

        $cell = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ", "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ");

		$spreadsheet = new Spreadsheet();
		$nb = ['Q : Qty Akhir, E : Total Qty Expired, P : Price'];
		$header = ['Tanggal'];
		$product = ['Product ID', 'Product Name'];
		$field = ['Q', 'E', 'P'];

		$spreadsheet->getActiveSheet()
		    ->fromArray($nb,NULL,'A1');
		$spreadsheet->getActiveSheet()
		    ->fromArray($header,NULL,'A2')->mergeCells("A2:B2");

		$col = 2;
		foreach ($headerDay as $key => $value) {
			
			$merge = $cell[$col].'2:'.$cell[$col+2].'2';
			$spreadsheet->getActiveSheet()
		    	->fromArray([$value],NULL,$cell[$col].'2')->mergeCells($merge);
		    $col = $col+3;
		}

		$spreadsheet->getActiveSheet()
		    ->fromArray($product,NULL,'A3');

		$col = 2;
		foreach ($headerDay as $key => $value) {

			$spreadsheet->getActiveSheet()
		    	->fromArray($field,NULL,$cell[$col].'3');
		    $col = $col+3;
		}

		$row = 4;
		foreach ($productMapping as $product) {

			$customer = [];
			foreach ($customerStockRekap as $cust) {
				if ($product['productid'] == $cust['productid']) {
					$customer[$cust['periode']] = $cust;
				}
			}

			$productField = array($product['productid'], $product['product_name']);
			$spreadsheet->getActiveSheet()
		    	->fromArray($productField,NULL,'A'.$row);

		    $col = 2;
			foreach ($periode as $per) {

				if (isset($customer[$per['periode']])) {
					$qtyAkhir = $customer[$per['periode']]['qty_akhir'] != null ? $customer[$per['periode']]['qty_akhir'] : '-';
					$totalQtyExp = $customer[$per['periode']]['total_qty_exp'] != null ? $customer[$per['periode']]['total_qty_exp'] : '-';
					$price = $customer[$per['periode']]['price'] != null ? $customer[$per['periode']]['price'] : '-';

					$detailField = array($qtyAkhir, $totalQtyExp, $price);

					$spreadsheet->getActiveSheet()
		    			->fromArray($detailField,NULL,$cell[$col].$row);
				} else {
					$detailField = array('-', '-', '-');

					$spreadsheet->getActiveSheet()
		    			->fromArray($detailField,NULL,$cell[$col].$row);
				}

				$col = $col+3;
			}

			$row++;
		}
 
		$writer = new Xlsx($spreadsheet);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
    }
}