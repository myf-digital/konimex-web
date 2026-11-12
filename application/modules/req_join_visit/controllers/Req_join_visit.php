<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Req_join_visit extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Req_join_visit_model', 'req_join');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->req_join->load($data));
    }

    public function load_salesman()
    {
        $data = param_input();
        responseJSON(['rows' => $this->req_join->get_salesman($data)]);
    }

    public function get_detail()
    {
        $req_no = $this->input->post('req_no');
        if (empty($req_no)) {
            responseJSON(['status' => false, 'message' => 'Parameter req_no tidak valid']);
            return;
        }

        $detail = $this->req_join->get_detail($req_no);
        if (!$detail) {
            responseJSON(['status' => false, 'message' => 'Data join visit tidak ditemukan']);
            return;
        }

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
            'usersession'    => trim((string) ($this->input->get('usersession') ?? ''), " \t\n\r\0\x0B/"),
            'restrict_level' => trim((string) ($this->input->get('restrict_level') ?? ''), " \t\n\r\0\x0B/"),
            'start_date'     => trim((string) ($this->input->get('start_date') ?? ''), " \t\n\r\0\x0B/"),
            'end_date'       => trim((string) ($this->input->get('end_date') ?? ''), " \t\n\r\0\x0B/"),
            'salesmanid'     => trim((string) ($this->input->get('salesmanid') ?? ''), " \t\n\r\0\x0B/"),
            'filter_status'  => trim((string) ($this->input->get('filter_status') ?? ''), " \t\n\r\0\x0B/"),
        ];

        $rows = $this->req_join->get_export_data($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report Join Visit');

        $headers = [
            'A1' => 'NO',
            'B1' => 'REQ NO',
            'C1' => 'PERIODE',
            'D1' => 'KODE PENGAJU',
            'E1' => 'NAMA PENGAJU',
            'F1' => 'KODE TPE PESERTA',
            'G1' => 'NAMA TPE PESERTA',
            'H1' => 'KODE CUSTOMER',
            'I1' => 'NAMA CUSTOMER',
            'J1' => 'USER ID',
            'K1' => 'NAMA PROFESSIONAL',
            'L1' => 'KETERANGAN REQUEST',
            'M1' => 'STATUS APPROVAL',
            'N1' => 'STATUS REALISASI',
            'O1' => 'PRODUK DETAILING',
            'P1' => 'MULAI DETAILING',
            'Q1' => 'SELESAI DETAILING',
            'R1' => 'KETERANGAN DETAILING',
            'S1' => 'DISETUJUI / DIREVIEW OLEH',
            'T1' => 'TGL REVIEW'
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

        $sheet->getStyle('A1:T1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $dataRows = [];
        $no = 1;
        foreach ($rows as $r) {
            $approver = !empty($r['modified_by'])
                ? (!empty($r['nama_approver']) && $r['nama_approver'] !== $r['modified_by']
                    ? $r['modified_by'] . ' - ' . $r['nama_approver']
                    : $r['modified_by'])
                : '';

            $dataRows[] = [
                $no++,
                $r['req_no'],
                $r['periode'],
                $r['created_by'],
                $r['nama_pengaju'],
                $r['salesmanid'],
                $r['nama_salesman'],
                $r['customerid'],
                $r['nama_customer'],
                $r['user_id'],
                $r['nama_professional'],
                $r['keterangan'],
                $r['status_desc'],
                $r['status_realisasi'],
                $r['products'],
                $r['start_detailing'],
                $r['end_detailing'],
                $r['visit_keterangan'],
                $approver,
                $r['modified_date']
            ];
        }

        if (!empty($dataRows)) {
            $sheet->fromArray($dataRows, NULL, 'A2', true);
            $highestRow = 1 + count($dataRows);
            $sheet->getStyle('A2:T' . $highestRow)->applyFromArray([
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

            $sheet->getStyle('A2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M2:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Laporan_Req_Join_Visit_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

