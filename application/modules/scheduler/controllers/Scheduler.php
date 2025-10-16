<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler extends BaseController
{
    protected $super_email;

    public function __construct() {
        parent::__construct();
        $this->load->model('Scheduler_model', 'scheduler');
        $this->load->library('email');
        $this->load->library('pdf');
        $this->load->library('excel');
        $this->load->config('email');
        $this->super_email = $this->config->item('email')['cc'] ?? [];
    }

    public function send_daily_report() {
		$email = $this->input->post('email');
		
		if ($email) $emails = [
			(object) ['send_to' => $email]
		];
		else $emails = $this->scheduler->get_all_send_to();
        
		$result = [];
		foreach ($emails as $val) {
			if (!$val->send_to) continue;

            $tables = [
                'outlet_coverage' => [],
                'target_call_daily' => [],
                'target_call_monthly' => [],
            ];
            if (in_array($val->send_to, $this->super_email)) {
                $tables = $this->scheduler->get_data_send_email();
            } else {
                $tables = $this->scheduler->get_data_send_email($val->send_to);
            }

			$data = [
				'name' => explode('@', $val->send_to)[0] ?? $val->send_to,
				'subject' => 'Laporan Aktivitas PAR-MA - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_tables_html($tables),
			];
			if (sending_email($val->send_to, $data['subject'], 'emails/template', $data)) {
                log_message('info', 'Laporan harian terkirim: ' . date('Y-m-d H:i:s'));
				$result[] = $val->send_to . ': ✅ Email berhasil dikirim!';
			} else {
                log_message('error', 'Gagal mengirim laporan: ' . $this->email->print_debugger());
				$result[] = $val->send_to . ': ❌ Gagal mengirim email.';
			}
		}
		responseJSON($result);
    }

    public function send_daily_report_pdf() {
		$email = $this->input->post('email');
		
		if ($email) $emails = [
			(object) ['send_to' => $email]
		];
		else $emails = $this->scheduler->get_all_send_to();

		$result = [];
		foreach ($emails as $val) {
			if (!$val->send_to) continue;

            $title = 'Laporan Aktivitas PAR-MA - ' . format_date_id(date('Y-m-d'), true, false);
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetTitle($title);
            $pdf->AddPage();
            $pdf->SetDrawColor(255, 255, 255);
            $pdf->SetLineWidth(0);

            $tables = [
                'outlet_coverage' => [],
                'target_call_daily' => [],
                'target_call_monthly' => [],
            ];
            if (in_array($val->send_to, $this->super_email)) {
                $tables = $this->scheduler->get_data_send_email();
            } else {
                $tables = $this->scheduler->get_data_send_email($val->send_to);
            }

			$data = [
				'name' => explode('@', $val->send_to)[0] ?? $val->send_to,
				'subject' => 'Laporan Aktivitas PAR-MA - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_tables_html($tables),
			];
            $html = $this->load->view('emails/template', $data, TRUE);

            $pdf->writeHTML($html, true, false, true, false, '');
            $filePath = FCPATH . 'uploads/laporan_aktivitas_PAR-MA_' . date('Ymd') . '.pdf';
            $pdf->Output($filePath, 'F');

			if (sending_email($val->send_to, $data['subject'], 'emails/template', $data, [$filePath])) {
                log_message('info', 'Laporan harian terkirim (attach PDF): ' . date('Y-m-d H:i:s'));
				$result[] = $val->send_to . ': ✅ Email berhasil dikirim (attach PDF)!';
			} else {
                log_message('error', 'Gagal mengirim laporan (attach PDF): ' . $this->email->print_debugger());
				$result[] = $val->send_to . ': ❌ Gagal mengirim email (attach PDF).';
			}
            @unlink($filePath);
		}
		responseJSON($result);
    }

    public function send_daily_report_excel() {
		$email = $this->input->post('email');
		
		if ($email) $emails = [
			(object) ['send_to' => $email]
		];
		else $emails = $this->scheduler->get_all_send_to();

        $result = [];
		foreach ($emails as $val) {
			if (!$val->send_to) continue;

            $tables = [
                'outlet_coverage' => [],
                'target_call_daily' => [],
                'target_call_monthly' => [],
            ];
            if (in_array($val->send_to, $this->super_email)) {
                $tables = $this->scheduler->get_data_send_email();
            } else {
                $tables = $this->scheduler->get_data_send_email($val->send_to);
            }

            $objPHPExcel = new PHPExcel();

            // Outlet Coverage
            $objPHPExcel->createSheet(0);
            $sheetOutletCoverage = $objPHPExcel->setActiveSheetIndex(0);
            $sheetOutletCoverage->setTitle('Outlet Coverage');
            $sheetOutletCoverage
                ->setCellValue('A1', 'No')
                ->setCellValue('B1', 'City/Area')
                ->setCellValue('C1', 'Code PAR-MA')
                ->setCellValue('D1', 'PAR-MA Name')
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

            // Target Call Daily
            $objPHPExcel->createSheet(1);
            $sheetTargetCallDaily = $objPHPExcel->setActiveSheetIndex(1);
            $sheetTargetCallDaily->setTitle('Target Call Daily');
            $sheetTargetCallDaily
                ->setCellValue('A1', 'No')
                ->setCellValue('B1', 'Tanggal')
                ->setCellValue('C1', 'Code PAR-MA')
                ->setCellValue('D1', 'PAR-MA Name')
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

            // Target Call Monthly
            $objPHPExcel->createSheet(2);
            $sheetTargetCallMonthly = $objPHPExcel->setActiveSheetIndex(2);
            $sheetTargetCallMonthly->setTitle('Target Call Monthly');
            $sheetTargetCallMonthly
                ->setCellValue('A1', 'No')
                ->setCellValue('B1', 'Code PAR-MA')
                ->setCellValue('C1', 'PAR-MA Name')
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
            $filePath = FCPATH . 'uploads/laporan_aktivitas_PAR-MA_' . date('Ymd') . '.xlsx';
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($filePath);

			$data = [
				'name' => explode('@', $val->send_to)[0] ?? $val->send_to,
				'subject' => 'Laporan Aktivitas PAR-MA - ' . format_date_id(date('Y-m-d'), true, false),
				'message' => render_tables_html($tables),
			];

			if (sending_email($val->send_to, $data['subject'], 'emails/template', $data, [$filePath])) {
                log_message('info', 'Laporan harian terkirim (attach Excel): ' . date('Y-m-d H:i:s'));
				$result[] = $val->send_to . ': ✅ Email berhasil dikirim (attach Excel)!';
			} else {
                log_message('error', 'Gagal mengirim laporan (attach Excel): ' . $this->email->print_debugger());
				$result[] = $val->send_to . ': ❌ Gagal mengirim email (attach Excel).';
			}
            @unlink($filePath);
		}
		responseJSON($result);
    }
}
