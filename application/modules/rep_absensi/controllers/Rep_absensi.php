<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Rep_absensi extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_absensi_model', 'rep_absensi');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->rep_absensi->load($data));
    }

    public function load_parma()
    {
        $data = param_input();
        responseJSON($this->rep_absensi->load_parma($data));
    }

    function open_detail() {
		$start = $this->input->post("start");
		$end = $this->input->post("end");
		$salesmanid = $this->input->post("salesmanid");
		$idjabatan = $this->input->post("idjabatan");
		$restrict_level = $this->input->post("restrict_level");
		$usersession = $this->input->post("usersession");

        $strquery = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $addquery = "";
        if (!empty($salesmanid) && $salesmanid != 'all') {
            $addquery=" and a.salesmanid in ('".$salesmanid."') ";
        }
        $q = $this->db->query(" 
            select 
                a.periode, 
                a.salesmanid, 
                s.nama_salesman, 
                b.nama_area, 
                concat(a.salesmanid, '-', coalesce(s.nama_salesman, '')) as parma_user,
                coalesce(nullif(b.nama_subarea, ''), nullif(b.nama_area, ''), nullif(b.nama_regional, ''), '') as parma_area,
                a.start_time,a.start_image,a.start_keterangan,a.start_latitude,a.start_longitude,
                a.end_time,a.end_image,a.end_keterangan,a.end_latitude,a.end_longitude,
                TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time) AS durasi_menit
            from 
            attendance_parma a 
            LEFT JOIN m_sales_salesman s ON a.salesmanid = s.salesmanid
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR ', ') AS nama_subarea,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional
                FROM m_salesman_area msa
                LEFT JOIN m_area_subarea sa ON msa.subareaid = sa.subareaid
                LEFT JOIN m_area_areasite ar ON msa.areaid = ar.areaid
                LEFT JOIN m_area_regional r ON msa.regionalid = r.regionalid
                GROUP BY msa.salesmanid
            ) b ON a.salesmanid = b.salesmanid
            where a.periode between '$start' and '$end' $addquery $strquery
            order by a.periode desc;
        ");

		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Kunjungan</h3>';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th class="text-center" style="width: 50px">No</th>';
		$html .= '<th style="width: 100px">Periode</th>';
		$html .= '<th style="width: 150px">TPE</th>';
		$html .= '<th style="width: 150px">Area</th>';
		$html .= '<th style="width: 150px">Start Time</th>';
		$html .= '<th style="width: 120px">Foto Checkin</th>';
		$html .= '<th style="width: 150px">Keterangan Checkin</th>';
		$html .= '<th style="width: 150px">End Time</th>';
		$html .= '<th style="width: 120px">Foto Checkout</th>';
		$html .= '<th style="width: 150px">Keterangabn Checkout</th>';
		$html .= '<th style="width: 100px">Durasi</th>';
        $html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="container-table-content">';
        $html .= '<table class="table table-striped table-bordered table-condensed fixed-table">';
        $html .= '<tbody>';

        $i=1;
		foreach ($data as $value) {            
			$html .= '<tr>';
			$html .= '<td class="text-center" style="width: 50px">'.$i.'</td>';
			$html .= '<td style="width: 100px">'.$value['periode'].'</td>';
			$html .= '<td style="width: 150px">'.$value['parma_user'].'</td>';
			$html .= '<td style="width: 150px">'.$value['parma_area'].'</td>';
			$html .= '<td style="width: 150px">'.format_time($value['start_time']).'</td>';
            $html .= '<td style="width: 120px">';
            if (!empty($value['start_image']) or $value['start_image']<>''){
                $arrimages = explode(',', $value['start_image']);
                foreach ($arrimages as &$images) {
                    $imgIn = "'".$value['parma_user']."','".$urlimage.$images."','".($value['start_keterangan'] ?? '')."'";
                    $html .= ' <img class="img-rounded" onclick="preview_image('.$imgIn.')" alt="Image CheckIn" style="width:100px; height:100px; cursor:pointer;" src="'.$urlimage.$images.'">';
                }
            }
            $html .= '</td>';
			$html .= '<td style="width: 150px">'.$value['start_keterangan'].'</td>';
			$html .= '<td style="width: 150px">'.format_time($value['end_time']).'</td>';
			$html .= '<td style="width: 120px">';
            if (!empty($value['end_image']) or $value['end_image']<>''){
                $arrimages = explode(',', $value['end_image']);
                foreach ($arrimages as &$images) {
                    $imgOut = "'".$value['parma_user']."','".$urlimage.$images."','".($value['end_keterangan'] ?? '')."'";
                    $html .= ' <img class="img-rounded" onclick="preview_image('.$imgOut.')" alt="Image CheckOut" style="width:100px; height:100px; cursor:pointer;" src="'.$urlimage.$images.'">';
                }
            }
            $html .= '</td>';
            $html .= '<td style="width: 150px">'.$value['end_keterangan'].'</td>';
			$html .= '<td style="width: 100px">'.cal_duration_date($value['start_time'],$value['end_time']).'</td>';
			
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table></div></div>';
        $html .= '<script type="text/javascript">
                $(".container-table-content").on("scroll", function() {
                    $(".container-table").scrollLeft($(this).scrollLeft());
                });
                $(".container-table").on("scroll", function() {
                    $(".container-table-content").scrollLeft($(this).scrollLeft());
                });
                function preview_image(parma,image,ket) {
                    let tempFile = [{
                        href: image,
                        title: `TPE: ${parma} <br /> Keterangan: ${ket}`
                    }];
                    $.fancybox.open(tempFile, {
                        helpers: {
                            thumbs: {
                                width: 75,
                                height: 50
                            }
                        }
                    });
				}
            </script>';
		echo $html;
	}

    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '1024M');
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
		$restrict_level = $this->uri->segment('7');
		$salesmanid = $this->uri->segment('8');

		$filename = "Report_Absensi_".$start."-".$end.".xlsx";

        $strquery = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $addquery = "";
        if (!empty($salesmanid) && $salesmanid != 'all') {
            $addquery=" and a.salesmanid in ('".$salesmanid."') ";
		    $filename = "Report_Absensi_".$salesmanid."_".$start."-".$end.".xlsx";
        }
        $q = $this->db->query(" 
            select 
                a.periode, 
                a.salesmanid, 
                s.nama_salesman, 
                b.nama_area, 
                concat(a.salesmanid, '-', coalesce(s.nama_salesman, '')) as parma_user,
                coalesce(nullif(b.nama_subarea, ''), nullif(b.nama_area, ''), nullif(b.nama_regional, ''), '') as parma_area,
                a.start_time,
                a.start_image,
                a.start_keterangan,
                a.start_latitude,
                a.start_longitude,
                a.end_time,
                a.end_image,
                a.end_keterangan,
                a.end_latitude,
                a.end_longitude,
                TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time) AS durasi_menit
            from 
            attendance_parma a 
            LEFT JOIN m_sales_salesman s ON a.salesmanid = s.salesmanid
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR ', ') AS nama_subarea,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional
                FROM m_salesman_area msa
                LEFT JOIN m_area_subarea sa ON msa.subareaid = sa.subareaid
                LEFT JOIN m_area_areasite ar ON msa.areaid = ar.areaid
                LEFT JOIN m_area_regional r ON msa.regionalid = r.regionalid
                GROUP BY msa.salesmanid
            ) b ON a.salesmanid = b.salesmanid
            where a.periode between '$start' and '$end' $addquery $strquery
            order by a.periode desc;
        ");

        $lovkunjungan = $q->result_array();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report Absensi');

        $sheet->setCellValue('A1', 'List Report Absensi TPE')
            ->setCellValue('A2', 'No.')
            ->setCellValue('B2', 'Periode')
            ->setCellValue('C2', 'TPE')
            ->setCellValue('D2', 'Area')
            ->setCellValue('E2', 'Start Time')
            ->setCellValue('F2', 'Foto Checkin')
            ->setCellValue('G2', 'Keterangan Checkin')
            ->setCellValue('H2', 'End Time')
            ->setCellValue('I2', 'Foto Checkout')
            ->setCellValue('J2', 'Keterangan Checkout')
            ->setCellValue('K2', 'Durasi');

        $i = 3;
        $no = 1;
        foreach ($lovkunjungan as $vkunjungan) {
            $sheet->setCellValue('A'.$i, $no)
                ->setCellValue('B'.$i, $vkunjungan['periode'])
                ->setCellValue('C'.$i, $vkunjungan['parma_user'])
                ->setCellValue('D'.$i, $vkunjungan['parma_area'])
                ->setCellValue('E'.$i, format_time($vkunjungan['start_time']))
                ->setCellValue('F'.$i, '')
                ->setCellValue('G'.$i, $vkunjungan['start_keterangan'])
                ->setCellValue('H'.$i, format_time($vkunjungan['end_time']))
                ->setCellValue('I'.$i, '')
                ->setCellValue('J'.$i, $vkunjungan['end_keterangan'])
                ->setCellValue('K'.$i, cal_duration_date($vkunjungan['start_time'],$vkunjungan['end_time']));

            if (!empty($vkunjungan['start_image']) || $vkunjungan['start_image'] != '') { 
                $start_images = explode(',', $vkunjungan['start_image']);
                $start_img = trim($start_images[0]);
                $sheet->setCellValue('F'.$i, 'Foto Check In');
                $sheet->getCell('F'.$i)->getHyperlink()->setUrl(URL_IMAGE.$start_img);
                $sheet->getStyle('F'.$i)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => 'single'
                    ]
                ]);
            } else {
                $sheet->setCellValue('F'.$i, '');
            }

            if (!empty($vkunjungan['end_image']) || $vkunjungan['end_image'] != '') { 
                $end_images = explode(',', $vkunjungan['end_image']);
                $end_img = trim($end_images[0]);
                $sheet->setCellValue('I'.$i, 'Foto Check Out');
                $sheet->getCell('I'.$i)->getHyperlink()->setUrl(URL_IMAGE.$end_img);
                $sheet->getStyle('I'.$i)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => 'single'
                    ]
                ]);
            } else {
                $sheet->setCellValue('I'.$i, '');
            }

            $i++;
            $no++;
        }

        $lastRow = ($i > 3) ? ($i - 1) : 2;

        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:K2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        if ($lastRow >= 3) {
            $sheet->getStyle('A3:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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
}