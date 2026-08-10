<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_promo extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rep_promo_model', 'report_promo');
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
        responseJSON($this->report_promo->load($data));
    }

    public function load_promo()
    {
        $data = param_input();
        responseJSON($this->report_promo->load_promo($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->report_promo->load_account($data));
    }

    public function load_regional()
    {
        $data = param_input();
        responseJSON($this->report_promo->load_regional($data));
    }

    public function load_area()
    {
        $data = param_input();
        responseJSON($this->report_promo->load_area($data));
    }

    public function load_city()
    {
        $data = param_input();
        responseJSON($this->report_promo->load_city($data));
    }

    function open_detail_gimmick() {
		
		$tipepromo = $this->input->post("tipepromo");
		$idaccount = $this->input->post("idaccount");
		$idpromo = $this->input->post("idpromo");
		$start = $this->input->post("start");
		$end = $this->input->post("end");
		$regional = $this->input->post("regional");
		$area = $this->input->post("area");
		$subarea = $this->input->post("subarea");
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
        
        $regionalquery = $regional != 'null' ? ' and c.regionalid="'.$regional.'" ' : '';
        $areaquery = $area != 'null' ? ' and c.areaid="'.$area.'" ' : '';
        $subareaquery = ($subarea && $subarea != 'null') ? ' and c.subareaid="'.$subarea.'" ' : '';

        if ($idpromo!='null'){$addquery=" and a.idpromo in (".$idpromo.") ";} else { $addquery="";}

        $q = $this->db->query(" 
                                select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                       f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_promo_gsk_gimmick a
                                join mapping_promo_active b on a.idpromo=b.idpromo 
                                left join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$regionalquery.$areaquery.$subareaquery.$addquery.$strquery.";
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Promo Gimmick</h3>';
		$html .= '<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
        $html .= '<th style="width: 50px">No</th>';
		$html .= '<th style="width: 100px">Tanggal</th>';
		$html .= '<th style="width: 250px">Promo</th>';
		$html .= '<th style="width: 100px">Account</th>';
		$html .= '<th style="width: 150px">User MEDREP</th>';
		$html .= '<th style="width: 80px">Outlet ID</th>';
		$html .= '<th style="width: 100px">Kode Outlet</th>';
		$html .= '<th style="width: 250px">Nama Outlet</th>';
		$html .= '<th style="width: 350px">Alamat</th>';
		$html .= '<th style="width: 150px">Area</th>';
		//$html .= '<th style="white-space: nowrap;">Stock Awal</th>';
		$html .= '<th style="width: 100px">Qty Pasang</th>';
		$html .= '<th style="width: 200px">Deskripsi</th>';
		$html .= '<th style="width: 150px">Foto 1</th>';
		$html .= '<th style="width: 165px">Foto 2</th>';
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
			$html .= '<td style="width: 50px">'.$i.'</td>';
			$html .= '<td style="width: 100px">'.$value['periode'].'</td>';
			$html .= '<td style="width: 250px">'.$value['promo'].'</td>';
			$html .= '<td style="width: 100px">'.$value['nama_class'].'</td>';
			$html .= '<td style="width: 150px">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
            $html .= '<td style="width: 80px">'.$value['customerid'].'</td>';
			$html .= '<td style="width: 100px">'.$value['kode_outlet'].'</td>';
			$html .= '<td style="width: 250px">'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 350px">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 150px">'.$value['nama_area'].'</td>';
			//$html .= '<td style="width: 200px">'.$value['stock_awal'].'</td>';
			$html .= '<td style="width: 100px">'.$value['qty_pasang'].'</td>';
			$html .= '<td style="width: 200px">'.$value['description'].'</td>';
			$html .= '<td style="width: 150px"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
			$html .= '<td style="width: 150px"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image_st'].'"></td>';
			
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

	function open_detail() {
		
		$tipepromo = $this->input->post("tipepromo");
		$idaccount = $this->input->post("idaccount");
		$idpromo = $this->input->post("idpromo");
		$start = $this->input->post("start");
		$end = $this->input->post("end");
		$regional = $this->input->post("regional");
		$area = $this->input->post("area");
		$subarea = $this->input->post("subarea");
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

        $regionalquery = $regional != 'null' ? ' and c.regionalid="'.$regional.'" ' : '';
        $areaquery = $area != 'null' ? ' and c.areaid="'.$area.'" ' : '';
        $subareaquery = ($subarea && $subarea != 'null') ? ' and c.subareaid="'.$subarea.'" ' : '';

        if ($idpromo!='null'){$addquery=" and a.idpromo in (".$idpromo.") ";} else { $addquery="";}

        $q = $this->db->query(" 
                                select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                       f.nama_regional, g.nama_area, h.nama_area as ciry 
                                from t_activity_promo_gsk a
                                join mapping_promo_active b on a.idpromo=b.idpromo 
                                left join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where a.tipepromo=url_decode('$tipepromo') and a.periode between '".$start."' and '".$end."' ".$regionalquery.$areaquery.$subareaquery.$addquery.$strquery.";
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Promo</h3>';
		$html .='<div class="container-table">';
		$html .= '<table class="table table-bordered table-condensed fixed-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .= '<th style="width: 80px;">No</th>';
		$html .= '<th style="width: 100px">Tanggal</th>';
		$html .= '<th style="width: 450px">Promo</th>';
		$html .= '<th style="width: 150px">Account</th>';
		$html .= '<th style="width: 300px">User MEDREP</th>';
		$html .= '<th style="width: 100px">Outlet ID</th>';
		$html .= '<th style="width: 150px">Kode Outlet</th>';
		$html .= '<th style="width: 200px">Nama Outlet</th>';
		$html .= '<th style="width: 450px">Alamat</th>';
		$html .= '<th style="width: 200px">Area</th>';
		$html .= '<th style="width: 200px">Tipe Promo</th>';
		$html .= '<th style="width: 200px">Display</th>';
		$html .= '<th style="width: 200px">Harga Normal</th>';
		$html .= '<th style="width: 200px">Harga Promo</th>';
		$html .= '<th style="width: 200px">Deskripsi</th>';
		$html .= '<th style="width: 100px">Foto</th>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		$html .='<div class="container-table-content">';
		$html .= '<table class="table table-striped table-bordered table-condensed fixed-table">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td style="width: 80px;">'.$i.'</td>';
			$html .= '<td style="width: 100px;">'.$value['periode'].'</td>';
			$html .= '<td style="width: 450px;">'.$value['promo'].'</td>';
			$html .= '<td style="width: 150px;">'.$value['nama_class'].'</td>';
			$html .= '<td style="width: 300px;">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
            $html .= '<td style="width: 100px;">'.$value['customerid'].'</td>';
			$html .= '<td style="width: 150px;">'.$value['kode_outlet'].'</td>';
			$html .= '<td style="width: 200px;">'.$value['nama_customer'].'</td>';
			$html .= '<td style="width: 450px;">'.$value['alamat'].'</td>';
			$html .= '<td style="width: 200px;">'.$value['nama_area'].'</td>';
			$html .= '<td style="width: 200px;">'.$value['tipepromo'].'</td>';
			$html .= '<td style="width: 200px;">'.$value['display'].'</td>';
			$html .= '<td style="width: 200px;">'.number_format($value['harga_normal'], 0, '.', ',').'</td>';
			$html .= '<td style="width: 200px;">'.number_format($value['harga_promo'], 0, '.', ',').'</td>';
			$html .= '<td style="width: 200px;">'.$value['description'].'</td>';
			$html .= '<td style="width: 100px;"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
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
		ini_set('memory_limit', '512M');
        
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        $end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('6');
        $usersession = $this->uri->segment('7');
        $restrict_level = $this->uri->segment('8');
		$tipepromo = $this->uri->segment('9');
		$regional = $this->uri->segment('10');
		$area = $this->uri->segment('11');

        $subarea = $this->uri->segment('12');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }
		$filename = "Promo_".$start.".xlsx";

        $strquery = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regionalquery = $regional != 'null' ? ' and c.regionalid="'.$regional.'" ' : '';
        $areaquery = $area != 'null' ? ' and c.areaid="'.$area.'" ' : '';
        $subareaquery = ($subarea && $subarea != 'null') ? ' and c.subareaid="'.$subarea.'" ' : '';
        
        $q = $this->db->query(" 
                            select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                   f.nama_regional, g.nama_area, h.nama_area as city 
                            from t_activity_promo_gsk a
                            join mapping_promo_active b on a.idpromo=b.idpromo 
                            left join m_customer c on a.customerid=c.customerid
                            left join m_customer_class d on c.classid=d.classid
                            left join m_sales_salesman e on a.salesmanid=e.salesmanid
                            left join m_area_regional f on c.regionalid=f.regionalid
                            left join m_area_areasite g on c.areaid = g.areaid
                            left join m_area_subarea h on c.subareaid = h.subareaid
                            where a.tipepromo=url_decode('$tipepromo') and a.periode between '".$start."' and '".$end."' ".$regionalquery.$areaquery.$subareaquery.$strquery.$addquery.";
                        ");
        $lovpjp = $q->result_array();
        //echo $this->db->last_query();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Tanggal ')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Promo')
                    ->setCellValue('D2', 'Type Promo')
                    ->setCellValue('E2', 'Account')
                    ->setCellValue('F2', 'User MEDREP')
                    ->setCellValue('G2', 'Outlet ID')
                    ->setCellValue('H2', 'Kode Outlet')
                    ->setCellValue('I2', 'Nama Outlet')
                    ->setCellValue('J2', 'Alamat')
                    ->setCellValue('K2', 'Area')
                    ->setCellValue('L2', 'Display')
                    ->setCellValue('M2', 'Harga Normal')
                    ->setCellValue('N2', 'Harga Promo')
                    ->setCellValue('O2', 'Deskripsi')
                    ->setCellValue('P2', 'Foto')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['promo'])
                                    ->setCellValue('D'.$i, $vpjp['tipepromo'])
                                    ->setCellValue('E'.$i, $vpjp['nama_class'])
                                    ->setCellValue('F'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('G'.$i, $vpjp['customerid'])
                                    ->setCellValue('H'.$i, $vpjp['kode_outlet'])
                                    ->setCellValue('I'.$i, $vpjp['nama_customer'])
                                    ->setCellValue('J'.$i, $vpjp['alamat'])
                                    ->setCellValue('K'.$i, $vpjp['nama_area'])
                                    ->setCellValue('L'.$i, $vpjp['display'])
                                    ->setCellValue('M'.$i, $vpjp['harga_normal'])
                                    ->setCellValue('N'.$i, $vpjp['harga_promo'])
                                    ->setCellValue('O'.$i, $vpjp['description']);
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
                                        $objDrawing->setCoordinates('P'.$i);
                                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                        $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
                                    }
                                    else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, '');
                                    }
									}
									else
                                    {
                                        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, '');
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

    public function savetoxlsx_gimmick($data)
    {
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        $end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('6');
        $usersession = $this->uri->segment('7');
        $restrict_level = $this->uri->segment('8');
        $regional = $this->uri->segment('9');
        $area = $this->uri->segment('10');
        $subarea = $this->uri->segment('11');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }
		$filename = "Promo_Gimmick".$start.".xlsx";

        $strquery = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regionalquery = $regional != 'null' ? ' and c.regionalid="'.$regional.'" ' : '';
        $areaquery = $area != 'null' ? ' and c.areaid="'.$area.'" ' : '';
        $subareaquery = ($subarea && $subarea != 'null') ? ' and c.subareaid="'.$subarea.'" ' : '';

        $q = $this->db->query(" 
                            select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                   f.nama_regional, g.nama_area, h.nama_area as city 
                            from t_activity_promo_gsk_gimmick a
                            join mapping_promo_active b on a.idpromo=b.idpromo 
                            left join m_customer c on a.customerid=c.customerid
                            left join m_customer_class d on c.classid=d.classid
                            left join m_sales_salesman e on a.salesmanid=e.salesmanid
                            left join m_area_regional f on c.regionalid=f.regionalid
                            left join m_area_areasite g on c.areaid = g.areaid
                            left join m_area_subarea h on c.subareaid = h.subareaid
                            where a.periode between '".$start."' and '".$end."' ".$regionalquery.$areaquery.$subareaquery.$strquery.$addquery.";
                        ");
        ini_set('memory_limit', '512M');

        //echo $this->db->last_query(); die();
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Gimmick')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Promo')
                    ->setCellValue('D2', 'Account')
                    ->setCellValue('E2', 'User MEDREP')
                    ->setCellValue('F2', 'Outlet ID')
                    ->setCellValue('G2', 'Kode Outlet')
                    ->setCellValue('H2', 'Nama Outlet')
                    ->setCellValue('I2', 'Alamat')
                    ->setCellValue('J2', 'Area')
                    //->setCellValue('K2', 'Stock Awal')
                    ->setCellValue('K2', 'Qty Pasang')
                    ->setCellValue('L2', 'Deskripsi')
                    ->setCellValue('M2', 'Foto 1')
                    ->setCellValue('N2', 'Foto 2')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['promo'])
                                    ->setCellValue('D'.$i, $vpjp['nama_class'])
                                    ->setCellValue('E'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('F'.$i, $vpjp['customerid'])
                                    ->setCellValue('G'.$i, $vpjp['kode_outlet'])
                                    ->setCellValue('H'.$i, $vpjp['nama_customer'])
                                    ->setCellValue('I'.$i, $vpjp['alamat'])
                                    ->setCellValue('J'.$i, $vpjp['nama_area'])
                                    //->setCellValue('K'.$i, $vpjp['stock_awal'])
                                    ->setCellValue('K'.$i, $vpjp['qty_pasang'])
                                    ->setCellValue('L'.$i, $vpjp['description']);
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
                                            $objDrawing->setCoordinates('M'.$i);
                                            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                                        } else {
                                            $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '');
                                        }
									} else {
                                        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '');
                                    }

									if (!empty($vpjp['image_st'])) {
                                        // if(file_exists($sampleimage))
                                            if(file_exists(DIR_IMAGE_PATH.$vpjp['image_st']))	
                                            {
                                                // echo DIR_IMAGE_PATH.$vpjp['image'];
                                                $objDrawing = new PHPExcel_Worksheet_Drawing();
                                                // $objDrawing->setPath($sampleimage);
                                                $objDrawing->setPath(DIR_IMAGE_PATH.$vpjp['image_st']);
                                                $objDrawing->setWidth(120); 
                                                $objDrawing->setHeight(120); 
                                                $objDrawing->setCoordinates('N'.$i);
                                                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                                                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
                                            } else {
                                                $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
                                            }
                                        } else {
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

    public function savetoxlsx_text_only($data)
    {
		ini_set('memory_limit', '512M');
        
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        $end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('6');
        $usersession = $this->uri->segment('7');
        $restrict_level = $this->uri->segment('8');
		$tipepromo = $this->uri->segment('9');
		$regional = $this->uri->segment('10');
		$area = $this->uri->segment('11');
        $subarea = $this->uri->segment('12');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }
		$filename = "Promo_".$start.".xlsx";

        $strquery = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regionalquery = $regional != 'null' ? ' and c.regionalid="'.$regional.'" ' : '';
        $areaquery = $area != 'null' ? ' and c.areaid="'.$area.'" ' : '';
        $subareaquery = ($subarea && $subarea != 'null') ? ' and c.subareaid="'.$subarea.'" ' : '';

        $q = $this->db->query(" 
                            select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                   f.nama_regional, g.nama_area, h.nama_area as city 
                            from t_activity_promo_gsk a
                            join mapping_promo_active b on a.idpromo=b.idpromo 
                            left join m_customer c on a.customerid=c.customerid
                            left join m_customer_class d on c.classid=d.classid
                            left join m_sales_salesman e on a.salesmanid=e.salesmanid
                            left join m_area_regional f on c.regionalid=f.regionalid
                            left join m_area_areasite g on c.areaid = g.areaid
                            left join m_area_subarea h on c.subareaid = h.subareaid
                            where a.tipepromo=url_decode('$tipepromo') and a.periode between '".$start."' and '".$end."' ".$regionalquery.$areaquery.$subareaquery.$strquery.$addquery.";
                        ");
        $lovpjp = $q->result_array();
        //echo $this->db->last_query();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Tanggal ')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Promo')
                    ->setCellValue('D2', 'Type Promo')
                    ->setCellValue('E2', 'Account')
                    ->setCellValue('F2', 'User MEDREP')
                    ->setCellValue('G2', 'Outlet ID')
                    ->setCellValue('H2', 'Kode Outlet')
                    ->setCellValue('I2', 'Nama Outlet')
                    ->setCellValue('J2', 'Alamat')
                    ->setCellValue('K2', 'Area')
                    ->setCellValue('L2', 'Display')
                    ->setCellValue('M2', 'Harga Normal')
                    ->setCellValue('N2', 'Harga Promo')
                    ->setCellValue('O2', 'Deskripsi')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['promo'])
                                    ->setCellValue('D'.$i, $vpjp['tipepromo'])
                                    ->setCellValue('E'.$i, $vpjp['nama_class'])
                                    ->setCellValue('F'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('G'.$i, $vpjp['customerid'])
                                    ->setCellValue('H'.$i, $vpjp['kode_outlet'])
                                    ->setCellValue('I'.$i, $vpjp['nama_customer'])
                                    ->setCellValue('J'.$i, $vpjp['alamat'])
                                    ->setCellValue('K'.$i, $vpjp['nama_area'])
                                    ->setCellValue('L'.$i, $vpjp['display'])
                                    ->setCellValue('M'.$i, $vpjp['harga_normal'])
                                    ->setCellValue('N'.$i, $vpjp['harga_promo'])
                                    ->setCellValue('O'.$i, $vpjp['description']);
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

    public function savetoxlsx_gimmick_text_only($data)
    {
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        $end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('6');
        $usersession = $this->uri->segment('7');
        $restrict_level = $this->uri->segment('8');
        $regional = $this->uri->segment('9');
        $area = $this->uri->segment('10');
        $subarea = $this->uri->segment('11');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }
		$filename = "Promo_Gimmick".$start.".xlsx";

        $strquery = "";
        if (!empty($restrict_level)) {
            $restrict_query = get_salesman_restrict($usersession, $restrict_level);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regionalquery = $regional != 'null' ? ' and c.regionalid="'.$regional.'" ' : '';
        $areaquery = $area != 'null' ? ' and c.areaid="'.$area.'" ' : '';
        $subareaquery = ($subarea && $subarea != 'null') ? ' and c.subareaid="'.$subarea.'" ' : '';

        $q = $this->db->query(" 
                            select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                   f.nama_regional, g.nama_area, h.nama_area as city 
                            from t_activity_promo_gsk_gimmick a
                            join mapping_promo_active b on a.idpromo=b.idpromo 
                            left join m_customer c on a.customerid=c.customerid
                            left join m_customer_class d on c.classid=d.classid
                            left join m_sales_salesman e on a.salesmanid=e.salesmanid
                            left join m_area_regional f on c.regionalid=f.regionalid
                            left join m_area_areasite g on c.areaid = g.areaid
                            left join m_area_subarea h on c.subareaid = h.subareaid
                            where a.periode between '".$start."' and '".$end."' ".$regionalquery.$areaquery.$subareaquery.$strquery.$addquery.";
                        ");
        ini_set('memory_limit', '512M');

        //echo $this->db->last_query(); die();
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Gimmick')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Promo')
                    ->setCellValue('D2', 'Account')
                    ->setCellValue('E2', 'User MEDREP')
                    ->setCellValue('F2', 'Outlet ID')
                    ->setCellValue('G2', 'Kode Outlet')
                    ->setCellValue('H2', 'Nama Outlet')
                    ->setCellValue('I2', 'Alamat')
                    ->setCellValue('J2', 'Area')
                    //->setCellValue('K2', 'Stock Awal')
                    ->setCellValue('K2', 'Qty Pasang')
                    ->setCellValue('L2', 'Deskripsi')
                    ;

                    $i = 3;
                    $no = 1;
					// $sampleimage = "/var/www/digital-record-card-api/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                    foreach ($lovpjp as $vpjp) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vpjp['periode'])
                                    ->setCellValue('C'.$i, $vpjp['promo'])
                                    ->setCellValue('D'.$i, $vpjp['nama_class'])
                                    ->setCellValue('E'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('F'.$i, $vpjp['customerid'])
                                    ->setCellValue('G'.$i, $vpjp['kode_outlet'])
                                    ->setCellValue('H'.$i, $vpjp['nama_customer'])
                                    ->setCellValue('I'.$i, $vpjp['alamat'])
                                    ->setCellValue('J'.$i, $vpjp['nama_area'])
                                    ->setCellValue('K'.$i, $vpjp['qty_pasang'])
                                    ->setCellValue('L'.$i, $vpjp['description']);
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
