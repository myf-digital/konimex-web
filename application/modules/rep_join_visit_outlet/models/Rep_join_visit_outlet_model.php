<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_join_visit_outlet_model extends CI_Model
{

    public function load($data)
    {

        if ($data["restrict_level"]=='4'){
            $strquery = " and e.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')";
        }
        else if ($data["restrict_level"]=='3'){
            $strquery = " and e.areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data["usersession"]."')";
        }
        else if ($data["restrict_level"]=='2'){
            $strquery = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')";
        }
        else {
            $strquery = "";
        }

		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
        $field = " a.* ";
        $table = " (
						select a.id_evaluation_outlet,a.periode,a.customerid,a.username,a.created_date,b.kode_outlet,b.nama_customer 
							from outlet_visit_evaluation a left join m_customer b on a.customerid=b.customerid
						where a.periode between '$start_date' and '$end_date'
                    ) a";

        return easy_pagging($data, $field, $table);
    }

    public function get_event($data)
    {

        $field = " a.* ";
        $table = " ( select * from product_knowledge_event
                        where is_active=1
						order by id_event asc
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

    public function get_product_knowledge_xls($data) {

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

        //$regional = $data['regionalid'] != 'null' ? ' and a.regionalid="'.$data['regionalid'].'" ' : '';
        //$area = $data['areaid'] != 'null' ? ' and a.areaid="'.$data['areaid'].'" ' : '';
        //$subarea = $data['city'] != 'null' ? ' and a.subareaid="'.$data['city'].'" ' : '';
		$eventid = $data['id_event'];
		
		$q_detail = $this->db->query("
						select a.id_event, c.event, c.start_period, c.end_period, a.periode, a.username, e.nama_salesman,e.tipe_sales gff_tipe, e.nama_regional,e.nama_area,e.city,
								a.final_score, e.image_profile
						FROM evaluation_product_knowledge a
						left join product_knowledge_event c on a.id_event = c.id_event
						left join v_gff_info e on a.username = e.salesmanid
						where a.id_event = '$eventid'".$strquery."
			");

        return $q_detail->result_array();   
    }
    
}
