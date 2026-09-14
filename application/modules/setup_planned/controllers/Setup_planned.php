<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Setup_planned extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup_planned_model', 'setup_planned');
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
    
    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
    }

    public function load()
    {   
        $data = param_input();
        responseJSON($this->setup_planned->load($data));
    }

    public function get_detail()
    {
        $data = param_input();
        responseJSON($this->setup_planned->get_detail($data));
    }

    public function create()
    {
        $data = param_input();
        $result = $this->setup_planned->create($data);
        if ($result['status'] == true) {
            response($result['data'], 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }

    public function update()
    {
        $data = param_input();
        $result = $this->setup_planned->update($data);
        if ($result['status'] == true) {
            response($result['data'], 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }

    public function delete()
    {
        $data = param_input();
        $result = $this->setup_planned->delete($data);
        if ($result['status'] == true) {
            response(null, 200, $result['message']);
        } else {
            response(null, 400, $result['message']);
        }
    }

    public function update_status()
    {
        $data = param_input();
        $result = $this->setup_planned->update_status($data);
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
        
        $datas = $this->setup_planned->load($data, 'export');
        
        $reqNos = !empty($datas) ? array_column($datas, 'req_no') : [];
        $allDetails = [];
        if (!empty($reqNos)) {
            $res = $this->setup_planned->get_detail(['req_no' => $reqNos]);
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
            'ID TPE',
            'NAMA TPE',
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

    public function download_template($salesmanid = '')
    {
        if (empty($salesmanid)) {
            $salesmanid = $this->uri->segment('3');
        }

        ini_set("memory_limit", "1024M");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Planned');

        $headers = [
            'SALESMANID',
            'OUTLETID',
            'NAMA_OUTLET',
            'USERID',
            'NAMA_USER',
            'TANGGAL',
        ];

        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getRowDimension(1)->setRowHeight(25);

        $rowNum = 2;
        if (!empty($salesmanid)) {
            $data = $this->setup_planned->get_template_data($salesmanid);
            if (!empty($data)) {
                foreach ($data as $item) {
                    $sheet->fromArray([
                        $item['salesmanid'],
                        $item['customerid'],
                        $item['nama_customer'],
                        $item['user_id'],
                        $item['nama_user'],
                        NULL,
                    ], NULL, 'A' . $rowNum);
                    $rowNum++;
                }
            }
        }

        $highestRow = max(2, $sheet->getHighestRow());
        $highestColumn = 'F';
        $fullRange = 'A1:' . $highestColumn . $highestRow;

        $sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->freezePane('A2');

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ]);

        $headerRange = 'A1:' . $highestColumn . '1';
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
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'startColor' => ['rgb' => '3C8DBC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $filename = "Template_Setup_Planned_" . (!empty($salesmanid) ? str_replace(',', '_', $salesmanid) . "_" : "") . date('His') . ".xlsx";

        if (ob_get_length()) ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function upload()
    {
        ini_set("memory_limit", "1024M");

        $usersession = $this->input->post('usersession');
        $rolename = $this->input->post('rolename');

        if (empty($_FILES['fileupload']['name'])) {
            responseJSON(['status' => false, 'message' => 'File tidak boleh kosong']);
            return;
        }

        $allowedExtensions = ['xls', 'xlsx'];
        $ext = strtolower(pathinfo($_FILES['fileupload']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            responseJSON(['status' => false, 'message' => 'Format file harus .xls atau .xlsx']);
            return;
        }

        $fileTmp = $_FILES['fileupload']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($fileTmp);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, false, false);

            if (empty($sheet) || count($sheet) < 2) {
                responseJSON(['status' => false, 'message' => 'File kosong atau tidak memiliki baris data']);
                return;
            }

            $headerRowIndex = 0;
            $header = array_values(array_filter(array_map(function($v) {
                return strtoupper(trim((string)$v));
            }, $sheet[0]), function($v) {
                return $v !== '';
            }));

            $expected = [
                'SALESMANID',
                'OUTLETID',
                'NAMA_OUTLET',
                'USERID',
                'NAMA_USER',
                'TANGGAL',
            ];

            if ($header !== $expected && isset($sheet[1])) {
                $checkRow1 = array_values(array_filter(array_map(function($v) {
                    return strtoupper(trim((string)$v));
                }, $sheet[1]), function($v) {
                    return $v !== '';
                }));
                if ($checkRow1 === $expected) {
                    $headerRowIndex = 1;
                    $header = $checkRow1;
                }
            }

            $headerFirst = array_slice($header, 0, 6);
            if ($headerFirst !== $expected) {
                responseJSON([
                    'status' => false,
                    'message' => 'Format header Excel tidak sesuai. Wajib berisi kolom: SALESMANID, OUTLETID, NAMA_OUTLET, USERID, NAMA_USER, TANGGAL'
                ]);
                return;
            }

            $grouped = [];
            for ($i = $headerRowIndex + 1; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                $salesmanid = trim((string)($row[0] ?? ''));
                $customerid = trim((string)($row[1] ?? ''));
                $nama_customer = trim((string)($row[2] ?? ''));
                $userid = trim((string)($row[3] ?? ''));
                $nama_user = trim((string)($row[4] ?? ''));
                $tanggal = $row[5] ?? '';

                if ($salesmanid === '' && $customerid === '' && $userid === '' && (string)$tanggal === '') {
                    continue;
                }

                if ($salesmanid === '') {
                    responseJSON([
                        'status' => false,
                        'message' => "Kolom SALESMANID kosong pada baris ke-" . ($i + 1)
                    ]);
                    return;
                }

                if ($customerid === '') {
                    responseJSON([
                        'status' => false,
                        'message' => "Kolom OUTLETID kosong pada baris ke-" . ($i + 1)
                    ]);
                    return;
                }

                if ($userid === '') {
                    responseJSON([
                        'status' => false,
                        'message' => "Kolom USERID kosong pada baris ke-" . ($i + 1)
                    ]);
                    return;
                }

                if ((string)$tanggal === '') {
                    responseJSON([
                        'status' => false,
                        'message' => "Kolom TANGGAL kosong pada baris ke-" . ($i + 1)
                    ]);
                    return;
                }

                $formattedDate = false;
                if (is_numeric($tanggal) && (float)$tanggal > 0) {
                    try {
                        $dateObj = Date::excelToDateTimeObject((float)$tanggal);
                        $formattedDate = $dateObj->format('Y-m-d');
                    } catch (\Throwable $t) {
                        $formattedDate = false;
                    }
                }

                if (!$formattedDate && is_string($tanggal) && trim($tanggal) !== '') {
                    $trimmedDate = trim($tanggal);
                    $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'd.m.Y'];
                    foreach ($formats as $fmt) {
                        $d = \DateTime::createFromFormat('!' . $fmt, $trimmedDate);
                        if ($d && $d->format($fmt) === $trimmedDate) {
                            $formattedDate = $d->format('Y-m-d');
                            break;
                        }
                    }
                }

                if (!$formattedDate) {
                    responseJSON([
                        'status' => false,
                        'message' => "Format tanggal tidak valid pada baris ke-" . ($i + 1) . ": " . (string)$tanggal . " (Wajib format DD/MM/YYYY atau YYYY-MM-DD)"
                    ]);
                    return;
                }

                if (!isset($grouped[$salesmanid])) {
                    $grouped[$salesmanid] = [];
                }

                $grouped[$salesmanid][] = [
                    'customerid' => $customerid,
                    'nama_customer' => $nama_customer,
                    'user_id' => $userid,
                    'user_name' => $nama_user,
                    'tanggal' => $formattedDate,
                    'row_num' => $i + 1
                ];
            }

            if (empty($grouped)) {
                responseJSON(['status' => false, 'message' => 'Tidak ada baris data yang valid dalam file Excel']);
                return;
            }

            $result = $this->setup_planned->process_upload($grouped, $usersession, $rolename);
            responseJSON($result);

        } catch (\Throwable $e) {
            responseJSON(['status' => false, 'message' => "Terjadi kesalahan saat memproses file: " . $e->getMessage()]);
        }
    }
}
