<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Ref_product extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model', 'product');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
    }

    public function create()
    {
        $data = param_input();
        response($this->product->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->product->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->product->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->product->load($data));
    }

    public function list()
    {
        $data = param_input();
        responseJSON($this->product->list($data));
    }

    public function download_template()
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Product');

        $headers = [
            'A1' => 'KODE_PRODUK',
            'B1' => 'BARCODE',
            'C1' => 'NAMA_PRODUK',
            'D1' => 'GROUP_PRODUK',
            'E1' => 'KATEGORI_PRODUK',
            'F1' => 'BRAND',
            'G1' => 'HJP',
            'H1' => 'HNA',
            'I1' => 'STATUS'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A65A']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Data');

        $refData = $this->product->get_reference_data();
        $groups = $refData['groups'] ?? [];
        $categories = $refData['categories'] ?? [];
        $brands = $refData['brands'] ?? [];

        $refSheet->setCellValue('A1', 'DAFTAR REFERENSI DATA MASTER PRODUK');
        $refSheet->mergeCells('A1:I1');
        $refSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '333333']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $refSheet->getRowDimension(1)->setRowHeight(28);

        $refHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0073B7']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '004C7A']
                ]
            ]
        ];

        $refDataBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        $refSheet->setCellValue('A3', 'GROUP PRODUK');
        $refSheet->getStyle('A3')->applyFromArray($refHeaderStyle);
        $refSheet->getRowDimension(3)->setRowHeight(22);

        $rowG = 4;
        foreach ($groups as $g) {
            $refSheet->setCellValue('A' . $rowG, $g['name']);
            $refSheet->getStyle('A' . $rowG)->applyFromArray($refDataBorder);
            $rowG++;
        }

        $refSheet->setCellValue('C3', 'KATEGORI PRODUK');
        $refSheet->getStyle('C3')->applyFromArray($refHeaderStyle);

        $rowC = 4;
        foreach ($categories as $c) {
            $refSheet->setCellValue('C' . $rowC, $c['name']);
            $refSheet->getStyle('C' . $rowC)->applyFromArray($refDataBorder);
            $rowC++;
        }

        $refSheet->setCellValue('E3', 'BRAND ID');
        $refSheet->setCellValue('F3', 'NAMA BRAND');
        $refSheet->getStyle('E3:F3')->applyFromArray($refHeaderStyle);

        $rowB = 4;
        foreach ($brands as $b) {
            $refSheet->setCellValue('E' . $rowB, $b['brandid']);
            $refSheet->setCellValue('F' . $rowB, $b['name']);
            $refSheet->getStyle('E' . $rowB . ':F' . $rowB)->applyFromArray($refDataBorder);
            $refSheet->getStyle('E' . $rowB)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rowB++;
        }

        $refSheet->setCellValue('H3', 'KODE STATUS');
        $refSheet->setCellValue('I3', 'KETERANGAN STATUS');
        $refSheet->getStyle('H3:I3')->applyFromArray($refHeaderStyle);

        $statuses = [
            ['code' => 'ACTIVE', 'desc' => 'Aktif (Produk Aktif)'],
            ['code' => 'DISCONTINUE', 'desc' => 'Tidak Aktif / Diskontinu']
        ];
        $rowS = 4;
        foreach ($statuses as $s) {
            $refSheet->setCellValue('H' . $rowS, $s['code']);
            $refSheet->setCellValue('I' . $rowS, $s['desc']);
            $refSheet->getStyle('H' . $rowS . ':I' . $rowS)->applyFromArray($refDataBorder);
            $refSheet->getStyle('H' . $rowS)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rowS++;
        }

        foreach (['A', 'C', 'E', 'F', 'H', 'I'] as $col) {
            $refSheet->getColumnDimension($col)->setAutoSize(true);
        }
        $refSheet->getColumnDimension('B')->setWidth(4);
        $refSheet->getColumnDimension('D')->setWidth(4);
        $refSheet->getColumnDimension('G')->setWidth(4);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Template_Product_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function download_excel()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $products = $this->product->list();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Product');

        $headers = [
            'A1' => 'KODE_PRODUK',
            'B1' => 'BARCODE',
            'C1' => 'NAMA_PRODUK',
            'D1' => 'GROUP_PRODUK',
            'E1' => 'KATEGORI_PRODUK',
            'F1' => 'BRAND',
            'G1' => 'HJP',
            'H1' => 'HNA',
            'I1' => 'STATUS'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0073B7']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $dataRows = [];
        foreach ($products as $row) {
            $dataRows[] = [
                $row['productid'],
                $row['barcode'],
                $row['nama_invoice'],
                $row['group_product'],
                $row['category_product'],
                $row['nama_brand'],
                (float) ($row['h_grosir'] ?? 0),
                (float) ($row['h_ritel'] ?? 0),
                $row['status_desc'] ?? ($row['status'] == 'A' ? 'ACTIVE' : 'DISCONTINUE')
            ];
        }

        if (!empty($dataRows)) {
            $sheet->fromArray($dataRows, NULL, 'A2');
            $highestRow = 1 + count($dataRows);
            $sheet->getStyle('A2:I' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E0E0E0']
                    ]
                ]
            ]);
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Data_Product_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function upload()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->db->save_queries = FALSE;

        if (!isset($_FILES['fileupload']) || empty($_FILES['fileupload']['tmp_name'])) {
            responseJSON(['status' => false, 'message' => "File tidak ditemukan"]);
            return;
        }

        $fileName = $_FILES['fileupload']['name'];
        $fileTmp  = $_FILES['fileupload']['tmp_name'];
        $fileSize = $_FILES['fileupload']['size'];

        $allowedExt = ['xls', 'xlsx'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            responseJSON(['status' => false, 'message' => "File harus berekstensi Excel (.xls atau .xlsx)"]);
            return;
        }

        if ($fileSize > 10 * 1024 * 1024) {
            responseJSON(['status' => false, 'message' => "Ukuran file terlalu besar (maksimal 10MB)"]);
            return;
        }

        $brandRows = $this->product->get_all_brands();
        $brandMap = [];
        $defaultBrandId = 1;
        $defaultBrandName = 'Konimex';
        foreach ($brandRows as $b) {
            $brandMap[strtoupper(trim($b['brand']))] = [
                'brandid' => $b['brandid'],
                'nama_brand' => $b['brand']
            ];
            $brandMap[strtoupper(trim($b['brandid']))] = [
                'brandid' => $b['brandid'],
                'nama_brand' => $b['brand']
            ];
            if ($b['brandid'] == 1) {
                $defaultBrandId = $b['brandid'];
                $defaultBrandName = $b['brand'];
            }
        }

        $this->load->database();
        $this->db->trans_start();

        try {
            $spreadsheet = IOFactory::load($fileTmp);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            if (empty($sheet) || count($sheet) < 2) {
                responseJSON(['status' => false, 'message' => "File excel kosong atau tidak memiliki data"]);
                return;
            }

            $expectedHeaders = [
                'KODE_PRODUK',
                'BARCODE',
                'NAMA_PRODUK',
                'GROUP_PRODUK',
                'KATEGORI_PRODUK',
                'BRAND',
                'HJP',
                'HNA',
                'STATUS'
            ];

            $headerRow = array_map(function($val) {
                return strtoupper(trim((string)$val));
            }, $sheet[0] ?? []);

            $isValidHeader = true;
            foreach ($expectedHeaders as $colIdx => $expectedName) {
                if (!isset($headerRow[$colIdx]) || $headerRow[$colIdx] !== $expectedName) {
                    $isValidHeader = false;
                    break;
                }
            }

            if (!$isValidHeader) {
                responseJSON(['status' => false, 'message' => "Format header kolom tidak sesuai template. Pastikan header sesuai: " . implode(', ', $expectedHeaders)]);
                return;
            }

            $batchData = [];
            $batchSize = 500;
            $param = param_input();
            $username = !empty($param['usersession']) ? $param['usersession'] : 'Admin';

            $totalProcessed = 0;
            for ($i = 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                $productid = trim((string)($row[0] ?? ''));
                if (empty($productid)) continue;

                $barcode = trim((string)($row[1] ?? ''));
                $nama_invoice = trim((string)($row[2] ?? ''));
                $group_product = trim((string)($row[3] ?? '')) ?: 'Konimex';
                $category_product = trim((string)($row[4] ?? ''));
                $brandInput = strtoupper(trim((string)($row[5] ?? '')));
                
                $brandid = $defaultBrandId;
                $nama_brand = $defaultBrandName;
                if (!empty($brandInput) && isset($brandMap[$brandInput])) {
                    $brandid = $brandMap[$brandInput]['brandid'];
                    $nama_brand = $brandMap[$brandInput]['nama_brand'];
                }

                $h_grosir = (float)str_replace(',', '.', (string)($row[6] ?? 0));
                $h_ritel = (float)str_replace(',', '.', (string)($row[7] ?? 0));

                $statusInput = strtoupper(trim((string)($row[8] ?? 'A')));
                $status = ($statusInput === 'ACTIVE' || $statusInput === 'A') ? 'A' : 'D';

                $batchData[] = [
                    'productid' => $productid,
                    'barcode' => $barcode,
                    'nama_invoice' => $nama_invoice,
                    'group_product' => $group_product,
                    'category_product' => $category_product,
                    'brandid' => $brandid,
                    'nama_brand' => $nama_brand,
                    'h_grosir' => $h_grosir,
                    'h_ritel' => $h_ritel,
                    'status' => $status,
                    'modified_by' => $username,
                    'modified_date' => date('Y-m-d H:i:s')
                ];

                $totalProcessed++;

                if (count($batchData) >= $batchSize) {
                    $this->product->insert_batch_on_duplicate(
                        'm_product',
                        $batchData,
                        ['barcode', 'nama_invoice', 'group_product', 'category_product', 'brandid', 'nama_brand', 'h_grosir', 'h_ritel', 'status', 'modified_by', 'modified_date']
                    );
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                $this->product->insert_batch_on_duplicate(
                    'm_product',
                    $batchData,
                    ['barcode', 'nama_invoice', 'group_product', 'category_product', 'brandid', 'nama_brand', 'h_grosir', 'h_ritel', 'status', 'modified_by', 'modified_date']
                );
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                responseJSON(['status' => false, 'message' => "Terjadi kesalahan saat menyimpan data ke database"]);
            } else {
                responseJSON([
                    'status' => true,
                    'message' => "Berhasil mengimpor {$totalProcessed} data produk!"
                ]);
            }
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            responseJSON(['status' => false, 'message' => "Error saat membaca file: " . $e->getMessage()]);
        }
    }
}
