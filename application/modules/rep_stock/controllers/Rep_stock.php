<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_stock extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_stock_model', 'report_stock');
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
        responseJSON($this->report_stock->load($data));
    }

    public function load_account()
    {
        $data = param_input();
        responseJSON($this->report_stock->load_account($data));
    }

    public function load_product()
    {
        $data = param_input();
        responseJSON($this->report_stock->load_product($data));
    }

    function view_stock() {
		
		$idaccount = $this->input->post("idaccount");
		$productarr = array();
		$productarr = explode(",", $this->input->post("productid"));
		$productstr = "";
		for($i=0;$i<count($productarr);$i++){
			if ($i==0){
			$productstr .=  "'".$productarr[$i]."'";
			}else{
			$productstr .=  ",'".$productarr[$i]."'";
			}
        }
		
		//$start = $this->input->post("start");
		//$end = $this->input->post("end");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");

        if ($idjabatan=='2' or $idjabatan=='3')
			$strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
		else if($idjabatan=='16' or $idjabatan=='17'){
			$strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
		}else{
            $strquery = "";
        }

        $q = $this->db->query(" 
								select a.periode, a.salesmanid, f.nama_salesman, a.customerid, b.kode_outlet, e.nama_area city, a.productid, d.nama_invoice, a.qty_akhir, a.price, a.exp_date
											,b.nama_customer, b.classid, c.nama_class, b.subareaid, a.modified_date from t_sales_crc a 
								left join m_customer b on a.customerid = b.customerid
								left join m_customer_class c on b.classid = c.classid
								left join m_product d on a.productid = d.productid
								left join m_area_subarea e on b.subareaid = e.subareaid
								left join m_sales_salesman f on f.salesmanid = a.salesmanid
								where b.classid = '".$idaccount."' and a.productid in (".$productstr.") and a.modified_date is not null
								and a.periode between DATE_FORMAT(now() - INTERVAL 14 DAY, '%Y-%m-%d') and DATE_FORMAT(now(), '%Y-%m-%d')
								".$strquery."
								order by a.customerid asc, a.periode desc;
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Stock Outlet</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">Tanggal</th>';
		$html .= '<th style="white-space: nowrap;">MEDREP</th>';
		$html .= '<th style="white-space: nowrap;">OutletID</th>';
		$html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Account</th>';
		$html .= '<th style="white-space: nowrap;">Kota</th>';
		$html .= '<th style="white-space: nowrap;">ProductId</th>';
		$html .= '<th style="white-space: nowrap;">Product</th>';
		$html .= '<th style="white-space: nowrap;">Qty Stock</th>';
		$html .= '<th style="white-space: nowrap;">Price</th>';
		$html .= '<th style="white-space: nowrap;">Exp Date</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		$periodetemp1 = '';
		$customertemp1 = '';
		$periodetemp2 = '';
		$customertemp2 = '';
		foreach ($data as $value) {
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$value['periode'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['kode_outlet'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['nama_class'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['city'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['productid'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['nama_invoice'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['qty_akhir'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['price'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$value['exp_date'].'</td>';
					$html .= '</tr>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table></div>';
				
		echo $html;

	}

	function open_detail() {
		
		$idaccount = $this->input->post("idaccount");
		$idpromo = $this->input->post("idpromo");
		$start = $this->input->post("start");
		//$end = $this->input->post("end");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");

        if ($idjabatan=='2' or $idjabatan=='3')
			$strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
		else if($idjabatan=='16' or $idjabatan=='17'){
			$strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
		}else{
            $strquery = "";
        }

        if ($idpromo!='null'){$addquery=" and a.idpromo in (".$idpromo.") ";} else { $addquery="";}

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
                                where a.periode = '".$start."'".$addquery.$strquery.";
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Promo</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">No</th>';
		$html .= '<th style="white-space: nowrap;">Tanggal</th>';
		$html .= '<th style="white-space: nowrap;">Promo</th>';
		$html .= '<th style="white-space: nowrap;">Account</th>';
		$html .= '<th style="white-space: nowrap;">User MEDREP</th>';
		$html .= '<th style="white-space: nowrap;">Outlet ID</th>';
		$html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Alamat</th>';
		$html .= '<th style="white-space: nowrap;">Kota</th>';
		$html .= '<th style="white-space: nowrap;">Tipe Promo</th>';
		$html .= '<th style="white-space: nowrap;">Display</th>';
		$html .= '<th style="white-space: nowrap;">Harga Normal</th>';
		$html .= '<th style="white-space: nowrap;">Harga Promo</th>';
		$html .= '<th style="white-space: nowrap;">Deskripsi</th>';
		$html .= '<th style="white-space: nowrap;">Foto</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td>'.$i.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['periode'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['promo'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_class'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
            $html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['kode_outlet'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['alamat'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['city'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['tipepromo'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['display'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.number_format($value['harga_normal'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;">'.number_format($value['harga_promo'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['description'].'</td>';
			$html .= '<td style="white-space: nowrap;"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
			
            //$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			//$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			//$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'">'.$flag.'</td>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table></div>';
				
		echo $html;

	}

	function savetoxlsbak() {
		echo base_url();
	}

    function savetoxls() {
		
		
		ini_set('memory_limit', '256M');
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        $end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('6');
        $usersession = $this->uri->segment('7');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }

		$filename = "Promo_".$filename.".xls";
		$html ='<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:300px;
					overflow:auto;  
					margin-top:20px;
				}

				#table-wrapper table {
					width:100%;
				}

				#table-wrapper table thead th .text {
					position:absolute;   
					top:-20px;
					z-index:2;
					height:20px;
					width:35%;
					border:1px solid;
				}
                </style>';
            
        
                if ($idjabatan=='2' or $idjabatan=='3')
                    $strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
                else if($idjabatan=='16' or $idjabatan=='17'){
                    $strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
                }else{
                    $strquery = "";
                }
        
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
                                        where a.periode between '".$start."' and '".$end."' ".$addquery.$strquery.";
                                    ");
                //echo $this->db->last_query();
                $data = $q->result_array();
                $urlimage = URL_IMAGE;
				$sampleimage = "https://api.digitalrecordcard.com/uploads/imageoutlet/IMG_OUTLET_2020_03_11_GSKMD11_16408_2004080945573.jpg";
                
                $html ='<div class="box-body"><h3>List Promo</h3>';
                $html .= '<table class="table table-striped table-bordered table-condensed ">';
                $html .= '<thead">';
                $html .= '<tr>';
                $html .= '<th style="white-space: nowrap;">No</th>';
                $html .= '<th style="white-space: nowrap;">Tanggal</th>';
                $html .= '<th style="white-space: nowrap;">Promo</th>';
                $html .= '<th style="white-space: nowrap;">Account</th>';
                $html .= '<th style="white-space: nowrap;">User MEDREP</th>';
                $html .= '<th style="white-space: nowrap;">Outlet ID</th>';
                $html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
                $html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
                $html .= '<th style="white-space: nowrap;">Alamat</th>';
                $html .= '<th style="white-space: nowrap;">Kota</th>';
                $html .= '<th style="white-space: nowrap;">Tipe Promo</th>';
                $html .= '<th style="white-space: nowrap;">Display</th>';
                $html .= '<th style="white-space: nowrap;">Harga Normal</th>';
                $html .= '<th style="white-space: nowrap;">Harga Promo</th>';
                $html .= '<th style="white-space: nowrap;">Deskripsi</th>';
                $html .= '<th style="white-space: nowrap;">Foto</th>';
                $html .= '</tr>';
                $html .= '</thead">';
                $html .= '<tbody">';
                $i=1;
                foreach ($data as $value) {
                    $html .= '<tr>';
                    $html .= '<td>'.$i.'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['periode'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['promo'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['nama_class'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['kode_outlet'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['alamat'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['city'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['tipepromo'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['display'].'</td>';
                    $html .= '<td style="white-space: nowrap;">'.number_format($value['harga_normal'], 0, '.', ',').'</td>';
                    $html .= '<td style="white-space: nowrap;">'.number_format($value['harga_promo'], 0, '.', ',').'</td>';
                    $html .= '<td style="white-space: nowrap;">'.$value['description'].'</td>';
                    //$html .= '<td style="white-space: nowrap;"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
                    $html .= '<td style="white-space: nowrap;"><img class="img-rounded" style="width:100px; height:100px;" src="'.sampleimage.'"></td>';
                    
                    //$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
                    //$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
                    //$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
                    //$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'">'.$flag.'</td>';
                    $i++;
                }
                $html .= '</tbody>';
                $html .= '</table></div>';
				
				//var_dump($urlimage.@$value['image']);
           
        //header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		
		
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header('Cache-Control: max-age=0');
        header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
        
		echo $html;		
	}    

    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '256M');
        
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        //$end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }
		$filename = "Promo_".$start.".xlsx";

        if ($idjabatan=='2' or $idjabatan=='3')
            $strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
        else if($idjabatan=='16' or $idjabatan=='17'){
            $strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
        }else{
            $strquery = "";
        }

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
                            where a.periode = '".$start."' ".$strquery.$addquery.";
                        ");
        //echo $this->db->last_query();
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Tanggal ')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Promo')
                    ->setCellValue('D2', 'Account')
                    ->setCellValue('E2', 'User MEDREP')
                    ->setCellValue('F2', 'Outlet ID')
                    ->setCellValue('G2', 'Kode Outlet')
                    ->setCellValue('H2', 'Nama Outlet')
                    ->setCellValue('I2', 'Alamat')
                    ->setCellValue('J2', 'Kota')
                    ->setCellValue('K2', 'Tipe Promo')
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
                                    ->setCellValue('D'.$i, $vpjp['nama_class'])
                                    ->setCellValue('E'.$i, $vpjp['salesmanid'].'-'.$vpjp['nama_salesman'])
                                    ->setCellValue('F'.$i, $vpjp['customerid'])
                                    ->setCellValue('G'.$i, $vpjp['kode_outlet'])
                                    ->setCellValue('H'.$i, $vpjp['nama_customer'])
                                    ->setCellValue('I'.$i, $vpjp['alamat'])
                                    ->setCellValue('J'.$i, $vpjp['city'])
                                    ->setCellValue('K'.$i, $vpjp['tipepromo'])
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
		ini_set('memory_limit', '256M');
        
        $idpromo = $this->uri->segment('3');
        $start = $this->uri->segment('4');
        //$end = $this->uri->segment('5');
        $idjabatan = $this->uri->segment('5');
        $usersession = $this->uri->segment('6');

        if ($idpromo!='null'){
            $addquery=" and a.idpromo in (".$idpromo.") ";
            $filename='selected';
        } else {
            $addquery="";
            $filename="all";
        }
		$filename = "Promo_Gimmick".$start.".xlsx";

        if ($idjabatan=='2' or $idjabatan=='3')
            $strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
        else if($idjabatan=='16' or $idjabatan=='17'){
            $strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
        }else{
            $strquery = "";
        }

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
                            where a.periode = '".$start."' ".$strquery.$addquery.";
                        ");
        //echo $this->db->last_query();
        $lovpjp = $q->result_array();
        
        $this->load->library('excel');

        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'List Promo Tanggal ')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Tanggal')
                    ->setCellValue('C2', 'Promo')
                    ->setCellValue('D2', 'Account')
                    ->setCellValue('E2', 'User MEDREP')
                    ->setCellValue('F2', 'Outlet ID')
                    ->setCellValue('G2', 'Kode Outlet')
                    ->setCellValue('H2', 'Nama Outlet')
                    ->setCellValue('I2', 'Alamat')
                    ->setCellValue('J2', 'Kota')
                    ->setCellValue('K2', 'Stock Awal')
                    ->setCellValue('L2', 'Qty Pasang')
                    ->setCellValue('M2', 'Deskripsi')
                    ->setCellValue('N2', 'Foto 1')
                    ->setCellValue('O2', 'Foto 2')
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
                                    ->setCellValue('J'.$i, $vpjp['city'])
                                    ->setCellValue('K'.$i, $vpjp['stock_awal'])
                                    ->setCellValue('L'.$i, $vpjp['qty_pasang'])
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
                                            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
                                        } else {
                                            $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
                                        }
									} else {
                                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '');
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
