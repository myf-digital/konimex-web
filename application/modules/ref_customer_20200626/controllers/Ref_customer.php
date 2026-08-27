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
        ini_set("memory_limit","512M");
        ini_set('max_execution_time', '60');
        
        if ($account=='' or empty($account) or $account=='null'){
            $account='All';
            $filename = 'All_Account';
        }
        //$salesmanid = $data['salesmanid'];

        if ($restrict_level=='4'){
            if ($account=='All'){
                $strsubquery = "";
            }else{
                $strsubquery = " and a.classid='".$account."'";
            }
            
            $strquery = " and a.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."'
                                                )";
            $strquery = $strquery.$strsubquery;
        }
        else if ($restrict_level=='3'){
            if ($account=='All'){
                $strsubquery = "";
            }else{
                $strsubquery = " and a.classid='".$account."'";
            }
            $strquery = " and a.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."'
                                                )";
            
            $strquery = $strquery.$strsubquery;
        }
        else if ($restrict_level=='2'){
            if ($account=='All'){
                $strsubquery = "";
            }else{
                $strsubquery = " and a.classid='".$account."'";
            }
            $strquery = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."'
                                                ) ";
            $strquery = $strquery.$strsubquery;
        }
        else {
            $strquery = "";
        }
        

        $query = " select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, f.nama_salesman gff_name, f.tipe_sales position
                                    from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                                    left join m_area_areasite c on a.areaid = c.areaid
                                    left join m_area_subarea d on a.subareaid = d.subareaid
                                    left join m_customer_class e on a.classid = e.classid
                                    left join m_sales_salesman f on a.salesmanid = f.salesmanid
					where a.customerid <> '' ".$strquery."
                  ";

        $execquery = $this->db->query($query);
        $lovoutlet = $execquery->result_array();
		
		$filename = "Outlet_".$filename.".xlsx";

        $this->load->library('excel');
    
        //$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'OUTLETID_DRC')
                    ->setCellValue('B1', 'KODE OUTLET')
                    ->setCellValue('C1', 'Nama Outlet')
                    ->setCellValue('D1', 'Alamat')
                    ->setCellValue('E1', 'Regional')
                    ->setCellValue('F1', 'Area')
                    ->setCellValue('G1', 'SubArea/City')
                    ->setCellValue('H1', 'BU')
                    ->setCellValue('I1', 'Channel/Type')
                    ->setCellValue('J1', 'SubChannel/Account')
                    ->setCellValue('K1', 'DC')
                    ->setCellValue('L1', 'TPE')
                    ->setCellValue('M1', 'TPE Name')
                    ->setCellValue('N1', 'Position')
                    ;
        $i = 2;
        foreach ($lovoutlet as $voutlet) {
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $voutlet['customerid'])
                        ->setCellValue('B'.$i, $voutlet['kode_outlet'])
                        ->setCellValue('C'.$i, $voutlet['nama_customer'])
                        ->setCellValue('D'.$i, $voutlet['alamat'])
                        ->setCellValue('E'.$i, $voutlet['nama_regional'])
                        ->setCellValue('F'.$i, $voutlet['nama_area'])
                        ->setCellValue('G'.$i, $voutlet['nama_subarea'])
                        ->setCellValue('H'.$i, $voutlet['segmentid'])
                        ->setCellValue('I'.$i, $voutlet['typeid'])
                        ->setCellValue('J'.$i, $voutlet['nama_account'])
                        ->setCellValue('K'.$i, $voutlet['mcc'])
                        ->setCellValue('L'.$i, $voutlet['salesmanid'])
                        ->setCellValue('M'.$i, $voutlet['gff_name'])
                        ->setCellValue('N'.$i, $voutlet['position']);
                $i++;
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
