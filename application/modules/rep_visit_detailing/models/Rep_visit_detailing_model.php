<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_visit_detailing_model extends CI_Model
{
    function load($data) {
        $field = "a.* ";
        $table = " (
            select
                a.periode,
                a.siteid,
                a.salesmanid,
                a.salesman_name,
                a.customerid,
                a.professional_name,
                a.array_product,
                a.keterangan,
                a.start_detailing,
                a.end_detailing,
                a.status,
                a.reason,
                a.latitude_cell,
                a.longitude_cell,
                concat('".URL_IMAGE."', a.url_img_detailing) as url_img_detailing,
                concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                b.nama_salesman,
                c.nama_customer,
                CASE
                    WHEN a.status = 5 THEN 'Tidak Valid'
                    WHEN a.status = 3 THEN 'Valid'
                    WHEN a.status = 2 THEN 'Belum Valid'
                    ELSE 'Butuh Verifikasi'
                END as status_label,
                CASE
                    WHEN (
                        SELECT GROUP_CONCAT(DISTINCT rb.brand ORDER BY rb.brand SEPARATOR ',')
                        FROM ref_brand rb
                        WHERE FIND_IN_SET(rb.brandid, a.array_product) > 0
                    ) IS NOT NULL THEN (
                        SELECT GROUP_CONCAT(DISTINCT rb.brand ORDER BY rb.brand SEPARATOR ',')
                        FROM ref_brand rb
                        WHERE FIND_IN_SET(rb.brandid, a.array_product) > 0
                    )
                    ELSE a.array_product
                END as brands
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid 
            left join m_customer c on a.customerid=c.customerid
            where a.periode between '".(@$data["get_date1"] ?? today())."' and '".(@$data["get_date2"] ?? today())."'
        ) a";        
        return easy_pagging($data, $field, $table);
    }
	
	function get_lat_long() {
		$this->db->select("latitude,longitude");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();
		return $data;
	}

	function get_tracking($sid, $periode) {
        $where = "";
        if ($sid) $where = " and a.salesmanid = '" . $sid . "'";
		$q = $this->db->query(" 
            select
                a.periode,
                a.siteid,
                a.salesmanid,
                a.salesman_name,
                a.professional_name,
                a.customerid,
                a.tipe_pic,
                c.nama_customer,
                c.alamat,
                (select nama_class from m_customer_class where classid=c.classid) account,
                ifnull(a.latitude_cell,0) latitude_cell,
                ifnull(a.longitude_cell,0) longitude_cell,
                concat('".URL_IMAGE."', a.url_img_detailing) as url_img_detailing,
                concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                DATE_FORMAT(a.start_detailing,'%H:%i') start_detailing,
                DATE_FORMAT(a.end_detailing,'%H:%i') end_detailing,
                timediff(a.end_detailing,a.start_detailing) lamakunjungan,
                CASE
                    WHEN (
                        SELECT GROUP_CONCAT(DISTINCT rb.brand ORDER BY rb.brand SEPARATOR ',')
                        FROM ref_brand rb
                        WHERE FIND_IN_SET(rb.brandid, a.array_product) > 0
                    ) IS NOT NULL THEN (
                        SELECT GROUP_CONCAT(DISTINCT rb.brand ORDER BY rb.brand SEPARATOR ',')
                        FROM ref_brand rb
                        WHERE FIND_IN_SET(rb.brandid, a.array_product) > 0
                    )
                    ELSE a.array_product
                END as brands,
                a.keterangan, a.reason
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid 
            left join m_customer c on a.customerid=c.customerid
            where a.periode = '".$periode."'" . $where . "
            order by a.start_detailing asc		
        ");
		return $q->result_array();
	}

    function savetoxlsx($data) {
        $q = $this->db->query("
            select
                a.periode,
                a.siteid,
                a.salesmanid,
                a.salesman_name,
                a.customerid,
                a.professional_name,
                a.array_product,
                a.keterangan,
                a.start_detailing,
                a.end_detailing,
                a.status,
                a.reason,
                a.latitude_cell,
                a.longitude_cell,
                concat('".URL_IMAGE."', a.url_img_detailing) as url_img_detailing,
                concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                b.nama_salesman,
                c.nama_customer,
                CASE
                    WHEN a.status = 5 THEN 'Tidak Valid'
                    WHEN a.status = 3 THEN 'Valid'
                    WHEN a.status = 2 THEN 'Belum Valid'
                    ELSE 'Butuh Verifikasi'
                END as status_label,
                CASE
                    WHEN (
                        SELECT GROUP_CONCAT(DISTINCT rb.brand ORDER BY rb.brand SEPARATOR ',')
                        FROM ref_brand rb
                        WHERE FIND_IN_SET(rb.brandid, a.array_product) > 0
                    ) IS NOT NULL THEN (
                        SELECT GROUP_CONCAT(DISTINCT rb.brand ORDER BY rb.brand SEPARATOR ',')
                        FROM ref_brand rb
                        WHERE FIND_IN_SET(rb.brandid, a.array_product) > 0
                    )
                    ELSE a.array_product
                END as brands
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid 
            left join m_customer c on a.customerid=c.customerid
            where a.periode between '".@$data["get_date1"]."' and '".@$data["get_date2"]."'"
        );
		return $q->result_array();
    }
}
