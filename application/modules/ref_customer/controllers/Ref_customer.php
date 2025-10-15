<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_model', 'customer');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function create()
    {
        $data = param_input();
        response($this->customer->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer->load($data));
    }

    public function savetoxls()
    {
        $account = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');
        $filtername = $this->uri->segment('7');
        $data = array("account" => $account , "username" => $username, "jabatan" => $jabatan, "restrict_level" => $restrict_level, "filename" => $filtername);
        $this->customer->savetoxls($data);
    }


    function savetoxlsx() {
        $account = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');
        $filename = $this->uri->segment('7');
        ini_set("memory_limit","2048M");
        ini_set('max_execution_time', '0');
        
        if ($account=='' or empty($account) or $account=='null'){
            $account='All';
            $filename = 'All_Account';
        }
        //$salesmanid = $data['salesmanid'];
		if ($account=='All'){
			$strsubquery = "";
		}else{
			$strsubquery = " and a.classid='".$account."'";
		}

        if ($restrict_level=='4'){            
            $strquery = " and a.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."'
                                                )";
            $strquery = $strquery.$strsubquery;
        }
        else if ($restrict_level=='3'){
            $strquery = " and a.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."'
                                                )";
            
            $strquery = $strquery.$strsubquery;
        }
        else if ($restrict_level=='2'){
            $strquery = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."'
                                                ) ";
            $strquery = $strquery.$strsubquery;
        }
        else {
            $strquery = "".$strsubquery;
        }
        

        $query_old = " select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, f.nama_salesman gff_name, f.tipe_sales position
                                    from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                                    left join m_area_areasite c on a.areaid = c.areaid
                                    left join m_area_subarea d on a.subareaid = d.subareaid
                                    left join m_customer_class e on a.classid = e.classid
                                    left join m_sales_salesman f on a.salesmanid = f.salesmanid
                    where a.customerid <> '' ".$strquery."
                  ";
        $query = " select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, 
                        ifnull((select GROUP_CONCAT(concat(salesmanid,'-',nama_salesman,'-',tipe_sales) SEPARATOR ',') 
                        from m_sales_salesman 
                        where salesmanid in (select salesmanid from m_customer_ob 
                                            where customerid=a.customerid) and tipe_sales='PAR'),'') as gffmd,
                        MAX(CASE WHEN f.minggu = 1 THEN f.hari END) AS minggu_1,
                        MAX(CASE WHEN f.minggu = 2 THEN f.hari END) AS minggu_2,
                        MAX(CASE WHEN f.minggu = 3 THEN f.hari END) AS minggu_3,
                        MAX(CASE WHEN f.minggu = 4 THEN f.hari END) AS minggu_4
                        from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                        left join m_area_areasite c on a.areaid = c.areaid
                        left join m_area_subarea d on a.subareaid = d.subareaid
                        left join m_customer_class e on a.classid = e.classid
                        left join t_sales_setup_rrk f on a.customerid = f.customerid 
                        where a.customerid <> '' ".$strquery."
                    GROUP BY
                    a.customerid, b.nama_regional, c.nama_area, d.nama_area, e.nama_class
                    ;";

        $query1 = " select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, 
                  (select count(1) from m_sales_salesman 
                              where salesmanid in (select salesmanid from m_customer_ob where customerid=a.customerid) and tipe_sales='MERCHANDISER') as gffmd,
                  (select count(1) from m_sales_salesman 
                              where salesmanid in (select salesmanid from m_customer_ob where customerid=a.customerid) and tipe_sales='SPG') as gffspg,
                  (select count(1) from m_sales_salesman 
                              where salesmanid in (select salesmanid from m_customer_ob where customerid=a.customerid) and tipe_sales='SALESMAN MT') as gffmt,
                  (select count(1) from m_sales_salesman 
                              where salesmanid in (select salesmanid from m_customer_ob where customerid=a.customerid) and tipe_sales='SALESMAN GT') as gffgt
                  from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                  left join m_area_areasite c on a.areaid = c.areaid
                  left join m_area_subarea d on a.subareaid = d.subareaid
                  left join m_customer_class e on a.classid = e.classid
                  where a.customerid <> '' ".$strquery."
            ";


        $execquery = $this->db->query($query);
        $lovoutlet = $execquery->result_array();
		
		$filename = "Master_Data_Outlet.xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'PAR-MA ID Outlet')
                    ->setCellValue('B2', 'ID Outlet Distributor')
                    ->setCellValue('C2', 'Latest JJID')
                    ->setCellValue('D2', 'Latest Customer Name')
                    ->setCellValue('E2', 'PAR-MA Nama Outlet')
                    ->setCellValue('F2', 'Alamat')
                    ->setCellValue('G2', 'Regional')
                    ->setCellValue('H2', 'Area')
                    ->setCellValue('I2', 'Cluster')
                    ->setCellValue('J2', 'Tier')
                    ->setCellValue('K2', 'Tipe Kepemilikan')
                    ->setCellValue('L2', 'Distributir')
                    ->setCellValue('M2', 'PAR-MA')
                    ->setCellValue('N2', 'Minggu 1')
                    ->setCellValue('O2', 'Minggu 2')
                    ->setCellValue('P2', 'Minggu 3')
                    ->setCellValue('Q2', 'Minggu 4')
                    ->setCellValue('R2', 'Latitude')
                    ->setCellValue('S2', 'Longitude')
                    ->setCellValue('N1', 'Keterangan hari : 0: Minggu, 1: Senin, 2: Selasa, 3:Rabu, 4:Kamis, 5:Jumat, 6:Sabtu; Week Active ')
                    ;
        $i = 3;
        foreach ($lovoutlet as $voutlet) {
            //if ($voutlet['gffmd']!='' or $voutlet['gffspg']!='' or $voutlet['gffmt']!='' or $voutlet['gffgt']!=''){
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $voutlet['customerid'])
                        ->setCellValue('B'.$i, $voutlet['cust_id_map'])
                        ->setCellValue('C'.$i, $voutlet['latest_jjid'])
                        ->setCellValue('D'.$i, $voutlet['latest_customer_name'])
                        ->setCellValue('E'.$i, $voutlet['nama_customer'])
                        ->setCellValue('F'.$i, $voutlet['alamat'])
                        ->setCellValue('G'.$i, $voutlet['nama_regional'])
                        ->setCellValue('H'.$i, $voutlet['nama_area'])
                        ->setCellValue('I'.$i, $voutlet['typeid'])
                        ->setCellValue('J'.$i, $voutlet['nama_account'])
                        ->setCellValue('K'.$i, $voutlet['spot_id'])
                        ->setCellValue('L'.$i, $voutlet['mcc'])
                        ->setCellValue('M'.$i, $voutlet['gffmd'])
                        ->setCellValue('N'.$i, $voutlet['minggu_1'])
                        ->setCellValue('O'.$i, $voutlet['minggu_2'])
                        ->setCellValue('P'.$i, $voutlet['minggu_3'])
                        ->setCellValue('Q'.$i, $voutlet['minggu_4'])
                        ->setCellValue('R'.$i, $voutlet['latitude'])
                        ->setCellValue('S'.$i, $voutlet['longitude'])
						;
                $i++;
                //}
            }

        $objPHPExcel->getActiveSheet()->setTitle('Data Outlet');
        $objPHPExcel->createSheet();

            /*$execquery = $this->db->query($query1);
            $lovoutlet = $execquery->result_array();
    
            $objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'OUTLETID_DRC')
            ->setCellValue('B1', 'KODE OUTLET')
            ->setCellValue('C1', 'Nama Outlet')
            ->setCellValue('D1', 'Account')
            ->setCellValue('E1', 'Regional')
            ->setCellValue('F1', 'Area')
            ->setCellValue('G1', 'SubArea/City')
            ->setCellValue('H1', 'MERCHANDISER')
            ->setCellValue('I1', 'SPG')
            ->setCellValue('J1', 'SALESMAN MT')
            ->setCellValue('K1', 'SALESMAN GT')
            ;
            $i = 2;
            foreach ($lovoutlet as $voutlet) {
                if ($voutlet['gffmd']!='0' or $voutlet['gffspg']!='0' or $voutlet['gffmt']!='0' or $voutlet['gffgt']!='0'){
                $objPHPExcel->setActiveSheetIndex(1)
                            ->setCellValue('A'.$i, $voutlet['customerid'])
                            ->setCellValue('B'.$i, $voutlet['kode_outlet'])
                            ->setCellValue('C'.$i, $voutlet['nama_customer'])
                            ->setCellValue('D'.$i, $voutlet['nama_account'])
                            ->setCellValue('E'.$i, $voutlet['nama_regional'])
                            ->setCellValue('F'.$i, $voutlet['nama_area'])
                            ->setCellValue('G'.$i, $voutlet['nama_subarea'])
                            ->setCellValue('H'.$i, $voutlet['gffmd'])
                            ->setCellValue('I'.$i, $voutlet['gffspg'])
                            ->setCellValue('J'.$i, $voutlet['gffmt'])
                            ->setCellValue('K'.$i, $voutlet['gffgt']);
                    $i++;
                    }
                }

            $objPHPExcel->getActiveSheet()->setTitle('Count GFF');
			*/
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

	function open_gff_detail() {
		$customerid = $this->input->post("customerid");
		
        $q = $this->db->query(" 
                                select a.customerid,a.nama_customer,b.salesmanid,c.nama_salesman,c.tipe_sales position from m_customer a 
                                join m_customer_ob b on a.customerid=b.customerid 
                                join m_sales_salesman c on b.salesmanid=c.salesmanid
                                where a.customerid = '".$customerid."';
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
		$html = '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">User PAR-MA</th>';
		$html .= '<th style="white-space: nowrap;">Nama PAR-MA</th>';
		$html .= '<th style="white-space: nowrap;">Posisi</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;">'.$value['salesmanid'].'</td>';
            $html .= '<td style="white-space: nowrap;">'.$value['nama_salesman'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['position'].'</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table>';
				
		echo $html;

	}
}
