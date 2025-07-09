<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conf_setup_pjp extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup_pjp_model', 'setup_pjp');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_addpjp()
    {
        $this->template->show($this, 'form_addpjp');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
    }

    public function form_switch()
    {
        $this->template->show($this, 'form_switch');
    }

    public function add_switch()
    {
        $data = param_input();
        response($this->setup_pjp->add_switch($data));
    }

    public function create()
    {
        $data = param_input();
        response($this->setup_pjp->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->setup_pjp->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->setup_pjp->delete($data));
    }

    public function load()
    {   
        //$cookieData = get_cookie("session");
        $data = param_input();
        responseJSON($this->setup_pjp->load($data));
    }
    
    public function savetoxlsx()
    {
        $salesmanid = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $restrict_level = $this->uri->segment('6');
        //$filename = $this->uri->segment('7');

        ini_set("memory_limit","2048M");
        ini_set('max_execution_time', '0');
        
        if ($salesmanid=='' or empty($salesmanid) or $salesmanid=='null'){
            $filename='All_GFF';
            if ($restrict_level=='4'){
                $strquery = " where a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                    app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                    where a.username='".$username."')
                                                    )";
            }
            else if ($restrict_level=='3'){
                $strquery = " where a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$username."')
                                                    )";
            }
            else if ($restrict_level=='2'){
                $strquery = " where a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                    app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                    where a.username='".$username."')
                                                    ) ";
            }
            else {
                $strquery = "";
            }
        }else{
            $filename=$salesmanid;
            $strquery = "where a.salesmanid = '$salesmanid'";
        }
            
            $filename = "PJP_".$filename.".xlsx";
            $query = "select a.siteid, a.salesmanid, d.nama_salesman, d.tipe_sales as position, a.ram_rsm, a.aas_aam_tss_tsm, 
                                a.customerid, b.typeid channel, GROUP_CONCAT(a.minggu ORDER BY a.minggu ASC SEPARATOR ',') as minggu, a.hari,
                                b.kode_outlet, b.latest_jjid, b.nama_customer, b.alamat, b.mcc as dc, b.spot_id as outlet_type,c.nama_class, e.nama_area city
                        from t_sales_setup_rrk a left join m_customer b on a.customerid = b.customerid left join m_customer_class c on b.classid=c.classid 
                        left join m_sales_salesman d on d.salesmanid=a.salesmanid
                        left join m_area_areasite e on e.areaid = b.areaid
                        $strquery
                      group by a.siteid, a.salesmanid, d.nama_salesman, d.tipe_sales, a.ram_rsm, a.aas_aam_tss_tsm, a.customerid, b.typeid, a.hari
                    ";
            //echo $this->db->last_query();
            $execquery = $this->db->query($query);
            $lovpjp = $execquery->result_array();
			
			
			$rowweek = $this->db->query("select aktif_week from m_setup_site limit 0,1")->row();
			$weekaktif = $rowweek->aktif_week;
            $this->load->library('excel');
        
            //$objDrawing = new PHPExcel_Worksheet_Drawing();
            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'Keterangan hari : 0: Minggu, 1: Senin, 2: Selasa, 3:Rabu, 4:Kamis, 5:Jumat, 6:Sabtu; Week Active '.$weekaktif)
                        ->setCellValue('A2', 'USER GFF')
                        ->setCellValue('B2', 'KE USER GFF')
                        ->setCellValue('C2', 'NAMA GFF')
                        ->setCellValue('D2', 'POSITION')
                        ->setCellValue('E2', 'ID OUTLET')
                        ->setCellValue('F2', 'LATEST JJID')
                        ->setCellValue('G2', 'NAMA OUTLET')
                        ->setCellValue('H2', 'ALAMAT')
                        ->setCellValue('I2', 'CHANNEL')
                        ->setCellValue('J2', 'SUB CHANNEL/ACCOUNT')
                        ->setCellValue('K2', 'MINGGU')
                        ->setCellValue('L2', 'HARI')
                        ->setCellValue('M2', 'KOTA')
                        ;
                        $objPHPExcel->getActiveSheet()->getStyle('B2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('009900');
                        $objPHPExcel->getActiveSheet()->getStyle('K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('009900');
                        $objPHPExcel->getActiveSheet()->getStyle('L2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('009900');
                        $i = 3;
                        foreach ($lovpjp as $vpjp) {
                            $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.$i, $vpjp['salesmanid'])
                                        ->setCellValue('B'.$i, $vpjp['salesmanid'])
                                        ->setCellValue('C'.$i, $vpjp['nama_salesman'])
                                        ->setCellValue('D'.$i, $vpjp['position'])
                                        ->setCellValue('E'.$i, $vpjp['customerid'])
                                        ->setCellValue('F'.$i, $vpjp['latest_jjid'])
                                        ->setCellValue('G'.$i, $vpjp['nama_customer'])
                                        ->setCellValue('H'.$i, $vpjp['alamat'])
                                        ->setCellValue('I'.$i, $vpjp['channel'])
                                        ->setCellValue('J'.$i, $vpjp['nama_class'])
                                        ->setCellValue('K'.$i, $vpjp['minggu'])
                                        ->setCellValue('L'.$i, $vpjp['hari'])
                                        ->setCellValue('M'.$i, $vpjp['city']);
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

    public function upload(){
        $data = param_input();
        $this->load->library('excel');
        $usersession = $data['usersession']; 
        $fileName = date('Ymd').time().$_FILES['fileupload']['name'];
         
        $config['upload_path'] = DIR_DOC; //buat folder dengan nama assets di root folder
        //echo $fileName.";".DIR_DOC; die();
        $config['allowed_types'] = 'xlsx|csv|xls';
        $config['file_name'] = $fileName;
        $config['max_size'] = '2048';
        $config['max_width'] = '0';
        $config['max_height'] = '0';
         
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if(! $this->upload->do_upload('fileupload') )
        {
            echo $this->upload->display_errors();
            return;
        }
             
        $media = $this->upload->data();
        $inputFileName = DIR_DOC.$media['file_name'];
        //echo $inputFileName; die();

        try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
 
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            //insert table temp
            $jmlrow=0;
            $jmlrowfaild = 0;
            for ($row = 3; $row <= $highestRow; $row++){                  //  Read a row of data into an array                 
				$i=0;
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
				
                $arr = explode(",",$rowData[0][10]);
                $arrhari = explode(",",$rowData[0][11]);
                $allowed_values_hari = ['0', '1', '2', '3', '4', '5', '6'];
                $allowed_values_week = ['1', '2', '3', '4'];
                foreach ($arr as $item) {

                    foreach ($arrhari as $itemhari) {
                        if (in_array($itemhari, $allowed_values_hari) && in_array($item, $allowed_values_week)) {
                            $datatemp = array(
                            "filename"=> $fileName,
                            "salesmanid"=> $rowData[0][0],
                            "salesmanid_to"=> $rowData[0][1],
                            "customerid"=> $rowData[0][4],
                            "minggu"=> $item,
                            "hari"=> $itemhari
                            );
                            $insert = $this->db->insert("t_pjp_upload_detail",$datatemp);
                            if (!$insert){
                                $datatemp['reason']='Error insert query';
                                $insert = $this->db->insert("t_pjp_upload_detail_error",$datatemp);
                                $jmlrowfaild++;
                            }
                        }else{
                            $datatemp = array(
                            "filename"=> $fileName,
                            "salesmanid"=> $rowData[0][0],
                            "salesmanid_to"=> $rowData[0][1],
                            "customerid"=> $rowData[0][4],
                            "minggu"=> $item,
                            "hari"=> $itemhari,
                            "reason"=>'Error variable minggu atau hari'
                            );
                            $insert = $this->db->insert("t_pjp_upload_detail_error",$datatemp);
                            if (!$insert){
                                $datatemp['reason']='Error insert query';
                                $insert = $this->db->insert("t_pjp_upload_detail_error",$datatemp);
                                $jmlrowfaild++;
                            }else{
                                $jmlrowfaild++;
                            }
                        }

                        $jmlrow++;  
                    }
                }
            }
            
            sleep(3);

            $datam = array(
                "filename"=> $fileName,
                "jumlah"=> $jmlrow,
                "success"=> $jmlrow,
                "failed"=> $jmlrowfaild,
                "status"=> 'Uploaded',
                "upload_by"=> $usersession,
                "upload_date"=> date("Y-m-d H:i:s")
            );
            $insert = $this->db->insert("t_pjp_upload",$datam);

            sleep(3);

            //exec table pjp
			$query = "select distinct customerid,salesmanid,salesmanid_to from t_pjp_upload_detail where filename='".$fileName."' 
                        and salesmanid in (select salesmanid from m_sales_salesman) 
                        and customerid in (select customerid from m_customer where customerid <> '');";
            $execquery = $this->db->query($query);
            $lovtoko = $execquery->result_array();
            foreach ($lovtoko as $vtoko) {
                $this->db->query("delete from t_sales_setup_rrk where customerid='".$vtoko['customerid']."' and salesmanid = '".$vtoko['salesmanid']."';");
                $this->db->query("delete from m_customer_ob where customerid='".$vtoko['customerid']."' and salesmanid = '".$vtoko['salesmanid']."';");
            }

            $querypjp = "replace into t_sales_setup_rrk(salesmanid,customerid,minggu,hari,created_by,created_date)
                        select salesmanid_to,customerid,minggu,hari,'$usersession',now() from t_pjp_upload_detail where filename='".$fileName."' and customerid <> '';";
                        $execquerypjp = $this->db->query($querypjp);

            
            foreach ($lovtoko as $vtoko) {
                $dataob = array(
                "customerid"=> $vtoko['customerid'],
                "salesmanid"=> $vtoko['salesmanid_to'],
                "created_by"=> $usersession,
                "created_date"=> date("Y-m-d H:i:s")
            );
            $insert = $this->db->insert("m_customer_ob",$dataob);
            $querymcust = "update m_customer set salesmanid=(select GROUP_CONCAT(distinct salesmanid SEPARATOR ',') AS salesmanid from t_sales_setup_rrk where customerid='".$vtoko['customerid']."') 
                            where customerid = '".$vtoko['customerid']."'";
                            $execquerymcust = $this->db->query($querymcust);
            }

            /*
            for ($row = 3; $row <= $highestRow; $row++){                  //  Read a row of data into an array                 
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
                $data = array(
                    "customerid"=> $rowData[0][3]
                );
                $this->db->delete("t_sales_setup_rrk",$data);
            }

            for ($row = 3; $row <= $highestRow; $row++){                  //  Read a row of data into an array                 
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
                                                 
                //Sesuaikan sama nama kolom tabel di database                                
                 $data = array(
                    "salesmanid"=> $rowData[0][0],
                    "customerid"=> $rowData[0][3],
                    "minggu"=> $rowData[0][9],
                    "hari"=> $rowData[0][10]
                );
                 
                //sesuaikan nama dengan nama tabel
                $insert = $this->db->insert("t_sales_setup_rrk",$data);
                $sqlupdatecust = "update m_customer set salesmanid='".$rowData[0][0]."' where customerid = '".$rowData[0][3]."';";
                $execcust = $this->db->query($sqlupdatecust);
                //delete_files($media['file_path']);
                     
            }*/
        //redirect('excel/');
        response(true);
    }
}
