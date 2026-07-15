<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_absensi_model extends CI_Model
{
    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select 
                        a.salesmanid, 
                        a.nama_salesman, 
                        a.tipe_sales,
                        (
                            select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                            from m_salesman_area msa
                            join m_area_regional r on r.regionalid = msa.regionalid
                            where msa.salesmanid = a.salesmanid
                        ) as nama_regional,
                        (
                            select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                            from m_salesman_area msa
                            join m_area_areasite ar on msa.areaid = ar.areaid
                            where msa.salesmanid = a.salesmanid
                        ) as nama_area,
                        (
                            select group_concat(distinct mas.nama_area order by mas.nama_area asc separator ', ')
                            from m_salesman_area msa
                            join m_area_subarea mas on mas.subareaid = msa.subareaid
                            where msa.salesmanid = a.salesmanid
                        ) as nama_subarea
                      from m_sales_salesman a where a.tipe_sales='MEDREP'
                      order by a.salesmanid asc ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_parma($data)
    {
        $data["rows"] = !empty($data["rows"]) ? $data["rows"] : 1000;
        $field = " a.* ";
        $table = " (
            select 
            a.salesmanid, 
            a.nama_salesman, 
            a.tipe_sales,
            (
                select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                from m_salesman_area msa
                join m_area_regional r on r.regionalid = msa.regionalid
                where msa.salesmanid = a.salesmanid
            ) as nama_regional,
            (
                select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                from m_salesman_area msa
                join m_area_areasite ar on msa.areaid = ar.areaid
                where msa.salesmanid = a.salesmanid
            ) as nama_area
            from m_sales_salesman a where a.tipe_sales='MEDREP'
            order by a.salesmanid asc ) as a";
        return easy_pagging($data, $field, $table);
    }
}
