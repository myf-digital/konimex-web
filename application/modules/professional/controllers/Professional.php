<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

    public function load()
    {
        $data = param_input();
        responseJSON($this->professional->load($data));
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
}
