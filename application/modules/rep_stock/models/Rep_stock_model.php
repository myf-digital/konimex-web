<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_stock_model extends CI_Model
{


    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_product($data)
    {
        $field = " a.* ";
        $table = " ( select a.idaccount, a.productid, b.nama_invoice 
                        from mapping_sku_active a left join m_product b on a.productid=b.productid 
                     where a.idaccount='".$data['classid']."'
                    ) as a";
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
