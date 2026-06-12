<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Ref_customer extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_model', 'customer');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function create()
    {
        $data = param_input();
        response($this->customer->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer->update($data));
    }

    public function update_location()
    {
        $data = param_input();
        response($this->customer->update_location($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer->load($data));
    }

    public function savetoxls()
    {
        $account = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');
        $filtername = $this->uri->segment('7');
        $data = array("account" => $account , "username" => $username, "jabatan" => $jabatan, "restrict_level" => $restrict_level, "filename" => $filtername);
        $this->customer->savetoxls($data);
    }

    function savetoxlsx() {
        $account = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');
        $filtername = $this->uri->segment('7');
        ini_set("memory_limit","2048M");
        ini_set('max_execution_time', '0');
        
        if ($account=='' or empty($account) or $account=='null'){
            $account='All';
            $filename = 'All_Account';
        }

        $data = array(
            "account" => $account ,
            "usersession" => $username,
            "restrict_level" => $restrict_level,
            "filename" => $filtername
        );
        $outlets = $this->customer->load($data, 'export');
		
		$filename = "Data_Outlet_".str_replace(' ', '_', $filtername)."_".date('Ymd_His').".xlsx";

        $spreadsheet = new Spreadsheet();
        $header = [
            'ID Outlet',
            'ID Outlet Distributor',
            'Nama Outlet',
            'Regional',
            'Area',
            'Sub Area',
            'Channel',
            'Sub Channel',
            'Alamat',
            'User',
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Outlet');
        $sheet->setCellValue('A1', 'DATA OUTLET '.strtoupper(str_replace(' ', '_', $filtername)))->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->fromArray($header, NULL,'A2');
        $sheet->getRowDimension(2)->setRowHeight(25);

        $i = 1;
        $rowNum = 3;
        foreach ($outlets as $outlet) {
            $listUser = '';
            if (!empty($outlet['list_professional'])) {
                $profArray = explode('||', $outlet['list_professional']);
                $formattedProfs = [];
                foreach ($profArray as $idx => $profName) {
                    $formattedProfs[] = ($idx + 1) . '. ' . trim($profName);
                }
                $listUser = implode("\n", $formattedProfs);
            }

            $content = [
                $outlet['customerid'],
                $outlet['cust_id_map'],
                $outlet['nama_customer'],
                $outlet['nama_regional'],
                $outlet['nama_area'],
                $outlet['nama_subarea'],
                $outlet['typeid'],
                $outlet['nama_account'],
                $outlet['alamat'],
                $listUser
            ];

            $sheet->fromArray($content, NULL, 'A'.$rowNum);

            $i++;
            $rowNum++;
        }
		// Ambil range seluruh worksheet
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
		$sheet->getStyle('I3:J' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->freezePane('A3');

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        $headerRange = 'A2:' . $highestColumn . '2';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

	function open_gff_detail() {
		$customerid = $this->input->post("customerid");
		
        $q = $this->db->query(" 
                                select a.customerid,a.nama_customer,b.salesmanid,c.nama_salesman,c.tipe_sales position from m_customer a 
                                join m_customer_ob b on a.customerid=b.customerid 
                                join m_sales_salesman c on b.salesmanid=c.salesmanid
                                where a.customerid = '".$customerid."';
                            ");
		$data = $q->result_array();
		$html = '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">User MEDREP</th>';
		$html .= '<th style="white-space: nowrap;">Nama MEDREP</th>';
		$html .= '<th style="white-space: nowrap;">Posisi</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$value['salesmanid'].'</td>';
            $html .= '<td style="white-space: nowrap;">'.$value['nama_salesman'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['position'].'</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table>';
				
		echo $html;

	}
}
