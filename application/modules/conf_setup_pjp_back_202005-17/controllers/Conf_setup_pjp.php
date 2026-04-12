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
        //$data = array("salesmanid" => $salesmanid);
        //responseJSON($this->setup_pjp->savetoxlsx($data));

            ini_set('memory_limit', '256M');
            
            $filename = "PJP_".$salesmanid.".xlsx";
            $query = "select a.siteid, a.salesmanid, d.nama_salesman, d.tipe_sales as position, a.ram_rsm, a.aas_aam_tss_tsm, a.customerid, b.typeid channel, a.minggu, a.hari,
                             b.kode_outlet, b.nama_customer, b.alamat, b.mcc, c.nama_class
                      from t_sales_setup_rrk a left join m_customer b on a.customerid = b.customerid left join m_customer_class c on b.classid=c.classid 
                      left join m_sales_salesman d on d.salesmanid=a.salesmanid
                      where a.salesmanid = '$salesmanid'
                      order by b.nama_customer, a.minggu asc
                    ";
            //echo $this->db->last_query();
            $execquery = $this->db->query($query);
            $lovpjp = $execquery->result_array();
    
            $this->load->library('excel');
    
            //$objDrawing = new PHPExcel_Worksheet_Drawing();
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'Keterangan hari : 0: Minggu, 1: Senin, 2: Selasa, 3:Rabu, 4:Kamis, 5:Jumat, 6:Sabtu')
                        ->setCellValue('A2', 'KODE PAR-MA')
                        ->setCellValue('B2', 'NAMA PAR-MA')
                        ->setCellValue('C2', 'POSITION')
                        ->setCellValue('D2', 'ID OUTLET')
                        ->setCellValue('E2', 'KODE OUTLET')
                        ->setCellValue('F2', 'NAMA OUTLET')
                        ->setCellValue('G2', 'CHANNEL')
                        ->setCellValue('H2', 'SUB CHANNEL/ACCOUNT')
                        ->setCellValue('I2', 'MINGGU')
                        ->setCellValue('J2', 'HARI')
                        ;
                        $i = 3;
                        foreach ($lovpjp as $vpjp) {
                            $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.$i, $vpjp['salesmanid'])
                                        ->setCellValue('B'.$i, $vpjp['nama_salesman'])
                                        ->setCellValue('C'.$i, $vpjp['position'])
                                        ->setCellValue('D'.$i, $vpjp['customerid'])
                                        ->setCellValue('E'.$i, $vpjp['kode_outlet'])
                                        ->setCellValue('F'.$i, $vpjp['nama_customer'])
                                        ->setCellValue('G'.$i, $vpjp['channel'])
                                        ->setCellValue('H'.$i, $vpjp['nama_class'])
                                        ->setCellValue('I'.$i, $vpjp['minggu'])
                                        ->setCellValue('J'.$i, $vpjp['hari']);
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

/*    public function savetoxlsx()
    {
        $salesmanid = $this->uri->segment('3');
        $data = array("salesmanid" => $salesmanid);
        responseJSON($this->setup_pjp->savetoxlsx($data));
    }

    public function upload()
    {
        // Load plugin PHPExcel nya
        //include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('excel');

        $config['upload_path'] = realpath('uploads');
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['max_size'] = '10000';
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            echo "Gagal";
            //upload gagal
            //$this->session->set_flashdata('notif', '<div class="alert alert-danger"><b>PROSES IMPORT GAGAL!</b> '.$this->upload->display_errors().'</div>');
            //redirect halaman
            //redirect('conf_setup_pjp/form_upload/');

        } else {

            $data_upload = $this->upload->data();

            $excelreader     = new PHPExcel_Reader_Excel2007();
            $loadexcel         = $excelreader->load('uploads/'.$data_upload['file_name']); // Load file yang telah diupload ke folder excel
            $sheet             = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

            $data = array();

            $numrow = 1;
            foreach($sheet as $row){
                            if($numrow > 1){
                                array_push($data, array(
                                    'nama_dosen' => $row['A'],
                                    'email'      => $row['B'],
                                    'alamat'      => $row['C'],
                                ));
                    }
                $numrow++;
            }
            $this->db->insert_batch('tbl_dosen', $data);
            //delete file from server
            unlink(realpath('uploads/'.$data_upload['file_name']));

            //upload success
            $this->session->set_flashdata('notif', '<div class="alert alert-success"><b>PROSES IMPORT BERHASIL!</b> Data berhasil diimport!</div>');
            //redirect halaman
            redirect('form_upload/');

        }
    }
*/
    public function upload(){
        $this->load->library('excel');

        $fileName = time().$_FILES['fileupload']['name'];
         
        $config['upload_path'] = DIR_IMAGE; //buat folder dengan nama assets di root folder
        echo $fileName.";".DIR_IMAGE; die();
        $config['file_name'] = $fileName;
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size'] = 10000;
         
        $this->load->library('upload');
        $this->upload->initialize($config);
         
        if(! $this->upload->do_upload() )
        $this->upload->display_errors();
             
        $media = $this->upload->data();
        $inputFileName = './uploads/'.$media['file_name'];
         
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
             
            for ($row = 2; $row <= $highestRow; $row++){                  //  Read a row of data into an array                 
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                                                 
                //Sesuaikan sama nama kolom tabel di database                                
                 $data = array(
                    "idimport"=> $rowData[0][0],
                    "nama"=> $rowData[0][1],
                    "alamat"=> $rowData[0][2],
                    "kontak"=> $rowData[0][3]
                );
                 
                //sesuaikan nama dengan nama tabel
                $insert = $this->db->insert("eimport",$data);
                delete_files($media['file_path']);
                     
            }
        redirect('excel/');
    }
}
