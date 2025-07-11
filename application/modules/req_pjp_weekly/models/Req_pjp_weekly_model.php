<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Req_pjp_weekly_model extends CI_Model
{
    function update($data)
    {
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $modified_date = $datetime->datetime;

        $data_insert = [];
        $pjp_detail = [];
        if (isset($data['week1']) && isset($data['customerid'])) {
            foreach ($data['week1'] as $week1) {
                foreach ($data['customerid'] as $cust) {
                    $data_insert[] = [
                        'siteid' => $data['siteid'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'created_by' => $data['usersession'],
                        'created_date' => $modified_date,
                        'minggu' => '1',
                        'hari' => $week1,
                    ];
                    $pjp_detail[] = [
                        'req_no' => $data['req_no'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'minggu' => '1',
                        'hari' => $week1,
                    ];
                }
            }
        }
        if (isset($data['week2']) && isset($data['customerid'])) {
            foreach ($data['week2'] as $week2) {
                foreach ($data['customerid'] as $cust) {
                    $data_insert[] = [
                        'siteid' => $data['siteid'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'created_by' => $data['usersession'],
                        'created_date' => $modified_date,
                        'minggu' => '2',
                        'hari' => $week2,
                    ];
                    $pjp_detail[] = [
                        'req_no' => $data['req_no'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'minggu' => '2',
                        'hari' => $week2,
                    ];
                }
            }
        }
        if (isset($data['week3']) && isset($data['customerid'])) {
            foreach ($data['week3'] as $week3) {
                foreach ($data['customerid'] as $cust) {
                    $data_insert[] = [
                        'siteid' => $data['siteid'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'created_by' => $data['usersession'],
                        'created_date' => $modified_date,
                        'minggu' => '3',
                        'hari' => $week3,
                    ];
                    $pjp_detail[] = [
                        'req_no' => $data['req_no'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'minggu' => '3',
                        'hari' => $week3,
                    ];
                }
            }
        }
        if (isset($data['week4']) && isset($data['customerid'])) {
            foreach ($data['week4'] as $week4) {
                foreach ($data['customerid'] as $cust) {
                    $data_insert[] = [
                        'siteid' => $data['siteid'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'created_by' => $data['usersession'],
                        'created_date' => $modified_date,
                        'minggu' => '4',
                        'hari' => $week4,
                    ];
                    $pjp_detail[] = [
                        'req_no' => $data['req_no'],
                        'salesmanid' => $data['salesmanid'],
                        'customerid' => $cust,
                        'minggu' => '4',
                        'hari' => $week4,
                    ];
                }
            }
        }

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('t_sales_setup_rrk');
        
        $this->db->where('req_no', $data['req_no']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('req_pjp_weekly_detail');

        $this->db->where_in('customerid', $data['customerid']);
        $this->db->update('m_customer', [
            'salesmanid' => $data['salesmanid'],
            'modified_by' => $data['usersession'],
            'modified_date' => $modified_date,
        ]);

        $this->db->where_in('customerid', $data['customerid']);
        $this->db->update('m_customer_ob', ['salesmanid' => $data['salesmanid']]);

        $status = [];
        if (count($data_insert) > 0) {
            $this->db->trans_start();
            $exeinsert = $this->db->insert_batch('t_sales_setup_rrk', $data_insert);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Gagal insert_bath t_sales_setup_rrk: ' . $exeinsert);
                $status[] = 'Gagal insert_bath t_sales_setup_rrk: ' . $exeinsert;
            }
        }
        if (count($pjp_detail) > 0) {
            $this->db->trans_start();
            $exedetail = $this->db->insert_batch('req_pjp_weekly_detail', $pjp_detail);
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
        $sql = 'select * req_pjp_weekly where siteid=? and salesmanid=?';
        $result = $this->db->query($sql, [$data['siteid'], $data['salesmanid']])->row();
        if (!$result) return false;
        
        $this->db->where('req_no', $result->req_no);
        $this->db->delete('req_pjp_weekly_detail');

        $this->db->where('req_no', $result->req_no);
        $this->db->delete('req_pjp_weekly');

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('t_sales_setup_rrk');
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
                from req_pjp_weekly a
                left join m_sales_salesman b on b.salesmanid=a.salesmanid
                left join m_area_regional c on c.regionalid = b.regionalid
                left join m_area_areasite d on d.areaid = b.areaid
                ".$strquery."
            ) a";
        return easy_pagging($data, $field, $table);
    }
}
