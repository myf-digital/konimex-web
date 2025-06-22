<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_competitor extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_competitor_model', 'rep_competitor');
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
        responseJSON($this->rep_competitor->load($data));
    }

    public function load_promo()
    {
        $data = param_input();
        responseJSON($this->rep_competitor->load_promo($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->rep_competitor->load_account($data));
    }

    function open_detail() {
		
		$start = $this->input->post("start");
		$end = $this->input->post("end");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");

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

        //if ($idpromo!='null'){$addquery=" and a.idpromo in (".$idpromo.") ";} else { $addquery="";}

        /*$qold = $this->db->query(" 
                                select b.nama_invoice, b.category, d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_competitor a
                                join m_product_competitor b on a.productid=b.productid 
                                left join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");*/

        $q = $this->db->query(" 
                                select b.nama_invoice, b.category, d.nama_class,a.*,e.nama_salesman,e.tipe_sales,
                                f.nama_regional, g.nama_area, h.nama_area as city , a.start_date, a.end_date
                                from t_activity_competitor a
                                join m_product_competitor b on a.productid=b.productid 
                                left join m_customer_class d on a.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on e.regionalid=f.regionalid
                                left join m_area_areasite g on e.areaid = g.areaid
                                left join m_area_subarea h on e.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");
        //echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Promo Competitor</h3>';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 100px">Tanggal</th>';
		$html .= '<th style="width: 300px">User GFF</th>';
		$html .= '<th style="width: 200px">Account</th>';
		//$html .= '<th style="width: 200px">Outlet ID</th>';
		//$html .= '<th style="width: 200px">Kode Outlet</th>';
		//$html .= '<th style="width: 200px">Nama Outlet</th>';
		//$html .= '<th style="width: 200px">Alamat</th>';
		$html .= '<th style="width: 200px">Kota</th>';
		$html .= '<th style="width: 450px">Nama Produk</th>';
		$html .= '<th style="width: 80px">Sewa</th>';
		$html .= '<th style="width: 100px">Tipe Sewa</th>';
		$html .= '<th style="width: 200px">Harga Normal</th>';
        $html .= '<th style="width: 200px">Harga Promo</th>';
		$html .= '<th style="width: 100px">Start Periode</th>';
		$html .= '<th style="width: 100px">End Periode</th>';
        $html .= '<th style="width: 600px">Deskripsi</th>';
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
			//$html .= '<td style="width: 200px">'.$value['customerid'].'</td>';
            //$html .= '<td style="width: 200px">'.$value['kode_outlet'].'</td>';
			//$html .= '<td style="width: 200px">'.$value['nama_customer'].'</td>';
			//$html .= '<td style="width: 200px">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 200px">'.$value['city'].'</td>';
			$html .= '<td style="width: 450px">'.$value['nama_invoice'].'</td>';
			$html .= '<td style="width: 80px">'.$value['sewa'].'</td>';
			$html .= '<td style="width: 100px">'.$value['tipesewa'].'</td>';
			$html .= '<td style="width: 200px">'.$value['harga_normal'].'</td>';
			$html .= '<td style="width: 200px">'.$value['harga_promo'].'</td>';
			$html .= '<td style="width: 100px">'.$value['start_date'].'</td>';
			$html .= '<td style="width: 100px">'.$value['end_date'].'</td>';
			$html .= '<td style="width: 600px">'.$value['description'].'</td>';
			$html .= '<td style="width: 100px"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
			
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

	function open_detail_npd() {
		
		$start = $this->input->post("start");
		$end = $this->input->post("end");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");

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

        /*$qold = $this->db->query(" 
                                select d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                       f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_npd_competitor a
                                left join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");*/
        $q = $this->db->query(" 
                                select d.nama_class,a.*,e.nama_salesman,e.tipe_sales,
                                       f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_npd_competitor a
                                left join m_customer_class d on a.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on e.regionalid=f.regionalid
                                left join m_area_areasite g on e.areaid = g.areaid
                                left join m_area_subarea h on e.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List New Product Competitor</h3>';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-striped table-bordered table-condensed fixed-tabel">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
		$html .= '<th style="width: 80px">Tanggal</th>';
		$html .= '<th style="width: 200px">User GFF</th>';
		$html .= '<th style="width: 200px">Account</th>';
		//$html .= '<th style="width: 200px">Outlet ID</th>';
		//$html .= '<th style="width: 200px">Kode Outlet</th>';
		//$html .= '<th style="width: 200px">Nama Outlet</th>';
		//$html .= '<th style="width: 200px">Alamat</th>';
		$html .= '<th style="width: 200px">Kota</th>';
        $html .= '<th style="width: 450px">Nama Produk</th>';
        $html .= '<th style="width: 200px">Harga</th>';
		$html .= '<th style="width: 600px">Description</th>';
		$html .= '<th style="width: 200px">Foto</th>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';

		$html .= '<div class="container-table-content">';
		$html .= '<table class="table table-striped table-bordered table-condensed fixed-tabel">';
		$html .= '<tbody>';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td style="width: 80px">'.$i.'</td>';
			$html .= '<td style="width: 80px">'.$value['periode'].'</td>';
			$html .= '<td style="width: 200px">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
			$html .= '<td style="width: 200px">'.$value['nama_class'].'</td>';
            //$html .= '<td style="width: 200px">'.$value['customerid'].'</td>';
			//$html .= '<td style="width: 200px">'.$value['kode_outlet'].'</td>';
			//$html .= '<td style="width: 200px">'.$value['nama_customer'].'</td>';
			//$html .= '<td style="width: 200px">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 200px">'.$value['city'].'</td>';
			$html .= '<td style="width: 450px">'.$value['product_name'].'</td>';
			$html .= '<td style="width: 200px">'.number_format($value['harga_normal'], 0, '.', ',').'</td>';
			$html .= '<td style="width: 600px">'.$value['description'].'</td>';
			$html .= '<td style="width: 200px"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
			$html .= '</tr>';
    
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
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
        $restrict_level = $this->uri->segment('7');

		$period=date_create($start);
		$filename = "Promo_competitor_".date_format($period,"M-Y").".xlsx";

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
                                select b.nama_invoice, b.category, d.nama_class,a.*,e.nama_salesman,e.tipe_sales,
                                f.nama_regional, g.nama_area, h.nama_area as city , a.start_date, a.end_date
                                from t_activity_competitor a
                                join m_product_competitor b on a.productid=b.productid 
                                left join m_customer_class d on a.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on e.regionalid=f.regionalid
                                left join m_area_areasite g on e.areaid = g.areaid
                                left join m_area_subarea h on e.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");
        //echo $this->db->last_query();
		ini_set('memory_limit', '512M');
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Competitor')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'User GFF')
                    ->setCellValue('D2', 'Account')
                    //->setCellValue('E2', 'Outlet ID')
                    //->setCellValue('F2', 'Kode Outlet')
                    //->setCellValue('G2', 'Nama Outlet')
                    //->setCellValue('H2', 'Alamat')
                    ->setCellValue('E2', 'Kota')
                    ->setCellValue('F2', 'Nama Produk')
                    ->setCellValue('G2', 'Sewa')
                    ->setCellValue('H2', 'Tipe Sewa')
                    ->setCellValue('I2', 'Harga Normal')
                    ->setCellValue('J2', 'Harga Promo')
                    ->setCellValue('K2', 'Start Date')
                    ->setCellValue('L2', 'End Date')
                    ->setCellValue('M2', 'Deskripsi')
                    ->setCellValue('N2', 'Foto')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('D'.$i, $vpjp['nama_class'])
                                    //->setCellValue('E'.$i, $vpjp['customerid'])
                                    //->setCellValue('F'.$i, $vpjp['kode_outlet'])
                                    //->setCellValue('G'.$i, $vpjp['nama_customer'])
                                    //->setCellValue('H'.$i, $vpjp['alamat'])
                                    ->setCellValue('E'.$i, $vpjp['city'])
                                    ->setCellValue('F'.$i, $vpjp['nama_invoice'])
                                    ->setCellValue('G'.$i, $vpjp['sewa'])
                                    ->setCellValue('H'.$i, $vpjp['tipesewa'])
                                    ->setCellValue('I'.$i, $vpjp['harga_normal'])
                                    ->setCellValue('J'.$i, $vpjp['harga_promo'])
                                    ->setCellValue('K'.$i, $vpjp['start_date'])
                                    ->setCellValue('L'.$i, $vpjp['end_date'])
                                    ->setCellValue('M'.$i, $vpjp['description']);
									// echo DIR_IMAGE_PATH.$vpjp['image'];
                                    // if(file_exists(DIR_IMAGE_PATH.$vpjp['image']))
									if (!empty($vpjp['image'])) {
										
									
                                    // if(file_exists($sampleimage))
									if(file_exists(DIR_IMAGE_PATH.$vpjp['image']))	
                                    {
										// echo DIR_IMAGE_PATH.$vpjp['image'];
                                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                                        // $objDrawing->setPath($sampleimage);
                                        $objDrawing->setPath(DIR_IMAGE_PATH.$vpjp['image']);
                                        $objDrawing->setWidth(120); 
                                        $objDrawing->setHeight(120); 
                                        $objDrawing->setCoordinates('N'.$i);
                                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                        $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
                                    }
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
                                    }
									}
									else
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


    public function savetoxlsx_textonly($data)
    {
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
        $restrict_level = $this->uri->segment('7');

		$period=date_create($start);
		$filename = "Promo_competitor_".date_format($period,"M-Y").".xlsx";

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
                                select b.nama_invoice, b.category, d.nama_class,a.*,e.nama_salesman,e.tipe_sales,
                                f.nama_regional, g.nama_area, h.nama_area as city , a.start_date, a.end_date
                                from t_activity_competitor a
                                join m_product_competitor b on a.productid=b.productid 
                                left join m_customer_class d on a.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on e.regionalid=f.regionalid
                                left join m_area_areasite g on e.areaid = g.areaid
                                left join m_area_subarea h on e.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");
        //echo $this->db->last_query();
		ini_set('memory_limit', '512M');
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Competitor')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Nama Produk')
                    ->setCellValue('C2', 'Account')
                    ->setCellValue('D2', 'Deskripsi')
                    ->setCellValue('E2', 'Harga Normal')
                    ->setCellValue('F2', 'Harga Promo')
                    ->setCellValue('G2', 'Discount')
                    ->setCellValue('H2', 'Start Date')
                    ->setCellValue('I2', 'End Date')
                    ->setCellValue('J2', 'Kota')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['nama_invoice'])
                                    ->setCellValue('C'.$i, $vpjp['nama_class'])
                                    ->setCellValue('D'.$i, $vpjp['description'])
                                    ->setCellValue('E'.$i, $vpjp['harga_normal'])
                                    ->setCellValue('F'.$i, $vpjp['harga_promo'])
                                    ->setCellValue('G'.$i, (($vpjp['harga_normal']-$vpjp['harga_promo'])/$vpjp['harga_normal'])*100)
                                    ->setCellValue('H'.$i, $vpjp['start_date'])
                                    ->setCellValue('I'.$i, $vpjp['end_date'])
                                    ->setCellValue('J'.$i, $vpjp['city'])
									;
						$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode('#,##0.00');									
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
	
    public function savetoxlsx_npd($data)
    {
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
        $restrict_level = $this->uri->segment('7');

		$period=date_create($start);
        $filename = "Product_new_competitor_".date_format($period,"M-Y").".xlsx";

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
                                select d.nama_class,a.*,e.nama_salesman,e.tipe_sales,
                                       f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_npd_competitor a
                                left join m_customer_class d on a.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on e.regionalid=f.regionalid
                                left join m_area_areasite g on e.areaid = g.areaid
                                left join m_area_subarea h on e.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");
		//echo $this->db->last_query();
		ini_set('memory_limit', '512M');
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List New Product Competitor ')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'User GFF')
                    ->setCellValue('D2', 'Account')
                    //->setCellValue('E2', 'Outlet ID')
                    //->setCellValue('F2', 'Kode Outlet')
                    //->setCellValue('G2', 'Nama Outlet')
                    //->setCellValue('H2', 'Alamat')
                    ->setCellValue('E2', 'Kota')
                    ->setCellValue('F2', 'Nama Produk')
                    ->setCellValue('G2', 'Harga')
                    ->setCellValue('H2', 'Deskripsi')
                    ->setCellValue('I2', 'Foto')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('D'.$i, $vpjp['nama_class'])
                                    //->setCellValue('E'.$i, $vpjp['customerid'])
                                    //->setCellValue('F'.$i, $vpjp['kode_outlet'])
                                    //->setCellValue('G'.$i, $vpjp['nama_customer'])
                                    //->setCellValue('H'.$i, $vpjp['alamat'])
                                    ->setCellValue('E'.$i, $vpjp['city'])
                                    ->setCellValue('F'.$i, $vpjp['product_name'])
                                    ->setCellValue('G'.$i, $vpjp['harga_normal'])
                                    ->setCellValue('H'.$i, $vpjp['description']);
									// echo DIR_IMAGE_PATH.$vpjp['image'];
                                    // if(file_exists(DIR_IMAGE_PATH.$vpjp['image']))
									if (!empty($vpjp['image'])) {
                                    // if(file_exists($sampleimage))
                                        if(file_exists(DIR_IMAGE_PATH.$vpjp['image']))	
                                        {
                                            // echo DIR_IMAGE_PATH.$vpjp['image'];
                                            $objDrawing = new PHPExcel_Worksheet_Drawing();
                                            // $objDrawing->setPath($sampleimage);
                                            $objDrawing->setPath(DIR_IMAGE_PATH.$vpjp['image']);
                                            $objDrawing->setWidth(120); 
                                            $objDrawing->setHeight(120); 
                                            $objDrawing->setCoordinates('I'.$i);
                                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                                        } else {
                                            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
                                        }
									} else {
                                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
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


    public function savetoxlsx_npd_textonly($data)
    {
        
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');
        $restrict_level = $this->uri->segment('7');

		$period=date_create($start);
        $filename = "Product_new_competitor_".date_format($period,"M-Y").".xlsx";

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
                                select d.nama_class,a.*,e.nama_salesman,e.tipe_sales,
                                       f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_npd_competitor a
                                left join m_customer_class d on a.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on e.regionalid=f.regionalid
                                left join m_area_areasite g on e.areaid = g.areaid
                                left join m_area_subarea h on e.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$strquery.";
                            ");
		//echo $this->db->last_query();
		ini_set('memory_limit', '512M');
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List New Product Competitor ')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'User GFF')
                    ->setCellValue('D2', 'Account')
                    ->setCellValue('E2', 'Kota')
                    ->setCellValue('F2', 'Nama Produk')
                    ->setCellValue('G2', 'Harga')
                    ->setCellValue('H2', 'Deskripsi')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('D'.$i, $vpjp['nama_class'])
                                    ->setCellValue('E'.$i, $vpjp['city'])
                                    ->setCellValue('F'.$i, $vpjp['product_name'])
                                    ->setCellValue('G'.$i, $vpjp['harga_normal'])
                                    ->setCellValue('H'.$i, $vpjp['description']);
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
