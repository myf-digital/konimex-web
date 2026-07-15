<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_product extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_product_model', 'stock_product');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->stock_product->load($data));
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
        responseJSON($this->stock_product->load_history($data));
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
        responseJSON($this->stock_product->load_history_detail($data));
    }

    public function download_template()
    {
        $filePath = FCPATH . 'assets/templates/Laporan_Stok_Template.xlsx';
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
                'NAMA_CABANG',
                'KODE PRODUCT PRINCIPAL',
                'NAMA PRODUCT',
                'EXPIRED_DATE',
                'BATCH NUM',
                'QTY RUSAK',
                'QTY BAIK',
                'QTY TOTAL'
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

            $this->db->empty_table('stock_product');
            $this->db->where('periode', $periode);
            $this->db->delete('stock_product_history');
            foreach ($sheet as $index => $row) {
                if ($index == 0) continue;
                if (!isset($row[0], $row[1], $row[2])) continue;

                $data[] = [
                    'nama_cabang' => $row[0],
                    'kode_product_principal' => $row[1],
                    'nama_product' => $row[2],
                    'expired_date' => $row[3],
                    'batch_num' => $row[4],
                    'qty_rusak' => $row[5],
                    'qty_baik' => $row[6],
                    'qty_total' => $row[7],
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                $rowNumber++;

                if (count($data) >= $batchSize) {
                    $this->stock_product->insert_batch_on_duplicate(
                        'stock_product',
                        $data,
                        ['nama_cabang', 'kode_product_principal', 'batch_num', 'expired_date']
                    );

                    $productData = [];
                    foreach ($data as $row) {
                        $productData[] = array_merge([
                            'idproduct' => $row['kode_product_principal'],
                            'product_name' => $row['nama_product'],
                            'brandid' => 1,
                            'group_product' => 'HIMALAYA',
                            'category_product' => 'OTC',
                            'status' => 'A',
                            'created_date' => date('Y-m-d H:i:s'),
                        ]);
                    }
                    $this->stock_product->insert_batch_on_duplicate(
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
                    $this->stock_product->insert_batch_on_duplicate('stock_product_history', $historyData);

                    $data = [];
                }
            }

            if (!empty($data)) {
                $this->stock_product->insert_batch_on_duplicate(
                    'stock_product',
                    $data,
                    ['nama_cabang', 'kode_product_principal', 'batch_num', 'expired_date']
                );

                $productData = [];
                foreach ($data as $d) {
                    $productData[] = array_merge([
                        'idproduct' => $d['kode_product_principal'],
                        'product_name' => $d['nama_product'],
                        'brandid' => 1,
                        'group_product' => 'HIMALAYA',
                        'category_product' => 'OTC',
                        'status' => 'A',
                        'created_date' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->stock_product->insert_batch_on_duplicate(
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
                $this->stock_product->insert_batch_on_duplicate('stock_product_history', $historyData);
            }

            if ($rowNumber > 0) {
                $this->db->where('created_at >=', $periode . ' 00:00:00');
                $this->db->where('created_at <=', $periode . ' 23:59:59');
                $this->db->delete('stock_uploads');

                $this->db->insert('stock_uploads', [
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
}
