<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_sales_pjp_model extends CI_Model
{
	function getSalesRekapPjp($salesmanid) {

		$query = $this->db->query("select minggu, hari, count(*) as jml from t_sales_setup_rrk where salesmanid='".$salesmanid."' group by minggu, hari");
		return $query->result_array();
	}

	function getSales($salesmanid) {

		$query = $this->db->query("select s.nama_salesman as nama, s.tipe_sales as posisi, s.aktif from m_sales_salesman s where salesmanid='".$salesmanid."'");
		return $query->row();
	}
}