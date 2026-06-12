<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Setup_dub extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup_dub_model', 'setup_dub');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function load()
    {   
        $data = param_input();
        responseJSON($this->setup_dub->load($data));
    }

    public function get_detail()
    {
        $data = param_input();
        responseJSON($this->setup_dub->get_detail($data));
    }

    public function create()
    {
        $data = param_input();
        $result = $this->setup_dub->create($data);
        if ($result['status'] == true) {
            response($result['data'], 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }

    public function update()
    {
        $data = param_input();
        $result = $this->setup_dub->update($data);
        if ($result['status'] == true) {
            response($result['data'], 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }

    public function delete()
    {
        $data = param_input();
        $result = $this->setup_dub->delete($data);
        if ($result['status'] == true) {
            response(null, 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }

    public function update_status()
    {
        $data = param_input();
        $result = $this->setup_dub->update_status($data);
        if ($result['status'] == true) {
            response($result['data'], 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }
    
    public function savetoxlsx()
    {
        $salesmanid = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');
        $filtername = $this->uri->segment('7');
        
        if (empty($filtername)) {
            $filtername = "All";
        } else {
            $filtername = str_replace("%20", " ", $filtername);
        }

        ini_set("memory_limit","2048M");
        ini_set('max_execution_time', '0');

        $data = [
            'salesmanid' => $salesmanid,
            'username' => $username,
            'jabatan' => $jabatan,
            'restrict_level' => $restrict_level,
            'filtername' => $filtername,
        ];
        
        $datas = $this->setup_dub->load($data, 'export');
        
        $reqNos = !empty($datas) ? array_column($datas, 'req_no') : [];
        $allDetails = [];
        if (!empty($reqNos)) {
            $res = $this->setup_dub->get_detail(['req_no' => $reqNos]);
            $allDetails = !empty($res->result) ? $res->result : [];
        }

        // Group details by req_no in memory
        $detailsByReqNo = [];
        foreach ($allDetails as $detail) {
            $rn = $detail['req_no'];
            if (!isset($detailsByReqNo[$rn])) {
                $detailsByReqNo[$rn] = [];
            }
            $detailsByReqNo[$rn][] = $detail;
        }

        $filename = "Data_DUB_".str_replace(' ', '_', $filtername)."_".date('Ymd_His').".xlsx";

        $spreadsheet = new Spreadsheet();
        $header = [
            'REQ NO',
            'ID MEDREP',
            'NAMA MEDREP',
            'PERIODE',
            'KETERANGAN',
            'STATUS',
            'DUB',
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data DUB');
        $sheet->setCellValue('A1', 'DATA DUB '.strtoupper(str_replace(' ', '_', $filtername)))->mergeCells('A1:G1');
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
        foreach ($datas as $dt) {
            $details = !empty($detailsByReqNo[$dt['req_no']]) ? $detailsByReqNo[$dt['req_no']] : [];

            $listUser = '';
            if (!empty($details) && is_array($details)) {
                $grouped = [];
                foreach ($details as $detail) {
                    $cid = $detail['customerid'];
                    $outletName = !empty($detail['outlet']) ? $detail['outlet'] : 'Outlet tidak diketahui';
                    if (!isset($grouped[$cid])) {
                        $grouped[$cid] = [
                            'outlet_name' => $outletName,
                            'users' => []
                        ];
                    }
                    $userName = !empty($detail['user_name']) ? $detail['user_name'] : 'Professional tidak diketahui';
                    $grouped[$cid]['users'][] = $detail['user_id'] . ' - ' . $userName;
                }

                $formattedGroups = [];
                foreach ($grouped as $cid => $group) {
                    $groupText = "[" . $cid . " - " . $group['outlet_name'] . "]\n";
                    foreach ($group['users'] as $user) {
                        $groupText .= "- " . $user . "\n";
                    }
                    $formattedGroups[] = trim($groupText);
                }
                $listUser = implode("\n\n", $formattedGroups);
            }

            $status = '-';
            switch ($dt['status']) {
                case '1':
                    $status = 'Pending';
                    break;
                case '3':
                    $status = 'Approved';
                    if (!empty($dt['reason'])) {
                        $status .= "\n Reason: " . $dt['reason'];
                    }
                    break;
                case '5':
                    $status = 'Rejected';
                    if (!empty($dt['reason'])) {
                        $status .= "\n Reason:" . $dt['reason'];
                    }
                    break;
            }

            $content = [
                $dt['req_no'],
                $dt['salesmanid'],
                $dt['nama_salesman'],
                !empty($dt['periode']) && $dt['periode'] !== '0000-00-00' ? format_date_id($dt['periode'], false, false) : '',
                $dt['keterangan'],
                $status,
                $listUser,
            ];

            $sheet->fromArray($content, NULL, 'A'.$rowNum);

            $i++;
            $rowNum++;
        }
		// Ambil range seluruh worksheet
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
		$sheet->getStyle('G3:' . $highestColumn . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->freezePane('A3');

        foreach (range('A', $highestColumn) as $col) {
            if ($col === 'G') {
                $sheet->getColumnDimension($col)->setAutoSize(false);
                $sheet->getColumnDimension($col)->setWidth(60);
            } else {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
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
        header('Content-Disposition: attachment;filename="'. $filename .'"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    
    }
}
