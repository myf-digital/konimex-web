<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_doi_model extends CI_Model
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

    function getDoi($data)
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

        $brand = $data['brand'] != 'null' ? ' and c.brandid="'.$data['brand'].'" ' : '';
        $sku = $data['sku'] != 'null' ? ' and c.productid="'.$data['sku'].'" ' : '';
        $regional = $data['regionalid'] != 'null' ? ' and b.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and b.subareaid="'.$data['areaid'].'" ' : '';
            
        $sql = 'select a.*, b.nama_customer, b.segmentid, b.typeid, c.nama_invoice, c.nama_brand, c.group_product, d.nama_regional, e.nama_area from t_stock_all_outlet_doi a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid left join m_area_regional d on b.regionalid=d.regionalid left join m_area_subarea e on b.subareaid=e.subareaid where a.tahun = "'.$data['year'].'" and a.bulan = "'.$data['month'].'" '.$brand.$sku.$regional.$area.$strquery;
        $query = $this->db->query($sql);

        return $query->result_array();
    }

}
