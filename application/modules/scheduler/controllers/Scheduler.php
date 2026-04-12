<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends BaseController
{
    protected $super_email;
    protected $email_template;

    public function __construct() {
        parent::__construct();
        $this->load->model('Scheduler_model', 'scheduler');
        $this->load->library('pdf');
        $this->load->library('excel');
        $this->load->library('email');
        $this->load->config('email');
        $this->load->helper('email');

        $this->super_email = $this->config->item('email')['cc'] ?? [];
        $this->email_template = 'emails/template';
    }

    public function send_daily_report() {
		$email = $this->input->get('email');
		$attach = $this->input->get('attach');
        $arrAttach = $attach ? explode(',', $attach): [];
		
		if ($email) $emails = [
			(object) ['send_to' => $email]
		];
		else $emails = $this->scheduler->get_all_send_to();
        
		$result = [];
		foreach ($emails as $val) {
			if (!$val->send_to) continue;

            $tables = [
                'outlet_coverage' => [],
                'target_call_monthly' => [],
                'target_call_daily' => [],
            ];
            if (in_array($val->send_to, $this->super_email)) {
                $tables = $this->scheduler->get_data_send_email();
            } else {
                $tables = $this->scheduler->get_data_send_email($val->send_to);
            }
			$data = [
				'name' => explode('@', $val->send_to)[0] ?? $val->send_to,
				'subject' => 'Laporan Aktivitas MEDREP - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_tables_html($tables),
			];

            $fileAttach = [];
            $infoAttach = [];
            $filePdf = null;
            $fileExcel = null;
            if (in_array('pdf', $arrAttach)) {
                $title = 'Laporan Aktivitas MEDREP - ' . format_date_id(date('Y-m-d'), true, false);
                $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetTitle($title);
                $pdf->AddPage();
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetLineWidth(0);

                $dataPDF = array_merge($data, ['is_pdf' => true]);
                $html = $this->load->view($this->email_template, $dataPDF, TRUE);

                $pdf->writeHTML($html, true, false, true, false, '');
                $filePdf = FCPATH . 'uploads/laporan_aktivitas_MEDREP_' . date('Ymd') . '.pdf';
                $pdf->Output($filePdf, 'F');
                
                $fileAttach[] = $filePdf;
                $infoAttach[] = 'pdf';
            }

            if (in_array('excel', $arrAttach)) {
                $objPHPExcel = new PHPExcel();

                // Outlet Coverage
                $objPHPExcel->createSheet(0);
                $sheetOutletCoverage = $objPHPExcel->setActiveSheetIndex(0);
                $sheetOutletCoverage->setTitle('Outlet Coverage');
                $sheetOutletCoverage
                    ->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'City/Area')
                    ->setCellValue('C1', 'Code MEDREP')
                    ->setCellValue('D1', 'MEDREP Name')
                    ->setCellValue('E1', 'Apotik')
                    ->setCellValue('F1', 'Clinic')
                    ->setCellValue('G1', 'Hospital');
                if (!empty($tables['outlet_coverage'])) {
                    $i = 1;
                    $row = 2;
                    foreach ($tables['outlet_coverage'] as $oc) {
                        $sheetOutletCoverage->setCellValue('A'.$row, $i)
                            ->setCellValue('B'.$row, $oc->nama_area ?? '')
                            ->setCellValue('C'.$row, $oc->parma ?? '')
                            ->setCellValue('D'.$row, $oc->nama_parma ?? '')
                            ->setCellValue('E'.$row, $oc->Apotik ?? '')
                            ->setCellValue('F'.$row, $oc->Clinic ?? '')
                            ->setCellValue('G'.$row, $oc->Hospital ?? '');
                        
                        $i++;
                        $row++;
                    }
                }

                // Target Call Monthly
                $objPHPExcel->createSheet(1);
                $sheetTargetCallMonthly = $objPHPExcel->setActiveSheetIndex(1);
                $sheetTargetCallMonthly->setTitle('Target Call Monthly');
                $sheetTargetCallMonthly
                    ->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Code MEDREP')
                    ->setCellValue('C1', 'MEDREP Name')
                    ->setCellValue('D1', 'Area')
                    ->setCellValue('E1', 'Target')
                    ->setCellValue('F1', 'Call')
                    ->setCellValue('G1', 'Extra Call')
                    ->setCellValue('H1', 'Actual');
                if (!empty($tables['target_call_monthly'])) {
                    $i = 1;
                    $row = 2;
                    foreach ($tables['target_call_monthly'] as $tcm) {
                        $sheetTargetCallMonthly->setCellValue('A'.$row, $i)
                            ->setCellValue('B'.$row, $tcm->parma ?? '')
                            ->setCellValue('C'.$row, $tcm->nama_parma ?? '')
                            ->setCellValue('D'.$row, $tcm->nama_area ?? '')
                            ->setCellValue('E'.$row, $tcm->target_call ?? '')
                            ->setCellValue('F'.$row, $tcm->Call ?? '')
                            ->setCellValue('G'.$row, $tcm->ExtraCall ?? '')
                            ->setCellValue('H'.$row, $tcm->actual_call ?? '');
                        
                        $i++;
                        $row++;
                    }
                }

                // Target Call Daily
                $objPHPExcel->createSheet(2);
                $sheetTargetCallDaily = $objPHPExcel->setActiveSheetIndex(2);
                $sheetTargetCallDaily->setTitle('Target Call Daily');
                $sheetTargetCallDaily
                    ->setCellValue('A1', 'No')
                    ->setCellValue('B1', 'Tanggal')
                    ->setCellValue('C1', 'Code MEDREP')
                    ->setCellValue('D1', 'MEDREP Name')
                    ->setCellValue('E1', 'Area')
                    ->setCellValue('F1', 'Target')
                    ->setCellValue('G1', 'Call')
                    ->setCellValue('H1', 'Extra Call')
                    ->setCellValue('I1', 'Actual');
                if (!empty($tables['target_call_daily'])) {
                    $i = 1;
                    $row = 2;
                    foreach ($tables['target_call_daily'] as $tcd) {
                        $periode = format_date_id($tcd->periode, true, false);
                        $sheetTargetCallDaily->setCellValue('A'.$row, $i)
                            ->setCellValue('B'.$row, $periode ?? '')
                            ->setCellValue('C'.$row, $tcd->parma ?? '')
                            ->setCellValue('D'.$row, $tcd->nama_parma ?? '')
                            ->setCellValue('E'.$row, $tcd->nama_area ?? '')
                            ->setCellValue('F'.$row, $tcd->target_call ?? '')
                            ->setCellValue('G'.$row, $tcd->Call ?? '')
                            ->setCellValue('H'.$row, $tcd->ExtraCall ?? '')
                            ->setCellValue('I'.$row, $tcd->actual_call ?? '');
                        
                        $i++;
                        $row++;
                    }
                }

                $fileExcel = FCPATH . 'uploads/laporan_aktivitas_MEDREP_' . date('Ymd') . '.xlsx';
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save($fileExcel);

                $fileAttach[] = $fileExcel;
                $infoAttach[] = 'excel';
            }

            $textInfoAttach = count($infoAttach) > 0 ? ' (Attach: ' . implode(', ', $infoAttach) . ')' : '';
			if (sending_email($val->send_to, $data['subject'], $this->email_template, $data, $fileAttach)) {
                log_message('info', 'Laporan harian terkirim' . $textInfoAttach . ' : '  . date('Y-m-d H:i:s'));
				$result[] = $val->send_to . ': ✅ Email berhasil dikirim!' . $textInfoAttach;
			} else {
                log_message('error', 'Gagal mengirim laporan' . $textInfoAttach . ' : ' . $this->email->print_debugger());
				$result[] = $val->send_to . ': ❌ Gagal mengirim email.' . $textInfoAttach;
			}
            if ($filePdf) @unlink($filePdf);
            if ($fileExcel) @unlink($fileExcel);
		}
		responseJSON($result);
    }

    public function send_pending_order() {
		$email = $this->input->get('email');
		$attach = $this->input->get('attach');
        $arrAttach = $attach ? explode(',', $attach): [];
		
		if ($email) $emails = [
			(object) ['send_to' => $email]
		];
		else $emails = $this->scheduler->get_all_order_send_to();
        
		$result = [];
		foreach ($emails as $val) {
			if (!$val->send_to) continue;

            $cc_to = null;
            $list = [
                'order_pending' => [],
            ];
            if (in_array($val->send_to, $this->super_email)) {
                $list = $this->scheduler->get_data_order_send_email();
            } else {
                $list = $this->scheduler->get_data_order_send_email($val->send_to);
            }
            $dataOrders = group_order_by_no_po($list['order_pending']);

			$data = [
				'name' => explode('@', $val->send_to)[0] ?? $val->send_to,
				'subject' => 'Laporan Pending Order MEDREP - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_order_pending_html($dataOrders),
                'cc_to' => $cc_to ? explode(',', $cc_to) : [],
			];

            $fileAttach = [];
            $infoAttach = [];
            $filePdf = null;
            $fileExcel = null;
            if (in_array('pdf', $arrAttach)) {
                $title = 'Laporan Pending Order MEDREP - ' . format_date_id(date('Y-m-d'), true, false);
                $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetTitle($title);
                $pdf->AddPage();
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetLineWidth(0);

                $dataPDF = array_merge($data, ['is_pdf' => true]);
                $html = $this->load->view($this->email_template, $dataPDF, TRUE);

                $pdf->writeHTML($html, true, false, true, false, '');
                $filePdf = FCPATH . 'uploads/laporan_pending_order_MEDREP_' . date('Ymd') . '.pdf';
                $pdf->Output($filePdf, 'F');
                
                $fileAttach[] = $filePdf;
                $infoAttach[] = 'pdf';
            }

            if (in_array('excel', $arrAttach)) {
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->createSheet(0);
                $sheet = $objPHPExcel->setActiveSheetIndex(0);
                $sheet->setTitle('Pending Order');

                $row = 1;
                foreach ($dataOrders as $od) {
                    $header = $od['headers'];

                    $sheet->setCellValue("A{$row}", "No PO");
                    $sheet->setCellValue("B{$row}", $header['no_po']);
                    $sheet->setCellValue("D{$row}", "Customer");
                    $sheet->setCellValue("E{$row}", $header['nama_customer']);
                    $row++;

                    $sheet->setCellValue("A{$row}", "No Sales");
                    $sheet->setCellValue("B{$row}", $header['no_sales']);
                    $sheet->setCellValue("D{$row}", "Medrep");
                    $sheet->setCellValue("E{$row}", $header['salesman']);
                    $row++;

                    $sheet->setCellValue("A{$row}", "Tanggal");
                    $sheet->setCellValue("B{$row}", $header['tanggal']);
                    $sheet->setCellValue("D{$row}", "Status");
                    $sheet->setCellValue("E{$row}", $header['status']);
                    $row += 2;

                    $sheet->fromArray(
                        ['No', 'Produk', 'Brand', 'Qty', 'Harga', 'Total'],
                        NULL,
                        "A{$row}"
                    );
                    $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $row++;

                    foreach ($od['details'] as $key => $v_detail) {
                        $sheet->setCellValue("A{$row}", ($key+1));
                        $sheet->setCellValue("B{$row}", $v_detail->nama_invoice);
                        $sheet->setCellValue("C{$row}", $v_detail->nama_brand);
                        $sheet->setCellValue("D{$row}", number_format($v_detail->qty_kecil, 0, '.', ','));
                        $sheet->setCellValue("E{$row}", number_format($v_detail->h_jual, 0, '.', ','));
                        $sheet->setCellValue("F{$row}", number_format(($v_detail->qty_kecil * $v_detail->h_jual), 0, '.', ','));
                        $row++;
                    }
                    $row += 3;
                }

                $fileExcel = FCPATH . 'uploads/laporan_pending_order_MEDREP_' . date('Ymd') . '.xlsx';
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save($fileExcel);

                $fileAttach[] = $fileExcel;
                $infoAttach[] = 'excel';
            }

            $textInfoAttach = count($infoAttach) > 0 ? ' (Attach: ' . implode(', ', $infoAttach) . ')' : '';
			if (sending_email($val->send_to, $data['subject'], $this->email_template, $data, $fileAttach)) {
                log_message('info', 'Laporan terkirim' . $textInfoAttach . ' : '  . date('Y-m-d H:i:s'));
				$result[] = $val->send_to . ': ✅ Email berhasil dikirim!' . $textInfoAttach;
			} else {
                log_message('error', 'Gagal mengirim laporan' . $textInfoAttach . ' : ' . $this->email->print_debugger());
				$result[] = $val->send_to . ': ❌ Gagal mengirim email.' . $textInfoAttach;
			}
            if ($filePdf) @unlink($filePdf);
            if ($fileExcel) @unlink($fileExcel);
		}
		responseJSON($result);
    }
}
