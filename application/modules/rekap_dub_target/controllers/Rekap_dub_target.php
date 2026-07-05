<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Rekap_dub_target extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rekap_dub_target_model', 'rekap');
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
            'salesman_ids' => !empty($param['salesman_ids']) ? $param['salesman_ids'] : [],
            'usersession' => $param['usersession'],
            'restrict_level' => $param['restrict_level'],
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
            'salesmanid' => $param['salesmanid'] ?? '',
            'start_date' => $param['start_date'] ?? date('Y-m-01'),
            'end_date' => $param['end_date'] ?? date('Y-m-d'),
            'usersession' => $param['usersession'] ?? "",
            'restrict_level' => $param['restrict_level'] ?? 0
        ];

        if (empty($data['salesmanid'])) {
            responseJSON(['status' => false, 'message' => 'Salesman ID tidak valid.']);
            return;
        }

        $data = $this->rekap->load_detail($data);
        responseJSON([
            'status' => true,
            'data' => $data
        ]);
    }

    public function export()
    {
        $salesmanIds = $this->input->get('salesman_ids') ?? null;
        $data = [
            'start_date' => $this->input->get('start_date') ?? date('Y-m-01'),
            'end_date' => $this->input->get('end_date') ?? date('Y-m-d'),
            'usersession' => $this->input->get('usersession') ?? "",
            'restrict_level' => $this->input->get('restrict_level') ?? 0,
            'salesman_ids' => !empty($salesmanIds) ? explode(',', $salesmanIds) : [],
        ];

        $result = $this->rekap->load_summary($data);

        if (empty($data['salesman_ids']) && !empty($data['restrict_level'])) {
            $restrict_query = get_salesman_restrict($data['usersession'], $data['restrict_level']);
            if ($restrict_query) {
                $query = $this->db->query($restrict_query);
                if ($query) {
                    $data['salesman_ids'] = array_column($query->result_array(), 'salesmanid');
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap DUB Target');

        $sheet->setCellValue('A1', 'No')
              ->setCellValue('B1', 'Salesman ID')
              ->setCellValue('C1', 'Nama Salesman')
              ->setCellValue('D1', 'Tipe Sales')
              ->setCellValue('E1', 'Target DUB')
              ->setCellValue('F1', 'Actual DUB (Planned)')
              ->setCellValue('G1', 'Pencapaian DUB (%)')
              ->setCellValue('H1', 'Target Visit')
              ->setCellValue('I1', 'Actual Visit')
              ->setCellValue('J1', 'Pencapaian Visit (%)');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;
        $no = 1;
        foreach ($result as $r) {
            $target_dub = (int)$r['target_dub'];
            $act_planned = (int)$r['actual_call_planned'];
            $pct_dub = $target_dub > 0 ? round(($act_planned / $target_dub) * 100) : 0;

            $target_visit = (int)$r['target_call_visit'];
            $act_visit = (int)$r['actual_call_visit'];
            $pct_visit = $target_visit > 0 ? round(($act_visit / $target_visit) * 100) : 0;

            $sheet->setCellValue("A{$row}", $no)
                  ->setCellValue("B{$row}", $r['salesmanid'])
                  ->setCellValue("C{$row}", $r['nama_salesman'])
                  ->setCellValue("D{$row}", $r['tipe_sales'])
                  ->setCellValue("E{$row}", $target_dub)
                  ->setCellValue("F{$row}", $act_planned)
                  ->setCellValue("G{$row}", $pct_dub)
                  ->setCellValue("H{$row}", $target_visit)
                  ->setCellValue("I{$row}", $act_visit)
                  ->setCellValue("J{$row}", $pct_visit);

            $row++;
            $no++;
        }

        $sheet->getStyle("A1:J" . ($row - 1))->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Detail Visit');

        $detailSheet->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Tanggal')
                    ->setCellValue('C1', 'Salesman ID')
                    ->setCellValue('D1', 'Nama Salesman')
                    ->setCellValue('E1', 'Tipe Sales')
                    ->setCellValue('F1', 'Customer ID')
                    ->setCellValue('G1', 'Nama Customer')
                    ->setCellValue('H1', 'Tipe Customer')
                    ->setCellValue('I1', 'Nama Dokter')
                    ->setCellValue('J1', 'Check In')
                    ->setCellValue('K1', 'Check Out')
                    ->setCellValue('L1', 'Durasi')
                    ->setCellValue('M1', 'Jenis Visit')
                    ->setCellValue('N1', 'Detailing Produk')
                    ->setCellValue('O1', 'Keterangan');

        $detailSheet->getStyle('A1:O1')->getFont()->setBold(true);
        $detailSheet->getStyle('A1:O1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $details = $this->rekap->load_all_detail($data);
        $dRow = 2;
        $dNo = 1;
        foreach ($details as $d) {
            $is_planned = (int)$d['is_planned'] === 1 ? 'Planned' : 'Unplanned';
            $detailSheet->setCellValue("A{$dRow}", $dNo)
                        ->setCellValue("B{$dRow}", $d['tanggal'])
                        ->setCellValue("C{$dRow}", $d['salesmanid'])
                        ->setCellValue("D{$dRow}", $d['nama_salesman'])
                        ->setCellValue("E{$dRow}", $d['tipe_sales'])
                        ->setCellValue("F{$dRow}", $d['customerid'])
                        ->setCellValue("G{$dRow}", $d['nama_customer'])
                        ->setCellValue("H{$dRow}", $d['typeid'])
                        ->setCellValue("I{$dRow}", $d['nama_dokter'])
                        ->setCellValue("J{$dRow}", $d['check_in'])
                        ->setCellValue("K{$dRow}", $d['check_out'])
                        ->setCellValue("L{$dRow}", $d['duration'])
                        ->setCellValue("M{$dRow}", $is_planned)
                        ->setCellValue("N{$dRow}", $d['array_product'])
                        ->setCellValue("O{$dRow}", $d['keterangan']);
            $dRow++;
            $dNo++;
        }

        $detailSheet->getStyle("A1:O" . ($dRow - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'O') as $col) {
            $detailSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Rekap_DUB_Target_{$data['start_date']}_to_{$data['end_date']}";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
