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

    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
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
            'Alamat',
            'User',
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Outlet');
        $sheet->setCellValue('A1', 'DATA OUTLET '.strtoupper(str_replace(' ', '_', $filtername)))->mergeCells('A1:I1');
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
		$sheet->getStyle('H3:I' . $highestRow)->getAlignment()->setWrapText(true);
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

    public function mapping_customer_area()
    {
        response($this->customer->mapping_customer_area());
    }

    public function download_template()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->db->save_queries = FALSE;

        $regionalid = $this->input->get('regionalid');
        $areaid = $this->input->get('areaid');
        $subareaid = $this->input->get('subareaid');
        $usersession = $this->input->get('usersession');
        $restrict_level = $this->input->get('restrict_level');

        $templateData = $this->customer->get_template_data($regionalid, $areaid, $subareaid, $usersession, $restrict_level);
        $channels = $templateData['channels'];
        $locations = $templateData['locations'];
        $specializations = $templateData['specializations'];
        $prof_types = $templateData['prof_types'];

        $spreadsheet = new Spreadsheet();

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Template Outlet');
        $headers1 = [
            'NO',
            'ID_OUTLET',
            'KODE_OUTLET',
            'NAMA_OUTLET',
            'CHANNEL',
            'TELPON',
            'EMAIL',
            'REGIONAL',
            'AREA',
            'SUB_AREA',
            'ALAMAT',
            'ID_USER',
            'NAMA_USER',
            'SPESIALISASI',
            'TIPE_USER',
        ];
        $sheet1->fromArray($headers1, NULL, 'A1');
        $sheet1->getRowDimension(1)->setRowHeight(25);
        $headerRange1 = 'A1:O1';
        $sheet1->getStyle($headerRange1)->applyFromArray([
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
        foreach (range('A', 'O') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }
        
        $sampleRow = [
            'Contoh',
            '9999',
            'OTL001',
            'Klinik Sehat',
            'Klinik',
            '08123456789',
            'klinik.sehat@konimex.com',
            'Barat',
            'Jawa Barat I',
            'Bandung Selatan',
            'Jl. Sudirman No. 12',
            '5555',
            'dr. Budi Utomo',
            'Spesialisasi Anak',
            'Dokter',
        ];
        $sheet1->fromArray($sampleRow, NULL, 'A2');

        $sheet1->getStyle('A2:O2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ]
        ]);

        $sheet1->getStyle('A1:O2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $outlets = $this->customer->get_all_outlets_and_users($regionalid, $areaid, $subareaid, $usersession, $restrict_level);
        if (!empty($outlets)) {
            $rowNum = 3;
            $no = 1;
            foreach ($outlets as $row) {
                $sheet1->setCellValue('A' . $rowNum, $no);
                $sheet1->setCellValue('B' . $rowNum, $row['customerid']);
                $sheet1->setCellValue('C' . $rowNum, $row['kode_outlet']);
                $sheet1->setCellValue('D' . $rowNum, $row['nama_customer']);
                $sheet1->setCellValue('E' . $rowNum, $row['channel']);
                $sheet1->setCellValue('F' . $rowNum, $row['telp']);
                $sheet1->setCellValue('G' . $rowNum, $row['email']);
                $sheet1->setCellValue('H' . $rowNum, $row['regional']);
                $sheet1->setCellValue('I' . $rowNum, $row['area']);
                $sheet1->setCellValue('J' . $rowNum, $row['sub_area']);
                $sheet1->setCellValue('K' . $rowNum, $row['alamat']);
                $sheet1->setCellValue('L' . $rowNum, $row['id_professional']);
                $sheet1->setCellValue('M' . $rowNum, $row['nama_professional']);
                $sheet1->setCellValue('N' . $rowNum, $row['spesialisasi_name']);
                $sheet1->setCellValue('O' . $rowNum, $row['tipe_user']);
                $rowNum++;
                $no++;
            }

            $sheet1->getStyle('A3:O' . ($rowNum - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
        }

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Referensi Data');
        
        $sheet2->setCellValue('A1', 'CHANNEL_ID');
        $sheet2->setCellValue('B1', 'NAMA_CHANNEL');
        $rowChan = 2;
        foreach ($channels as $chan) {
            $sheet2->setCellValue('A' . $rowChan, $chan['typeid']);
            $sheet2->setCellValue('B' . $rowChan, $chan['nama_type']);
            $rowChan++;
        }
        
        $sheet2->setCellValue('D1', 'REGIONAL_ID');
        $sheet2->setCellValue('E1', 'NAMA_REGIONAL');
        $sheet2->setCellValue('F1', 'AREA_ID');
        $sheet2->setCellValue('G1', 'NAMA_AREA');
        $sheet2->setCellValue('H1', 'SUB_AREA_ID');
        $sheet2->setCellValue('I1', 'NAMA_SUB_AREA');
        
        $rowLoc = 2;
        foreach ($locations as $loc) {
            $sheet2->setCellValue('D' . $rowLoc, $loc['regionalid']);
            $sheet2->setCellValue('E' . $rowLoc, $loc['nama_regional']);
            $sheet2->setCellValue('F' . $rowLoc, $loc['areaid']);
            $sheet2->setCellValue('G' . $rowLoc, $loc['nama_area']);
            $sheet2->setCellValue('H' . $rowLoc, $loc['subareaid']);
            $sheet2->setCellValue('I' . $rowLoc, $loc['nama_subarea']);
            $rowLoc++;
        }

        $sheet2->setCellValue('K1', 'SPESIALISASI_ID');
        $sheet2->setCellValue('L1', 'NAMA_SPESIALISASI');
        $rowSpec = 2;
        foreach ($specializations as $spec) {
            $sheet2->setCellValue('K' . $rowSpec, $spec['id']);
            $sheet2->setCellValue('L' . $rowSpec, $spec['name']);
            $rowSpec++;
        }

        $sheet2->setCellValue('N1', 'TIPE_USER');
        $rowType = 2;
        foreach ($prof_types as $type) {
            $sheet2->setCellValue('N' . $rowType, $type);
            $rowType++;
        }
        
        $sheet2->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet2->getStyle('D1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet2->getStyle('K1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet2->getStyle('N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $highestRowChan = $rowChan - 1;
        $highestRowLoc = $rowLoc - 1;
        $highestRowSpec = $rowSpec - 1;
        $highestRowType = $rowType - 1;

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet2->getStyle('A1:B' . $highestRowChan)->applyFromArray($borderStyle);
        $sheet2->getStyle('D1:I' . $highestRowLoc)->applyFromArray($borderStyle);
        $sheet2->getStyle('K1:L' . $highestRowSpec)->applyFromArray($borderStyle);
        $sheet2->getStyle('N1:N' . $highestRowType)->applyFromArray($borderStyle);

        foreach (range('A', 'N') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
        
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $filename = "Template_Upload_Outlet_User_" . date('Ymd_His') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function upload()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->db->save_queries = FALSE;

        $fileName = $_FILES['fileupload']['name'];
        $fileTmp  = $_FILES['fileupload']['tmp_name'];
        $fileSize = $_FILES['fileupload']['size'];
        $usersession = $this->input->post('usersession') ? $this->input->post('usersession') : 'Admin';

        if (!isset($fileTmp) || empty($fileTmp)) {
            responseJson(['status' => false, 'message' => "File tidak ditemukan"]);
            return;
        }

        $allowedExt = ['xls', 'xlsx'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            responseJson(['status' => false, 'message' => "File harus Excel (.xls atau .xlsx)"]);
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);

        $allowedMime = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!in_array($mime, $allowedMime)) {
            responseJson(['status' => false, 'message' => "Format file tidak valid"]);
            return;
        }

        if ($fileSize > 10 * 1024 * 1024) {
            responseJson(['status' => false, 'message' => "File terlalu besar (max 10MB)"]);
            return;
        }
        
        $restrict_level = $this->input->post('restrict_level');
        $result = $this->customer->import_excel($fileTmp, $usersession, $restrict_level);
        responseJson($result);
    }
}

