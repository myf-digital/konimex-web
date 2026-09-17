<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends BaseController
{
    protected $super_email;
    protected $email_template;

    public function __construct() {
        parent::__construct();
        $this->load->model('Scheduler_model', 'scheduler');
        $this->load->config('email');
        $this->load->helper('email');

        $this->super_email = $this->config->item('email')['cc'] ?? [];
        $this->email_template = 'emails/template';
    }

    public function send_daily_report() {
        $this->load->library('pdf');
        $this->load->library('excel');
        $this->load->library('email');
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
				'subject' => 'Laporan Aktivitas TPE - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_tables_html($tables),
			];

            $fileAttach = [];
            $infoAttach = [];
            $filePdf = null;
            $fileExcel = null;
            if (in_array('pdf', $arrAttach)) {
                $title = 'Laporan Aktivitas TPE - ' . format_date_id(date('Y-m-d'), true, false);
                $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetTitle($title);
                $pdf->AddPage();
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetLineWidth(0);

                $dataPDF = array_merge($data, ['is_pdf' => true]);
                $html = $this->load->view($this->email_template, $dataPDF, TRUE);

                $pdf->writeHTML($html, true, false, true, false, '');
                $filePdf = FCPATH . 'uploads/laporan_aktivitas_TPE_' . date('Ymd') . '.pdf';
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
                    ->setCellValue('C1', 'Code TPE')
                    ->setCellValue('D1', 'TPE Name')
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
                    ->setCellValue('B1', 'Code TPE')
                    ->setCellValue('C1', 'TPE Name')
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
                    ->setCellValue('C1', 'Code TPE')
                    ->setCellValue('D1', 'TPE Name')
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

                $fileExcel = FCPATH . 'uploads/laporan_aktivitas_TPE_' . date('Ymd') . '.xlsx';
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
        $this->load->library('pdf');
        $this->load->library('excel');
        $this->load->library('email');
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
				'subject' => 'Laporan Pending Order TPE - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_order_pending_html($dataOrders),
                'cc_to' => $cc_to ? explode(',', $cc_to) : [],
			];

            $fileAttach = [];
            $infoAttach = [];
            $filePdf = null;
            $fileExcel = null;
            if (in_array('pdf', $arrAttach)) {
                $title = 'Laporan Pending Order TPE - ' . format_date_id(date('Y-m-d'), true, false);
                $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetTitle($title);
                $pdf->AddPage();
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetLineWidth(0);

                $dataPDF = array_merge($data, ['is_pdf' => true]);
                $html = $this->load->view($this->email_template, $dataPDF, TRUE);

                $pdf->writeHTML($html, true, false, true, false, '');
                $filePdf = FCPATH . 'uploads/laporan_pending_order_TPE_' . date('Ymd') . '.pdf';
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
                    $sheet->setCellValue("D{$row}", "TPE");
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

                $fileExcel = FCPATH . 'uploads/laporan_pending_order_TPE_' . date('Ymd') . '.xlsx';
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

    public function run_daily_target($cli_date = NULL) {
        $start_time = microtime(true);
        $periode = $this->input->get('periode');
        if (empty($periode) && !empty($cli_date)) {
            $periode = $cli_date;
        }

        if (empty($periode)) {
            $periode = date('Y-m-d', strtotime('-1 day'));
        }

        $time = strtotime($periode);
        if (!$time) {
            if (strlen($periode) == 7 && strpos($periode, '-') !== false) {
                $time = strtotime($periode . '-01');
            }
        }

        if (!$time) {
            responseJSON([
                'status' => false,
                'message' => "Format tanggal tidak valid. (contoh: 2026-07-02)"
            ]);
            return;
        }

        $formatted_date = date('Y-m-d', $time);

        try {
            $this->scheduler->run_daily_target($formatted_date);
            $duration = round(microtime(true) - $start_time, 2);

            $this->notif_telegram_daily_target($formatted_date, true, $duration);

            responseJSON([
                'status' => true,
                'message' => "Proses rekap cut-off data untuk tanggal {$formatted_date} berhasil dijalankan."
            ]);
        } catch (Exception $e) {
            $duration = round(microtime(true) - $start_time, 2);
            $this->notif_telegram_daily_target($formatted_date, false, $duration, $e->getMessage());

            responseJSON([
                'status' => false,
                'message' => "Terjadi kesalahan saat memproses rekap harian: " . $e->getMessage()
            ]);
        }
    }

    private function notif_telegram_daily_target($formatted_date, $status, $duration, $errorMessage = null)
    {
        $status_badge = $status ? "✅ OK" : "❌ Gagal";

        $total_dub_visit = $this->db->where('tanggal', $formatted_date)->count_all_results('rekap_dub_visit');
        $salesman_row = $this->db->select('count(distinct salesmanid) as total')->where('tanggal', $formatted_date)->get('rekap_dub_visit')->row();
        $total_salesman = $salesman_row ? $salesman_row->total : 0;

        $total_spesialis_visit = $this->db->where('tanggal', $formatted_date)->count_all_results('rekap_spesialis_visit');
        $total_produk_visit = $this->db->where('tanggal', $formatted_date)->count_all_results('rekap_produk_visit');

        $title = "<b>CRON: DAILY TARGET REPORT</b>";
        $msg_lines = [
            "📅 <b>Target Tanggal (Cut-Off):</b> <code>{$formatted_date}</code>",
            "⏱️ <b>Durasi Eksekusi:</b> {$duration}s",
            "",
            "📊 <b>Status Eksekusi:</b> {$status_badge}",
        ];

        if (!$status && !empty($errorMessage)) {
            $msg_lines[] = "⚠️ <b>Error:</b> <i>" . htmlspecialchars($errorMessage) . "</i>";
        }

        $msg_lines = array_merge($msg_lines, [
            "",
            "📦 <b>Ringkasan Jumlah Rekap [{$formatted_date}]:</b>",
            "• Rekap DUB Visit: <b>" . number_format($total_dub_visit) . "</b> baris (" . number_format($total_salesman) . " Salesman)",
            "• Rekap Spesialis Visit: <b>" . number_format($total_spesialis_visit) . "</b> baris",
            "• Rekap Produk Visit: <b>" . number_format($total_produk_visit) . "</b> baris",
            "",
            "🕐 <i>Selesai: " . date('Y-m-d H:i:s') . "</i>"
        ]);

        $message = implode("\n", $msg_lines);
        send_telegram($title, $message);
    }

    public function populate_history() {
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        if (empty($start)) {
            $start = '2025-07-14';
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }

        $start_time = strtotime($start);
        $end_time = strtotime($end);

        if (!$start_time || !$end_time || $start_time > $end_time) {
            responseJSON([
                'status' => false,
                'message' => 'Range tanggal tidak valid.'
            ]);
            return;
        }

        $processed = [];
        $current = $start_time;
        while ($current <= $end_time) {
            $date = date('Y-m-d', $current);
            try {
                $this->scheduler->run_daily_target($date);
                $processed[] = $date;
            } catch (Exception $e) {
                // Keep going but record the failure if needed
            }
            $current = strtotime('+1 day', $current);
        }

        responseJSON([
            'status' => true,
            'message' => 'Proses rekap histori berhasil diselesaikan.',
            'count' => count($processed),
            'range' => $start . ' s/d ' . $end
        ]);
    }

    public function rekonsiliasi_outlet_visit() {
        $start_time = microtime(true);

        try {
            $affected = $this->scheduler->rekonsiliasi_outlet_visit();
            $duration = round(microtime(true) - $start_time, 2);

            $totalAffected = $affected['total'] ?? 0;
            $affectedOutlet = $affected['m_customer'] ?? 0;
            $affectedDetailing = $affected['trx_visit_detailing'] ?? 0;
            $affectedTrans = $affected['t_sales_rrk_trans'] ?? 0;
            $affectedTokens = $affected['tokens'] ?? 0;

            if ($totalAffected > 0 || $this->input->get('force_notif')) {
                $this->notif_telegram_rekonsiliasi($affectedOutlet, $affectedDetailing, $affectedTrans, $affectedTokens, $totalAffected, $duration);
            }

            responseJSON([
                'status' => true,
                'message' => 'Proses rekonsiliasi outlet & visit selesai.',
                'affected_rows' => $affected,
                'duration' => $duration . 's'
            ]);
        } catch (Exception $e) {
            $duration = round(microtime(true) - $start_time, 2);
            responseJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan saat rekonsiliasi: ' . $e->getMessage()
            ]);
        }
    }

    private function notif_telegram_rekonsiliasi($affectedOutlet, $affectedDetailing, $affectedTrans, $affectedTokens, $totalAffected, $duration)
    {
        $title = "<b>CRON: REKONSILIASI OUTLET & VISIT</b>";
        $msg_lines = [
            "⚡ <b>Ditemukan Perubahan Data (Auto-Fixed)</b>",
            "⏱️ <b>Durasi Eksekusi:</b> {$duration}s",
            "",
            "📊 <b>Ringkasan Baris Ter-Update:</b>",
        ];
        if ($affectedOutlet) {
            $msg_lines[] = "• m_customer (Reset Customer ID M): <b>" . number_format($affectedOutlet) . "</b> baris";
        }
        if ($affectedDetailing) {
            $msg_lines[] = "• trx_visit_detailing (Sync Periode/NoUrut): <b>" . number_format($affectedDetailing) . "</b> baris";
        }
        if ($affectedTrans) {
            $msg_lines[] = "• t_sales_rrk_trans (Sync Periode Check-in): <b>" . number_format($affectedTrans) . "</b> baris";
        }
        if ($affectedTokens) {
            $msg_lines[] = "• tokens (Hapus Token Expired): <b>" . number_format($affectedTokens) . "</b> baris";
        }
        $msg_lines = array_merge($msg_lines, [
            "",
            "🎯 <b>Total Data Diperbaiki:</b> <b>" . number_format($totalAffected) . "</b> baris",
            "",
            "🕐 <i>Waktu: " . date('Y-m-d H:i:s') . "</i>"
        ]);

        $message = implode("\n", $msg_lines);
        send_telegram($title, $message);
    }
}
