<?php 
class Rep_attsales_model extends CI_Model {
	
	function get_header($periode) {
		
		$query = $this->db->query("call days_of_month('".$periode."');");

		return $query->result_array();
		
	}
	
	function get_salesman() {
		
		$q = $this->db->query("
			select siteid, salesmanid, nama_salesman  from 
			m_sales_salesman;");
		return $q->result_array();	
	}

	function get_salesman_aktif($salesmanid,$date) {

		$query = $this->db->query("
			select distinct periode, siteid, salesmanid, 1 aktif from t_sales_rrk_trans
			where salesmanid='".$salesmanid."' and DATE_FORMAT(periode,'%Y-%m-%d') = '".$date."';
				   ");
		return $query->result_array();
	}

}