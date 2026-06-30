<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

        if ($salesmanid!=''){$addquery=" and a.salesmanid in ('".$salesmanid."') ";} else { $addquery="";}
        $q = $this->db->query(" 
                                select
                                    a.periode,
                                    a.salesmanid,
                                    c.nama_salesman,
                                    c.nama_area,
                                    concat(a.salesmanid, '-', c.nama_salesman) as parma_user,
                                    coalesce(nullif(c.nama_subarea, ''), nullif(c.nama_area, ''), nullif(c.nama_regional, ''), '') as parma_area,
                                    a.customerid,b.nama_customer,b.typeid as cluster, b.alamat,
                                    b.nama_area as city, b.longitude, b.latitude,a.longitude_cell,a.latitude_cell,
                                    CASE
                                        WHEN a.latitude_cell IS NULL OR a.longitude_cell IS NULL
                                            OR b.latitude IS NULL OR b.longitude IS NULL
                                            OR a.latitude_cell = 0 OR a.longitude_cell = 0
                                            OR b.latitude = 0 OR b.longitude = 0
                                        THEN NULL
                                        ELSE CALCULATE_DISTANCE(b.latitude, b.longitude, a.latitude_cell, a.longitude_cell) * 1000
                                    END AS jarak_meter,
                                    a.check_in, a.check_out, TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit, d.image from 
                                    t_sales_rrk_trans a left join v_outlet_all b on a.customerid =b.customerid 
                                    left join (
                                        select 
                                            s.salesmanid,
                                            s.nama_salesman,
                                            (
                                                select group_concat(distinct sa.nama_area order by sa.nama_area asc separator ', ')
                                                from m_salesman_area msa
                                                join m_area_subarea sa on msa.subareaid = sa.subareaid
                                                where msa.salesmanid = s.salesmanid
                                            ) as nama_subarea,
                                            (
                                                select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                                                from m_salesman_area msa
                                                join m_area_areasite ar on msa.areaid = ar.areaid
                                                where msa.salesmanid = s.salesmanid
                                            ) as nama_area,
                                            (
                                                select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                                                from m_salesman_area msa
                                                join m_area_regional r on r.regionalid = msa.regionalid
                                                where msa.salesmanid = s.salesmanid
                                            ) as nama_regional
                                        from m_sales_salesman s
                                    ) c on a.salesmanid = c.salesmanid 
                                    left join m_customer_image d on a.periode =d.periode and a.salesmanid =d.salesmanid and a.customerid =d.customerid and d.image_type ='IMG_CHECKIN'
                                    where a.periode between '$start' and '$end' $addquery $strquery
                                order by a.periode desc;
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
		$html .= '<th style="width: 150px">User Medrep</th>';
		$html .= '<th style="width: 150px">Medrep Outlet ID</th>';
		$html .= '<th style="width: 150px">Customer ID Map</th>';
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
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        if ($salesmanid!=''){$addquery=" and a.salesmanid in ('".$salesmanid."') ";} else { $addquery="";}
        $q = $this->db->query(" 
                                select 
                                    a.periode, 
                                    a.salesmanid, 
                                    c.nama_salesman, 
                                    c.nama_area, 
                                    concat(a.salesmanid, '-', c.nama_salesman) as parma_user,
                                    coalesce(nullif(c.nama_subarea, ''), nullif(c.nama_area, ''), nullif(c.nama_regional, ''), '') as parma_area,
                                    a.customerid, b.nama_customer,b.typeid as cluster, b.alamat,
                                    b.nama_area as city, b.longitude, b.latitude,a.longitude_cell,a.latitude_cell,
                                    CASE
                                        WHEN a.latitude_cell IS NULL OR a.longitude_cell IS NULL
                                            OR b.latitude IS NULL OR b.longitude IS NULL
                                            OR a.latitude_cell = 0 OR a.longitude_cell = 0
                                            OR b.latitude = 0 OR b.longitude = 0
                                        THEN NULL
                                        ELSE CALCULATE_DISTANCE(b.latitude, b.longitude, a.latitude_cell, a.longitude_cell) * 1000
                                    END AS jarak_meter,
                                    a.check_in, a.check_out, TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit, d.image from 
                                    t_sales_rrk_trans a left join v_outlet_all b on a.customerid =b.customerid 
                                    left join (
                                        select 
                                            s.salesmanid,
                                            s.nama_salesman,
                                            (
                                                select group_concat(distinct sa.nama_area order by sa.nama_area asc separator ', ')
                                                from m_salesman_area msa
                                                join m_area_subarea sa on msa.subareaid = sa.subareaid
                                                where msa.salesmanid = s.salesmanid
                                            ) as nama_subarea,
                                            (
                                                select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                                                from m_salesman_area msa
                                                join m_area_areasite ar on msa.areaid = ar.areaid
                                                where msa.salesmanid = s.salesmanid
                                            ) as nama_area,
                                            (
                                                select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                                                from m_salesman_area msa
                                                join m_area_regional r on r.regionalid = msa.regionalid
                                                where msa.salesmanid = s.salesmanid
                                            ) as nama_regional
                                        from m_sales_salesman s
                                    ) c on a.salesmanid = c.salesmanid 
                                    left join m_customer_image d on a.periode =d.periode and a.salesmanid =d.salesmanid and a.customerid =d.customerid and d.image_type ='IMG_CHECKIN'
                                    where a.periode between '$start' and '$end' $addquery $strquery
                                order by a.periode desc;
                            ");

        $lovkunjungan = $q->result_array();
        
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'List Report Kunjungan')
            ->setCellValue('A2', 'No.')
            ->setCellValue('B2', 'Periode')
            ->setCellValue('C2', 'User Medrep')
            ->setCellValue('D2', 'Medrep Outlet ID')
            ->setCellValue('E2', 'Latest Customer Name')
            ->setCellValue('F2', 'Alamat')
            ->setCellValue('G2', 'Area')
            ->setCellValue('H2', 'Cluster')
            ->setCellValue('I2', 'CheckIn')
            ->setCellValue('J2', 'CheckOut')
            ->setCellValue('K2', 'Durasi')
            ->setCellValue('L2', 'Jarak')
            ->setCellValue('M2', 'Foto')
            ;

            $i = 3;
            $no = 1;
            foreach ($lovkunjungan as $vkunjungan) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $no)
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
                        if (file_exists(DIR_IMAGE_PATH.$vkunjungan['image'])) {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(DIR_IMAGE_PATH.$vkunjungan['image']);
                            $objDrawing->setWidth(120); 
                            $objDrawing->setHeight(120); 
                            $objDrawing->setCoordinates('O'.$i);
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '');
                        }
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '');
                    }
                $i++;
                $no++;
            }
                        
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

    }

}
