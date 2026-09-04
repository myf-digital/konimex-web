<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Rep_kunjungan extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_kunjungan_model', 'rep_kunjungan');
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
        responseJSON($this->rep_kunjungan->load($data));
    }

    public function load_parma()
    {
        $data = param_input();
        responseJSON($this->rep_kunjungan->load_parma($data));
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
            $addquery = " AND a.salesmanid in ('" . $salesmanid . "') ";
        }
        $q = $this->db->query(" 
            SELECT
                a.periode,
                a.salesmanid,
                s.nama_salesman,
                c.nama_area,
                CONCAT(a.salesmanid, '-', s.nama_salesman) AS parma_user,
                COALESCE(NULLIF(c.nama_subarea, ''), NULLIF(c.nama_area, ''), NULLIF(c.nama_regional, ''), '') AS parma_area,
                a.customerid, b.nama_customer, b.typeid AS cluster, b.alamat,
                b.nama_area AS city, b.longitude, b.latitude, a.longitude_cell, a.latitude_cell,
                CASE
                    WHEN a.latitude_cell IS NULL OR a.longitude_cell IS NULL
                        OR b.latitude IS NULL OR b.longitude IS NULL
                        OR a.latitude_cell = 0 OR a.longitude_cell = 0
                        OR b.latitude = 0 OR b.longitude = 0
                    THEN NULL
                    ELSE CALCULATE_DISTANCE(b.latitude, b.longitude, a.latitude_cell, a.longitude_cell) * 1000
                END AS jarak_meter,
                a.check_in, a.check_out, TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit, d.image 
            FROM t_sales_rrk_trans a 
            LEFT JOIN v_outlet_all b ON a.customerid = b.customerid 
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
            ) c ON a.salesmanid = c.salesmanid 
            LEFT JOIN m_customer_image d ON a.periode = d.periode AND a.salesmanid = d.salesmanid AND a.customerid = d.customerid AND d.image_type = 'IMG_CHECKIN'
            WHERE a.periode BETWEEN '$start' AND '$end' $addquery $strquery
            ORDER BY a.periode DESC;
        ");

		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Kunjungan</h3>';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';

		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 100px">Periode</th>';
		$html .= '<th style="width: 150px">TPE</th>';
		$html .= '<th style="width: 150px">Outlet ID</th>';
		$html .= '<th style="width: 150px">Outlet</th>';
		$html .= '<th style="width: 200px">Alamat</th>';
		$html .= '<th style="width: 200px">Area</th>';
		$html .= '<th style="width: 100px">Cluster</th>';
		$html .= '<th style="width: 100px">Checkin</th>';
        $html .= '<th style="width: 100px">Checkout</th>';
        $html .= '<th style="width: 100px">Durasi</th>';
        $html .= '<th style="width: 100px">Jarak</th>';
        $html .= '<th style="width: 120px">Foto Checkin</th>';
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
			$html .= '<td style="width: 80px">'.$i.'</td>';
			$html .= '<td style="width: 100px">'.$value['periode'].'</td>';
			$html .= '<td style="width: 150px">'.$value['parma_user'].'</td>';
			$html .= '<td style="width: 150px">'.$value['customerid'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 200px">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 200px">'.$value['parma_area'].'</td>';
			$html .= '<td style="width: 100px">'.$value['cluster'].'</td>';
			$html .= '<td style="width: 100px">'.format_time($value['check_in']).'</td>';
			$html .= '<td style="width: 100px">'.format_time($value['check_out']).'</td>';
			$html .= '<td style="width: 100px">'.cal_duration_date($value['check_in'],$value['check_out']).'</td>';
			$html .= '<td style="width: 100px">'.format_jarak($value['jarak_meter']).'</td>';
            $html .= '<td style="width: 120px">';
            if (!empty($value['image']) or $value['image']<>''){
                $arrimages = explode(',', $value['image']);
                foreach ($arrimages as &$images) {
                    $imgIn = "'".$value['parma_user']."','".$urlimage.$images."','".($value['start_keterangan'] ?? '')."'";
                    $html .= ' <img class="img-rounded" onclick="preview_image('.$imgIn.')" alt="Image CheckIn" style="width:100px; height:100px; cursor:pointer;" src="'.$urlimage.$images.'">';
                }
            }
            $html .= '</td>';
			
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

		$filename = "Report_Kunjungan_".$start."-".$end.".xlsx";

        $strquery = "";
        $strquery_sales = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
                $strquery_sales = " AND s.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $addquery = "";
        $addquery_sales = "";
        if (!empty($salesmanid) && $salesmanid != 'all') {
            $addquery = " AND a.salesmanid in ('" . $salesmanid . "') ";
            $addquery_sales = " AND s.salesmanid in ('" . $salesmanid . "') ";
		    $filename = "Report_Kunjungan_" . $salesmanid . "_" . $start . "-" . $end . ".xlsx";
        }

        $q_activity = $this->db->query("
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                coalesce(nullif(area.nama_subarea, ''), nullif(area.nama_area, ''), nullif(area.nama_regional, ''), '') as wilayah,
                coalesce(att.hari_kerja, 0) as hari_kerja,
                coalesce(pjp.target_hari, 0) as target_hari,
                coalesce(v.visit_dokter, 0) as visit_dokter,
                coalesce(v.visit_apotek, 0) as visit_apotek,
                coalesce(v.visit_others, 0) as visit_others,
                coalesce(v.actual_call, 0) as actual_call,
                coalesce(v.actual_extra_call, 0) as actual_extra_call
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR '\n') AS nama_subarea,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR '\n') AS nama_area,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR '\n') AS nama_regional
                FROM m_salesman_area msa
                LEFT JOIN m_area_subarea sa ON msa.subareaid = sa.subareaid
                LEFT JOIN m_area_areasite ar ON msa.areaid = ar.areaid
                LEFT JOIN m_area_regional r ON msa.regionalid = r.regionalid
                GROUP BY msa.salesmanid
            ) area ON s.salesmanid = area.salesmanid
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    COUNT(DISTINCT periode) AS hari_kerja
                FROM attendance_parma
                WHERE periode BETWEEN '$start' AND '$end'
                GROUP BY salesmanid
            ) att ON s.salesmanid = att.salesmanid
            LEFT JOIN (
                SELECT 
                    d.salesmanid,
                    COUNT(1) AS target_hari
                FROM req_pjp_daily_detail d
                WHERE d.periode BETWEEN '$start' AND '$end'
                GROUP BY d.salesmanid
            ) pjp ON s.salesmanid = pjp.salesmanid
            LEFT JOIN (
                SELECT 
                    t.salesmanid,
                    COUNT(CASE WHEN LOWER(TRIM(c.typeid)) IN ('rumah sakit', 'klinik', 'praktek pribadi') THEN 1 END) AS visit_dokter,
                    COUNT(CASE WHEN LOWER(TRIM(c.typeid)) IN ('apotek', 'apotek panel') THEN 1 END) AS visit_apotek,
                    COUNT(CASE WHEN c.typeid IS NULL OR LOWER(TRIM(c.typeid)) NOT IN ('rumah sakit', 'klinik', 'praktek pribadi', 'apotek', 'apotek panel') THEN 1 END) AS visit_others,
                    COUNT(CASE WHEN rrk.user_id IS NOT NULL THEN 1 END) AS actual_call,
                    COUNT(CASE WHEN rrk.user_id IS NULL THEN 1 END) AS actual_extra_call
                FROM trx_visit_detailing t
                LEFT JOIN m_customer c ON t.customerid = c.customerid
                LEFT JOIN (
                    SELECT DISTINCT periode, salesmanid, customerid, user_id
                    FROM t_sales_rrk_user
                    WHERE periode BETWEEN '$start' AND '$end'
                ) rrk ON t.periode = rrk.periode 
                     AND t.salesmanid = rrk.salesmanid 
                     AND t.customerid = rrk.customerid 
                     AND t.user_id = rrk.user_id
                WHERE t.periode BETWEEN '$start' AND '$end'
                GROUP BY t.salesmanid
            ) v ON s.salesmanid = v.salesmanid
            WHERE (s.aktif = 1 OR v.salesmanid IS NOT NULL OR att.salesmanid IS NOT NULL OR pjp.salesmanid IS NOT NULL)
              AND LOWER(TRIM(s.nama_salesman)) <> 'vacant'
              $strquery_sales
              $addquery_sales
            ORDER BY s.nama_salesman ASC
        ");
        $lovactivity = $q_activity->result_array();

        $q = $this->db->query(" 
            SELECT 
                a.periode, 
                a.salesmanid, 
                s.nama_salesman, 
                c.nama_area, 
                CONCAT(a.salesmanid, '-', s.nama_salesman) AS parma_user,
                COALESCE(NULLIF(c.nama_subarea, ''), NULLIF(c.nama_area, ''), NULLIF(c.nama_regional, ''), '') AS parma_area,
                a.customerid, b.nama_customer, b.typeid AS cluster, b.alamat,
                b.nama_area AS city, b.longitude, b.latitude, a.longitude_cell, a.latitude_cell,
                CASE
                    WHEN a.latitude_cell IS NULL OR a.longitude_cell IS NULL
                        OR b.latitude IS NULL OR b.longitude IS NULL
                        OR a.latitude_cell = 0 OR a.longitude_cell = 0
                        OR b.latitude = 0 OR b.longitude = 0
                    THEN NULL
                    ELSE CALCULATE_DISTANCE(b.latitude, b.longitude, a.latitude_cell, a.longitude_cell) * 1000
                END AS jarak_meter,
                a.check_in, a.check_out, TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit, d.image 
            FROM t_sales_rrk_trans a 
            LEFT JOIN v_outlet_all b ON a.customerid = b.customerid 
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
            ) c ON a.salesmanid = c.salesmanid 
            LEFT JOIN m_customer_image d ON a.periode = d.periode AND a.salesmanid = d.salesmanid AND a.customerid = d.customerid AND d.image_type = 'IMG_CHECKIN'
            WHERE a.periode BETWEEN '$start' AND '$end' $addquery $strquery
            ORDER BY a.periode DESC;
        ");

        $lovkunjungan = $q->result_array();

        $bulan_indo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $t_start = strtotime($start);
        $t_end = strtotime($end);
        $str_start = date('j', $t_start) . ' ' . ($bulan_indo[(int)date('n', $t_start)] ?? date('F', $t_start)) . ' ' . date('Y', $t_start);
        $str_end = date('j', $t_end) . ' ' . ($bulan_indo[(int)date('n', $t_end)] ?? date('F', $t_end)) . ' ' . date('Y', $t_end);
        $periode_title = ($start == $end) ? "Periode : " . $str_start : "Periode : " . $str_start . " - " . $str_end;
        
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        $sheetAct = $spreadsheet->getActiveSheet();
        $sheetAct->setTitle('Activity Report');

        $sheetAct->setCellValue('A1', 'ACTIVITY REPORT');
        $sheetAct->setCellValue('A2', $periode_title);

        $sheetAct->mergeCells('A1:L1');
        $sheetAct->mergeCells('A2:L2');

        $sheetAct->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheetAct->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $headersAct = [
            'A3' => 'Nama',
            'B3' => 'Jabatan',
            'C3' => 'Wilayah',
            'D3' => 'Hari Kerja',
            'E3' => 'Target/Hari',
            'F3' => 'Visit Dokter',
            'G3' => 'Visit Apotek',
            'H3' => 'Visit Others',
            'I3' => 'Actual Call',
            'J3' => 'Actual Extra Call',
            'K3' => 'Achievement',
            'L3' => 'Remark'
        ];

        foreach ($headersAct as $cell => $val) {
            $sheetAct->setCellValue($cell, $val);
        }

        $sheetAct->getStyle('A3:L3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0070C0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheetAct->getRowDimension(3)->setRowHeight(28);

        $rowAct = 4;
        foreach ($lovactivity as $act) {
            $total_actual = (int)$act['actual_call'] + (int)$act['actual_extra_call'];
            $target = (int)$act['target_hari'];

            if ($target > 0) {
                $ach_num = round(($total_actual / $target) * 100);
                $ach_str = $ach_num . '%';
            } else {
                $ach_num = ($total_actual > 0) ? 100 : 0;
                $ach_str = ($total_actual > 0) ? '100%' : '0%';
            }

            if ($ach_num >= 100) {
                $remark = 'Target Tercapai';
                $remark_bg = 'C6EFCE';
                $remark_fg = '006100';
            } else if ($ach_num >= 90) {
                $remark = 'Hampir Tercapai';
                $remark_bg = 'FFEB9C';
                $remark_fg = '9C6500';
            } else {
                $remark = 'Perlu Follow Up';
                $remark_bg = 'FFC7CE';
                $remark_fg = '9C0006';
            }

            $sheetAct->setCellValue('A' . $rowAct, $act['nama_salesman']);
            $sheetAct->setCellValue('B' . $rowAct, $act['tipe_sales']);
            $sheetAct->setCellValue('C' . $rowAct, $act['wilayah']);
            $sheetAct->setCellValue('D' . $rowAct, (int)$act['hari_kerja']);
            $sheetAct->setCellValue('E' . $rowAct, (int)$act['target_hari']);
            $sheetAct->setCellValue('F' . $rowAct, (int)$act['visit_dokter']);
            $sheetAct->setCellValue('G' . $rowAct, (int)$act['visit_apotek']);
            $sheetAct->setCellValue('H' . $rowAct, (int)$act['visit_others']);
            $sheetAct->setCellValue('I' . $rowAct, (int)$act['actual_call']);
            $sheetAct->setCellValue('J' . $rowAct, (int)$act['actual_extra_call']);
            $sheetAct->setCellValue('K' . $rowAct, $ach_str);
            $sheetAct->setCellValue('L' . $rowAct, $remark);

            $sheetAct->getStyle('L' . $rowAct)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $remark_bg],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $remark_fg],
                ],
            ]);

            $rowAct++;
        }

        $lastRowAct = ($rowAct > 4) ? ($rowAct - 1) : 3;
        $sheetAct->getStyle('A3:L' . $lastRowAct)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        if ($lastRowAct >= 4) {
            $sheetAct->getStyle('A4:A' . $lastRowAct)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheetAct->getStyle('B4:B' . $lastRowAct)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheetAct->getStyle('C4:C' . $lastRowAct)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheetAct->getStyle('D4:L' . $lastRowAct)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        }

        foreach (range('A', 'L') as $col) {
            $sheetAct->getColumnDimension($col)->setAutoSize(true);
        }

        $sheetKunjungan = $spreadsheet->createSheet(1);
        $spreadsheet->setActiveSheetIndex(1);
        $sheetKunjungan->setTitle('Report Kunjungan');

        $sheetKunjungan->setCellValue('A1', 'List Report Kunjungan TPE')
            ->setCellValue('A2', 'No.')
            ->setCellValue('B2', 'Periode')
            ->setCellValue('C2', 'TPE')
            ->setCellValue('D2', 'Outlet ID')
            ->setCellValue('E2', 'Outlet Name')
            ->setCellValue('F2', 'Alamat')
            ->setCellValue('G2', 'Area')
            ->setCellValue('H2', 'Cluster')
            ->setCellValue('I2', 'CheckIn')
            ->setCellValue('J2', 'CheckOut')
            ->setCellValue('K2', 'Durasi')
            ->setCellValue('L2', 'Jarak')
            ->setCellValue('M2', 'Foto');

        $i = 3;
        $no = 1;
        foreach ($lovkunjungan as $vkunjungan) {
            $sheetKunjungan->setCellValue('A'.$i, $no)
                ->setCellValue('B'.$i, $vkunjungan['periode'])
                ->setCellValue('C'.$i, $vkunjungan['parma_user'])
                ->setCellValue('D'.$i, $vkunjungan['customerid'])
                ->setCellValue('E'.$i, $vkunjungan['nama_customer'])
                ->setCellValue('F'.$i, $vkunjungan['alamat'])
                ->setCellValue('G'.$i, $vkunjungan['parma_area'])
                ->setCellValue('H'.$i, $vkunjungan['cluster'])
                ->setCellValue('I'.$i, format_time($vkunjungan['check_in']))
                ->setCellValue('J'.$i, format_time($vkunjungan['check_out']))
                ->setCellValue('K'.$i, cal_duration_date($vkunjungan['check_in'],$vkunjungan['check_out']))
                ->setCellValue('L'.$i, format_jarak($vkunjungan['jarak_meter']));

            if (!empty($vkunjungan['image'])) {
                $sheetKunjungan->setCellValue('M'.$i, 'Foto Kunjungan');
                $sheetKunjungan->getCell('M'.$i)->getHyperlink()->setUrl(URL_IMAGE.$vkunjungan['image']);
                $sheetKunjungan->getStyle('M'.$i)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => true,
                    ]
                ]);
            } else {
                $sheetKunjungan->setCellValue('M'.$i, '');
            }
            $i++;
            $no++;
        }

        $lastRow = ($i > 3) ? ($i - 1) : 2;

        $sheetKunjungan->mergeCells('A1:M1');
        $sheetKunjungan->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheetKunjungan->getStyle('A2:M2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheetKunjungan->getStyle('A2:M' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        if ($lastRow >= 3) {
            $sheetKunjungan->getStyle('A3:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach (range('A', 'M') as $col) {
            $sheetKunjungan->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

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