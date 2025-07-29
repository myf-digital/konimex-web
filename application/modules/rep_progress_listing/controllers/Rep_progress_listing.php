<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Rep_progress_listing extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_progress_listing_model', 'progress_listing');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->progress_listing->load($data));
    }
    
	function get_progress_listing_all()
    {
		$siteid = $this->input->post('siteid');
		$salesmanid = $this->input->post('salesmanid');
		$customerid = $this->input->post('customerid');
		$startdate = $this->input->post('startdate');
		$enddate = $this->input->post('enddate');

		$data = array(
			"salesmanid" => $salesmanid,
			"startdate" => $startdate, 
			"enddate" => $enddate
		);
		$progress_listings = $this->progress_listing->sql_progress_listing_detail($data);

		$html = '<div class="container-table" style="height:100%;">
                    <table class="table table-bordered table-condensed fixed-table" style="margin-bottom:0px;">
                        <tbody>
                            <tr>
                                <th rowspan="2" style="text-align:center;width:100px;vertical-align:middle;">Brand ID</th>
                                <th rowspan="2" style="text-align:center;width:200px;vertical-align:middle;">Nama Brand</th>
                                <th rowspan="2" style="text-align:center;width:150px;vertical-align:middle;">Progress</th>
                                <th colspan="10" style="text-align:center;width:2250px;">Sign Memo User</th>
                            </tr>
                            <tr>
                                <th style="text-align:center;width:225px;">Ambil Dok Registrasi</th>
                                <th style="text-align:center;width:225px;">Melengkapi Dok Registrasi</th>
                                <th style="text-align:center;width:225px;">Dokter 1</th>
                                <th style="text-align:center;width:225px;">Dokter 2</th>
                                <th style="text-align:center;width:225px;">Dokter 3</th>
                                <th style="text-align:center;width:225px;">Dokter 4</th>
                                <th style="text-align:center;width:225px;">Dokter 5</th>
                                <th style="text-align:center;width:225px">Dok Registrasi Lengkap</th>
                                <th style="text-align:center;width:225px;">Estimasi PO</th>
                                <th style="text-align:center;width:225px;">PO Release</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="container-table-content">
                    <table class="table table-striped table-bordered table-condensed fixed-table">
                        <tbody>
            ';
        
        foreach ($progress_listings as $v_detail) {
            $html .= '<tr">
                <td style="width:100px;">'.$v_detail['brandid'].'</td>
                <td style="width:200px;">'.$v_detail['brand'].'</td>
                <td style="width:150px;">'.$v_detail['progress'].'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['ambil_dok_registrasi']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['melengkapi_dok_registrasi']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['sign_dokter_1']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['sign_dokter_2']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['sign_dokter_3']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['sign_dokter_4']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['sign_dokter_5']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['dok_registrasi_lengkap']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['estimasi_po']).'</td>
                <td style="text-align:center;width:225px;text-align:center;">'.$this->progress_listing->signFormat($v_detail['po_release']).'</td>
            </tr>';
        }
        
		$param_link = '\''.$siteid.'\',\''.@$salesmanid.'\',\''.$customerid.'\'';	
        $html .='
            </tbody>
        </table>
        </div>
        <div class="box-footer">
            <a id="btn-home-form" href="javascript:void(0)" onclick="savexls('.$param_link.');" class="btn btn-success fa fa-download"> Save Excel</a>
        </div>';
        $html .= '<script type="text/javascript">
                    const common = new Common();

                    function savexls(siteid, salesmanid, customerid) {
                        var startdate1 = $("#get_date1");
                        var startdate2 = $("#get_date2");
                        common.direct("rep_progress_listing/savexls_progress_listing_detail/"+siteid+"/"+salesmanid+"/"+customerid+"/"+startdate1.val()+"/"+startdate2.val());
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

	function savexls_progress_listing_detail()
    {
		ini_set('memory_limit', '512M');
        $siteid = $this->uri->segment('3');
        $salesmanid = $this->uri->segment('4');
        $customerid = $this->uri->segment('5');
        $startdate = $this->uri->segment('6');
        $enddate = $this->uri->segment('7');

        $data = array(
        	"siteid" => $siteid,
        	"salesmanid" => $salesmanid,
        	"customerid" => $customerid,
        	"startdate" => $startdate,
        	"enddate" => $enddate,
        );
        $progress_listings = $this->progress_listing->sql_progress_listing_detail($data);

        $header = [
            'No',
            'Periode',
            'Salesman ID',
            'Salesman Name',
            'Customer ID',
            'Customer Name',
            'Brand ID',
            'Brand Name',
            'Progress',
            'Ambil Dokumen Register',
            'Melengkapi Dokumen Register',
            'Sign Dokter 1',
            'Sign Dokter 2',
            'Sign Dokter 3',
            'Sign Dokter 4',
            'Sign Dokter 5',
            'Dokumen Registrasi Lengkap',
            'Estimasi PO',
            'PO Release',
        ];
        $filename = "Progress_Listing_".$salesmanid."_".$startdate."_".$enddate;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header,NULL,'A1');

        $row = 2;
        foreach ($progress_listings as $key => $value) {
            $content = [
                $key + 1,
                $value['periode'],
                $value['salesmanid'],
                $value['nama_salesman'],
                $value['customerid'],
                $value['nama_customer'],
                $value['brandid'],
                $value['brand'],
                $value['progress'],
                $this->progress_listing->signFormat($value['ambil_dok_registrasi']),
                $this->progress_listing->signFormat($value['melengkapi_dok_registrasi']),
                $this->progress_listing->signFormat($value['sign_dokter_1']),
                $this->progress_listing->signFormat($value['sign_dokter_2']),
                $this->progress_listing->signFormat($value['sign_dokter_3']),
                $this->progress_listing->signFormat($value['sign_dokter_4']),
                $this->progress_listing->signFormat($value['sign_dokter_5']),
                $this->progress_listing->signFormat($value['dok_registrasi_lengkap']),
                $this->progress_listing->signFormat($value['estimasi_po']),
                $this->progress_listing->signFormat($value['po_release']),
            ];
            $sheet->fromArray($content,NULL,'A'.$row);
            $row++;
        }
 
		// Ambil range seluruh worksheet
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');		
	}

	function savexls_progress_listing_all()
    {
		ini_set('memory_limit', '512M');

        $startdate = $this->uri->segment('3');
        $enddate = $this->uri->segment('4');

        $data = array(
			"startdate" => $startdate,
			"enddate" => $enddate, 
		);
        $progress_listings = $this->progress_listing->sql_progress_listing_detail($data);

        $header = [
            'No',
            'Periode',
            'Salesman ID',
            'Salesman Name',
            'Customer ID',
            'Customer Name',
            'Brand ID',
            'Brand Name',
            'Progress',
            'Ambil Dokumen Register',
            'Melengkapi Dokumen Register',
            'Sign Dokter 1',
            'Sign Dokter 2',
            'Sign Dokter 3',
            'Sign Dokter 4',
            'Sign Dokter 5',
            'Dokumen Registrasi Lengkap',
            'Estimasi PO',
            'PO Release',
        ];
		$filename = "Progress_Listing_All_Salesman_".$startdate."_".$enddate;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($header,NULL,'A1');

        $row = 2;
        foreach ($progress_listings as $key => $value) {
            $content = [
                $key + 1,
                $value['periode'],
                $value['salesmanid'],
                $value['nama_salesman'],
                $value['customerid'],
                $value['nama_customer'],
                $value['brandid'],
                $value['brand'],
                $value['progress'],
                $this->progress_listing->signFormat($value['ambil_dok_registrasi']),
                $this->progress_listing->signFormat($value['melengkapi_dok_registrasi']),
                $this->progress_listing->signFormat($value['sign_dokter_1']),
                $this->progress_listing->signFormat($value['sign_dokter_2']),
                $this->progress_listing->signFormat($value['sign_dokter_3']),
                $this->progress_listing->signFormat($value['sign_dokter_4']),
                $this->progress_listing->signFormat($value['sign_dokter_5']),
                $this->progress_listing->signFormat($value['dok_registrasi_lengkap']),
                $this->progress_listing->signFormat($value['estimasi_po']),
                $this->progress_listing->signFormat($value['po_release']),
            ];
            $sheet->fromArray($content,NULL,'A'.$row);

            $row++;
        }

		// Ambil range seluruh worksheet
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
 
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');	
	}
}
