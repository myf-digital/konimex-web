<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drc_dashboard_model extends CI_Model
{

    function getRekapSlob($data)
    {

        $where = "";

        if ($data['brandid'] != 'null') {
            $where = "b.brandid = '".$data['brandid']."' and";
            if ($data['productid'] != 'null') {
                $where = "b.productid = '".$data['productid']."' and";
            }
        }

        $sql = "select a.qty_ed, a.bulan, a.tahun, b.nama_invoice as product, (a.qty_ed*b.h_grosir) as nilai, c.typeid as channel, c.kode_outlet, c.nama_customer as outlet, d.nama_class as account, e.nama_regional as regional, f.nama_area as area, g.nama_area as subarea from t_stock_all_outlet a left join m_product b on a.productid = b.productid left join m_customer c on a.customerid = c.customerid left join m_customer_class d on c.classid = d.classid left join m_area_regional e on c.regionalid = e.regionalid left join m_area_areasite f on c.areaid = f.areaid left join m_area_subarea g on c.subareaid = g.subareaid where ".$where." c.subareaid = '".$data['subareaid']."' having a.qty_ed > 0 order by product, outlet asc";

        $query = $this->db->query($sql);

        return $query->result_array();
    }
    
    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_promo($data)
    {
        $field = " a.* ";
        $table = " ( select promo, GROUP_CONCAT(a.idpromo) as idpromo  from 
                     mapping_promo_active a left join m_customer_class b on a.classid=b.classid
                     group by promo
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

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
    
	function get_city() {
		$q = $this->db->query("
        select a.subareaid, a.nama_area city, b.periode, ifnull(b.md,0) md, ifnull(b.spg,0) spg, ifnull(b.sfmt,0) sfmt, ifnull(b.sfgt,0) sfgt, ifnull(b.fc,0) fc 
        from m_area_subarea a left join set_quota_budget b on a.subareaid=b.idcity and b.periode = (select max(periode) from set_quota_budget)
        order by a.nama_area asc");
		return $q->result_array();
	}

	function get_salesman_aktif($salesmanid,$date) {

		$query = $this->db->query("
			select distinct periode, siteid, salesmanid, 1 aktif from t_sales_rrk_trans
			where salesmanid='".$salesmanid."' and DATE_FORMAT(periode,'%Y-%m-%d') = '".$date."';
				   ");
		return $query->result_array();
	}

	function get_salesman_aktif_sum($salesmanid,$date1,$date2) {

		$query = $this->db->query("
			select sum(a.aktif) sumaktif from (
			select distinct periode, siteid, salesmanid, 1 aktif from t_sales_rrk_trans
			where salesmanid='".$salesmanid."' and periode>=DATE_FORMAT('".$date1."','%Y-%m-%d') and periode<=DATE_FORMAT('".$date2."','%Y-%m-%d')) a");
		return $query->result_array();
	}

}
