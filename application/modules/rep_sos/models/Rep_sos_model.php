<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_sos_model extends CI_Model
{

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_promo($data)
    {
        $field = " a.* ";
        $table = " ( select promo, GROUP_CONCAT(a.idpromo) as idpromo, 
                     case when promo='Promo GSK' then '' else concat('(',DATE_FORMAT(a.start_periode,'%d-%m-%Y'),' s/d ',DATE_FORMAT(a.end_periode,'%d-%m-%Y'),')') end as periode  from 
                     mapping_promo_active a left join m_customer_class b on a.classid=b.classid
                     where a.classid = '".$data['classid']."'
                     group by promo
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
