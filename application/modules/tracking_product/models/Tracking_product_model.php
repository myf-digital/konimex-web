<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking_product_model extends CI_Model
{

    public function get_regional($data)
    {

        $field = " a.* ";
        $table = " ( select regionalid, nama_regional from m_area_regional 
                        order by regionalid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_city($data)
    {
        $field = " a.* ";
        $table = " ( select subareaid, nama_area from m_area_subarea where regionalid = '".$data['regionalid']."' 
                        order by subareaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    function getTrackingProduct($data)
    {

        if ($data['restrict_level']=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        //$brand = $data['brand'] != 'null' ? ' and c.brandid="'.$data['brand'].'" ' : '';
        $arraysku = explode(",", $data['sku']);
        $strsku = implode(',', array_map(function($item) {
                    return "'$item'";
                }, $arraysku));
        $sku = $data['sku'] != 'null' ? ' and productid in ('.$strsku.')' : '';
        $skuname = $data['sku'] != 'null' ? ' where productid in ('.$strsku.')' : '';
        $regional = $data['regionalid'] != 'null' ? ' and a.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and a.subareaid="'.$data['areaid'].'" ' : '';
        $datefilter = $data['year'].'-'.$data['month'].'-01';
        $year=$data['year'];
        $month=$data['month'];
        $sql = "
                select a.regionalid,a.nama_regional,a.subareaid,a.city, b.typeid, count(1) as total_stores_universe,
                        count(c.customerid) as available_product_baru,
                        ifnull(tp.target,0) as posm_target,
                        count(d.customerid) as posm_actual,
                        (select ifnull(GROUP_CONCAT(nama_invoice),'All') from m_product $skuname) as product_name
                FROM v_mapping_area a join v_outlet_all b on a.subareaid = b.subareaid
                left join (select distinct customerid from t_stock_all_outlet where 
                        tahun='$year' and bulan='$month' 
                        $sku) c on c.customerid = b.customerid
                left join (select customerid from t_activity_promo_gsk where periode between '$datefilter' and LAST_DAY('$datefilter') 
                            $sku) d on d.customerid = b.customerid
                left join target_posm tp on tp.start >= '$datefilter' and tp.end<=LAST_DAY('$datefilter')
                and tp.subareaid =a.subareaid and tp.channel =b.typeid  
                where b.typeid not in ('OFFICE','Others') $regional $area
                group by a.regionalid,a.nama_regional,a.subareaid,a.city, b.typeid;
                ";

        $query = $this->db->query($sql);
        
        return $query->result_array();
    }

}
