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
        /*if ($data["idjabatan"]=='2' or $data["idjabatan"]=='3')
            $strquery = " where a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$data["usersession"]."') ";
        else if($data["idjabatan"]=='16' or $data["idjabatan"]=='17'){
            $strquery = " where a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$data["usersession"]."') ";
        }else{
            $strquery = "";
        }*/
        
        if ($data["restrict_level"]=='4'){
            $strquery = " where b.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."'
                                                )";
        }
        else if ($data["restrict_level"]=='3'){
            $strquery = " where b.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."'
                                                )";
        }
        else if ($data["restrict_level"]=='2'){
            $strquery = " where b.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."'
                                                ) ";
        }
        else {
            $strquery = "";
        }


        $field = " a.* ";
        $table = " ( 
                    select x.siteid, x.salesmanid, x.nama_salesman, x.position, concat(x.nama_salesman,' (',x.position,')') as gffname, x.ram_rsm, x.aas_aam_tss_tsm, x.customerid, x.kode_outlet, x.nama_customer, x.alamat, x.mcc, x.nama_class,
                    GROUP_CONCAT(x.minggu SEPARATOR ',') AS group_minggu,
                    GROUP_CONCAT(x.hari SEPARATOR ',') AS group_hari, 
                    GROUP_CONCAT(distinct(x.minggu) SEPARATOR ',') AS group_nama_minggu,
                    GROUP_CONCAT(distinct(x.nama_hari) SEPARATOR ',') AS group_nama_hari,
                    (select aktif_week from m_setup_site limit 0,1) as week_aktif,
                    x.city
                    from (
                    select a.siteid, a.salesmanid, d.nama_salesman, d.tipe_sales as position, a.ram_rsm, a.aas_aam_tss_tsm, a.customerid, a.minggu, a.hari, 
                        case when a.hari=0 then 'Minggu' 
                        when a.hari=1 then 'Senin'
                        when a.hari=2 then 'Selasa' 
                        when a.hari=3 then 'Rabu' 
                        when a.hari=4 then 'Kamis' 
                        when a.hari=5 then 'Jumat'
                        when a.hari=6 then 'Sabtu' end nama_hari,
                    b.kode_outlet, b.nama_customer, b.alamat, e.nama_area as city,b.mcc, c.nama_class
                    from t_sales_setup_rrk a left join m_customer b on a.customerid = b.customerid left join m_customer_class c on b.classid=c.classid 
                    left join m_sales_salesman d on d.salesmanid=a.salesmanid
                    left join m_area_subarea e on e.subareaid = b.subareaid
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

}
