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
        $table = " ( select subareaid, nama_area from m_area_subarea where regionalid = '".$data['regionalid']."' 
                        order by subareaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    function getDoi($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $brand = $data['brand'] != 'null' ? ' and c.brandid="'.$data['brand'].'" ' : '';
        $sku = $data['sku'] != 'null' ? ' and c.productid="'.$data['sku'].'" ' : '';
        $regional = $data['regionalid'] != 'null' ? ' and b.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and b.areaid="'.$data['areaid'].'" ' : '';
            
        $sql = '
            select
                a.*,
                b.nama_customer,
                b.segmentid,
                b.typeid,
                c.nama_invoice,
                c.nama_brand,
                c.group_product,
                d.nama_regional,
                e.nama_area,
                e.nama_area as city
                from t_stock_all_outlet_doi a
                left join m_customer b on a.customerid=b.customerid
                left join m_product c on a.productid=c.productid
                left join m_area_regional d on b.regionalid=d.regionalid
                left join m_area_areasite e on b.areaid=e.areaid
                left join m_area_subarea f on b.subareaid=f.subareaid
                where a.tahun = "'.$data['year'].'" and a.bulan = "'.$data['month'].'" '.$brand.$sku.$regional.$area.$strquery;
        $query = $this->db->query($sql);

        return $query->result_array();
    }

}
