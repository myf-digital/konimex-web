<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_order extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_order_model', 'order');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->order->load($data));
    }

    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->order->get_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->order->get_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->order->get_city($data));
    }
    
	function get_order_all() {
	
		$salesmanid = $this->input->post('salesmanid');
		$startdate1 = $this->input->post('startdate1');
		$startdate2 = $this->input->post('startdate2');

		$data = array(
			"salesmanid" => $salesmanid,
			"startdate1" => $startdate1, 
			"startdate2" => $startdate2
		);

		$orders = $this->order->get_order_all($data);

		$html = '<div class="container-table">
                    <table class="table table-bordered table-condensed fixed-table">
                    <tbody>
                    <tr>
                    <th style="width: 250px">Customer ID </th>
                    <th style="width: 200px">Customer Name </th>
                    <th style="width: 600px">No Sales </th>
                    <th style="width: 200px">Salesman Name </th>
                    <th style="width: 150px">Sales Type</th>
                    <th style="width: 100px">Date </th>
                    <th style="width: 100px">Product ID </th>
                    <th style="width: 200px" >Product Name</th>
                    <th style="width: 100px;text-align:right;" >QTY PCS</th>
                    <th style="width: 200px;text-align:right;" >Price</th>
                    <th style="width: 200px;text-align:right;" >Gross</th>
                    <th style="width: 200px;text-align:right;" >Discount</th>
                    <th style="width: 200px;text-align:right;" >Net</th>
                    </tr>
                    </tbody>
                    </table>
                    </div>
                    <div class="container-table-content">
                    <table class="table table-striped table-bordered table-condensed fixed-table">
                    <tbody>';

		$totbruto = 0;
        $totdisc  = 0;
        $totnetto = 0;
        
        foreach ($orders as $v_detail) {
            $html .= '<tr>
                       <td style="width: 250px">'.$v_detail['customerid'].'</td>
                       <td style="width: 200px">'.$v_detail['nama_customer'].'</td>
                       <td style="width: 600px">'.$v_detail['no_sales'].'</td>
                       <td style="width: 200px">'.$v_detail['nama_salesman'].'</td>
                       <td style="width: 150px">'.$v_detail['tipe_sales'].'</td>
                       <td style="width: 100px">'.$v_detail['tanggal'].'</td>
                       <td style="width: 100px">'.$v_detail['productid'].'</td>
                       <td style="width: 200px">'.$v_detail['nama_invoice'].'</td>
                       <td style="width: 100px;text-align:right;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>
                       <td style="width: 200px;text-align:right;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>
                       <td style="width: 200px;text-align:right;">'.number_format($v_detail['total_bruto'], 2, '.', ',').'</td>
                       <td style="width: 200px;text-align:right;">'.number_format($v_detail['total_discount'], 2, '.', ',').'</td>			
                       <td style="width: 200px;text-align:right;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>
                       </tr>';        
            $totbruto = $totbruto+$v_detail['total_bruto'];
            $totdisc = $totdisc+$v_detail['total_discount'];
            $totnetto = $totnetto+$v_detail['total_netto'];
        }
        $html .='<tr>
                    <th colspan="10" style="text-align:right;">Total </th>
                    <th style="width: 200px;text-align:right;" >'.number_format($totbruto, 2, '.', ',').'</th>
                    <th style="width: 200px;text-align:right;" >'.number_format($totdisc, 2, '.', ',').'</th>
                    <th style="width: 200px;text-align:right;" >'.number_format($totnetto, 2, '.', ',').'</th>	
                    </tr>
                    </tbody>
                    </table>
                    </div>
                    <div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="savexls(\''.$salesmanid.'\');" class="btn btn-success fa fa-download"> Save Excel</a></div>';
        $html .= '<script type="text/javascript">
                    const common = new Common();

                    function savexls(salesmanid) {
                        var startdate1 = $("#get_date1");
                        var startdate2 = $("#get_date2");
                        common.direct("rep_order/savexls_order_all/"+salesmanid+"/"+startdate1.val()+"/"+startdate2.val());
                    }

                    $(".container-table-content").on("scroll", function() {
                        $(".container-table").scrollLeft($(this).scrollLeft());
                    });
                    $(".container-table").on("scroll", function() {
                        $(".container-table-content").scrollLeft($(this).scrollLeft());
                    });

                </script>
                    ';

		echo $html;
	} 

	function savexls_order_all() {
		
		ini_set('memory_limit', '512M');
        $salesmanid = $this->uri->segment('3');
        $startdate1 = $this->uri->segment('4');
        $startdate2 = $this->uri->segment('5');

        $data = array(
        	"salesmanid" => $salesmanid, 
        	"startdate1" => $startdate1, 
        	"startdate2" => $startdate2
        );

        $filename = "Order_All_".$salesmanid."_".$startdate1."_".$startdate2;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Customer ID', 'Customer Name', 'Customer Address', 'Customer Phone', 'Customer Area', 'No Sales', 'Salesman Name', 'Sales Type', 'Date', 'Product ID', 'Product Name', 'QTY PCS', 'Price', 'Gross', 'Discount', 'Net'];

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($header,NULL,'A1');

        $orders = $this->order->get_order_all_xls($data);

        $i = 1;
        $row = 2;
        $totbruto = 0;
        $totdisc  = 0;
        $totnetto = 0;
        foreach ($orders as $value) {

            $content = [
                $i, 
                $value['customerid'], 
                $value['nama_customer'],
                $value['alamat'],
                $value['telp'],
                $value['area'],
                $value['no_sales'],
                $value['nama_salesman'],
                $value['tipe_sales'],
                $value['tanggal'],
                $value['productid'],
                $value['nama_invoice'],
                $value['qty_jual_in_pcs'],
                $value['h_jual'],
                $value['total_bruto'],
                $value['total_discount'],
                $value['total_netto'],
            ];

            $sheet->fromArray($content,NULL,'A'.$row);

            $i++;
            $row++;
            $totbruto += $value['total_bruto'];
            $totdisc += $value['total_discount'];
            $totnetto += $value['total_netto'];
        }

        $total = [
        	'Total',
        	$totbruto,
        	$totdisc,
        	$totnetto
        ];

        $sheet->fromArray($total,NULL,'N'.$row);
 
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');		
	}

	function savexls_order_all_salesman() {
		ini_set('memory_limit', '512M');

        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $usersession = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');

        $regionalid = $this->uri->segment('7');
        $areaid = $this->uri->segment('8');
        $subareaid = $this->uri->segment('9');

        $data = array(
			"start" => $start,
			"end" => $end, 
			"usersession" => $usersession,
			"restrict_level" => $restrict_level,
			"regionalid" => $regionalid,
			"areaid" => $areaid,
			"subareaid" => $subareaid
		);

		$filename = "Order_All_Salesman_".$start."_".$end;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Customer ID', 'Customer Name', 'Customer Address', 'Customer Phone', 'Customer Area', 'No Sales', 'Salesman Name', 'Sales Type', 'Date', 'Product ID', 'Product Name', 'QTY PCS', 'Price', 'Gross', 'Discount', 'Net'];

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($header,NULL,'A1');

        $orders = $this->order->get_order_all_salesman_xls($data);

        $i = 1;
        $row = 2;
        $totbruto = 0;
        $totdisc  = 0;
        $totnetto = 0;
        foreach ($orders as $value) {

            $content = [
                $i, 
                $value['customerid'], 
                $value['nama_customer'],
                $value['alamat'],
                $value['telp'],
                $value['area'],
                $value['no_sales'],
                $value['nama_salesman'],
                $value['tipe_sales'],
                $value['tanggal'],
                $value['productid'],
                $value['nama_invoice'],
                $value['qty_jual_in_pcs'],
                $value['h_jual'],
                $value['total_bruto'],
                $value['total_discount'],
                $value['total_netto'],
            ];

            $sheet->fromArray($content,NULL,'A'.$row);

            $i++;
            $row++;
            $totbruto += $value['total_bruto'];
            $totdisc += $value['total_discount'];
            $totnetto += $value['total_netto'];
        }

        $total = [
        	'Total',
        	$totbruto,
        	$totdisc,
        	$totnetto
        ];

        $sheet->fromArray($total,NULL,'N'.$row);
 
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');	
	} 


	function get_tagihan_all() {
	
		$salesmanid = $this->input->post('salesmanid');
		$startdate1 = $this->input->post('startdate1');
		$startdate2 = $this->input->post('startdate2');

		$html = $this->order->get_tagihan_all($salesmanid,$startdate1,$startdate2);
		echo $html;
		
	}

	function savexls_tagihan_all() {
		
		ini_set('memory_limit', '512M');

        $salesmanid = $this->uri->segment('3');
        $startdate1 = $this->uri->segment('4');
        $startdate2 = $this->uri->segment('5');

        $data = array(
        	"salesmanid" => $salesmanid , 
        	"startdate1" => $startdate1, 
        	"startdate2" => $startdate2
        );

        $filename = "Invoice_All_".$salesmanid."_".$startdate1."_".$startdate2;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'Customer ID', 'Customer Name', 'No Invoice', 'No Sales', 'Salesman Name', 'Sales Type', 'Bill Value', 'Pay', 'Total'];

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($header,NULL,'A1');

        $billings = $this->order->get_tagihan_all_xls($data);

        $i = 1;
        $row = 2;
        $total_tagihan = 0;
        $total_tunai = 0;
        $total_all = 0; 	
        foreach ($billings as $value) {

            $total = $value['bayar_tunai']; 

    		$content = [
                $i, 
                $value['customerid'], 
                $value['nama_customer'],
                $value['alamat'],
                $value['no_ink'],
                $value['no_sales'],
                $value['nama_salesman'],
                $value['tipe_sales'],
                $value['bayar'],
                $value['bayar_tunai'],
                $total
            ];

            $sheet->fromArray($content,NULL,'A'.$row);

            $i++;
            $row++;
    		$total_tagihan += $value['bayar'];
            $total_tunai += $value['bayar_tunai'];
            $total_all += $total;
        }

        // $total = [
        // 	'Total',
        // 	$totbruto,
        // 	$totdisc,
        // 	$totnetto
        // ];

        // $sheet->fromArray($total,NULL,'N'.$row);
	} 

}
