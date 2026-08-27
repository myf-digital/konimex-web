<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_sos extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_sos_model', 'rep_sos');
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
        responseJSON($this->rep_sos->load($data));
    }

    public function load_promo()
    {
        $data = param_input();
        responseJSON($this->rep_sos->load_promo($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->rep_sos->load_account($data));
    }

    function open_detail() {
		
		$start = $this->input->post("start");
		$end = $this->input->post("end");
		$idaccount = $this->input->post("idaccount");
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

        $q_old = $this->db->query(" 
                            select a.periode, d.nama_class,a.qty_sos_gsk, a.qty_sos_competitor, 
                                case when a.type_sos='B' then 'Toothbrush' when a.type_sos='P' then 'Toothpaste' else '' end type_sos, 
                                a.sos, e.salesmanid, e.nama_salesman,e.tipe_sales, 
                                c.customerid, c.kode_outlet, c.nama_customer,c.alamat,f.nama_regional, g.nama_area, h.nama_area as city,
                                (select group_concat(image SEPARATOR ',')from m_customer_image where transaction_id=a.transaction_id)  image
                                from t_activity_sos a 
                                join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where c.classid='$idaccount' and a.periode between '$start' and '$end' $strquery;
                            ");
        $q = $this->db->query(" 
                            select a.periode, d.nama_class,a.qty_sos_gsk, a.qty_sos_competitor, 
                                case when a.type_sos='B' then 'Toothbrush' when a.type_sos='P' then 'Toothpaste' else '' end type_sos, 
                                a.sos, e.salesmanid, e.nama_salesman,e.tipe_sales, 
                                c.customerid, c.kode_outlet, c.nama_customer,c.alamat,f.nama_regional, g.nama_area, h.nama_area as city,
                                a.image
                                from t_activity_sos a 
                                join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where c.classid='$idaccount' and a.periode between '$start' and '$end' $strquery;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List SOS</h3>';
		$html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';

		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 100px">Tanggal</th>';
		$html .= '<th style="width: 300px">User TPE</th>';
		$html .= '<th style="width: 200px">Account</th>';
		$html .= '<th style="width: 100px">Outlet ID</th>';
		$html .= '<th style="width: 150px">Kode Outlet</th>';
		$html .= '<th style="width: 300px">Nama Outlet</th>';
		$html .= '<th style="width: 600px">Alamat</th>';
		$html .= '<th style="width: 200px">Kota</th>';
		$html .= '<th style="width: 200px">Kategori</th>';
		$html .= '<th style="width: 200px">Facing Produk GSK</th>';
		$html .= '<th style="width: 150px">Facing Kategori</th>';
		$html .= '<th style="width: 100px">SOS</th>';
        $html .= '<th style="width: 100px">Foto</th>';
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
			$html .= '<td style="width: 300px">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_class'].'</td>';
			$html .= '<td style="width: 100px">'.$value['customerid'].'</td>';
            $html .= '<td style="width: 150px">'.$value['kode_outlet'].'</td>';
			$html .= '<td style="width: 300px">'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 600px">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 200px">'.$value['city'].'</td>';
			$html .= '<td style="width: 200px">'.$value['type_sos'].'</td>';
			$html .= '<td style="width: 200px">'.$value['qty_sos_gsk'].'</td>';
			$html .= '<td style="width: 150px">'.$value['qty_sos_competitor'].'</td>';
			$html .= '<td style="width: 100px">'.$value['sos'].'</td>';
            $html .= '<td style="width: 100px">';
            if (!empty($value['image']) or $value['image']<>''){
                $arrimages = explode(',', $value['image']);
                foreach ($arrimages as &$images) {
                    $html .= ' <img class="img-rounded" alt="Image SOS" style="width:100px; height:100px;" src="'.$urlimage.$images.'">';
                }
            }
            $html .= '</td>';
			
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
		$idaccount = $this->uri->segment('8');

		$filename = "Report_SOS_".$start."-".$end.".xlsx";

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
                            select a.periode, d.nama_class,a.qty_sos_gsk, a.qty_sos_competitor, 
                                case when a.type_sos='B' then 'Toothbrush' when a.type_sos='P' then 'Toothpaste' else '' end type_sos, 
                                a.sos, e.salesmanid, e.nama_salesman,e.tipe_sales, 
                                c.customerid, c.kode_outlet, c.nama_customer,c.alamat,f.nama_regional, g.nama_area, h.nama_area as city,
                                (select group_concat(image SEPARATOR ',')from m_customer_image where transaction_id=a.transaction_id) as image
                                from t_activity_sos a 
                                join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where c.classid='$idaccount' and a.periode between '$start' and '$end' $strquery;
                            ");
        //echo $this->db->last_query();
        $lovsos = $q->result_array();
        
        $this->load->library('excel');
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Report SOS')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'User TPE')
                    ->setCellValue('D2', 'Account')
                    ->setCellValue('E2', 'Outlet ID')
                    ->setCellValue('F2', 'Kode Outlet')
                    ->setCellValue('G2', 'Nama Outlet')
                    ->setCellValue('H2', 'Alamat')
                    ->setCellValue('I2', 'Kota')
                    ->setCellValue('J2', 'Kategori')
                    ->setCellValue('K2', 'Facing Produk GSK')
                    ->setCellValue('L2', 'Facing Kategori')
                    ->setCellValue('M2', 'SOS')
                    ->setCellValue('N2', 'Foto')
                    ;
        
                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovsos as $vsos) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vsos['periode'])
                                    ->setCellValue('C'.$i, $vsos['salesmanid'].'-'.$vsos['nama_salesman'])
                                    ->setCellValue('D'.$i, $vsos['nama_class'])
                                    ->setCellValue('E'.$i, $vsos['customerid'])
                                    ->setCellValue('F'.$i, $vsos['kode_outlet'])
                                    ->setCellValue('G'.$i, $vsos['nama_customer'])
                                    ->setCellValue('H'.$i, $vsos['alamat'])
                                    ->setCellValue('I'.$i, $vsos['city'])
                                    ->setCellValue('J'.$i, $vsos['type_sos'])
                                    ->setCellValue('K'.$i, $vsos['qty_sos_gsk'])
                                    ->setCellValue('L'.$i, $vsos['qty_sos_competitor'])
                                    ->setCellValue('M'.$i, $vsos['sos']);
									// echo DIR_IMAGE_PATH.$vsos['image'];
                                    // if(file_exists(DIR_IMAGE_PATH.$vsos['image']))
                                    if (!empty($vsos['image']) or $vsos['image']<>''){
                                        $arrimages = explode(',', $vsos['image']);
                                        $w=count($arrimages)*15;
                                        $wimg=count($arrimages)*120;
                                        foreach ($arrimages as &$images) {
                                            // if(file_exists($sampleimage))
                                            if(file_exists(DIR_IMAGE_PATH.$images))	
                                            {
                                                // echo DIR_IMAGE_PATH.$vsos['image'];
                                                $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                // $objDrawing->setPath($sampleimage);
                                                $objDrawing->setPath(DIR_IMAGE_PATH.$images);
                                                $objDrawing->setWidth($wimg); 
                                                $objDrawing->setHeight(120);
                                                $objDrawing->setCoordinates('N'.$i);
                                                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth($w);
                                            }
                                            else
                                            {
                                                $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
                                            }
                                        }

                                    }else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
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
