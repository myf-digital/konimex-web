<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_dokumentasi extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_dokumentasi_model', 'rep_dokumentasi');
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
        responseJSON($this->rep_dokumentasi->load($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->rep_dokumentasi->load_account($data));
    }

    function open_detail() {
		
		$start = $this->input->post("start");
		//$end = $this->input->post("end");
		$end = $this->input->post("start");
		$account = $this->input->post("account");
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

        $q = $this->db->query(" 
                                select a.periode, d.nama_class,a.customerid, a.salesmanid, a.check_in, a.check_out, a.latitude_cell, a.longitude_cell, e.nama_salesman,e.tipe_sales, 
                                c.kode_outlet, c.nama_customer,c.alamat,f.nama_regional, g.nama_area, h.nama_area as city, 
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_CHECKIN_CRC') as img_before,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_CHECKOUT_CRC') as img_after,
                                (select option from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_CHECKOUT_CRC') as option,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_I') as img_sell_out1,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_II') as img_sell_out2,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_III') as img_sell_out3
                                from t_sales_rrk_trans a 
                                join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where c.classid='$account' and a.periode between '$start' and '$start' $strquery;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Dokumentasi</h3>';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 100px">Tanggal</th>';
		$html .= '<th style="width: 200px">Account</th>';
		$html .= '<th style="width: 100px">Outlet ID</th>';
		$html .= '<th style="width: 150px">Kode Outlet</th>';
		$html .= '<th style="width: 300px">Nama Outlet</th>';
		$html .= '<th style="width: 600px">Alamat</th>';
		$html .= '<th style="width: 200px">Kota</th>';
		$html .= '<th style="width: 150px">Foto Before</th>';
		$html .= '<th style="width: 150px">Foto After</th>';
		$html .= '<th style="width: 150px">Status Planogram</th>';
		$html .= '<th style="width: 200px">Foto Selling Out I</th>';
		$html .= '<th style="width: 200px">Foto Selling Out II</th>';
        $html .= '<th style="width: 200px">Foto OOS</th>';
		$html .= '<th style="width: 300px">User MEDREP</th>';
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
			$html .= '<td style="width: 200px">'.$value['nama_class'].'</td>';
			$html .= '<td style="width: 100px">'.$value['customerid'].'</td>';
            $html .= '<td style="width: 150px">'.$value['kode_outlet'].'</td>';
			$html .= '<td style="width: 300px">'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 600px">'.$value['alamat'].'</td>';
            $html .= '<td style="width: 200px">'.$value['city'].'</td>';
            $html .= '<td style="width: 150px">';
            if (!empty($value['img_before']) or $value['img_before']<>''){
                $a=0;
                $arrimages = explode('|', $value['img_before']);
                foreach ($arrimages as $images) {
                    if ($a==0){
                        $html .= ' <img class="img-rounded" alt="Image Before" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
                    }else{
                        $html .= ' <br>'.$images;
                    }
                    $a++;
                }
            }
            $html .= '</td>';
            $html .= '<td style="width: 150px">';
            if (!empty($value['img_after']) or $value['img_after']<>''){
                $b=0;
                $arrimages = explode('|', $value['img_after']);
                foreach ($arrimages as $images) {
                    if ($b==0){
                        $html .= ' <img class="img-rounded" alt="Image After" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
                    }else{
                        $html .= ' <br>'.$images;
					}
                    $b++;
                }
            }
			$html .= '</td>';
			$html .= '<td style="width: 150px">'.$value['option'].'</td>';
            $html .= '<td style="width: 200px">';
            if (!empty($value['img_sell_out1']) or $value['img_sell_out1']<>''){
                $arrimages = explode('|', $value['img_sell_out1']);
                $c=0;
                foreach ($arrimages as $images) {
                    if ($c==0){
                        $html .= ' <img class="img-rounded" alt="Image Selling Out I" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
                    }else{
                        $html .= ' <br>'.$images;
                    }
                    $c++;
                }
            }
            $html .= '</td>';
            $html .= '<td style="width: 200px">';
            if (!empty($value['img_sell_out2']) or $value['img_sell_out2']<>''){
                $arrimages = explode('|', $value['img_sell_out2']);
                $d=0;
                foreach ($arrimages as $images) {
                    if ($d==0){
                        $html .= ' <img class="img-rounded" alt="Image Selling Out II" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
                    }else{
                        $html .= ' <br>'.$images;
                    }
                    $d++;
                }
            }
            $html .= '</td>';
            $html .= '<td style="width: 200px">';
            if (!empty($value['img_sell_out3']) or $value['img_sell_out3']<>''){
                $arrimages = explode('|', $value['img_sell_out3']);
                $e=0;
                foreach ($arrimages as $images) {
                    if ($e==0){
                        $html .= ' <img class="img-rounded" alt="Image  Selling Out III" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
                    }else{
                        $html .= ' <br>'.$images;
                    }
                    $e++;
                }
            }
            $html .= '</td>';
            $html .= '<td style="width: 300px">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
			
            //$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			//$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			//$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'">'.$flag.'</td>';
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
		$account = $this->uri->segment('8');

		$filename = "Report_Dokumentasi_".$start.".xlsx";

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

        $q = $this->db->query(" 
                                select a.periode, d.nama_class,a.customerid, a.salesmanid, a.check_in, a.check_out, a.latitude_cell, a.longitude_cell, e.nama_salesman,e.tipe_sales, 
                                c.kode_outlet, c.nama_customer,c.alamat,f.nama_regional, g.nama_area, h.nama_area as city, 
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_CHECKIN_CRC') as img_before,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_CHECKOUT_CRC') as img_after,
                                (select option from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_CHECKOUT_CRC') as option,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_I') as img_sell_out1,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_II') as img_sell_out2,
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_III') as img_sell_out3
                                from t_sales_rrk_trans a 
                                join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where c.classid='$account' and a.periode between '$start' and '$start' $strquery;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();

        $this->load->library('excel');
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Report Dokumentasi')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Account')
                    ->setCellValue('D2', 'Outlet ID')
                    ->setCellValue('E2', 'Kode Outlet')
                    ->setCellValue('F2', 'Nama Outlet')
                    ->setCellValue('G2', 'Alamat')
                    ->setCellValue('H2', 'Kota')
                    ->setCellValue('I2', 'Foto Before')
                    ->setCellValue('J2', 'Foto After')
                    ->setCellValue('K2', 'Status Planogram')
                    ->setCellValue('L2', 'Foto Selling Out I')
                    ->setCellValue('M2', 'Foto Selling Out II')
                    ->setCellValue('N2', 'Foto OOS')
                    ->setCellValue('O2', 'User MEDREP')
                    ;
        
                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($data as $lov) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $lov['periode'])
                                    ->setCellValue('C'.$i, $lov['nama_class'])
                                    ->setCellValue('D'.$i, $lov['customerid'])
                                    ->setCellValue('E'.$i, $lov['kode_outlet'])
                                    ->setCellValue('F'.$i, $lov['nama_customer'])
                                    ->setCellValue('G'.$i, $lov['alamat'])
                                    ->setCellValue('H'.$i, $lov['city']);
									// echo DIR_IMAGE_PATH.$lov['image'];
                                    // if(file_exists(DIR_IMAGE_PATH.$lov['image']))
                                    if (!empty($lov['img_before']) or $lov['img_before']<>''){
                                        $arrimages = explode('|', $lov['img_before']);
                                        $a=0;
                                        foreach ($arrimages as &$images) {
                                            if ($a==0){
                                                if(file_exists(DIR_IMAGE_PATH.$images))	
                                                {
                                                    // echo DIR_IMAGE_PATH.$lov['image'];
                                                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                    // $objDrawing->setPath($sampleimage);
                                                    $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                    $objDrawing->setWidth(120); 
                                                    $objDrawing->setHeight(120);
                                                    $objDrawing->setCoordinates('I'.$i);
                                                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(110);
                                                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                                                }
                                                else
                                                {
                                                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
                                                }
                                            }else{
                                                $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $images)->getStyle('I'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                                            }
                                            $a++;
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
                                    }

                                    if (!empty($lov['img_after']) or $lov['img_after']<>''){
                                        $arrimages = explode('|', $lov['img_after']);
                                        $a=0;
                                        foreach ($arrimages as &$images) {
                                            if ($a==0){
                                                if(file_exists(DIR_IMAGE_PATH.$images))	
                                                {
                                                    // echo DIR_IMAGE_PATH.$lov['image'];
                                                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                    // $objDrawing->setPath($sampleimage);
                                                    $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                    $objDrawing->setWidth(120); 
                                                    $objDrawing->setHeight(120);
                                                    $objDrawing->setCoordinates('J'.$i);
                                                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(110);
                                                    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                                                }
                                                else
                                                {
                                                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, '');
                                                }
                                            }else{
                                                $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $images)->getStyle('J'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                                            }
                                            $a++;
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, '');
                                    }

                                    $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $lov['option']);

                                    if (!empty($lov['img_sell_out1']) or $lov['img_sell_out1']<>''){
                                        $arrimages = explode('|', $lov['img_sell_out1']);
                                        $a=0;
                                        foreach ($arrimages as &$images) {
                                            if ($a==0){
                                                if(file_exists(DIR_IMAGE_PATH.$images))	
                                                {
                                                    // echo DIR_IMAGE_PATH.$lov['image'];
                                                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                    // $objDrawing->setPath($sampleimage);
                                                    $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                    $objDrawing->setWidth(120); 
                                                    $objDrawing->setHeight(120);
                                                    $objDrawing->setCoordinates('L'.$i);
                                                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(110);
                                                    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
                                                }
                                                else
                                                {
                                                    $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, '');
                                                }
                                            }else{
                                                $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $images)->getStyle('K'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                                            }
                                            $a++;
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, '');
                                    }

                                    if (!empty($lov['img_sell_out2']) or $lov['img_sell_out2']<>''){
                                        $arrimages = explode('|', $lov['img_sell_out2']);
                                        $a=0;
                                        foreach ($arrimages as &$images) {
                                            if ($a==0){
                                                if(file_exists(DIR_IMAGE_PATH.$images))	
                                                {
                                                    // echo DIR_IMAGE_PATH.$lov['image'];
                                                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                    // $objDrawing->setPath($sampleimage);
                                                    $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                    $objDrawing->setWidth(120); 
                                                    $objDrawing->setHeight(120);
                                                    $objDrawing->setCoordinates('M'.$i);
                                                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(110);
                                                    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                                                }
                                                else
                                                {
                                                    $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '');
                                                }
                                            }else{
                                                $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $images)->getStyle('L'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                                            }
                                            $a++;
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '');
                                    }

                                    if (!empty($lov['img_sell_out3']) or $lov['img_sell_out3']<>''){
                                        $arrimages = explode('|', $lov['img_sell_out3']);
                                        $a=0;
                                        foreach ($arrimages as &$images) {
                                            if ($a==0){
                                                if(file_exists(DIR_IMAGE_PATH.$images))	
                                                {
                                                    // echo DIR_IMAGE_PATH.$lov['image'];
                                                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                    // $objDrawing->setPath($sampleimage);
                                                    $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                    $objDrawing->setWidth(120); 
                                                    $objDrawing->setHeight(120);
                                                    $objDrawing->setCoordinates('N'.$i);
                                                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(110);
                                                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
                                                }
                                                else
                                                {
                                                    $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
                                                }
                                            }else{
                                                $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $images)->getStyle('M'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                                            }
                                            $a++;
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
                                    }

                                    $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $lov['salesmanid'].'-'.$lov['nama_salesman']);
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

    public function savetoxlsx_oos($data)
    {
		ini_set('memory_limit', '256M');
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
		$restrict_level = $this->uri->segment('7');
		$account = $this->uri->segment('8');

		$filename = "Report_Dokumentasi_".$start.".xlsx";

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

        $q = $this->db->query(" 
                                select a.periode, d.nama_class,a.customerid, a.salesmanid, a.check_in, a.check_out, a.latitude_cell, a.longitude_cell, e.nama_salesman,e.tipe_sales, 
                                c.kode_outlet, c.nama_customer,c.alamat,f.nama_regional, g.nama_area, h.nama_area as city, 
                                (select concat(image,'|',ifnull(description,'')) from m_customer_image where periode=a.periode and salesmanid=a.salesmanid and customerid=a.customerid and image_type='IMG_SELL_OUT_III') as img_sell_out3
                                from t_sales_rrk_trans a 
                                join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where c.classid= '$account' and a.periode between '$start' and '$start' $strquery;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();

        $this->load->library('excel');
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Report Dokumentasi')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Account')
                    ->setCellValue('D2', 'Outlet ID')
                    ->setCellValue('E2', 'Kode Outlet')
                    ->setCellValue('F2', 'Nama Outlet')
                    ->setCellValue('G2', 'Alamat')
                    ->setCellValue('H2', 'Kota')
                    ->setCellValue('I2', 'Foto OOS')
                    ->setCellValue('J2', 'User MEDREP')
                    ;
        
                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($data as $lov) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $lov['periode'])
                                    ->setCellValue('C'.$i, $lov['nama_class'])
                                    ->setCellValue('D'.$i, $lov['customerid'])
                                    ->setCellValue('E'.$i, $lov['kode_outlet'])
                                    ->setCellValue('F'.$i, $lov['nama_customer'])
                                    ->setCellValue('G'.$i, $lov['alamat'])
                                    ->setCellValue('H'.$i, $lov['city']);
									// echo DIR_IMAGE_PATH.$lov['image'];
                                    // if(file_exists(DIR_IMAGE_PATH.$lov['image']))
                                    if (!empty($lov['img_sell_out3']) or $lov['img_sell_out3']<>''){
                                        $arrimages = explode('|', $lov['img_sell_out3']);
                                        $a=0;
                                        foreach ($arrimages as &$images) {
                                            if ($a==0){
                                                if(file_exists(DIR_IMAGE_PATH.$images))	
                                                {
                                                    // echo DIR_IMAGE_PATH.$lov['image'];
                                                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                    // $objDrawing->setPath($sampleimage);
                                                    $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                    $objDrawing->setWidth(120); 
                                                    $objDrawing->setHeight(120);
                                                    $objDrawing->setCoordinates('M'.$i);
                                                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                    $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(110);
                                                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                                                }
                                                else
                                                {
                                                    $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
                                                }
                                            }else{
                                                $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $images)->getStyle('M'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                                            }
                                            $a++;
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
                                    }

                                    $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $lov['salesmanid'].'-'.$lov['nama_salesman']);
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
