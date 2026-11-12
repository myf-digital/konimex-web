<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_dub extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_dub_model', 'rep_dub');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load_salesman()
    {
        $data = param_input();
        responseJSON(['rows' => $this->rep_dub->get_salesman($data)]);
    }

    public function load()
    {
        $data = param_input();
        if (empty($data['salesmanid'])) {
            responseJSON([
                'status'  => false,
                'message' => 'Salesman / TPE wajib dipilih.',
            ]);
            return;
        }
        $result = $this->rep_dub->load_report($data);
        responseJSON([
            'status' => true,
            'data'   => $result,
        ]);
    }

    public function download_excel()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $salesmanId = trim((string) ($this->input->get('salesmanid') ?? ''), " \t\n\r\0\x0B/");
        if ($salesmanId === '') {
            show_error('Filter Salesman / TPE wajib dipilih.', 400);
            return;
        }

        $data = [
            'usersession'    => trim((string) ($this->input->get('usersession') ?? ''), " \t\n\r\0\x0B/"),
            'restrict_level' => trim((string) ($this->input->get('restrict_level') ?? ''), " \t\n\r\0\x0B/"),
            'periode'        => trim((string) ($this->input->get('periode') ?? date('Y-m')), " \t\n\r\0\x0B/"),
            'week'           => trim((string) ($this->input->get('week') ?? 'all'), " \t\n\r\0\x0B/"),
            'salesmanid'     => $salesmanId,
        ];

        $report = $this->rep_dub->load_report($data);
        $detailRows = $report['rows'] ?? [];
        $summaryRows = $report['summary'] ?? [];

        $spreadsheet = new Spreadsheet();

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0073B7'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $bodyBorderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E0E0E0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Sheet 1: List Data DUB
        $sheetDetail = $spreadsheet->getActiveSheet();
        $sheetDetail->setTitle('List Data DUB');

        $weekLabel = ($report['week'] !== 'all' && $report['week'] !== '')
            ? 'Minggu Ke-' . $report['week']
            : 'Semua Minggu (1 Bulan)';

        $sheetDetail->setCellValue('A1', 'REPORT DUB (' . $report['periode'] . ' | ' . $weekLabel . ' | ' . $report['start_date'] . ' s/d ' . $report['end_date'] . ')');
        $sheetDetail->mergeCells('A1:N1');
        $sheetDetail->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $detailHeaders = [
            'A2' => 'NO',
            'B2' => 'KODE SALESMAN',
            'C2' => 'NAMA SALESMAN',
            'D2' => 'TIPE SALES',
            'E2' => 'WILAYAH',
            'F2' => 'SUMBER DUB',
            'G2' => 'KODE CUSTOMER',
            'H2' => 'NAMA CUSTOMER',
            'I2' => 'USER ID',
            'J2' => 'NAMA PROFESSIONAL',
            'K2' => 'SPESIALISASI',
            'L2' => 'TARGET CALL',
            'M2' => 'ACTUAL CALL',
            'N2' => 'PENCAPAIAN (%)',
        ];
        foreach ($detailHeaders as $cell => $val) {
            $sheetDetail->setCellValue($cell, $val);
        }
        $sheetDetail->getStyle('A2:N2')->applyFromArray($headerStyle);
        $sheetDetail->getRowDimension(2)->setRowHeight(24);

        $detailData = [];
        $no = 1;
        foreach ($detailRows as $r) {
            $detailData[] = [
                $no++,
                $r['salesmanid'],
                $r['nama_salesman'],
                $r['tipe_sales'],
                $r['wilayah'],
                $r['source_dub'],
                $r['customerid'],
                $r['nama_customer'],
                $r['user_id'],
                $r['nama_professional'],
                $r['spesialisasi'] ?: ($r['tipe_pic'] ?: '-'),
                (float) $r['target_call'],
                (int) $r['actual_call'],
                (float) $r['percentage_call'],
            ];
        }

        if (!empty($detailData)) {
            $sheetDetail->fromArray($detailData, NULL, 'A3', true);
            $highestDetailRow = 2 + count($detailData);
            $sheetDetail->getStyle('A3:N' . $highestDetailRow)->applyFromArray($bodyBorderStyle);
            $sheetDetail->getStyle('N3:N' . $highestDetailRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $sheetDetail->getStyle('A3:A' . $highestDetailRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetDetail->getStyle('D3:D' . $highestDetailRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetDetail->getStyle('I3:I' . $highestDetailRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetDetail->getStyle('L3:N' . $highestDetailRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'N') as $col) {
            $sheetDetail->getColumnDimension($col)->setAutoSize(true);
        }

        // Sheet 2: Rekap Per Salesman
        $sheetSummary = $spreadsheet->createSheet();
        $sheetSummary->setTitle('Rekap Per Salesman');

        $sheetSummary->setCellValue('A1', 'REKAP PENCAPAIAN DUB PER SALESMAN (' . $report['periode'] . ' | ' . $weekLabel . ' | ' . $report['start_date'] . ' s/d ' . $report['end_date'] . ')');
        $sheetSummary->mergeCells('A1:K1');
        $sheetSummary->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $summaryHeaders = [
            'A2' => 'NO',
            'B2' => 'KODE SALESMAN',
            'C2' => 'NAMA SALESMAN',
            'D2' => 'TIPE SALES',
            'E2' => 'WILAYAH',
            'F2' => 'SUMBER DUB',
            'G2' => 'TARGET DUB',
            'H2' => 'TERDAFTAR DUB',
            'I2' => 'TARGET CALL / DOKTER',
            'J2' => 'TOTAL ACTUAL CALL',
            'K2' => 'PENCAPAIAN DUB (%)',
        ];
        foreach ($summaryHeaders as $cell => $val) {
            $sheetSummary->setCellValue($cell, $val);
        }
        $sheetSummary->getStyle('A2:K2')->applyFromArray($headerStyle);
        $sheetSummary->getRowDimension(2)->setRowHeight(24);

        $summaryData = [];
        $noSum = 1;
        foreach ($summaryRows as $s) {
            $summaryData[] = [
                $noSum++,
                $s['salesmanid'],
                $s['nama_salesman'],
                $s['tipe_sales'],
                $s['wilayah'],
                $s['source_dub'],
                (int) $s['target_dub'],
                (int) $s['registered_dub'],
                (float) $s['target_per_doc'],
                (int) $s['total_actual'],
                (float) $s['percentage_dub'],
            ];
        }

        if (!empty($summaryData)) {
            $sheetSummary->fromArray($summaryData, NULL, 'A3', true);
            $highestSumRow = 2 + count($summaryData);
            $sheetSummary->getStyle('A3:K' . $highestSumRow)->applyFromArray($bodyBorderStyle);
            $sheetSummary->getStyle('K3:K' . $highestSumRow)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $sheetSummary->getStyle('A3:A' . $highestSumRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetSummary->getStyle('D3:D' . $highestSumRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetSummary->getStyle('G3:K' . $highestSumRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'K') as $col) {
            $sheetSummary->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $safeSalesman = strtolower($salesmanId) === 'all'
            ? 'Semua_TPE'
            : preg_replace('/[^A-Za-z0-9_-]/', '_', $salesmanId);
        $weekSlug = ($report['week'] !== 'all' && $report['week'] !== '') ? '_W' . $report['week'] : '_All';
        $filename = 'Report_DUB_' . $safeSalesman . '_' . $report['periode'] . $weekSlug . '_' . date('Ymd_His') . '.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

