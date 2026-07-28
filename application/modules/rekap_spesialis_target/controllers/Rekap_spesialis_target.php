<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Rekap_spesialis_target extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rekap_spesialis_target_model', 'rekap');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $param = param_input();
        $data = [
            'start_date' => $param['start_date'] ?? date('Y-m-01'),
            'end_date' => $param['end_date'] ?? date('Y-m-d'),
            'spesialisasi_ids' => !empty($param['spesialisasi_ids']) ? $param['spesialisasi_ids'] : [],
            'usersession' => $param['usersession'] ?? "",
            'restrict_level' => $param['restrict_level'] ?? 0,
        ];

        $result = $this->rekap->load_summary($data);
        responseJSON([
            'status' => true,
            'data' => $result
        ]);
    }

    public function load_detail()
    {
        $param = param_input();
        $data = [
            'start_date' => $param['start_date'] ?? date('Y-m-01'),
            'end_date' => $param['end_date'] ?? date('Y-m-d'),
            'spesialisasi_ids' => !empty($param['spesialisasi_ids']) ? $param['spesialisasi_ids'] : [],
            'usersession' => $param['usersession'] ?? "",
            'restrict_level' => $param['restrict_level'] ?? 0,
            'spesialisasi_id' => $param['spesialisasi_id'] ?? 0,
        ];

        if (empty($param['spesialisasi_id'])) {
            responseJSON(['status' => false, 'message' => 'Spesialisasi ID tidak valid.']);
            return;
        }

        $result = $this->rekap->load_detail($data);
        responseJSON([
            'status' => true,
            'data' => $result
        ]);
    }

    public function export()
    {
        $spesialisasiIds = $this->input->get('spesialisasi_ids') ?? null;
        $data = [
            'start_date' => $this->input->get('start_date') ?? date('Y-m-01'),
            'end_date' => $this->input->get('end_date') ?? date('Y-m-d'),
            'spesialisasi_ids' => !empty($spesialisasiIds) ? explode(',', $spesialisasiIds) : [],
            'usersession' => $this->input->get('usersession') ?? "",
            'restrict_level' => $this->input->get('restrict_level') ?? 0,
        ];

        $result = $this->rekap->load_summary($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Spesialis Target');

        $sheet->setCellValue('A1', 'No')
              ->setCellValue('B1', 'Spesialisasi ID')
              ->setCellValue('C1', 'Nama Spesialisasi')
              ->setCellValue('D1', 'Target')
              ->setCellValue('E1', 'Realisasi')
              ->setCellValue('F1', 'Pencapaian (%)');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;
        $no = 1;
        foreach ($result as $r) {
            $target = (int)$r['target'];
            $actual = (int)$r['actual'];
            $pct = $target > 0 ? round(($actual / $target) * 100) : 0;

            $sheet->setCellValue("A{$row}", $no)
                  ->setCellValue("B{$row}", $r['spesialisasi_id'])
                  ->setCellValue("C{$row}", $r['nama_spesialisasi'])
                  ->setCellValue("D{$row}", $target)
                  ->setCellValue("E{$row}", $actual)
                  ->setCellValue("F{$row}", $pct);

            $row++;
            $no++;
        }

        $sheet->getStyle("A1:F" . ($row - 1))->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Detail Visit');

        $detailSheet->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Tanggal')
                    ->setCellValue('C1', 'TPE ID')
                    ->setCellValue('D1', 'Nama TPE')
                    ->setCellValue('E1', 'Spesialisasi ID')
                    ->setCellValue('F1', 'Nama Spesialisasi')
                    ->setCellValue('G1', 'Customer ID')
                    ->setCellValue('H1', 'Nama Customer')
                    ->setCellValue('I1', 'Nama Dokter')
                    ->setCellValue('J1', 'Check In')
                    ->setCellValue('K1', 'Check Out')
                    ->setCellValue('L1', 'Durasi')
                    ->setCellValue('M1', 'Keterangan');

        $detailSheet->getStyle('A1:M1')->getFont()->setBold(true);
        $detailSheet->getStyle('A1:M1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $details = $this->rekap->load_all_detail($data);
        $dRow = 2;
        $dNo = 1;
        foreach ($details as $d) {
            $detailSheet->setCellValue("A{$dRow}", $dNo)
                        ->setCellValue("B{$dRow}", $d['tanggal'])
                        ->setCellValue("C{$dRow}", $d['salesmanid'])
                        ->setCellValue("D{$dRow}", $d['nama_salesman'])
                        ->setCellValue("E{$dRow}", $d['spesialisasi_id'])
                        ->setCellValue("F{$dRow}", $d['nama_spesialisasi'])
                        ->setCellValue("G{$dRow}", $d['customerid'])
                        ->setCellValue("H{$dRow}", $d['nama_customer'])
                        ->setCellValue("I{$dRow}", $d['nama_dokter'])
                        ->setCellValue("J{$dRow}", $d['check_in'])
                        ->setCellValue("K{$dRow}", $d['check_out'])
                        ->setCellValue("L{$dRow}", $d['duration'])
                        ->setCellValue("M{$dRow}", $d['keterangan']);
            $dRow++;
            $dNo++;
        }

        $detailSheet->getStyle("A1:M" . ($dRow - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'M') as $col) {
            $detailSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Rekap_Spesialis_Target_{$data['start_date']}_to_{$data['end_date']}";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
