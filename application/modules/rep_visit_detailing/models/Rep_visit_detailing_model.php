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
                concat('".URL_IMAGE."', a.url_file_signature) as url_file_signature,
                concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                b.nama_salesman,
                c.nama_customer,
                CASE
                    WHEN a.status = 5 THEN 'Tidak Valid'
                    WHEN a.status = 3 THEN 'Valid'
                    WHEN a.status = 2 THEN 'Belum Valid'
                    ELSE 'Butuh Verifikasi'
                END as status_label,
                rp.spesialisasi
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid 
            left join m_customer c on a.customerid=c.customerid
            left join (
                select 
                    a.id,
                    a.nama_professional,
                    b.name as spesialisasi
                from ref_professional a
                left join ref_spesialisasi b on b.id = a.spesialisasi_id
            ) as rp on rp.id = a.user_id
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
                concat('".URL_IMAGE."', a.url_file_signature) as url_file_signature,
                concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                DATE_FORMAT(a.start_detailing,'%H:%i') start_detailing,
                DATE_FORMAT(a.end_detailing,'%H:%i') end_detailing,
                timediff(a.end_detailing,a.start_detailing) lamakunjungan,
                a.keterangan,
                a.reason,
                rp.spesialisasi,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR '||')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, a.array_product) > 0
                    ),
                    a.array_product
                ) as products
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid 
            left join m_customer c on a.customerid=c.customerid
            left join (
                select 
                    a.id,
                    a.nama_professional,
                    b.name as spesialisasi
                from ref_professional a
                left join ref_spesialisasi b on b.id = a.spesialisasi_id
            ) as rp on rp.id = a.user_id
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
                concat('".URL_IMAGE."', a.url_file_signature) as url_file_signature,
                concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                b.nama_salesman,
                c.nama_customer,
                CASE
                    WHEN a.status = 5 THEN 'Tidak Valid'
                    WHEN a.status = 3 THEN 'Valid'
                    WHEN a.status = 2 THEN 'Belum Valid'
                    ELSE 'Butuh Verifikasi'
                END as status_label,
                rp.spesialisasi,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR ', ')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, a.array_product) > 0
                    ),
                    a.array_product
                ) as products
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid 
            left join m_customer c on a.customerid=c.customerid
            left join (
                select 
                    a.id,
                    a.nama_professional,
                    b.name as spesialisasi
                from ref_professional a
                left join ref_spesialisasi b on b.id = a.spesialisasi_id
            ) as rp on rp.id = a.user_id
            where a.periode between '".@$data["get_date1"]."' and '".@$data["get_date2"]."'"
        );
		return $q->result_array();
    }

    function get_survei($data)
    {
        $q = $this->db->query(" 
            select
                a.*,
                b.nama_professional,
                rs.name as spesialisasi
            from trx_visit_detailing_survei a
            left join ref_professional b on a.user_id=b.id
            left join ref_spesialisasi rs on rs.id = b.spesialisasi_id
            where a.siteid = '".$data["siteid"]."' and a.periode = '".$data["periode"]."' and a.salesmanid = '".$data["salesmanid"]."' and a.customerid = '".$data["customerid"]."'		
        ");
        return $q->result_array();
    }
}
