<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_join_visit_model extends CI_Model
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

		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
        $field = " a.* ";
        $table = " (
						select a.id_evaluation, a.periode, a.review_by, a.salesmanid, a.nama_salesman, a.nama_regional, a.nama_area, a.city, a.tipe_sales, a.review,
								FORMAT(a.final_score,0) as final_score, a.preparation, a.approach, a.regular_shelf_merchandising, a.advance_merchandising_on_regular_shelves,
								a.promo_implementation, a.sell_and_secure, a.drc_filling
						FROM v_join_visit_rekap a
						where a.periode between '$start_date' and '$end_date'$strquery
                    ) a";

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

    public function get_join_visit_gff_xls($data) {

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

		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
        $exec_query = $this->db->query("
						select a.id_evaluation, a.periode, a.review_by, a.salesmanid, a.nama_salesman, a.nama_regional, a.nama_area, a.city, a.tipe_sales, a.review,
								FORMAT(a.final_score,0) as final_score, a.preparation, a.approach, a.regular_shelf_merchandising, a.advance_merchandising_on_regular_shelves,
								a.promo_implementation, a.sell_and_secure, a.drc_filling
						FROM v_join_visit_rekap a
						where a.periode between '$start_date' and '$end_date'$strquery ");

        return $exec_query->result_array();   
    }
    
}
