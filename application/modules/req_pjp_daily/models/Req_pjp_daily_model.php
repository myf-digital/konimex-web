<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Req_pjp_daily_model extends CI_Model
{

    public function create($data)
    {
        /*$id = IDGenerator::getInstance()->nextID('t_sales_setup_rrk');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }*/

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

        $execreturn = false;

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

        $this->db->where('customerid', $data['customerid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->update('m_customer_ob', $dataupdate);

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
        if ($data["restrict_level"]=='4'){
            $strquery = " where b.subareaid in (select distinct b.subareaid from  
                        app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                        where a.username='".$data["usersession"]."'
                    )";
        } else if ($data["restrict_level"]=='3'){
            $strquery = " where b.areaid in (select distinct b.areaid from  
                        app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                        where a.username='".$data["usersession"]."'
                    )";
        } else if ($data["restrict_level"]=='2'){
            $strquery = " where b.regionalid in (select distinct b.regionalid from  
                        app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                        where a.username='".$data["usersession"]."'
                    ) ";
        } else {
            $strquery = "";
        }

        $field = " a.* ";
        $table = " ( 
                select
                    a.*,
                    b.tipe_sales,
                    c.nama_regional,
                    d.nama_area,
                    case
                        when a.status=5 then 'Rejected'
                        when a.status=3 then 'Approved'
                        else 'Pending'
                    end as status_label
                from req_pjp_daily a
                left join m_sales_salesman b on b.salesmanid=a.salesmanid
                left join m_area_regional c on c.regionalid = b.regionalid
                left join m_area_areasite d on d.areaid = b.areaid
                ".$strquery."
            ) a";
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
