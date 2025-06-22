<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_dokumentasi_model extends CI_Model
{

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_account($data)
    {
        $field = " a.* ";
        $table = " ( select classid, nama_class from m_customer_class order by nama_class asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

}
