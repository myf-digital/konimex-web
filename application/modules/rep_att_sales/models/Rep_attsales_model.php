<?php 
class Rep_attsales_model extends CI_Model {
	
	function get_header($periode) {
		
		$query = $this->db->query("call days_of_month('".$periode."');");

		return $query->result_array();
		
	}
	
	function get_salesman($idjabatan,$usersession) {

		if ($idjabatan=='2' or $idjabatan=='3')
			$strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
		else if($idjabatan=='16' or $idjabatan=='17'){
			$strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
		}else{
            $strquery = "";
        }

		
		$q = $this->db->query("
			select siteid, salesmanid, nama_salesman, tipe_sales  from 
			m_sales_salesman where aktif='1' $strquery;");
		return $q->result_array();
	}

	function get_salesman_aktif($salesmanid,$date) {

		$query = $this->db->query("
			select distinct periode, siteid, salesmanid, 1 aktif from t_sales_rrk_trans
			where salesmanid='".$salesmanid."' and DATE_FORMAT(periode,'%Y-%m-%d') = '".$date."';
				   ");
		return $query->result_array();
	}

	/*function get_salesman_aktif_sum($salesmanid,$date1,$date2) {

		$query = $this->db->query("
			select sum(a.aktif) sumaktif from (
			select distinct periode, siteid, salesmanid, 1 aktif from t_sales_rrk_trans
			where salesmanid='".$salesmanid."' and periode>=DATE_FORMAT('".$date1."','%Y-%m-%d') and periode<=DATE_FORMAT('".$date2."','%Y-%m-%d')) a");
		return $query->result_array();
	}*/
	
}
