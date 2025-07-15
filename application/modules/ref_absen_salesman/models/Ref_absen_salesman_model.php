<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_absen_salesman_model extends CI_Model
{

    public function create($data)
    {

        $data['checkin'] = $data['checkin_date'].' '.$data['checkin_time'];
        $data['checkout'] = $data['checkout_date'].' '.$data['checkout_time'];

        $removeKeys = array('checkin_date', 'checkin_time', 'checkout_date', 'checkout_time', 'fileupload');

        foreach($removeKeys as $key) {
           unset($data[$key]);
        }

        $manPower = $this->manPower($data);

        $data['pjp'] = $manPower->_pjp;
        $data['call'] = $manPower->_call;
        $data['extra_call'] = $manPower->_extra_call;
        $data['crc'] = $manPower->_crc;
        $data['promo'] = $manPower->_promo;
        $data['competitor'] = $manPower->_competitor;
        $data['order'] = $manPower->_order;
        $data['sos'] = $manPower->_sos;

        return $this->db->replace('t_sales_absensi', $data);
    }

    public function update($data)
    {
        $data['checkin'] = $data['checkin_date'].' '.$data['checkin_time'];
        $data['checkout'] = $data['checkout_date'].' '.$data['checkout_time'];

        $removeKeys = array('checkin_date', 'checkin_time', 'checkout_date', 'checkout_time', 'fileupload');

        foreach($removeKeys as $key) {
           unset($data[$key]);
        }

        $manPower = $this->manPower($data);

        //$data['checkin'] = $manPower->checkin;
        //$data['checkout'] = $manPower->checkout;
        $data['pjp'] = $manPower->_pjp;
        $data['call'] = $manPower->_call;
        $data['extra_call'] = $manPower->_extra_call;
        $data['crc'] = $manPower->_crc;
        $data['promo'] = $manPower->_promo;
        $data['competitor'] = $manPower->_competitor;
        $data['order'] = $manPower->_order;
        $data['sos'] = $manPower->_sos;

        $this->db->where('periode', $data['periode']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->update('t_sales_absensi', $data);
    }

    public function delete($data)
    {
        $this->db->where('periode', $data['periode']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('t_sales_absensi');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = " (select a.periode, a.salesmanid, a.status, a.checkin, a.checkout, a.keterangan, a.flag_adjust, case when a.flag_adjust=1 then 'Yes' else 'No' end adjust_pjp, concat('".URL_IMAGE."', a.image) as image, b.nama_salesman
                    from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid 
                    where a.periode between '".(@$data["get_date1"] ?? today())."' and '".(@$data["get_date2"] ?? today())."'
                    ) a";        
        //$field = "a.*, concat('".URL_IMAGE."', a.image) as image, b.nama_salesman";
        //$table = "t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid where a.periode between '".@$data["get_date1"]."' and '".@$data["get_date2"]."' ";
        return easy_pagging($data, $field, $table);
    }

    function manPower($data)
    {

        $sql = 'select (select count(1) from t_sales_rrk where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'") _pjp, 
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and customerid in (select customerid from t_sales_rrk where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'")) _call,
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and customerid not in (select customerid from t_sales_rrk where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'") ) _extra_call,
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and crc_time is not null) _crc,
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and promo_time is not null) _promo,
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and competitor_time is not null) _competitor,
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and order_time is not null) _order,
                (select count(1) from t_sales_rrk_trans where periode="'.$data['periode'].'" and salesmanid="'.$data['salesmanid'].'" and sos_time is not null) _sos';

        $query = $this->db->query($sql);

        return $query->row();
    }  
}
