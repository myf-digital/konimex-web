<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Req_visit_professional extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Req_visit_professional_model', 'req_visit');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->req_visit->load($data));
    }

    public function load_salesman()
    {
        $data = param_input();
        responseJSON(['rows' => $this->req_visit->get_salesman($data)]);
    }

    public function get_detail()
    {
        $req_no = $this->input->post('req_no');
        if (empty($req_no)) {
            responseJSON(['status' => false, 'message' => 'Parameter req_no tidak valid']);
            return;
        }

        $detail = $this->req_visit->get_detail($req_no);
        if (!$detail) {
            responseJSON(['status' => false, 'message' => 'Data visit professional tidak ditemukan']);
            return;
        }

        // Parse visit_doc
        $images = [];
        $rawDoc = $detail['visit_doc'] ?? '';
        if (!empty($rawDoc)) {
            $parsed = is_string($rawDoc) ? json_decode($rawDoc, true) : $rawDoc;
            if (is_array($parsed)) {
                foreach ($parsed as $img) {
                    if (empty($img)) continue;
                    $url = (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) 
                        ? $img 
                        : rtrim(URL_IMAGE, '/') . '/' . ltrim($img, '/');
                    $images[] = [
                        'path' => $img,
                        'url'  => $url
                    ];
                }
            }
        }
        $detail['images'] = $images;

        responseJSON([
            'status' => true,
            'data'   => $detail
        ]);
    }

    public function download_excel()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $data = [
            'usersession'    => $this->input->get('usersession') ?? '',
            'restrict_level' => $this->input->get('restrict_level') ?? '',
            'start_date'     => $this->input->get('start_date'),
            'end_date'       => $this->input->get('end_date')
        ];

        $rows = $this->req_visit->get_export_data($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Request Visit Professional');

        $headers = [
            'A1' => 'NO',
            'B1' => 'PERIODE',
            'C1' => 'KODE SALESMAN',
            'D1' => 'NAMA SALESMAN',
            'E1' => 'KODE CUSTOMER',
            'F1' => 'NAMA CUSTOMER',
            'G1' => 'USER ID',
            'H1' => 'NAMA PROFESSIONAL',
            'I1' => 'KETERANGAN REQUEST',
            'J1' => 'STATUS',
            'K1' => 'KETERANGAN KUNJUNGAN',
            'L1' => 'DISETUJUI OLEH',
            'M1' => 'TGL DISETUJUI'
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

        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $dataRows = [];
        $no = 1;
        foreach ($rows as $r) {
            $dataRows[] = [
                $no++,
                $r['periode'],
                $r['salesmanid'],
                $r['nama_salesman'],
                $r['customerid'],
                $r['nama_customer'],
                $r['user_id'],
                $r['nama_professional'],
                $r['keterangan'],
                $r['status_desc'],
                $r['visit_keterangan'],
                $r['approved_by'],
                $r['approved_date']
            ];
        }

        if (!empty($dataRows)) {
            $sheet->fromArray($dataRows, NULL, 'A2');
            $highestRow = 1 + count($dataRows);
            $sheet->getStyle('A2:M' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E0E0E0']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            // Alignment formatting
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Laporan_Req_Visit_Professional_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
