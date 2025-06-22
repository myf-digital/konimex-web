<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drc_dashboard_model extends CI_Model
{

    public function set_quota($data)
    {
		//die(var_dump($this->input->post()));

        $listcity = $this->input->post("listcity");
        $listcitynm = $this->input->post("listcitynm");
        $listspg = $this->input->post("listspg");
        $listmd = $this->input->post("listmd");
        $listsfmt = $this->input->post("listsfmt");
        $listsfgt = $this->input->post("listsfgt");
        $listfc = $this->input->post("listfc");
        $periode = date("Y-m")."-01";//$this->input->post("start_periode");
        //$usersession = $this->input->post("usersession");
        
        /*if(isset($data['listcity'])){ $listcity = $data['listcity']; unset($data['listcity']); }
        if(isset($data['listcitynm'])){ $listcitynm = $data['listcitynm']; unset($data['listcitynm']); }
        if(isset($data['listspg'])){ $listspg = $data['listspg']; unset($data['listspg']); }
        if(isset($data['listmd'])){ $listmd = $data['listmd']; unset($data['listmd']); }
        if(isset($data['listsfmt'])){ $listsfmt = $data['listsfmt']; unset($data['listsfmt']); }
        if(isset($data['listsfgt'])){ $listsfgt = $data['listsfgt']; unset($data['listsfgt']); }
        if(isset($data['listfc'])){ $listfc = $data['listfc']; unset($data['listfc']); }
        */
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $created_date = $datetime->datetime;

        //$date=date_create($periode);
        //$setperiode = date_format($date,"Y-m"."-01");
        $this->db->where('periode', $periode);
        $this->db->delete('set_quota_budget');

        for($i=0;$i<count($listcity);$i++){
            $data_array = array(
                "idcity" => $listcity[$i],
                "city" => $listcitynm[$i],
                "periode" => $periode,
                "md" => $listmd[$i],
                "spg" => $listspg[$i],
                "sfmt" => $listsfmt[$i],
                "sfgt" => $listsfgt[$i],
                "fc" => $listfc[$i],
                "created_date" => $created_date
                );
                $execrtrn = $this->db->insert('set_quota_budget', $data_array);
            }
        return $execrtrn;

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
