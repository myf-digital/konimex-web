<?php 
class Mon_outlet_model extends CI_Model {
	
	function get_siteid() {
		$this->db->select("siteid");
		$this->db->from("m_setup_site");
		$site = $this->db->get()->row()->siteid;
		return $site;
	}
	
	function get_lat_long() {
		$this->db->select("latitude,longitude");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();
		return $data;
	}

	function get_data_segment() {
		$this->db->select("segmentid,nama_segment");
		$this->db->from("m_customer_segment");
		if (!empty($segmentid)){
			$this->db->where("segmentid",$segmentid);
		}
		$data = $this->db->get()->result_array();
		return $data;
	}

	function get_data_class() {
		$this->db->select("classid,nama_class");
		$this->db->from("m_customer_class");
		if (!empty($classid)){
			$this->db->where("classid",$classid);
		}
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	function get_data_type() {
		$this->db->select("typeid,nama_type");
		$this->db->from("m_customer_type");
		if (!empty($typeid)){
			$this->db->where("typeid",$typeid);
		}
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	function get_data_spot() {
		$this->db->select("spot_id,nama_spot");
		$this->db->from("m_customer_spot");
		if (!empty($spot)){
			$this->db->where("spot",$spot);
		}
		$data = $this->db->get()->result_array();
		return $data;
	}
	
	function get_node($sid,$get_date) {
		
		$siteid = $this->get_siteid(); 
		
		$q = $this->db->query(" select z.* from (
								select case when b.customerid is null and c.no_sales is null then 'InvalidCall' 
									   when b.customerid is null and c.no_sales is not null then 'ExtraCall' 
									   when b.customerid is not null and c.no_sales is null or (c.no_sales is not null and c.retur=1) then 'Cal'
									   when b.customerid is not null and c.no_sales is not null and c.retur=0 then 'EffectiveCall' end as flag,
									   a.siteid, e.nama_site , a.salesmanid,  a.customerid , f.nama_customer, f.alamat, f.latitude, f.longitude, 
									   a.latitude_cell, a.longitude_cell, d.nama_salesman,a.check_in
								from t_sales_rrk_trans a left join t_sales_rrk b on 
								a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
								left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
								left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
								left join m_setup_site e ON a.siteid = e.siteid
								left join m_customer f on a.customerid=f.customerid
								where a.siteid ='".$siteid."' and a.periode='".$get_date."' and a.salesmanid='".$sid."'
								UNION ALL
								select 'Jadwal' as flag, rrk.siteid, site.nama_site , rrk.salesmanid,  rrk.customerid , cust.nama_customer, cust.alamat, cust.latitude, 
								cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, sls.nama_salesman, '' check_in
								from  t_sales_rrk rrk								
								INNER JOIN  m_sales_salesman sls ON rrk.siteid = sls.siteid and rrk.salesmanid = sls.salesmanid
								INNER JOIN  m_customer cust ON rrk.siteid = cust.siteid and rrk.customerid = cust.customerid and rrk.salesmanid=cust.salesmanid
								INNER JOIN  m_setup_site site ON rrk.siteid = site.siteid
								where rrk.siteid = '".$siteid."'  AND rrk.salesmanid = '".$sid."' AND rrk.periode = '".$get_date."'
								UNION ALL
								select 'Noo' as flag, cust.siteid, site.nama_site , cust.salesmanid,  cust.customerid , cust.nama_customer, 
										cust.alamat, cust.latitude, cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, 
										'' nama_salesman, '' check_in
								from  m_customer as cust
								INNER JOIN  m_sales_salesman sls ON cust.siteid = sls.siteid and cust.salesmanid = sls.salesmanid
								INNER JOIN  m_setup_site site ON cust.siteid = site.siteid
								where cust.siteid = '".$siteid."'  AND cust.salesmanid = '".$sid."' AND cust.createdate = '".$get_date."') z
								order by z.check_in asc
								");
		//$this->db->order_by('check_in', 'ASC');
		//$q=$this->db->get();
		return $q->result_array();
	}

	function get_detail_rrk($siteid,$customerid,$salesmanid,$get_date) {
	
		$this->db->select("DATE_FORMAT(check_in, '%H:%i:%s') check_in, DATE_FORMAT(check_out, '%H:%i:%s') check_out, DATE_FORMAT(order_time, '%H:%i:%s') order_time, 
						DATE_FORMAT(crc_time, '%H:%i:%s') crc_time, DATE_FORMAT(ink_time, '%H:%i:%s') tagihan_time, alasan,
						timediff(DATE_FORMAT(check_out, '%H:%i:%s'),DATE_FORMAT(check_in, '%H:%i:%s')) lama_kunjungan");
		$this->db->from("t_sales_rrk_trans");
		$this->db->where("customerid",$customerid);
		$this->db->where("siteid",$siteid);
		$this->db->where("salesmanid",$salesmanid);
		$this->db->where("periode",$get_date);
		$data = $this->db->get()->row();
		return $data;
	}

	function get_fancy() {
	
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
		
		$sql = "select image from m_customer_image where customerid = '".$customerid."' and salesmanid = '".$salesid."' order by id desc";
        $result_array = $this->db->query($sql);
        $response['images'] = $result_array->result();
        return $response;	
	
	}
	
	function get_outlet($segmentid,$classid,$typeid) {
		$siteid = $this->get_siteid(); 

		$q = $this->db->query(" select customerid, ifnull(latitude,0) latitude_cell, ifnull(longitude,0) longitude_cell, nama_customer, alamat,
										DATE_FORMAT(createdate,'%H:%i') waktu
								from m_customer
								where siteid = '".$siteid."'  AND segmentid like '%".$segmentid."%' 
								AND classid like '%".$classid."%' and typeid like '%".$typeid."%' 
								and latitude <> 0 and longitude <> 0
								order by createdate desc
								");

		return $q->result_array();
	}
	
}