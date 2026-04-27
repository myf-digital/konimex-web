<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Professional extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('professional_model', 'professional');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form_set_outlet()
    {
        $this->template->show($this, 'form_set_outlet');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->professional->load($data));
    }

    public function detail()
    {
        $data = param_input();
        responseJSON($this->professional->detail($data));
    }

    public function outlet()
    {
        $data = param_input();
        responseJSON($this->professional->outlet($data));
    }

    public function update()
    {
        $data = param_input();
        responseJSON($this->professional->update($data));
    }

    public function target()
    {
        $this->template->show($this, 'content_target');
    }

    public function load_target()
    {
        $data = param_input();
        responseJSON($this->professional->load_target($data));
    }

    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
    }

    public function history_upload()
    {
        $this->template->show($this, 'history_upload');
    }

    public function load_history()
    {
        $data = param_input();
        responseJSON($this->professional->load_history($data));
    }

    public function history_upload_detail()
    {
        $this->template->show($this, 'history_upload_detail');
    }

    public function load_history_detail()
    {
        $id = $this->input->get('id');
        $data = param_input();
        $data['id'] = $id;
        responseJSON($this->professional->load_history_detail($data));
    }

    public function download_template()
    {
        $filePath = FCPATH . 'assets/templates/Target_Professional_Template.xls';
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "Template file not found.";
        }
    }

    public function upload()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->db->save_queries = FALSE;

        $fileName = $_FILES['fileupload']['name'];
        $fileTmp  = $_FILES['fileupload']['tmp_name'];
        $fileSize = $_FILES['fileupload']['size'];

        if (!isset($fileTmp)) {
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

        if ($fileSize > 10 * 1024 * 1024) { // 10MB
            responseJson(['status' => false, 'message' => "File terlalu besar (max 10MB)"]);
            return;
        }
        
        $this->load->database();
        $this->db->trans_start();
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmp);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $header = $sheet[0];
            $header = array_values(array_filter($header, function ($h) {
                return !is_null($h) && $h !== '';
            }));

            $expected = [
                'CAB',
                'CABANG',
                'CUSTOMERID',
                'NAMA_CUSTOMER',
                'ID_PROFESSIONAL',
                'NAMA_PROFESSIONAL',
                'PRODUCTID',
                'NAMA_INVOICE',
                'SAT_KECIL',
                'QTY',
                'TOTAL',
            ];

            if (array_map('strtoupper', $header) !== $expected) {
                responseJSON(['status' => false, 'message' => 'Format header tidak sesuai']);
                return;
            }

            $uploadId = 'UPL-' . date('YmdHis');
            $periode = date('Y-m-d');

            $data = [];
            $batchSize = 500;
            $rowNumber = 0;

            $this->db->empty_table('target_professional');
            $this->db->where('periode', $periode);
            $this->db->delete('target_professional_history');
            foreach ($sheet as $index => $row) {
                if ($index == 0) continue;
                if (!isset($row[0], $row[1], $row[2])) continue;

                $data[] = [
                    'cab' => $row[0],
                    'cabang' => $row[1],
                    'customerid' => $row[2],
                    'nama_customer' => $row[3],
                    'id_professional' => $row[4],
                    'nama_professional' => $row[5],
                    'productid' => $row[6],
                    'nama_invoice' => $row[7],
                    'sat_kecil' => $row[8],
                    'qty' => $row[9],
                    'total' => $row[10],
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $rowNumber++;

                if (count($data) >= $batchSize) {
                    $this->professional->insert_batch_on_duplicate(
                        'target_professional',
                        $data,
                        ['cab', 'customerid', 'id_professional', 'productid']
                    );

                    $productData = [];
                    foreach ($data as $row) {
                        $productData[] = array_merge([
                            'idproduct' => $row['productid'],
                            'product_name' => $row['nama_invoice'],
                            'brandid' => 1,
                            'group_product' => 'KONIMEX',
                            'category_product' => 'OTC',
                            'status' => 'A',
                            'created_date' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    $this->professional->insert_batch_on_duplicate(
                        'ref_product',
                        $productData,
                        ['idproduct', 'product_name', 'brandid', 'group_product']
                    );

                    $historyData = [];
                    foreach ($data as $row) {
                        $historyData[] = array_merge($row, [
                            'upload_id' => $uploadId,
                            'periode' => $periode,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    $this->professional->insert_batch_on_duplicate('target_professional_history', $historyData);

                    $data = [];
                }
            }

            if (!empty($data)) {
                $this->professional->insert_batch_on_duplicate(
                    'target_professional',
                    $data,
                    ['cab', 'customerid', 'id_professional', 'productid']
                );

                $productData = [];
                foreach ($data as $d) {
                    $productData[] = array_merge([
                        'idproduct' => $d['productid'],
                        'product_name' => $d['nama_invoice'],
                        'brandid' => 1,
                        'group_product' => 'KONIMEX',
                        'category_product' => 'OTC',
                        'status' => 'A',
                        'created_date' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->professional->insert_batch_on_duplicate(
                    'ref_product',
                    $productData,
                    ['idproduct', 'product_name', 'brandid', 'group_product']
                );

                $historyData = [];
                foreach ($data as $row) {
                    $historyData[] = array_merge($row, [
                        'upload_id' => $uploadId,
                        'periode' => $periode,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->professional->insert_batch_on_duplicate('target_professional_history', $historyData);
            }

            if ($rowNumber > 0) {
                $this->db->where('created_at >=', $periode . ' 00:00:00');
                $this->db->where('created_at <=', $periode . ' 23:59:59');
                $this->db->delete('target_uploads');

                $this->db->insert('target_uploads', [
                    'id' => $uploadId,
                    'file_name' => $fileName,
                    'total_row' => $rowNumber,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $this->db->trans_complete();

            responseJson(['status' => true, 'message' => "Import selesai. Total row: {$rowNumber}", 'data' => $data]);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            responseJson(['status' => false, 'message' => "Error: " . $e->getMessage()]);
        }
    }

    public function savetoxlsx()
    {
		ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $data = [];
        $data['get_date1'] = $this->uri->segment('3');
        $data['get_date2'] = $this->uri->segment('4');

        $result = $this->professional->savetoxlsx($data);
        $filename = "data_pelanggan_" . date('Y-m-d_His');

        $maxCustomer = 0;
        $uniqueCustomers = [];
        foreach ($result as $row) {
            $customers = !empty($row['customer_list']) 
                ? explode('||', $row['customer_list']) 
                : [];

            $count = count($customers);
            if ($count > $maxCustomer) {
                $maxCustomer = $count;
            }

            foreach ($customers as $cust) {
                $parts = explode(' - ', $cust);

                $customerid = $parts[0] ?? '';
                $nama = $parts[1] ?? '';

                if (!empty($customerid)) {
                    $uniqueCustomers[$customerid] = $nama;
                }
            }
        }
        
        $customerIds = array_keys($uniqueCustomers);
        $outlets = [];
        if (!empty($customerIds)) {
            $this->db->where_in('a.customerid', $customerIds);
            $customers = $this->db
                ->select('a.*, b.nama_class, c.nama_regional, d.nama_area, e.nama_area as nama_subarea')
                ->from('m_customer a')
                ->join('m_customer_class b', 'b.classid = a.classid', 'left')
                ->join('m_area_regional c', 'c.regionalid = a.regionalid', 'left')
                ->join('m_area_areasite d', 'd.areaid = a.areaid', 'left')
                ->join('m_area_subarea e', 'e.subareaid = a.subareaid', 'left')
                ->get()
                ->result_array();

            foreach ($customers as $row) {
                $outlets[$row['customerid']] = $row;
            }
        }

        $spreadsheet = new Spreadsheet();
        $header = [
            'Id Pelanggan',
            'Pelanggan',
        ];
        for ($i = 1; $i <= $maxCustomer; $i++) {
            $header[] = 'Tempat Praktek ' . $i;
        }

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pelanggan');
        $sheet->setCellValue('A1', 'DATA PELANGGAN')->mergeCells('A1:B1');
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
        foreach ($result as $value) {
            $customers = !empty($value['customer_list']) 
                ? explode('||', $value['customer_list']) 
                : [];

            $content = [
                $value['id'],
                $value['nama_professional'],
            ];

            for ($j = 0; $j < $maxCustomer; $j++) {
                $content[] = $customers[$j] ?? '';
            }

            $sheet->fromArray($content, NULL, 'A'.$rowNum);

            $i++;
            $rowNum++;
        }
		// Ambil range seluruh worksheet
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->freezePane('A3');
        $sheet->freezePane('C3');

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

        $spreadsheet->createSheet();
        $sheet2 = $spreadsheet->setActiveSheetIndex(1);
        $sheet2->setTitle('Outlet');

        $sheet2->setCellValue('A1', 'DATA OUTLET')->mergeCells('A1:F1');
        $sheet2->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet2->fromArray([
            'Outlet ID',
            'Nama Outlet',
            'Regional',
            'Area',
            'Sub Area',
            'Alamat',
        ], NULL, 'A2');
        $sheet2->getRowDimension(2)->setRowHeight(25);

        $sheet2->getStyle('A2:F2')->applyFromArray([
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

        $rowNum = 3;
        foreach ($outlets as $ot) {
            $sheet2->fromArray([
                $ot['customerid'] ?? '',
                $ot['nama_customer'] ?? '',
                $ot['nama_regional'] ?? '',
                $ot['nama_area'] ?? '',
                $ot['nama_subarea'] ?? '',
                $ot['alamat'] ?? '',
            ], NULL, 'A'.$rowNum);
            $rowNum++;
        }

        $highestRow2 = $sheet2->getHighestRow();
        $highestCol2 = $sheet2->getHighestColumn();

        $fullRange2 = 'A1:' . $highestCol2 . $highestRow2;
        $sheet2->getStyle($fullRange2)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet2->freezePane('A3');

        foreach (range('A', $highestCol2) as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
 
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
