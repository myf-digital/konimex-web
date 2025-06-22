<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_available_mcs_model extends CI_Model
{

    public function load($data)
    {

        if ($data["restrict_level"]=='4'){
            $strquery = " and a.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')";
        }
        else if ($data["restrict_level"]=='3'){
            $strquery = " and a.areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data["usersession"]."')";
        }
        else if ($data["restrict_level"]=='2'){
            $strquery = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != '' ? ' and a.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != '' ? ' and a.areaid="'.$data['areaid'].'" ' : '';
        $subarea = $data['subareaid'] != '' ? ' and a.subareaid="'.$data['subareaid'].'" ' : '';
		$classid = $data['classid'];
        $field = " a.* ";
        $table = " (
						select z.*, format((z.active_sku/z.active_mcs)*100,2) as _percentage from (
						select a.customerid,a.kode_outlet, a.nama_customer, a.alamat, a.typeid, a.classid, c.nama_class, a.regionalid, d.nama_regional, 
								a.areaid, e.nama_area, a.subareaid, f.nama_area as city, a.mcc as dc,count(b.typeid) as active_mcs, 
						(select COUNT(1) from mapping_sku_active_last3months 
							where customerid=a.customerid and 
									productid in (select aa.productid from mapping_sku_active_data_mcs aa where typeid=a.typeid)) as active_sku
						from m_customer a 
						left join mapping_sku_active_data_mcs b on a.typeid = b.typeid and b.productid in (select bb.productid from mapping_sku_active bb where bb.idaccount=a.classid)
						left join m_customer_class c on a.classid=c.classid
						left join m_area_regional d on a.regionalid=d.regionalid
						left join m_area_areasite e on a.areaid=e.areaid
						left join m_area_subarea f on a.subareaid=f.subareaid
						where a.customerid <> '' and a.customerid in (select distinct customerid from t_stock_all_outlet where tahun=DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y') and bulan in (DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%m')))
							  and a.typeid not in ('TOKO PANEL') and a.classid ='$classid' ".$regional.$area.$subarea.$strquery."
						group by a.customerid , a.typeid 
						) z
                    ) a";

        return easy_pagging($data, $field, $table);
    }

    public function get_account($data)
    {

        $field = " a.* ";
        $table = " ( select classid, nama_class from m_customer_class
                        where bu ='MT' 
						order by classid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_regional($data)
    {

        $field = " a.* ";
        $table = " ( select regionalid, nama_regional from m_area_regional 
                        order by regionalid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_area($data)
    {
        $field = " a.* ";
        $table = " ( select areaid, nama_area from m_area_areasite where regionalid = '".$data['regionalid']."' 
                        order by areaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_city($data)
    {
        $field = " a.* ";
        $table = " ( select subareaid, nama_area from m_area_subarea where areaid = '".$data['areaid']."' 
                        order by subareaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_available_mcs_xls($data) {

        if ($data["restrict_level"]=='4'){
            $strquery = " and a.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')";
        }
        else if ($data["restrict_level"]=='3'){
            $strquery = " and a.areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data["usersession"]."')";
        }
        else if ($data["restrict_level"]=='2'){
            $strquery = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != 'null' ? ' and a.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and a.areaid="'.$data['areaid'].'" ' : '';
        $subarea = $data['city'] != 'null' ? ' and a.subareaid="'.$data['city'].'" ' : '';
		$classid = $data['account'];
		
		$q_detail = $this->db->query("
				select z.*, format((z.active_sku/z.active_mcs)*100,2) as _percentage from (
				select a.customerid,a.kode_outlet, a.nama_customer, a.alamat, a.typeid, a.classid, c.nama_class, a.regionalid, d.nama_regional, 
						a.areaid, e.nama_area, a.subareaid, f.nama_area as city, a.mcc as dc,count(b.typeid) as active_mcs, 
				(select COUNT(1) from mapping_sku_active_last3months 
					where customerid=a.customerid and 
							productid in (select aa.productid from mapping_sku_active_data_mcs aa where typeid=a.typeid)) as active_sku
				from m_customer a 
				left join mapping_sku_active_data_mcs b on a.typeid = b.typeid and productid in (select bb.productid from mapping_sku_active bb where bb.idaccount=a.classid)
				left join m_customer_class c on a.classid=c.classid
				left join m_area_regional d on a.regionalid=d.regionalid
				left join m_area_areasite e on a.areaid=e.areaid
				left join m_area_subarea f on a.subareaid=f.subareaid
				where a.customerid <> '' and a.customerid in (select distinct customerid from t_stock_all_outlet where tahun=DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%Y') and bulan in (DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 MONTH),'%m')))
					  and a.typeid not in ('TOKO PANEL') and a.classid ='$classid' ".$regional.$area.$subarea.$strquery."
				group by a.customerid , a.typeid 
				) z
			");

        return $q_detail->result_array();   
    }
    
}
