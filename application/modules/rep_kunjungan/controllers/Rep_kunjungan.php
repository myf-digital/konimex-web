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
        }

        $hari_kerja = get_hari_kerja($start, $end);
        $year = date('Y', strtotime($end));
        $month = (int)date('m', strtotime($end));
        $start_month = date('Y-m-01', strtotime($end));
        $end_month = date('Y-m-t', strtotime($end));
        $hk_bulan_ini = get_hari_kerja($start_month, $end_month);

        $role_row = $this->db->query("SELECT value FROM ref_param_global WHERE key_param = 'key_role_sales' LIMIT 1")->row_array();
        $role_sales = !empty($role_row['value']) 
            ? "'" . implode("','", explode('|', $role_row['value'])) . "'" 
            : "''";

        $q_activity = $this->db->query("
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                CONCAT_WS(' - ', NULLIF(s.salesmanid, ''), NULLIF(s.nama_salesman, '')) as salesman,
                coalesce(nullif(area.nama_subarea, ''), nullif(area.nama_area, ''), nullif(area.nama_regional, ''), '') as wilayah,
                $hari_kerja as hari_kerja,
                coalesce(rmt.target_hari, 0) as target_hari,
                coalesce(v.visit_dokter, 0) as visit_dokter,
                coalesce(v.visit_apotek, 0) as visit_apotek,
                coalesce(v.visit_others, 0) as visit_others,
                coalesce(v.actual_call, 0) as actual_call,
                coalesce(v.actual_extra_call, 0) as actual_extra_call
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR '\\n') AS nama_subarea,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR '\\n') AS nama_area,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR '\\n') AS nama_regional
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
                    ar.role_name,
                    ROUND(((rmt.target_dub * rmt.target_call_visit) / NULLIF($hk_bulan_ini, 12)) * $hari_kerja) AS target_hari
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE rmt.tahun = '$year' AND rmt.bulan = '$month'
            ) rmt ON LOWER(TRIM(s.tipe_sales)) = LOWER(TRIM(rmt.role_name))
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
            WHERE (s.aktif = 1 OR v.salesmanid IS NOT NULL OR att.salesmanid IS NOT NULL)
              AND LOWER(TRIM(s.nama_salesman)) <> 'vacant'
              AND s.tipe_sales IN ($role_sales)
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
                a.check_in,
                a.check_out,
                TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit,
                d.image,
                (
                    SELECT COUNT(*) FROM
                    trx_visit_detailing x
                    WHERE x.salesmanid = a.salesmanid
                    AND x.customerid = a.customerid
                    AND x.periode = a.periode
                ) AS total_kunjungan
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

        $bulan_indo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $t_start = strtotime($start);
        $t_end = strtotime($end);
        $str_start = date('j', $t_start) . ' ' . ($bulan_indo[(int)date('n', $t_start)] ?? date('F', $t_start)) . ' ' . date('Y', $t_start);
        $str_end = date('j', $t_end) . ' ' . ($bulan_indo[(int)date('n', $t_end)] ?? date('F', $t_end)) . ' ' . date('Y', $t_end);
        $periode_title = ($start == $end) ? "Periode : " . $str_start : "Periode : " . $str_start . " - " . $str_end;
		
		        $html = '<div class="box-body" style="padding: 10px 0; white-space: normal;">';
        $html .= '<div class="nav-tabs-custom" style="box-shadow: none; margin-bottom: 0;">';
        $html .= '<ul class="nav nav-tabs">';
        $html .= '<li class="active"><a href="#tab_activity_report" data-toggle="tab" style="font-weight: bold;"><i class="fa fa-bar-chart"></i> Activity Report</a></li>';
        $html .= '<li><a href="#tab_list_kunjungan" data-toggle="tab" style="font-weight: bold;"><i class="fa fa-list"></i> List Kunjungan</a></li>';
        $html .= '</ul>';
        
        $html .= '<div class="tab-content" style="padding: 15px 0;">';

        $html .= '<div class="tab-pane active" id="tab_activity_report">';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th style="width: 50px; text-align: center;">No</th>';
		$html .= '<th style="width: 200px; text-align: center;">Nama</th>';
		$html .= '<th style="width: 120px; text-align: center;">Jabatan</th>';
		$html .= '<th style="width: 180px; text-align: center;">Wilayah</th>';
		$html .= '<th style="width: 100px; text-align: center;">Hari Kerja</th>';
		$html .= '<th style="width: 100px; text-align: center;">Target/Hari</th>';
		$html .= '<th style="width: 110px; text-align: center;">Visit Dokter</th>';
		$html .= '<th style="width: 110px; text-align: center;">Visit Apotek</th>';
		$html .= '<th style="width: 110px; text-align: center;">Visit Others</th>';
        $html .= '<th style="width: 110px; text-align: center;">Actual Call</th>';
        $html .= '<th style="width: 130px; text-align: center;">Actual Extra Call</th>';
        $html .= '<th style="width: 110px; text-align: center;">Achievement</th>';
        $html .= '<th style="width: 165px; text-align: center;">Remark</th>';
        $html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="container-table-content">';
        $html .= '<table class="table table-striped table-bordered table-condensed fixed-table">';
        $html .= '<tbody>';

        $i_act = 1;
        if (!empty($lovactivity)) {
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
                    $remark_bg = '#C6EFCE';
                    $remark_fg = '#006100';
                } else if ($ach_num >= 90) {
                    $remark = 'Hampir Tercapai';
                    $remark_bg = '#FFEB9C';
                    $remark_fg = '#9C6500';
                } else {
                    $remark = 'Perlu Follow Up';
                    $remark_bg = '#FFC7CE';
                    $remark_fg = '#9C0006';
                }

                $html .= '<tr>';
                $html .= '<td style="width: 50px; text-align: center;">' . $i_act . '</td>';
                $html .= '<td style="width: 200px; text-align: left;">' . ($act['salesman'] ?? '') . '</td>';
                $html .= '<td style="width: 120px; text-align: center;">' . ($act['tipe_sales'] ?? '') . '</td>';
                $html .= '<td style="width: 180px; text-align: left;">' . ($act['wilayah'] ?? '') . '</td>';
                $html .= '<td style="width: 100px; text-align: center;">' . number_format((int)$act['hari_kerja'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 100px; text-align: center;">' . number_format((int)$act['target_hari'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 110px; text-align: center;">' . number_format((int)$act['visit_dokter'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 110px; text-align: center;">' . number_format((int)$act['visit_apotek'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 110px; text-align: center;">' . number_format((int)$act['visit_others'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 110px; text-align: center;">' . number_format((int)$act['actual_call'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 130px; text-align: center;">' . number_format((int)$act['actual_extra_call'], 0, '.', ',') . '</td>';
                $html .= '<td style="width: 110px; text-align: center; font-weight: bold;">' . $ach_str . '</td>';
                $html .= '<td style="width: 150px; text-align: center; background-color: ' . $remark_bg . '; color: ' . $remark_fg . '; font-weight: bold;">' . $remark . '</td>';
                $html .= '</tr>';
                $i_act++;
            }
        } else {
            $html .= '<tr><td colspan="13" style="text-align: center; padding: 20px; color: #888;">Tidak ada data activity report untuk periode ini.</td></tr>';
        }

        $html .= '</tbody>';
        $html .= '</table></div>';
        $html .= '</div>';

        $html .= '<div class="tab-pane" id="tab_list_kunjungan">';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';

		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 100px">Periode</th>';
		$html .= '<th style="width: 150px">Medrep</th>';
		$html .= '<th style="width: 150px">Outlet ID</th>';
		$html .= '<th style="width: 200px">Outlet</th>';
		$html .= '<th style="width: 200px">Alamat</th>';
		$html .= '<th style="width: 200px">Area</th>';
		$html .= '<th style="width: 100px">Cluster</th>';
		$html .= '<th style="width: 100px">Checkin</th>';
        $html .= '<th style="width: 100px">Checkout</th>';
        $html .= '<th style="width: 100px">Durasi</th>';
        $html .= '<th style="width: 100px">Jarak</th>';
        $html .= '<th style="width: 100px">Kunjungan</th>';
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
			$html .= '<td style="width: 100px">'.$value['total_kunjungan'].' User</td>';
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
		$html .= '</table></div>';
        $html .= '</div>'; // End tab_list_kunjungan

        $html .= '</div>'; // End tab-content
        $html .= '</div>'; // End nav-tabs-custom
        $html .= '</div>'; // End box-body

        $html .= '<script type="text/javascript">
                $("#tab_activity_report .container-table-content").on("scroll", function() {
                    $("#tab_activity_report .container-table").scrollLeft($(this).scrollLeft());
                });
                $("#tab_activity_report .container-table").on("scroll", function() {
                    $("#tab_activity_report .container-table-content").scrollLeft($(this).scrollLeft());
                });

                $("#tab_list_kunjungan .container-table-content").on("scroll", function() {
                    $("#tab_list_kunjungan .container-table").scrollLeft($(this).scrollLeft());
                });
                $("#tab_list_kunjungan .container-table").on("scroll", function() {
                    $("#tab_list_kunjungan .container-table-content").scrollLeft($(this).scrollLeft());
                });

                function preview_image(parma,image,ket) {
                    let tempFile = [{
                        href: image,
                        title: `MEDREP: ${parma} <br /> Keterangan: ${ket}`
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
        
        $hari_kerja = get_hari_kerja($start, $end);
        $year = date('Y', strtotime($end));
        $month = (int)date('m', strtotime($end));
        $start_month = date('Y-m-01', strtotime($end));
        $end_month = date('Y-m-t', strtotime($end));
        $hk_bulan_ini = get_hari_kerja($start_month, $end_month);

        $role_row = $this->db->query("SELECT value FROM ref_param_global WHERE key_param = 'key_role_sales' LIMIT 1")->row_array();
        $role_sales = !empty($role_row['value']) 
            ? "'" . implode("','", explode('|', $role_row['value'])) . "'" 
            : "''";

        $q_activity = $this->db->query("
            SELECT 
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                CONCAT_WS(' - ', NULLIF(s.salesmanid, ''), NULLIF(s.nama_salesman, '')) as salesman,
                coalesce(nullif(area.nama_subarea, ''), nullif(area.nama_area, ''), nullif(area.nama_regional, ''), '') as wilayah,
                $hari_kerja as hari_kerja,
                coalesce(rmt.target_hari, 0) as target_hari,
                coalesce(v.visit_dokter, 0) as visit_dokter,
                coalesce(v.visit_apotek, 0) as visit_apotek,
                coalesce(v.visit_others, 0) as visit_others,
                coalesce(v.actual_call, 0) as actual_call,
                coalesce(v.actual_extra_call, 0) as actual_extra_call
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT 
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR '\\n') AS nama_subarea,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR '\\n') AS nama_area,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR '\\n') AS nama_regional
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
                    ar.role_name,
                    ROUND(((rmt.target_dub * rmt.target_call_visit) / NULLIF($hk_bulan_ini, 12)) * $hari_kerja) AS target_hari
                FROM role_mapping_target rmt
                JOIN app_role ar ON ar.role_id = rmt.role_id
                WHERE rmt.tahun = '$year' AND rmt.bulan = '$month'
            ) rmt ON LOWER(TRIM(s.tipe_sales)) = LOWER(TRIM(rmt.role_name))
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
            WHERE (s.aktif = 1 OR v.salesmanid IS NOT NULL OR att.salesmanid IS NOT NULL)
              AND LOWER(TRIM(s.nama_salesman)) <> 'vacant'
              AND s.tipe_sales IN ($role_sales)
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
                a.check_in,
                a.check_out,
                TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit,
                d.image,
                (
                    SELECT COUNT(*) FROM
                    trx_visit_detailing x
                    WHERE x.salesmanid = a.salesmanid
                    AND x.customerid = a.customerid
                    AND x.periode = a.periode
                ) AS total_kunjungan
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

        $q_detailing = $this->db->query(" 
            SELECT 
                a.periode,
                a.salesmanid,
                s.nama_salesman,
                CONCAT(a.salesmanid, '-', s.nama_salesman) AS parma_user,
                COALESCE(NULLIF(c.nama_subarea, ''), NULLIF(c.nama_area, ''), NULLIF(c.nama_regional, ''), '') AS parma_area,
                a.customerid,
                b.nama_customer,
                b.typeid AS cluster,
                b.alamat,
                a.professional_name,
                rp.tipe_pic,
                rp.spesialisasi,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR '\n')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, a.array_product) > 0
                    ),
                    a.array_product
                ) AS products,
                DATE_FORMAT(a.start_detailing, '%H:%i') AS start_detailing,
                DATE_FORMAT(a.end_detailing, '%H:%i') AS end_detailing,
                TIMEDIFF(a.end_detailing, a.start_detailing) AS durasi,
                a.keterangan,
                a.reason,
                CASE
                    WHEN a.status = 5 THEN 'Tidak Valid'
                    WHEN a.status = 3 THEN 'Valid'
                    WHEN a.status = 2 THEN 'Belum Valid'
                    ELSE 'Butuh Verifikasi'
                END AS status_label,
                a.url_img_detailing,
                a.url_file_signature
            FROM trx_visit_detailing a
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
            LEFT JOIN (
                SELECT 
                    a.id,
                    a.type AS tipe_pic,
                    a.nama_professional,
                    b.name AS spesialisasi
                FROM ref_professional a
                LEFT JOIN ref_spesialisasi b ON b.id = a.spesialisasi_id
            ) rp ON rp.id = a.user_id
            WHERE a.periode BETWEEN '$start' AND '$end' $addquery $strquery
            ORDER BY a.periode DESC, a.start_detailing ASC;
        ");

        $lovdetailing = $q_detailing->result_array();

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

            $sheetAct->setCellValue('A' . $rowAct, $act['salesman']);
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
            ->setCellValue('M2', 'Kunjungan')
            ->setCellValue('N2', 'Foto');

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
                ->setCellValue('L'.$i, format_jarak($vkunjungan['jarak_meter']))
                ->setCellValue('M'.$i, $vkunjungan['total_kunjungan'].' User');

            if (!empty($vkunjungan['image'])) {
                $sheetKunjungan->setCellValue('N'.$i, 'Foto Kunjungan');
                $sheetKunjungan->getCell('N'.$i)->getHyperlink()->setUrl(URL_IMAGE.$vkunjungan['image']);
                $sheetKunjungan->getStyle('N'.$i)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => true,
                    ]
                ]);
            } else {
                $sheetKunjungan->setCellValue('N'.$i, '');
            }
            $i++;
            $no++;
        }

        $lastRow = ($i > 3) ? ($i - 1) : 2;

        $sheetKunjungan->mergeCells('A1:N1');
        $sheetKunjungan->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheetKunjungan->getStyle('A2:N2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheetKunjungan->getStyle('A2:N' . $lastRow)->applyFromArray([
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

        foreach (range('A', 'N') as $col) {
            $sheetKunjungan->getColumnDimension($col)->setAutoSize(true);
        }

        $sheetDetailing = $spreadsheet->createSheet(2);
        $spreadsheet->setActiveSheetIndex(2);
        $sheetDetailing->setTitle('Detailing Kunjungan');

        $sheetDetailing->setCellValue('A1', 'List Detailing Kunjungan TPE')
            ->setCellValue('A2', 'No.')
            ->setCellValue('B2', 'Periode')
            ->setCellValue('C2', 'TPE')
            ->setCellValue('D2', 'Outlet ID')
            ->setCellValue('E2', 'Outlet Name')
            ->setCellValue('F2', 'Area')
            ->setCellValue('G2', 'Tipe Outlet')
            ->setCellValue('H2', 'PIC / User')
            ->setCellValue('I2', 'Tipe PIC')
            ->setCellValue('J2', 'Spesialisasi')
            ->setCellValue('K2', 'Produk')
            ->setCellValue('L2', 'Start Detailing')
            ->setCellValue('M2', 'End Detailing')
            ->setCellValue('N2', 'Durasi')
            ->setCellValue('O2', 'Keterangan')
            ->setCellValue('P2', 'Status')
            ->setCellValue('Q2', 'Reason')
            ->setCellValue('R2', 'Foto Detailing')
            ->setCellValue('S2', 'Signature');

        $i = 3;
        $no = 1;
        foreach ($lovdetailing as $vdetailing) {
            $sheetDetailing->setCellValue('A'.$i, $no)
                ->setCellValue('B'.$i, $vdetailing['periode'])
                ->setCellValue('C'.$i, $vdetailing['parma_user'])
                ->setCellValue('D'.$i, $vdetailing['customerid'])
                ->setCellValue('E'.$i, $vdetailing['nama_customer'])
                ->setCellValue('F'.$i, $vdetailing['parma_area'])
                ->setCellValue('G'.$i, $vdetailing['cluster'])
                ->setCellValue('H'.$i, $vdetailing['professional_name'])
                ->setCellValue('I'.$i, $vdetailing['tipe_pic'])
                ->setCellValue('J'.$i, $vdetailing['spesialisasi'])
                ->setCellValue('K'.$i, $vdetailing['products'])
                ->setCellValue('L'.$i, $vdetailing['start_detailing'])
                ->setCellValue('M'.$i, $vdetailing['end_detailing'])
                ->setCellValue('N'.$i, $vdetailing['durasi'])
                ->setCellValue('O'.$i, $vdetailing['keterangan'])
                ->setCellValue('P'.$i, $vdetailing['status_label'])
                ->setCellValue('Q'.$i, $vdetailing['reason']);

            if (!empty($vdetailing['url_img_detailing'])) {
                $sheetDetailing->setCellValue('R'.$i, 'Foto Detailing');
                $sheetDetailing->getCell('R'.$i)->getHyperlink()->setUrl(URL_IMAGE.$vdetailing['url_img_detailing']);
                $sheetDetailing->getStyle('R'.$i)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => true,
                    ]
                ]);
            } else {
                $sheetDetailing->setCellValue('R'.$i, '');
            }

            if (!empty($vdetailing['url_file_signature'])) {
                $sheetDetailing->setCellValue('S'.$i, 'Signature');
                $sheetDetailing->getCell('S'.$i)->getHyperlink()->setUrl(URL_IMAGE.$vdetailing['url_file_signature']);
                $sheetDetailing->getStyle('S'.$i)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => true,
                    ]
                ]);
            } else {
                $sheetDetailing->setCellValue('S'.$i, '');
            }

            $i++;
            $no++;
        }

        $lastRowDetailing = ($i > 3) ? ($i - 1) : 2;

        $sheetDetailing->mergeCells('A1:S1');
        $sheetDetailing->getStyle('A1:S1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheetDetailing->getStyle('A2:S2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheetDetailing->getStyle('A2:S' . $lastRowDetailing)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        if ($lastRowDetailing >= 3) {
            $sheetDetailing->getStyle('A3:A' . $lastRowDetailing)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetDetailing->getStyle('K3:K' . $lastRowDetailing)->getAlignment()->setWrapText(true);
            $sheetDetailing->getStyle('A3:S' . $lastRowDetailing)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        foreach (range('A', 'S') as $col) {
            $sheetDetailing->getColumnDimension($col)->setAutoSize(true);
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