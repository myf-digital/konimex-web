<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Req_pjp_daily_model extends CI_Model
{
    public function update($data)
    {
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $modified_date = $datetime->datetime;

        $pjp_detail = [];
        if (isset($data['customerid'])) {
            foreach ($data['customerid'] as $cust) {
                $pjp_detail[] = [
                    'req_no' => $data['req_no'],
                    'salesmanid' => $data['salesmanid'],
                    'periode' => $data['periode'],
                    'customerid' => $cust,
                ];
            }
        }

        $this->db->where('req_no', $data['req_no']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->update('req_pjp_daily', [
            'keterangan' => $data['keterangan'],
            'modified_by' => $data['usersession'],
            'modified_date' => $modified_date,
        ]);

        $this->db->where('req_no', $data['req_no']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('req_pjp_daily_detail');

        $this->db->where_in('customerid', $data['customerid']);
        $this->db->update('m_customer', [
            'salesmanid' => $data['salesmanid'],
            'modified_by' => $data['usersession'],
            'modified_date' => $modified_date,
        ]);

        $this->db->where_in('customerid', $data['customerid']);
        $this->db->update('m_customer_ob', ['salesmanid' => $data['salesmanid']]);

        $status = [];
        if (count($pjp_detail) > 0) {
            $this->db->trans_start();
            $exedetail = $this->db->insert_batch('req_pjp_daily_detail', $pjp_detail);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Gagal insert_bath req_pjp_weekly_detail: ' . $exedetail);
                $status[] = 'Gagal insert_bath req_pjp_weekly_detail: ' . $exedetail;
            }
        }

        if (count($status) > 0) {
            return $status;
        } else {
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
