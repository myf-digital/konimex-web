<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_join_visit_model extends CI_Model
{
    public function delete($data)
    {
        $this->db->where('periode', $data['periode']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('t_sales_absensi');
    }

    public function load($data)
    {
        $field = "a.*";
        $table = "(select a.id_eval, CONCAT('https://demo-gsk.sphere154.com/',b.image_profile) as url_image, a.periode, a.username as reviewer,c.name as nama_reviewer, a.salesmanid, b.nama_salesman as sales_name, b.tipe_sales, a.final_score as rating, a.review as review_content
from evaluation_join_visit a left join m_sales_salesman b on a.salesmanid=b.salesmanid 
  left join app_resource c on a.username=c.username
where a.periode between '".(@$data["get_date1"] ?? date('Y-m-d'))."' and '".(@$data["get_date2"] ?? date('Y-m-d'))."') as a ";
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
	
	function get_lat_long() {
		$this->db->select("latitude,longitude");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();
		return $data;
	}

	function get_tracking($sid,$periode) {
		$q = $this->db->query(" 
								select a.customerid, b.nama_customer, b.alamat,(select nama_class from m_customer_class where classid=b.classid) account,
										ifnull(a.latitude_cell,0) latitude_cell, ifnull(a.longitude_cell,0) longitude_cell,
										DATE_FORMAT(a.check_in,'%H:%i') checkin, DATE_FORMAT(a.check_out,'%H:%i') checkout, timediff(a.check_out,a.check_in) lamakunjungan
								from t_sales_rrk_trans a left join m_customer b on a.customerid=b.customerid
								where a.periode = '".$periode."' and a.salesmanid = '".$sid."'
								order by a.check_in asc		
								");

		return $q->result_array();
	}
	
}
