<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Spesialisasi extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('spesialisasi_model', 'spesialisasi');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->spesialisasi->load($data));
    }

    public function detail()
    {
        $data = param_input();
        responseJSON($this->spesialisasi->detail($data));
    }

    public function update()
    {
        $data = param_input();
        responseJSON($this->spesialisasi->update($data));
    }

    public function savetoxlsx()
    {
		ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $data = [];
        $result = $this->spesialisasi->savetoxlsx($data);
        $filename = "data_spesialisasi_" . date('Y-m-d_His');

        $spreadsheet = new Spreadsheet();
        $header = [
            'Id Spesialisasi',
            'Nama Spesialisasi',
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Spesialisasi');
        $sheet->setCellValue('A1', 'DATA SPESIALISASI')->mergeCells('A1:B1');
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
            $content = [
                $value['id'],
                $value['name'],
            ];

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

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
