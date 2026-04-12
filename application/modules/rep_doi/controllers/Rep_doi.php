<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Rep_doi extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_doi_model', 'report_doi');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->report_doi->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->report_doi->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->report_doi->get_city($data));
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

        $data = $this->report_doi->getDoi($params);

        $bulan=array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		
		$html ='<div class="box-body">';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 50px">No</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 80px">Bulan</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px">Principal</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 200px">Brand</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 400px">SKU</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 200px">Region</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 400px">Store Name</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 80px">Channel</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 150px">Sub-channel</th>';
		$html .= '<th rowspan="2" style="vertical-align : middle;text-align:center;width: 200px">Area</th>';
		$html .= '<th colspan="2" style="vertical-align : middle;text-align:center;width: 300px">01 Stock Awal</th>';
		$html .= '<th colspan="2" style="vertical-align : middle;text-align:center;width: 300px">02 Sell In</th>';
		$html .= '<th colspan="2" style="vertical-align : middle;text-align:center;width: 300px">03 Stock Akhir</th>';
		$html .= '<th colspan="2" style="vertical-align : middle;text-align:center;width: 300px">04 Sell Out</th>';
		$html .= '<th colspan="2" rowspan="2" style="vertical-align : middle;text-align:center;width: 150px">05 DOI</th>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Qty</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Value</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Qty</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Value</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Qty</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Value</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Qty</th>';
		$html .= '<th style="vertical-align : middle;text-align:center;width: 150px">Value</th>';
		$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		$html .= '<div class="container-table-content">';
		$html .= '<table class="table table-striped table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
        $i=1;
		foreach ($data as $value) {

			$stockAwalValue = $value['qty_stock_awal']*$value['harga'];
			$stockSellinValue = $value['qty_sell_in']*$value['harga'];
			$stockAkhirValue = $value['qty_stock_akhir']*$value['harga'];
			$stockSelloutValue = $value['qty_sell_out']*$value['harga'];

			$bln = $value['bulan'] != 10  ? str_replace('0', '', $value['bulan']) : $value['bulan'];

			$html .= '<tr>';
			$html .= '<td style="width: 50px">'.$i.'</td>';
			$html .= '<td style="width: 80px">'.$bulan[$bln].'</td>';
			$html .= '<td style="width: 150px">'.$value['group_product'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_brand'].'</td>';
			$html .= '<td style="width: 400px">'.$value['nama_invoice'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_regional'].'</td>';
			$html .= '<td style="width: 400px">'.$value['customerid'].'_'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 80px">'.$value['segmentid'].'</td>';
			$html .= '<td style="width: 150px">'.$value['typeid'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_area'].'</td>';

			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_stock_awal'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockAwalValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_sell_in'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockSellinValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_stock_akhir'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockAkhirValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['qty_sell_out'].'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.number_format($stockSelloutValue, 2, '.', ',').'</td>';
			$html .= '<td style="text-align:right;width: 150px">'.$value['doi'].'</td>';
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

        $data = $this->report_doi->getDoi($params);

        $bulan=array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        $filename = "Report_doi_".$year."_".$month;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Bulan', 'Principal', 'Brand', 'SKU', 'Region', 'Store Name', 'Channel', 'Sub-channel', 'Area', 'Qty', 'Value', 'Qty', 'Value', 'Qty', 'Value', 'Qty', 'Value', '05 DOI'];

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('K1', '01 Stock Awal')->mergeCells('K1:L1');
        $sheet->setCellValue('M1', '02 Sell In')->mergeCells('M1:N1');
        $sheet->setCellValue('O1', '03 Stock Akhir')->mergeCells('O1:P1');
        $sheet->setCellValue('Q1', '04 Sell Out')->mergeCells('Q1:R1');

        $sheet->fromArray($header,NULL,'A2');

        $i = 1;
        $row = 3;
        foreach ($data as $value) {

        	$stockAwalValue = $value['qty_stock_awal']*$value['harga'];
			$stockSellinValue = $value['qty_sell_in']*$value['harga'];
			$stockAkhirValue = $value['qty_stock_akhir']*$value['harga'];
			$stockSelloutValue = $value['qty_sell_out']*$value['harga'];

        	$bln = $value['bulan'] != 10  ? str_replace('0', '', $value['bulan']) : $value['bulan'];

            $content = [
                $i, 
                $bulan[$bln], 
                $value['group_product'],
                $value['nama_brand'],
                $value['nama_invoice'],
                $value['nama_regional'],
                $value['customerid'].'_'.$value['nama_customer'],
                $value['segmentid'],
                $value['typeid'],
                $value['nama_area'],
                $value['qty_stock_awal'],
                $stockAwalValue,
                $value['qty_sell_in'],
                $stockSellinValue,
                $value['qty_stock_akhir'],
                $stockAkhirValue,
                $value['qty_sell_out'],
                $stockSelloutValue,
                $value['doi']
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
