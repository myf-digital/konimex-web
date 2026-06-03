<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_kunjungan_model extends CI_Model
{

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select salesmanid, nama_salesman, tipe_sales, regionalid, nama_regional, areaid, nama_area 
                        from v_gff_info where tipe_sales='MEDREP'
                        order by salesmanid asc ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_parma($data)
    {
        $field = " a.* ";
        $table = " ( select salesmanid, nama_salesman, tipe_sales, regionalid, nama_regional, areaid, nama_area 
                        from v_gff_info where tipe_sales='MEDREP'
                        order by salesmanid asc ) as a";
        return easy_pagging($data, $field, $table);
    }

}
