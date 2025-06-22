<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_salesman_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_sales_salesman');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }

        $data_array_category = array(
            "categoryid" => "11",
            "salesmanid" => $data['salesmanid'],
            "nama_category" => "CATEGORY"." - ".$data['salesmanid']
        );
        $this->db->insert('m_sales_salesman_category', $data_array_category);
        
        /*$dataarray_ram = array(
            "ram_rsm" => $data['ram_rsm'],
            "aas_aam_tss_tsm" => $data['aas_aam_tss_tsm']
        );
        $this->db->insert('mapping_ram_aas', $dataarray_ram);
        
        $dataarray_ram_fc = array(
            "ram_rsm" => $data['ram_rsm'],
            "aas_aam_tss_tsm" => $data['fc']
        );
        $this->db->insert('mapping_ram_aas', $dataarray_ram_fc);
                
        $dataarray_gff_aas = array(
            "salesmanid" => $data['salesmanid'],
            "aas_aam_tss_tsm" => $data['aas_aam_tss_tsm']
        );
        $this->db->insert('mapping_sales_aas_aam', $dataarray_gff_aas);
        
        $dataarray_gff_fc = array(
            "salesmanid" => $data['salesmanid'],
            "aas_aam_tss_tsm" => $data['fc']
        );
        $this->db->insert('mapping_sales_aas_aam', $dataarray_gff_fc);*/

        $data['categoryid']="11";
        $data['password'] = md5($data['password']);
        return $this->db->insert('m_sales_salesman', $data);
    }

    public function update($data)
    {

        /*$this->db->delete('mapping_sales_aas_aam');

        $dataarray_ram = array(
            "ram_rsm" => $data['ram_rsm'],
            "aas_aam_tss_tsm" => $data['aas_aam_tss_tsm']
        );
        $this->db->insert('mapping_ram_aas', $dataarray_ram);

        $dataarray_ram_fc = array(
            "ram_rsm" => $data['ram_rsm'],
            "aas_aam_tss_tsm" => $data['fc']
        );
        $this->db->insert('mapping_ram_aas', $dataarray_ram_fc);
        
        $dataarray_gff_aas = array(
            "salesmanid" => $data['salesmanid'],
            "aas_aam_tss_tsm" => $data['aas_aam_tss_tsm']
        );
        $this->db->insert('mapping_sales_aas_aam', $dataarray_gff_aas);
        
        $dataarray_gff_fc = array(
            "salesmanid" => $data['salesmanid'],
            "aas_aam_tss_tsm" => $data['fc']
        );
        $this->db->insert('mapping_sales_aas_aam', $dataarray_gff_fc);
        */
        $data['password'] = md5($data['password']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->update('m_sales_salesman', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('m_sales_salesman_category');

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('m_sales_salesman');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*,b.nama_regional,c.nama_area,d.nama_area as city, case when a.aktif = 1 then 'Active' when a.aktif = 0 then 'Not Active' end aktifstatus
                            from m_sales_salesman a 
                            left join m_area_regional b on a.regionalid=b.regionalid
                            left join m_area_areasite c on a.areaid=c.areaid
                            left join m_area_subarea d on a.subareaid=d.subareaid
                    ) a";
        return easy_pagging($data, $field, $table);
    }

    function cekusergff($usergff) {
		
		$this->db->select("salesmanid");
		$this->db->from("m_sales_salesman");
		$this->db->where( "salesmanid", $usergff);
		$num = $this->db->get()->num_rows();		
		return $num;
	}

}
