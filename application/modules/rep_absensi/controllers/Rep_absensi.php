<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

        if ($restrict_level=='4'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												)";
		}
		else if ($restrict_level=='3'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
											app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
											where a.username='".$usersession."')
												)";
		}
		else if ($restrict_level=='2'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												) ";
		}
		else {
			$strquery = "";
		}

        if ($salesmanid != '') {
            $addquery = " and a.salesmanid in ('".$salesmanid."') ";
        } else {
            $addquery = "";
        }
        $q = $this->db->query(" 
                                select a.periode, a.salesmanid, b.nama_salesman, b.nama_area, concat(a.salesmanid,'-',b.nama_salesman, '-',b.nama_area) as parma_user,
                                        a.start_time,a.start_image,a.start_keterangan,a.start_latitude,a.start_longitude,
                                        a.end_time,a.end_image,a.end_keterangan,a.end_latitude,a.end_longitude,
                                        TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time) AS durasi_menit
                                from 
                                attendance_parma a left join v_gff_info b on a.salesmanid =b.salesmanid 
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
		$html .= '<th style="width: 150px">User Parma</th>';
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
                        title: `PAR-MA: ${parma} <br /> Keterangan: ${ket}`
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

        if ($restrict_level=='4') {
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												)";
		} else if ($restrict_level=='3') {
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
											app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
											where a.username='".$usersession."')
												)";
		} else if ($restrict_level=='2') {
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												) ";
		} else {
			$strquery = "";
		}

        if ($salesmanid!='') {
            $addquery=" and a.salesmanid in ('".$salesmanid."') ";
        } else {
            $addquery="";
        }
        $q = $this->db->query(" 
                                select a.periode, a.salesmanid, b.nama_salesman, b.nama_area, concat(a.salesmanid,'-',b.nama_salesman, '-',b.nama_area) as parma_user,
                                        a.start_time,a.start_image,a.start_keterangan,a.start_latitude,a.start_longitude,
                                        a.end_time,a.end_image,a.end_keterangan,a.end_latitude,a.end_longitude,
                                        TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time) AS durasi_menit
                                from 
                                attendance_parma a left join v_gff_info b on a.salesmanid =b.salesmanid 
                                where a.periode between '$start' and '$end' $addquery $strquery
                                order by a.periode desc;
                            ");

        $lovkunjungan = $q->result_array();
        
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'List Report Absensi Parma')
            ->setCellValue('A2', 'No.')
            ->setCellValue('B2', 'Periode')
            ->setCellValue('C2', 'User Parma')
            ->setCellValue('D2', 'Start Time')
            ->setCellValue('E2', 'Foto Checkin')
            ->setCellValue('F2', 'Keterangan Checkin')
            ->setCellValue('G2', 'End Time')
            ->setCellValue('H2', 'Foto Checkout')
            ->setCellValue('I2', 'Keterangan Checkout')
            ->setCellValue('J2', 'Durasi')
            ;

            $i = 3;
            $no = 1;
            foreach ($lovkunjungan as $vkunjungan) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $no)
                    ->setCellValue('B'.$i, $vkunjungan['periode'])
                    ->setCellValue('C'.$i, $vkunjungan['parma_user'])
                    ->setCellValue('D'.$i, format_time($vkunjungan['start_time']))
                    ->setCellValue('E'.$i, '')
                    ->setCellValue('F'.$i, $vkunjungan['start_keterangan'])
                    ->setCellValue('G'.$i, format_time($vkunjungan['end_time']))
                    ->setCellValue('H'.$i, '')
                    ->setCellValue('I'.$i, $vkunjungan['end_keterangan'])
                    ->setCellValue('J'.$i, cal_duration_date($vkunjungan['start_time'],$vkunjungan['end_time']));

                    if (!empty($vkunjungan['start_image']) or $vkunjungan['start_image']<>'') { 
                        if (file_exists(DIR_IMAGE_PATH.$vkunjungan['start_image'])) {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(DIR_IMAGE_PATH.$vkunjungan['start_image']);
                            $objDrawing->setWidth(120); 
                            $objDrawing->setHeight(120); 
                            $objDrawing->setCoordinates('E'.$i);
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, '');
                        }
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, '');
                    }

                    if (!empty($vkunjungan['end_image']) or $vkunjungan['end_image']<>'') {
                        if (file_exists(DIR_IMAGE_PATH.$vkunjungan['end_image']))	 {
                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(DIR_IMAGE_PATH.$vkunjungan['end_image']);
                            $objDrawing->setWidth(120); 
                            $objDrawing->setHeight(120); 
                            $objDrawing->setCoordinates('H'.$i);
                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, '');
                        }
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, '');
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
