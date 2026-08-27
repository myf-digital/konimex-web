<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

    public function load_salesman()
    {
        $data = param_input();
        responseJSON($this->order->get_salesman($data));
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
        $dataOrders = group_order_by_no_po($orders);

        $html = "";
        if (!empty($dataOrders) && count($dataOrders) > 0) {
            foreach ($dataOrders as $od) {
                $header = $od['headers'];
                
                $html .= "
                    <div class='row'>
                        <div class='col-md-6'>
                            <table class='no-border'>
                                <tr>
                                    <td width='25%'>No PO</td>
                                    <td width='1%'>:</td>
                                    <td class='text-bold'>{$header['no_po']}</td>
                                </tr>
                                <tr>
                                    <td width='25%'>No Faktur / Bill Doc</td>
                                    <td width='1%'>:</td>
                                    <td class='text-bold'>{$header['no_sales']}</td>
                                </tr>
                                <tr>
                                    <td width='25%'>Tanggal</td>
                                    <td width='1%'>:</td>
                                    <td class='text-bold'>{$header['tanggal']}</td>
                                </tr>
                            </table>
                        </div>
                        <div class='col-md-6'>
                            <table class='no-border'>
                                <tr>
                                    <td width='25%'>Customer</td>
                                    <td width='1%'>:</td>
                                    <td class='text-bold'>{$header['nama_customer']}</td>
                                </tr>
                                <tr>
                                    <td width='25%'>TPE</td>
                                    <td width='1%'>:</td>
                                    <td class='text-bold'>{$header['salesman']}</td>
                                </tr>
                                <tr>
                                    <td width='25%'>Status</td>
                                    <td width='1%'>:</td>
                                    <td class='text-bold'>{$header['status']}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                ";

                $details = $od['details'];
                if (!empty($details) && count($details) > 0) {
                    $html .= '
                        <div class="container-table">
                            <table class="table table-bordered table-condensed fixed-table">
                                <tbody>
                                    <tr>
                                        <th style="width: 100px">Image</th>
                                        <th style="width: 100px">Product ID</th>
                                        <th style="width: 200px">Product Name</th>
                                        <th style="width: 100px;text-align:right;">QTY PCS</th>
                                        <th style="width: 200px;text-align:right;">Price</th>
                                        <th style="width: 200px;text-align:right;">Gross</th>
                                        <th style="width: 200px;text-align:right;">Discount</th>
                                        <th style="width: 200px;text-align:right;">Net</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="container-table-content">
                            <table class="table table-striped table-bordered table-condensed fixed-table">
                                <tbody>
                    ';
                    
                    $totbruto = 0;
                    $totdisc  = 0;
                    $totnetto = 0;
                    
                    foreach ($details as $v_detail) {
                        $image = '';
                        $param_link = '\''.$v_detail->tanggal.'\',\''.@$v_detail->no_po.'\',\''.$v_detail->salesmanid.'\'';	
                        if (isset($v_detail->url_img_po) && $v_detail->url_img_po) {
                            $image = '
                                <img
                                    class="img-rounded"
                                    onclick="preview_image_order('.$param_link.');"
                                    alt="Image Order"
                                    style="width:100px; height:100px;"
                                    src="'.@$v_detail->url_img_po.'"
                                >
                            ';
                        }
                        $html .= '
                            <tr>
                                <td style="width: 100px" align="center">'.$image.'</td>
                                <td style="width: 100px">'.$v_detail->productid.'</td>
                                <td style="width: 200px">'.$v_detail->nama_invoice.'</td>
                                <td style="width: 100px;text-align:right;">'.number_format($v_detail->qty_jual_in_pcs, 0, '.', ',').'</td>
                                <td style="width: 200px;text-align:right;">'.number_format($v_detail->h_jual, 0, '.', ',').'</td>
                                <td style="width: 200px;text-align:right;">'.number_format($v_detail->total_bruto, 2, '.', ',').'</td>
                                <td style="width: 200px;text-align:right;">'.number_format($v_detail->total_discount, 2, '.', ',').'</td>			
                                <td style="width: 200px;text-align:right;">'.number_format($v_detail->total_netto, 2, '.', ',').'</td>
                            </tr>
                        ';        
                        $totbruto = $totbruto+$v_detail->total_bruto;
                        $totdisc = $totdisc+$v_detail->total_discount;
                        $totnetto = $totnetto+$v_detail->total_netto;
                    }
                    
                    $html .='
                                <tr>
                                    <th colspan="5" style="text-align:right;">Total</th>
                                    <th style="width: 200px;text-align:right;">'.number_format($totbruto, 2, '.', ',').'</th>
                                    <th style="width: 200px;text-align:right;">'.number_format($totdisc, 2, '.', ',').'</th>
                                    <th style="width: 200px;text-align:right;">'.number_format($totnetto, 2, '.', ',').'</th>	
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    ';
                }
            }
        }

        $html .='
        <div class="box-footer">
            <a id="btn-home-form" href="javascript:void(0)" onclick="savexls(\''.$salesmanid.'\');" class="btn btn-success fa fa-download"> Save Excel</a>
        </div>';
        $html .= '<script type="text/javascript">
                    const common = new Common();

                    function savexls(salesmanid) {
                        var startdate1 = $("#start_periode");
                        var startdate2 = $("#end_periode");
                        common.direct("rep_order/savexls_order_all/"+salesmanid+"/"+startdate1.val()+"/"+startdate2.val());
                    }

                    $(".container-table-content").on("scroll", function() {
                        $(".container-table").scrollLeft($(this).scrollLeft());
                    });
                    $(".container-table").on("scroll", function() {
                        $(".container-table-content").scrollLeft($(this).scrollLeft());
                    });

                    function preview_image_order(tanggal,no_po,salesmanid) {
                        $.ajax({
                            url: "'.site_url().'/rep_order/get_fancy_order",
                            type: "POST",
                            async: false,
                            dataType: "json",
                            data: { tanggal, no_po, salesmanid },
                            success: function (result) {
                                var tempFile = [];
                                $.each(result, function (index, value) {
                                    var title = `No PO: ${value.no_po}`;
                                    if (value.nama_customer) title += `<br /> Customer: ${value.nama_customer}`;
                                    var tempFileElemet = {href: value.url_img_po, title };
                                    tempFile.push(tempFileElemet);
                                });

                                $.fancybox.open(tempFile, {
                                    helpers: {
                                        thumbs: {
                                            width: 75,
                                            height: 50
                                        }
                                    }
                                });
                            }
                        });
                    }
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

        $orders = $this->order->get_order_all_xls($data);
        $dataOrders = group_order_by_no_po($orders);

        $filename = "Order_All_".$salesmanid."_".$startdate1."_".$startdate2;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $row = 1;
        foreach ($dataOrders as $od) {
            $header = $od['headers'];

            $sheet->setCellValue("A{$row}", "No PO");
            $sheet->setCellValue("B{$row}", $header['no_po']);
            $sheet->setCellValue("D{$row}", "Customer");
            $sheet->setCellValue("E{$row}", $header['nama_customer']);
            $row++;

            $sheet->setCellValue("A{$row}", "No Faktur / Bill Doc");
            $sheet->setCellValue("B{$row}", $header['no_sales']);
            $sheet->setCellValue("D{$row}", "TPE");
            $sheet->setCellValue("E{$row}", $header['salesman']);
            $row++;

            $sheet->setCellValue("A{$row}", "Tanggal");
            $sheet->setCellValue("B{$row}", $header['tanggal']);
            $sheet->setCellValue("D{$row}", "Status");
            $sheet->setCellValue("E{$row}", $header['status']);
            $row += 2;

            $sheet->fromArray(
                ['Product ID', 'Product Name', 'Qty (pcs)', 'Price', 'Gross', 'Discount', 'Net', 'Image'],
                NULL,
                "A{$row}"
            );
            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;

            $totBruto = $totDisc = $totNet = 0;
            foreach ($od['details'] as $v_detail) {
                $sheet->setCellValue("A{$row}", $v_detail->productid);
                $sheet->setCellValue("B{$row}", $v_detail->nama_invoice);
                $sheet->setCellValue("C{$row}", number_format($v_detail->qty_jual_in_pcs, 0, '.', ','));
                $sheet->setCellValue("D{$row}", number_format($v_detail->h_jual, 0, '.', ','));
                $sheet->setCellValue("E{$row}", number_format($v_detail->total_bruto, 0, '.', ','));
                $sheet->setCellValue("F{$row}", number_format($v_detail->total_discount, 0, '.', ','));
                $sheet->setCellValue("G{$row}", number_format($v_detail->total_netto, 0, '.', ','));
                $sheet->setCellValue("H{$row}", $v_detail->url_img_po ?: '');

                $totBruto += $v_detail->total_bruto;
                $totDisc  += $v_detail->total_discount;
                $totNet   += $v_detail->total_netto;
                $row++;
            }

            $sheet->setCellValue("D{$row}", "Total");
            $sheet->setCellValue("E{$row}", number_format($totBruto, 0, '.', ','));
            $sheet->setCellValue("F{$row}", number_format($totDisc, 0, '.', ','));
            $sheet->setCellValue("G{$row}", number_format($totNet, 0, '.', ','));
            $sheet->getStyle("D{$row}:G{$row}")->getFont()->setBold(true);

            $row += 3;
        }

        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("C:G")->getNumberFormat()->setFormatCode('#,##0.00');
        
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

        $orders = $this->order->get_order_all_salesman_xls($data);
        $dataOrders = group_order_by_no_po($orders);

		$filename = "Order_All_TPE_".$start."_".$end;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $row = 1;
        foreach ($dataOrders as $od) {
            $header = $od['headers'];

            $sheet->setCellValue("A{$row}", "No PO");
            $sheet->setCellValue("B{$row}", $header['no_po']);
            $sheet->setCellValue("D{$row}", "Customer");
            $sheet->setCellValue("E{$row}", $header['nama_customer']);
            $row++;

            $sheet->setCellValue("A{$row}", "No Faktur / Bill Doc");
            $sheet->setCellValue("B{$row}", $header['no_sales']);
            $sheet->setCellValue("D{$row}", "TPE");
            $sheet->setCellValue("E{$row}", $header['salesman']);
            $row++;

            $sheet->setCellValue("A{$row}", "Tanggal");
            $sheet->setCellValue("B{$row}", $header['tanggal']);
            $sheet->setCellValue("D{$row}", "Status");
            $sheet->setCellValue("E{$row}", $header['status']);
            $row += 2;

            $sheet->fromArray(
                ['Product ID', 'Product Name', 'Qty (pcs)', 'Price', 'Gross', 'Discount', 'Net', 'Image'],
                NULL,
                "A{$row}"
            );
            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;

            $totBruto = $totDisc = $totNet = 0;
            foreach ($od['details'] as $v_detail) {
                $sheet->setCellValue("A{$row}", $v_detail->productid);
                $sheet->setCellValue("B{$row}", $v_detail->nama_invoice);
                $sheet->setCellValue("C{$row}", number_format($v_detail->qty_jual_in_pcs, 0, '.', ','));
                $sheet->setCellValue("D{$row}", number_format($v_detail->h_jual, 0, '.', ','));
                $sheet->setCellValue("E{$row}", number_format($v_detail->total_bruto, 0, '.', ','));
                $sheet->setCellValue("F{$row}", number_format($v_detail->total_discount, 0, '.', ','));
                $sheet->setCellValue("G{$row}", number_format($v_detail->total_netto, 0, '.', ','));
                $sheet->setCellValue("H{$row}", $v_detail->url_img_po ?: '');

                $totBruto += $v_detail->total_bruto;
                $totDisc  += $v_detail->total_discount;
                $totNet   += $v_detail->total_netto;
                $row++;
            }

            $sheet->setCellValue("D{$row}", "Total");
            $sheet->setCellValue("E{$row}", number_format($totBruto, 0, '.', ','));
            $sheet->setCellValue("F{$row}", number_format($totDisc, 0, '.', ','));
            $sheet->setCellValue("G{$row}", number_format($totNet, 0, '.', ','));
            $sheet->getStyle("D{$row}:G{$row}")->getFont()->setBold(true);

            $row += 3;
        }

        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("C:G")->getNumberFormat()->setFormatCode('#,##0.00');
 
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

        $header = ['No', 'Customer ID', 'Customer Name', 'No Invoice', 'No Faktur / Bill Doc', 'TPE Name', 'TPE Type', 'Bill Value', 'Pay', 'Total'];

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
	}

	function get_fancy_order() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->order->get_fancy_order();
        echo json_encode($list);
	}
}
