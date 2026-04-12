<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_pjp_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('t_sales_setup_rrk');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }

        $customers = array();
        $week1 = array();
        $week2 = array();
        $week3 = array();
        $week4 = array();
        if(isset($data['customerid'])){ $customers = $data['customerid']; unset($data['customerid']); }
        if(isset($data['week1'])){ $week1 = $data['week1']; unset($data['week1']); }
        if(isset($data['week2'])){ $week2 = $data['week2']; unset($data['week2']); }
        if(isset($data['week3'])){ $week3 = $data['week3']; unset($data['week3']); }
        if(isset($data['week4'])){ $week4 = $data['week4']; unset($data['week4']); }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;

        for($a=0;$a<count($customers);$a++){
            $valcustomerid = $customers[$a];
            for($i=0;$i<count($week1);$i++){
                $data_array = array(
                    "salesmanid" => $data['salesmanid'],
                    "customerid" => $valcustomerid,
                    "created_by" => $data["usersession"],
                    "created_date" => $data["created_date"],
                    "minggu" => "1",
                    "hari" => $week1[$i]
                    );
                $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
            }
            
            for($i=0;$i<count($week2);$i++){
                $data_array = array(
                    "salesmanid" => $data['salesmanid'],
                    "customerid" => $valcustomerid,
                    "created_by" => $data["usersession"],
                    "created_date" => $data["created_date"],
                    "minggu" => "2",
                    "hari" => $week2[$i]
                    );
                $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
            }

            for($i=0;$i<count($week3);$i++){
                $data_array = array(
                    "salesmanid" => $data['salesmanid'],
                    "customerid" => $valcustomerid,
                    "created_by" => $data["usersession"],
                    "created_date" => $data["created_date"],
                    "minggu" => "3",
                    "hari" => $week3[$i]
                    );
                $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
            }

            for($i=0;$i<count($week4);$i++){
                $data_array = array(
                    "salesmanid" => $data['salesmanid'],
                    "customerid" => $valcustomerid,
                    "created_by" => $data["usersession"],
                    "created_date" => $data["created_date"],
                    "minggu" => "4",
                    "hari" => $week4[$i]
                    );
                $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
            }
        }
        if (!$execreturn){
            return false;
        }else{
            return true;
        }
        
    }

    public function update($data)
    {

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('t_sales_setup_rrk');

        $dataupdate = array("salesmanid"=>$data['salesmanid_new']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->update('m_customer', $dataupdate);

        $week1 = array();
        $week2 = array();
        $week3 = array();
        $week4 = array();
        if(isset($data['week1'])){ $week1 = $data['week1']; unset($data['week1']); }
        if(isset($data['week2'])){ $week2 = $data['week2']; unset($data['week2']); }
        if(isset($data['week3'])){ $week3 = $data['week3']; unset($data['week3']); }
        if(isset($data['week4'])){ $week4 = $data['week4']; unset($data['week4']); }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        
        for($i=0;$i<count($week1);$i++){
                    $data_array = array(
                        "siteid" => $data['siteid'],
                        "salesmanid" => $data['salesmanid_new'],
                        "customerid" => $data['customerid'],
                        "modified_by" => $data["usersession"],
                        "modified_date" => $data["created_date"],
                        "minggu" => "1",
                        "hari" => $week1[$i]
                        );
                    $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
        }
        
        for($i=0;$i<count($week2);$i++){
            $data_array = array(
                "siteid" => $data['siteid'],
                "salesmanid" => $data['salesmanid_new'],
                "customerid" => $data['customerid'],
                "modified_by" => $data["usersession"],
                "modified_date" => $data["created_date"],
                "minggu" => "2",
                "hari" => $week2[$i]
                );
            $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
        }

        for($i=0;$i<count($week3);$i++){
            $data_array = array(
                "siteid" => $data['siteid'],
                "salesmanid" => $data['salesmanid_new'],
                "customerid" => $data['customerid'],
                "modified_by" => $data["usersession"],
                "modified_date" => $data["created_date"],
                "minggu" => "3",
                "hari" => $week3[$i]
                );
            $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
        }

        for($i=0;$i<count($week4);$i++){
            $data_array = array(
                "siteid" => $data['siteid'],
                "salesmanid" => $data['salesmanid_new'],
                "customerid" => $data['customerid'],
                "modified_by" => $data["usersession"],
                "modified_date" => $data["created_date"],
                "minggu" => "4",
                "hari" => $week4[$i]
                );
            $execreturn = $this->db->insert('t_sales_setup_rrk', $data_array);
        }

        if (!$execreturn){
            return false;
        }else{
            return true;
        }
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('customerid', $data['customerid']);
        return $this->db->delete('t_sales_setup_rrk');
    }

    public function load($data)
    {
        if ($data["idjabatan"]=='2' or $data["idjabatan"]=='3')
            $strquery = " where a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$data["usersession"]."') ";
        else if($data["idjabatan"]=='16' or $data["idjabatan"]=='17'){
            $strquery = " where a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$data["usersession"]."') ";
        }else{
            $strquery = "";
        }

        $field = " a.* ";
        $table = " ( 
                    select x.siteid, x.salesmanid, x.nama_salesman, x.position, x.ram_rsm, x.aas_aam_tss_tsm, x.customerid, x.kode_outlet, x.nama_customer, x.alamat, x.mcc, x.nama_class,
                    GROUP_CONCAT(x.minggu SEPARATOR ',') AS group_minggu,
                    GROUP_CONCAT(x.hari SEPARATOR ',') AS group_hari, 
                    GROUP_CONCAT(distinct(x.minggu) SEPARATOR ',') AS group_nama_minggu,
                    GROUP_CONCAT(distinct(x.nama_hari) SEPARATOR ',') AS group_nama_hari,
                    (select aktif_week from m_setup_site limit 0,1) as week_aktif
                    from (
                    select a.siteid, a.salesmanid, d.nama_salesman, d.tipe_sales as position, a.ram_rsm, a.aas_aam_tss_tsm, a.customerid, a.minggu, a.hari, 
                        case when a.hari=0 then 'Minggu' 
                        when a.hari=1 then 'Senin'
                        when a.hari=2 then 'Selasa' 
                        when a.hari=3 then 'Rabu' 
                        when a.hari=4 then 'Kamis' 
                        when a.hari=5 then 'Jumat'
                        when a.hari=6 then 'Sabtu' end nama_hari,
                    b.kode_outlet, b.nama_customer, b.alamat, b.mcc, c.nama_class
                    from t_sales_setup_rrk a left join m_customer b on a.customerid = b.customerid left join m_customer_class c on b.classid=c.classid 
                    left join m_sales_salesman d on d.salesmanid=a.salesmanid
                    ".$strquery."
                    ) x
                    group by x.siteid, x.salesmanid, x.nama_salesman, x.position, x.ram_rsm, x.aas_aam_tss_tsm, x.customerid, x.kode_outlet, x.nama_customer, x.alamat, x.mcc, x.nama_class
                ) a 
                ";
        //$filter = "where aas_aam_tss_tsm like ".$data['userlogin']."";
        return easy_pagging($data, $field, $table);
    }

    public function add_switch($data)
    {
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;

        $sqlupdatecust = "update m_customer set salesmanid='".$data['salesmanid_to']."' where salesmanid = '".$data['salesmanid_from']."';";
        $sqlupdatepjp = "update t_sales_setup_rrk set salesmanid='".$data['salesmanid_to']."' where salesmanid = '".$data['salesmanid_from']."';";
        $execcust = $this->db->query($sqlupdatecust);
        $execpjp = $this->db->query($sqlupdatepjp);
        
        
        if (!$execcust and !$execpjp){
            return false;
        }else{
            return true;
        }
    }

    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '256M');
        
        $salesmanid = $data['salesmanid'];
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

    function savetoxls($data) {
		ini_set('memory_limit', '256M');
        $salesmanid = $data['salesmanid'];
        $query = "select a.siteid, a.salesmanid, d.nama_salesman, d.tipe_sales as position, a.ram_rsm, a.aas_aam_tss_tsm, a.customerid, b.typeid channel, a.minggu, a.hari,
                         b.kode_outlet, b.nama_customer, b.alamat, b.mcc, c.nama_class
                  from t_sales_setup_rrk a left join m_customer b on a.customerid = b.customerid left join m_customer_class c on b.classid=c.classid 
                  left join m_sales_salesman d on d.salesmanid=a.salesmanid
                  where a.salesmanid = '$salesmanid'
                  order by b.nama_customer, a.minggu asc
                ";
        $execquery = $this->db->query($query);
        $lovpjp = $execquery->result_array();
		
		$filename = "PJP_".$salesmanid.".xls";
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
            
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr><th>Keterangan hari : 0: Minggu, 1: Senin, 2: Selasa, 3:Rabu, 4:Kamis, 5:Jumat, 6:Sabtu</th></tr>';
		$html .= '<tr>';
		$html .='<th>KODE PAR-MA</th>';
		$html .='<th>NAMA PAR-MA</th>';
		$html .='<th>POSITION</th>';
		$html .='<th>ID DRC</th>';
		$html .='<th>KODE OUTLET</th>';
		$html .='<th>NAMA OUTLET</th>';
		$html .='<th>CHANNEL</th>';
		$html .='<th>SUB CHANNEL/ACCOUNT</th>';
		$html .='<th>MINGGU</th>';
		$html .='<th>HARI</th>';
		$html .= '</tr></thead>';
        $html .= '<tbody>';
        
        foreach ($lovpjp as $vpjp) {
                $html .= '<tr>';
                $html .='<td>'.$vpjp['salesmanid'].'</td>';
                $html .='<td>'.$vpjp['nama_salesman'].'</td>';
                $html .='<td>'.$vpjp['position'].'</t>';
                $html .='<td>'.$vpjp['customerid'].'</td>';
                $html .='<td>'.$vpjp['kode_outlet'].'</td>';
                $html .='<td>'.$vpjp['nama_customer'].'</td>';
                $html .='<td>'.$vpjp['channel'].'</td>';
                $html .='<td>'.$vpjp['nama_class'].'</td>';
                $html .='<td>'.$vpjp['minggu'].'</td>';
                $html .='<td>'.$vpjp['hari'].'</td>';
                $html .= '</tr>';
            }
		$html .= '</tbody>';
		$html .= '</table>';
        header('Content-Type: application/vnd.ms-excel');
        //header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header('Cache-Control: max-age=0');
        header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
        
		echo $html;		
	}		
}
