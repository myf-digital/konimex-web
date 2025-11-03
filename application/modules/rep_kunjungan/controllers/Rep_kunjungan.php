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

        if ($salesmanid!=''){$addquery=" and a.salesmanid in ('".$salesmanid."') ";} else { $addquery="";}
        $q = $this->db->query(" 
                                select a.periode, a.salesmanid, c.nama_salesman, c.nama_area, concat(a.salesmanid,'-',c.nama_salesman, '-',c.nama_area) as parma_user,
                                    a.customerid, b.latest_jjid,b.cust_id_map,b.nama_customer,b.typeid as cluster, b.alamat,
                                    b.nama_area as city, b.longitude, b.latitude,a.longitude_cell,a.latitude_cell,CALCULATE_DISTANCE(b.latitude,b.longitude,a.latitude_cell,a.longitude_cell)*1000 as jarak_meter,
                                    a.check_in, a.check_out, TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit, d.image from 
                                    t_sales_rrk_trans a left join v_outlet_all b on a.customerid =b.customerid 
                                    left join v_gff_info c on a.salesmanid =c.salesmanid 
                                    left join m_customer_image d on a.periode =d.periode and a.salesmanid =d.salesmanid and a.customerid =d.customerid and d.image_type ='IMG_CHECKIN'
                                    where a.periode between '$start' and '$end' $addquery $strquery
                                order by a.periode desc;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Kunjungan</h3>';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';

		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 100px">Periode</th>';
		$html .= '<th style="width: 150px">User Parma</th>';
		$html .= '<th style="width: 150px">Parma Outlet ID</th>';
		$html .= '<th style="width: 150px">Latest JJID</th>';
		$html .= '<th style="width: 150px">Customer ID Map</th>';
		$html .= '<th style="width: 200px">Latest Customer Name</th>';
		$html .= '<th style="width: 200px">Alamat</th>';
		$html .= '<th style="width: 100px">Area</th>';
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
			$html .= '<td style="width: 150px">'.$value['latest_jjid'].'</td>';
            $html .= '<td style="width: 150px">'.$value['cust_id_map'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 200px">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 100px">'.$value['city'].'</td>';
			$html .= '<td style="width: 100px">'.$value['cluster'].'</td>';
			$html .= '<td style="width: 100px">'.$value['check_in'].'</td>';
			$html .= '<td style="width: 100px">'.$value['check_out'].'</td>';
			$html .= '<td style="width: 100px">'.$value['durasi_menit'].' Menit.</td>';
			$html .= '<td style="width: 100px">'.$value['jarak_meter'].' Meter.</td>';
            $html .= '<td style="width: 120px">';
            if (!empty($value['image']) or $value['image']<>''){
                $arrimages = explode(',', $value['image']);
                foreach ($arrimages as &$images) {
                    $html .= ' <img class="img-rounded" alt="Image CheckIn" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
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
            </script>';
				
		echo $html;

	}

    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '256M');
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
		$restrict_level = $this->uri->segment('7');
		$salesmanid = $this->uri->segment('8');

		$filename = "Report_Kunjungan_".$start."-".$end.".xlsx";

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

        if ($salesmanid!=''){$addquery=" and a.salesmanid in ('".$salesmanid."') ";} else { $addquery="";}
        $q = $this->db->query(" 
                                select a.periode, a.salesmanid, c.nama_salesman, c.nama_area, concat(a.salesmanid,'-',c.nama_salesman, '-',c.nama_area) as parma_user,
                                    a.customerid, b.latest_jjid,b.cust_id_map,b.nama_customer,b.typeid as cluster, b.alamat,
                                    b.nama_area as city, b.longitude, b.latitude,a.longitude_cell,a.latitude_cell,CALCULATE_DISTANCE(b.latitude,b.longitude,a.latitude_cell,a.longitude_cell)*1000 as jarak_meter,
                                    a.check_in, a.check_out, TIMESTAMPDIFF(MINUTE, a.check_in, a.check_out) AS durasi_menit, d.image from 
                                    t_sales_rrk_trans a left join v_outlet_all b on a.customerid =b.customerid 
                                    left join v_gff_info c on a.salesmanid =c.salesmanid 
                                    left join m_customer_image d on a.periode =d.periode and a.salesmanid =d.salesmanid and a.customerid =d.customerid and d.image_type ='IMG_CHECKIN'
                                    where a.periode between '$start' and '$end' $addquery $strquery
                                order by a.periode desc;
                            ");

        //echo $this->db->last_query();
        $lovkunjungan = $q->result_array();
        
        $this->load->library('excel');
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Report Kunjungan')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Periode')
                    ->setCellValue('C2', 'User Parma')
                    ->setCellValue('D2', 'Parma Outlet ID')
                    ->setCellValue('E2', 'Latest JJID')
                    ->setCellValue('F2', 'Customer ID Map')
                    ->setCellValue('G2', 'Latest Customer Name')
                    ->setCellValue('H2', 'Alamat')
                    ->setCellValue('I2', 'Area')
                    ->setCellValue('J2', 'Cluster')
                    ->setCellValue('K2', 'CheckIn')
                    ->setCellValue('L2', 'CheckOut')
                    ->setCellValue('M2', 'Durasi')
                    ->setCellValue('N2', 'Jarak')
                    ->setCellValue('O2', 'Foto')
                    ;
        
                    $i = 3;
                    $no = 1;
					foreach ($lovkunjungan as $vkunjungan) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vkunjungan['periode'])
                                    ->setCellValue('C'.$i, $vkunjungan['parma_user'])
                                    ->setCellValue('D'.$i, $vkunjungan['customerid'])
                                    ->setCellValue('E'.$i, $vkunjungan['latest_jjid'])
                                    ->setCellValue('F'.$i, $vkunjungan['cust_id_map'])
                                    ->setCellValue('G'.$i, $vkunjungan['nama_customer'])
                                    ->setCellValue('H'.$i, $vkunjungan['alamat'])
                                    ->setCellValue('I'.$i, $vkunjungan['city'])
                                    ->setCellValue('J'.$i, $vkunjungan['cluster'])
                                    ->setCellValue('K'.$i, $vkunjungan['check_in'])
                                    ->setCellValue('L'.$i, $vkunjungan['check_out'])
                                    ->setCellValue('M'.$i, $vkunjungan['durasi_menit'].' Menit')
                                    ->setCellValue('N'.$i, $vkunjungan['jarak_meter'].' Meter');
									// echo DIR_IMAGE_PATH.$vkunjungan['image'];
                                    // if(file_exists(DIR_IMAGE_PATH.$vkunjungan['image']))
                                    if (!empty($vkunjungan['image']) or $vkunjungan['image']<>''){
                                        $arrimages = explode(',', $vkunjungan['image']);
                                        $w=count($arrimages)*15;
                                        $wimg=count($arrimages)*120;
                                        foreach ($arrimages as &$images) {
                                            // if(file_exists($sampleimage))
                                            if(file_exists(DIR_IMAGE_PATH.$images))	
                                            {
                                                // echo DIR_IMAGE_PATH.$vkunjungan['image'];
                                                $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                // $objDrawing->setPath($sampleimage);
                                                $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                $objDrawing->setWidth($wimg); 
                                                $objDrawing->setHeight(120);
                                                $objDrawing->setCoordinates('O'.$i);
                                                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth($w);
                                            }
                                            else
                                            {
                                                $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '');
                                            }
                                        }

                                    }else
                                    {
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
