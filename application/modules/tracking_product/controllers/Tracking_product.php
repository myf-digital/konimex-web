<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Tracking_product extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tracking_product_model', 'tracking_product');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->tracking_product->get_regional($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->tracking_product->get_city($data));
    }

    function open_detail() {
		
		$brand = $this->input->post("brand");
		$sku = $this->input->post("sku");
		$year = $this->input->post("year");
		$month = $this->input->post("month");

		$idjabatan = $this->input->post("idjabatan");
		$restrict_level = $this->input->post("restrict_level");
		$usersession = $this->input->post("usersession");

        $regionalid = $this->input->post("regionalid");
        $areaid = $this->input->post("areaid");

        $params = [
        	'brand' => $brand,
        	'sku' => $sku,
        	'year' => $year,
        	'month' => $month,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        ];

        $data = $this->tracking_product->getTrackingProduct($params);
		//$str = $this->db->last_query();
		//echo $str;
        $bulan=array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		
		$html ='<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th rowspan=2 style="vertical-align : middle;text-align:center;width: 30px">No</th>';
		$html .= '<th rowspan=2 style="vertical-align : middle;text-align:center;width: 120px">Regional</th>';
		$html .= '<th rowspan=2 style="vertical-align : middle;text-align:center;width: 120px">City</th>';
		$html .= '<th rowspan=2 style="vertical-align : middle;text-align:center;width: 120px">Channel</th>';
		$html .= '<th colspan=2 style="vertical-align : middle;text-align:center;width: 160px">Tracking Product</th>';
		$html .= '<th colspan=2 style="vertical-align : middle;text-align:center;width: 160px">POSM</th>';
		$html .= '</tr>';
		$html .= '<tr>';
        $html .= '<th style="vertical-align : middle;text-align:center;width: 80px">Total Store</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 80px">Available Product</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 80px">Target</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 80px">Actual</th>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		$html .= '<div class="container-table-content">';
		$html .= '<table class="table table-striped table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
        $i=1;
		foreach ($data as $value) {

			/*$stockAwalValue = $value['qty_stock_awal']*$value['harga'];
			$stockSellinValue = $value['qty_sell_in']*$value['harga'];
			$stockAkhirValue = $value['qty_stock_akhir']*$value['harga'];
			$stockSelloutValue = $value['qty_sell_out']*$value['harga'];
			*/
			$bln = $value['bulan'] != 10  ? str_replace('0', '', $value['bulan']) : $value['bulan'];

			$html .= '<tr>';
			$html .= '<td style="width:30px">'.$i.'</td>';
			$html .= '<td style="width:120px">'.$value['nama_regional'].'</td>';
			$html .= '<td style="width:120px">'.$value['city'].'</td>';
			$html .= '<td style="width:120px">'.$value['typeid'].'</td>';
			$html .= '<td style="vertical-align :middle;text-align:right;width:80px">'.$value['total_stores_universe'].'</td>';
			$html .= '<td style="vertical-align :middle;text-align:right;width:80px">'.$value['available_product_baru'].'</td>';
			$html .= '<td style="vertical-align :middle;text-align:right;width:80px">'.$value['posm_target'].'</td>';
			$html .= '<td style="vertical-align :middle;text-align:right;width:80px">'.$value['posm_actual'].'</td>';

			/*$html .= '<td style="text-align:right;width: 150px">'.$value['qty_stock_awal'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockAwalValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_sell_in'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockSellinValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_stock_akhir'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockAkhirValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_sell_out'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockSelloutValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['doi'].'</td>';
			*/$i++;
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
        $brand = $this->uri->segment('5');
        $sku = $this->uri->segment('6');
        $regionalid = $this->uri->segment('7');
        $areaid = $this->uri->segment('8');

        $usersession = $this->uri->segment('9');
        $restrict_level = $this->uri->segment('10');
        $idjabatan = $this->uri->segment('11');

        $params = [
        	'brand' => $brand,
        	'sku' => $sku,
        	'year' => $year,
        	'month' => $month,
        	'idjabatan' => $idjabatan,
        	'restrict_level' => $restrict_level,
        	'usersession' => $usersession,
        	'regionalid' => $regionalid,
        	'areaid' => $areaid,
        ];

        $data = $this->tracking_product->getTrackingProduct($params);

        $bulan=array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        $filename = "tracking_product_".$year."_".$month;

        $spreadsheet = new Spreadsheet();

		$header = ['No', 'Regional', 'City', 'Channel', 'Total Store', 'Available Product', 'POSM Target', 'POSM Actual'];

        $sheet = $spreadsheet->getActiveSheet();

        /*$sheet->setCellValue('K1', '01 Stock Awal')->mergeCells('K1:L1');
        $sheet->setCellValue('M1', '02 Sell In')->mergeCells('M1:N1');
        $sheet->setCellValue('O1', '03 Stock Akhir')->mergeCells('O1:P1');
        $sheet->setCellValue('Q1', '04 Sell Out')->mergeCells('Q1:R1');
		*/
        $sheet->fromArray($header,NULL,'A2');

        $i = 1;
        $row = 3;
        foreach ($data as $value) {
        	$sheet->setCellValue('A1', 'Product :'.$value['product_name'])->mergeCells('A1:H1');
        	/*
			$stockAwalValue = $value['qty_stock_awal']*$value['harga'];
			$stockSellinValue = $value['qty_sell_in']*$value['harga'];
			$stockAkhirValue = $value['qty_stock_akhir']*$value['harga'];
			$stockSelloutValue = $value['qty_sell_out']*$value['harga'];
			*/
        	$bln = $value['bulan'] != 10  ? str_replace('0', '', $value['bulan']) : $value['bulan'];

            $content = [
                $i, 
                $value['nama_regional'],
                $value['city'],
                $value['typeid'],
                $value['total_stores_universe'],
                $value['available_product_baru'],
                $value['posm_target'],
                $value['posm_actual']
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
