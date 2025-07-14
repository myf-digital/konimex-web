<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        unset($data["customerid_m"]);
        unset($data["siteid"]);
        $data["created_by"] = $data["usersession"];

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);
        $sql = "select max(CAST(customerid as UNSIGNED))+1 customerid_new from m_customer";
        $newidcust = $this->db->query($sql)->row();
        $data['customerid'] =  $newidcust->customerid_new;
        return $this->db->insert('m_customer', $data);
    }

    public function update($data)
    {
        //$this->db->where('siteid', $data['siteid']);
        //$this->db->where('customerid_m', $data['customerid_m']);
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);

        $this->db->where('customerid', $data['customerid']);
        return $this->db->update('m_customer', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid_m', $data['customerid_m']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->delete('m_customer');

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->where('salesmanid', $data['salesmanid']);
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

        $field = "a.* ";
        $table = " (select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, f.nama_salesman gff_name, f.tipe_sales position
                                    from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                                    left join m_area_areasite c on a.areaid = c.areaid
                                    left join m_area_subarea d on a.subareaid = d.subareaid
                                    left join m_customer_class e on a.classid = e.classid
                                    left join m_sales_salesman f on a.salesmanid = f.salesmanid
                                    ".$strquery."
                    ) a";
        return easy_pagging($data, $field, $table);
    }

}
