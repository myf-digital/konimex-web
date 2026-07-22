<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Rekap_produk_target extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rekap_produk_target_model', 'rekap');
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
            'product_ids' => !empty($param['product_ids']) ? explode(',', $param['product_ids']) : [],
            'usersession' => $param['usersession'] ?? "",
            'restrict_level' => $param['restrict_level'] ?? 0
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
            'product_id' => $param['product_id'] ?? '',
            'start_date' => $param['start_date'] ?? date('Y-m-01'),
            'end_date' => $param['end_date'] ?? date('Y-m-d'),
            'product_ids' => !empty($param['product_ids']) ? explode(',', $param['product_ids']) : [],
            'usersession' => $param['usersession'] ?? "",
            'restrict_level' => $param['restrict_level'] ?? 0,
        ];

        if (empty($data['product_id'])) {
            responseJSON(['status' => false, 'message' => 'Product ID tidak valid.']);
            return;
        }

        $visits = $this->rekap->load_detail_visit($data);
        $sales = $this->rekap->load_detail_sales($data);

        responseJSON([
            'status' => true,
            'visits' => $visits,
            'sales' => $sales
        ]);
    }

    public function export()
    {
        $productIds = $this->input->get('product_ids') ?? null;
        $data = [
            'start_date' => $this->input->get('start_date') ?? date('Y-m-01'),
            'end_date' => $this->input->get('end_date') ?? date('Y-m-d'),
            'product_ids' => !empty($productIds) ? explode(',', $productIds) : [],
            'usersession' => $this->input->get('usersession') ?? "",
            'restrict_level' => $this->input->get('restrict_level') ?? 0,
        ];

        $result = $this->rekap->load_summary($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Produk Target');

        $sheet->setCellValue('A1', 'No')
              ->setCellValue('B1', 'Product ID')
              ->setCellValue('C1', 'Nama Produk')
              ->setCellValue('D1', 'Target Visit')
              ->setCellValue('E1', 'Realisasi Visit')
              ->setCellValue('F1', 'Pencapaian Visit (%)')
              ->setCellValue('G1', 'Target Qty (pcs)')
              ->setCellValue('H1', 'Realisasi Qty (pcs)')
              ->setCellValue('I1', 'Pencapaian Qty (%)');

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;
        $no = 1;
        foreach ($result as $r) {
            $target_visit = (int)$r['target'];
            $act_visit = (int)$r['actual_visit'];
            $pct_visit = $target_visit > 0 ? round(($act_visit / $target_visit) * 100) : 0;

            $target_qty = (int)$r['target_qty'];
            $act_qty = (int)$r['actual_qty'];
            $pct_qty = $target_qty > 0 ? round(($act_qty / $target_qty) * 100) : 0;

            $sheet->setCellValue("A{$row}", $no)
                  ->setCellValue("B{$row}", $r['product_id'])
                  ->setCellValue("C{$row}", $r['nama_invoice'])
                  ->setCellValue("D{$row}", $target_visit)
                  ->setCellValue("E{$row}", $act_visit)
                  ->setCellValue("F{$row}", $pct_visit)
                  ->setCellValue("G{$row}", $target_qty)
                  ->setCellValue("H{$row}", $act_qty)
                  ->setCellValue("I{$row}", $pct_qty);

            $row++;
            $no++;
        }

        $sheet->getStyle("A1:I" . ($row - 1))->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $detailVisitSheet = $spreadsheet->createSheet();
        $detailVisitSheet->setTitle('Detail Visit');

        $detailVisitSheet->setCellValue('A1', 'No')
                         ->setCellValue('B1', 'Tanggal')
                         ->setCellValue('C1', 'TPE ID')
                         ->setCellValue('D1', 'Nama TPE')
                         ->setCellValue('E1', 'Product ID')
                         ->setCellValue('F1', 'Nama Produk')
                         ->setCellValue('G1', 'Customer ID')
                         ->setCellValue('H1', 'Nama Customer')
                         ->setCellValue('I1', 'Check In')
                         ->setCellValue('J1', 'Check Out')
                         ->setCellValue('K1', 'Durasi')
                         ->setCellValue('L1', 'Keterangan');

        $detailVisitSheet->getStyle('A1:L1')->getFont()->setBold(true);
        $detailVisitSheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $visits = $this->rekap->load_all_detail_visit($data);
        $vRow = 2;
        $vNo = 1;
        foreach ($visits as $v) {
            $detailVisitSheet->setCellValue("A{$vRow}", $vNo)
                             ->setCellValue("B{$vRow}", $v['tanggal'])
                             ->setCellValue("C{$vRow}", $v['salesmanid'])
                             ->setCellValue("D{$vRow}", $v['nama_salesman'])
                             ->setCellValue("E{$vRow}", $v['product_id'])
                             ->setCellValue("F{$vRow}", $v['nama_invoice'])
                             ->setCellValue("G{$vRow}", $v['customerid'])
                             ->setCellValue("H{$vRow}", $v['nama_customer'])
                             ->setCellValue("I{$vRow}", $v['check_in'])
                             ->setCellValue("J{$vRow}", $v['check_out'])
                             ->setCellValue("K{$vRow}", $v['duration'])
                             ->setCellValue("L{$vRow}", $v['keterangan']);
            $vRow++;
            $vNo++;
        }

        $detailVisitSheet->getStyle("A1:L" . ($vRow - 1))->getBorders()->getAllBorders()
                         ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'L') as $col) {
            $detailVisitSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $detailSalesSheet = $spreadsheet->createSheet();
        $detailSalesSheet->setTitle('Detail Penjualan');

        $detailSalesSheet->setCellValue('A1', 'No')
                         ->setCellValue('B1', 'Tanggal Transaksi')
                         ->setCellValue('C1', 'TPE ID')
                         ->setCellValue('D1', 'Nama TPE')
                         ->setCellValue('E1', 'Product ID')
                         ->setCellValue('F1', 'Nama Produk')
                         ->setCellValue('G1', 'No PO')
                         ->setCellValue('H1', 'Qty')
                         ->setCellValue('I1', 'Harga Jual')
                         ->setCellValue('J1', 'Total Harga');

        $detailSalesSheet->getStyle('A1:J1')->getFont()->setBold(true);
        $detailSalesSheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sales = $this->rekap->load_all_detail_sales($data);
        $sRow = 2;
        $sNo = 1;
        foreach ($sales as $s) {
            $detailSalesSheet->setCellValue("A{$sRow}", $sNo)
                             ->setCellValue("B{$sRow}", $s['tanggal'])
                             ->setCellValue("C{$sRow}", $s['salesmanid'])
                             ->setCellValue("D{$sRow}", $s['nama_salesman'])
                             ->setCellValue("E{$sRow}", $s['product_id'])
                             ->setCellValue("F{$sRow}", $s['nama_invoice'])
                             ->setCellValue("G{$sRow}", $s['no_po'])
                             ->setCellValue("H{$sRow}", (int)$s['qty_kecil'])
                             ->setCellValue("I{$sRow}", (float)$s['h_jual'])
                             ->setCellValue("J{$sRow}", (float)$s['total_price']);
            $sRow++;
            $sNo++;
        }

        $detailSalesSheet->getStyle("A1:J" . ($sRow - 1))->getBorders()->getAllBorders()
                         ->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'J') as $col) {
            $detailSalesSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Rekap_Produk_Target_{$data['start_date']}_to_{$data['end_date']}";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
