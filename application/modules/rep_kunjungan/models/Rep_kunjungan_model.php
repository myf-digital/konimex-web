<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_kunjungan_model extends CI_Model
{
    public function load($data)
    {
        $field = " a.* ";
        $table = " (
            select 
                s.salesmanid, 
                s.nama_salesman, 
                s.tipe_sales,
                b.nama_regional,
                b.nama_area,
                b.nama_subarea
            from m_sales_salesman s
            left join (
                select 
                    msa.salesmanid,
                    group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ') as nama_regional,
                    group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ') as nama_area,
                    group_concat(distinct mas.nama_area order by mas.nama_area asc separator ', ') as nama_subarea
                from m_salesman_area msa
                left join m_area_regional r on r.regionalid = msa.regionalid
                left join m_area_areasite ar on msa.areaid = ar.areaid
                left join m_area_subarea mas on mas.subareaid = msa.subareaid
                group by msa.salesmanid
            ) b on s.salesmanid = b.salesmanid
            order by s.salesmanid asc
        ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_parma($data)
    {
        $data["rows"] = !empty($data["rows"]) ? $data["rows"] : 1000;
        $field = " a.* ";
        $table = " (
            select 
                s.salesmanid, 
                s.nama_salesman, 
                s.tipe_sales,
                b.nama_regional,
                b.nama_area,
                b.nama_subarea
            from m_sales_salesman s
            left join (
                select 
                    msa.salesmanid,
                    group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ') as nama_regional,
                    group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ') as nama_area,
                    group_concat(distinct mas.nama_area order by mas.nama_area asc separator ', ') as nama_subarea
                from m_salesman_area msa
                left join m_area_regional r on r.regionalid = msa.regionalid
                left join m_area_areasite ar on msa.areaid = ar.areaid
                left join m_area_subarea mas on mas.subareaid = msa.subareaid
                group by msa.salesmanid
            ) b on s.salesmanid = b.salesmanid
            order by s.salesmanid asc
        ) as a";
        return easy_pagging($data, $field, $table);
    }
}