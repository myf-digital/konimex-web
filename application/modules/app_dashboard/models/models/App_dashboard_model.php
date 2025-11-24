<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_dashboard_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_setup_site');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        return $this->db->insert('m_setup_site', $data); 
    }

    public function update($data)
    {
        $this->db->where('siteid', $data['siteid']);
        return $this->db->update('m_setup_site', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        return $this->db->delete('m_setup_site');
    }

	function get_lat_long() {
		$this->db->select("latitude,longitude");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();
		return $data;
	}

    public function load($data)
    {
		if ($data["restrict_level"]=='4'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."')
												)";
		}
		else if ($data["restrict_level"]=='3'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
											app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
											where a.username='".$data["usersession"]."')
												)";
		}
		else if ($data["restrict_level"]=='2'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."')
												) ";
		}
		else {
			$strquery = "";
		}

		$field = " a.* ";
		$table = " (select distinct z.siteid, z.periode, z.salesmanid, b.nama_salesman, b.tipe_sales, c.nama_area city,
					(select count(1) from t_sales_rrk where periode=z.periode and salesmanid=z.salesmanid) _jadwal, 
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)) _call,
					(select count(1) from t_sales_rrk_trans where periode=z.periode and salesmanid=z.salesmanid and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) ) _extra_call,
					(select count(1) from t_sales_rrk_trans where periode=z.periode and salesmanid=z.salesmanid and crc_time is not null) _crc,
					(select count(1) from t_sales_rrk_trans where periode=z.periode and salesmanid=z.salesmanid and order_time is not null) _order
					from t_sales_rrk_trans z left join t_sales_rrk a on z.salesmanid=a.salesmanid and z.periode=a.periode
					left join m_sales_salesman b on z.salesmanid=b.salesmanid
					left join m_area_subarea c on c.subareaid=b.subareaid
					where z.periode = '".(@$data["get_date"] ?? date('Y-m-d'))."' ".$strquery."
					group by z.siteid, z.salesmanid, b.nama_salesman) a";
        return easy_pagging($data, $field, $table);
    }

	function get_record($siteid,$customerid,$salesmanid,$get_date) {
		$q = $this->db->query(" SELECT tcrc.siteid, site.nama_site,
			   tcrc.salesmanid, sls.nama_salesman,
			   tcrc.customerid,  cust.nama_customer,
			   tcrc.categoryid, product.nama_category,
			   tcrc.brandid, product.nama_brand,
			   tcrc.productid, replace(product.nama_invoice,'\'','`') nama_invoice,
			   tcrc.qty_rata  as r1,
			   tcrc.qty_akhir  as a1,
			   tcrc.qty_saran_order  as s1, 
			   tcrc.qty_fix_order  as f1,
			   ifnull(tcrc.total_qty_exp,0) as exp_qty,
			   DATE_FORMAT(tcrc.exp_date,'%d-%m-%Y') as exp_date,
			   tcrc.price,
			   ifnull(tcrc.total_sales_qty,0) as sell_out
				FROM
				t_sales_crc tcrc 
				JOIN m_customer_ob custob ON tcrc.salesmanid = custob.salesmanid and tcrc.customerid = custob.customerid
				JOIN m_customer cust ON tcrc.siteid = cust.siteid and tcrc.customerid = cust.customerid
				JOIN m_product product ON tcrc.productid = product.productid
				JOIN m_sales_salesman  sls ON tcrc.siteid = sls.siteid and tcrc.salesmanid = sls.salesmanid 
				JOIN m_setup_site site ON tcrc.siteid = site.siteid
				WHERE tcrc.siteid = '".$siteid."' AND
					  tcrc.periode = '".$get_date."' AND
					  tcrc.salesmanid = '".$salesmanid."' AND
					  tcrc.customerid = '".$customerid."'  
				order by tcrc.date_update desc
				");
		$data = $q->result_array();
	
		$return ='\'<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">\'+
				\'<thead>\'+
					\'<tr>\'+
						\'<td colspan="2" align="center">Tanggal</td>\'+
						\'<td colspan="8" align="center">'.$get_date.'</td>\'+						
					\'</tr>\'+
					\'<tr>\'+
						\'<td style="text-align:center;">Product ID</td>\'+
						\'<td style="text-align:left;padding:10px;">Product Name</td>\'+
						\'<td style="text-align:right;padding:10px;">Stock</td>\'+
						\'<td style="text-align:right;padding:10px;">Harga</td>\'+
						\'<td style="text-align:right;padding:10px;">Qty Expired</td>\'+
						\'<td style="text-align:right;padding:10px;">Expired Date</td>\'+
						\'<td style="text-align:right;padding:10px;">Selling Out</td>\'+
						\'<td style="text-align:right;padding:10px;">Rata-rata</td>\'+
						\'<td style="text-align:right;padding:10px;">Saran Order</td>\'+
						\'<td style="text-align:right;padding:10px;">Order</td>\'+
					\'</td>\'+
				\'</thead>\'+
				\'<tbody>\'+';
		
		foreach ($data as $value) {
			$return .= '\'<tr><td style="text-align:center;">'.$value['productid'].'</td>\'+';
			$return .= '\'<td style="text-align:left;padding:10px;">'.$value['nama_invoice'].'</td>\'+';
			
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['a1'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.number_format($value['price'], 2, '.', ',').'</td>\'+';			
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['exp_qty'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['exp_date'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['sell_out'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['r1'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['s1'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['f1'].'</td>\'+';
			
				$return .= '\'</tr>\'+';
					
		}
		
		$return .='\'</tbody></table>\'+';	
		
		return $return;
	}

function target_detail($salesmanid,$get_date) {
		$periode='';
		$percenttot=0;
		$qtd = $this->db->query(" select 
									periode, salesmanid,target,sales,percent,ob,oa,ec,oavsob,ecvsoa,salesvsec 
								from t_productivity
								where year(periode) = year(STR_TO_DATE('".$get_date."', '%Y-%m-%d')) and 
									  month(periode) = month(STR_TO_DATE('".$get_date."', '%Y-%m-%d'))
								and salesmanid = '".$salesmanid."'");
		$data = $qtd->result_array();
		if ($data){
		foreach ($data as $value) { 
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			$percent 	= $value['percent'];
			$ob 		= $value['ob'];
			$oa			= $value['oa'];
			$ec			= $value['ec'];
			$oavsob		= $value['oavsob'];
			$ecvsoa		= $value['ecvsoa'];
			$salesvsec	= $value['salesvsec'];
		}
		}else{
			$target 	= 0;
			$sales 		= 0;
			$percent 	= 0;
			$ob 		= 0;
			$oa			= 0;
			$ec			= 0;
			$oavsob		= 0;
			$ecvsoa		= 0;
			$salesvsec	= 0;
		}
		$time = strtotime($get_date);
		$newformat = date('M - Y',$time);
		
		$html ='<div><h3>Productivity Sales '.$newformat.'</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:400px;">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Description</th>';
		$html .= '<th style="white-space: nowrap;text-align:right; padding-right:15px;">Value</th>';	
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';		
		$html .= '<tr">';
		$html .= '<td text-align:left;padding-left:15px>Value Target</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($target, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Value Sales</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($sales, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Percent</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($percent, 2, '.', ',').'%</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Outlet Binaan (OB)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ob, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Outlet Aktif (OA)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($oa, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Effektif Call (EC)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ec, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>OA vs OB</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($oavsob, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Rata-Rata Transaksi(EC vs OA)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ecvsoa, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Transaksi Per EC (Sales vs EC)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($salesvsec, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		
		$data = '';
		$qtd = $this->db->query("
			select 
				salesmanid,
				DATE_FORMAT(periode,'%d-%m-%Y') periode,
				nama_prinsipal,
				target,
				sales,
				percent
			from t_target_prinsipal_salesman
			where DATE_FORMAT(periode,'%Y%m') = DATE_FORMAT('".$get_date."','%Y%m')
			and salesmanid = '".$salesmanid."'
		");

		$data = $qtd->result_array();
		foreach ($data as $vperiode) {
			$periode = $vperiode['periode'];
		}

		$html .= '<h3>Target Penjualan</h3><h7>Periode : '.$periode.'</h7>
				  <table class="table table-striped table-bordered table-condensed" style="width:700px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Prinsipal</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Target</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Sales</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target = 0; $total_sales = 0; $total_percent = 0;
		foreach ($data as $value) {
			
			
			$prinsipal 	= $value['nama_prinsipal'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			if ($value['sales']==0 || $value['target']==0){
			$percent = 0;
			} else {
			$percent = ($sales/$target)*100;
			}
			
			$total_target	= $total_target +  $target;
			$total_sales 	= $total_sales +  $sales;
			
			if ($total_target==0 || $total_sales==0){
			$percenttot = 0;
			} else {
			$percenttot = ($total_sales/$total_target)*100;
			}
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">'.$prinsipal.'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($target ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($sales ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percent ,2, '.', ',').' %</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Total</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_target ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_sales ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percenttot ,2, '.', ',').' %</th>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table></div>';
		
		//Target per group
		$q = $this->db->query("
			select 
				salesmanid,
				DATE_FORMAT(periode,'%d-%m-%Y') periode,
				nama_group,
				target,
				sales,
				percent
			from t_target_group_salesman
			where DATE_FORMAT(periode,'%Y%m') = DATE_FORMAT('".$get_date."','%Y%m')
			and salesmanid = '".$salesmanid."'
		");

		$data = $q->result_array();
		foreach ($data as $vperiode) {
			$periode = $vperiode['periode'];
		}

		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:700px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Nama Group</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Target</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Sales</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target = 0; $total_sales = 0; $total_percent = 0;
		foreach ($data as $value) {
			
			
			$group 	= $value['nama_group'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			if ($value['sales']==0 || $value['target']==0){
			$percent = 0;
			} else {
			$percent = ($sales/$target)*100;
			}
			
			$total_target	= $total_target +  $target;
			$total_sales 	= $total_sales +  $sales;
			
			if ($total_target==0 || $total_sales==0){
			$percenttot = 0;
			} else {
			$percenttot = ($total_sales/$total_target)*100;
			}
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">'.$group.'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($target ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($sales ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percent ,2, '.', ',').' %</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Total</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_target ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_sales ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percenttot ,2, '.', ',').' %</th>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';

		echo $html;
	}

function get_order($siteid,$customerid,$salesmanid,$get_date) {
        $return = '\'<table class="table table-striped table-bordered table-condensed">\'+
                    \'<thead>\'+
                    \'<tr>\'+
                    \'<th style="white-space: nowrap;padding-left:10px">Product ID </th>\'+
                    \'<th style="white-space: nowrap;padding-left:10px" >Nama Invoice</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Harga Jual</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Bruto</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Diskon</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Neto</th>\'+	
                    \'</tr>\'+
                    \'</thead>\'+
                    \'<tbody>\'+';
        $q_detail = $this->db->query("
            select 
            sls.siteid, 
            sls.salesmanid,
            salesamn.nama_salesman,
            sls.customerid,
            cst.nama_customer,
            cst.alamat,
            dtl.productid,
            product.nama_invoice,
            sum(case when dtl.flag_bonus = 0 then 'JUAL' else 'BONUS' end) as statu_order,
            sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
            sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
            dtl.h_jual,
            sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
            dtl.disc_cabang,
            dtl.disc_prinsipal,
            dtl.disc_xtra,
            dtl.disc_cod,
            SUM(dtl.rp_cabang) AS rp_cabang,
            SUM(dtl.rp_prinsipal) AS rp_prinsipal,
            SUM(dtl.rp_xtra) AS rp_xtra,
            sum(dtl.rp_cod) as rp_cod,
            sum(case when dtl.flag_bonus = 0 then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
            sum(case when dtl.flag_bonus = 0 then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
            from 
            t_sales_master sls left join
            t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales left JOIN
			left JOIN m_customer_ob custob ON sls.salesmanid = custob.salesmanid and sls.customerid = custob.customerid
            left join m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid left JOIN
            m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
            m_product product on dtl.productid = product.productid 
            where sls.siteid = '".$siteid."' AND 
                sls.salesmanid = '".$salesmanid."' AND
                sls.retur = 0 AND
                date(sls.tanggal) = '".$get_date."' AND
                sls.customerid = '".$customerid."'
            group by sls.siteid, 
                sls.salesmanid,
                salesamn.nama_salesman,
                sls.customerid,
                cst.nama_customer,
                cst.alamat,
                dtl.productid,
                product.nama_invoice,dtl.h_jual,
                dtl.disc_cabang,
                dtl.disc_prinsipal,
                dtl.disc_xtra,
                dtl.disc_cod
        ");
        $totbruto = 0;
        $totdisc  = 0;
        $totnetto = 0;
        $k_detail = $q_detail->result_array();
        foreach ($k_detail as $v_detail) {
            $return .= '\'<tr>\'+
                        \'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['productid'].'</td>\'+
                        \'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['nama_invoice'].'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_bruto'], 2, '.', ',').'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_discount'], 2, '.', ',').'</td>\'+			
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>\'+
                        \'</tr>\'+';
            $totbruto = $totbruto+$v_detail['total_bruto'];
            $totdisc = $totdisc+$v_detail['total_discount'];
            $totnetto = $totnetto+$v_detail['total_netto'];
        }
        $return .='\'<tr>\'+
                    \'<th colspan="4" style="white-space: nowrap;padding-left:10px">Total </th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totbruto, 2, '.', ',').'</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totdisc, 2, '.', ',').'</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totnetto, 2, '.', ',').'</th>\'+	
                    \'</tr>\'+
                    \'</tbody>\'+
                    \'</table>\'+';

    return $return;
}

    function get_crc($siteid,$salesmanid,$get_date,$customerid) {
        $q = $this->db->query("SELECT tcrc.siteid, site.nama_site,
            tcrc.salesmanid, sls.nama_salesman,
            tcrc.customerid,  cust.nama_customer,
            tcrc.categoryid, product.nama_category,
            tcrc.brandid, product.nama_brand,
            tcrc.productid, product.nama_invoice,
            tcrc.qty_rata  as r1,
            tcrc.qty_akhir  as a1,
            tcrc.qty_saran_order  as s1, 
            tcrc.qty_fix_order  as f1
                FROM
                t_sales_crc tcrc 
				JOIN m_customer_ob custob ON tcrc.salesmanid = custob.salesmanid and tcrc.customerid = custob.customerid
                INNER JOIN m_customer cust ON tcrc.siteid = cust.siteid and tcrc.customerid = cust.customerid
                INNER JOIN m_product product ON tcrc.productid = product.productid
                INNER JOIN m_sales_salesman  sls ON tcrc.siteid = sls.siteid and tcrc.salesmanid = sls.salesmanid 
                INNER JOIN m_setup_site site ON tcrc.siteid = site.siteid
                WHERE tcrc.siteid = '".$siteid."' AND
                    tcrc.periode = '".$get_date."' AND
                    tcrc.salesmanid = '".$salesmanid."' AND
                    tcrc.customerid = '".$customerid."'  
                ");
        $data = $q->result_array();

        $return ='<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">';
        $return .= '<thead>';
        $return .=	'<tr>';
        $return .='<th colspan="8" align="center">Tanggal</th>';
        $return .='<th colspan="4" align="center">'.$get_date.'</th>';
        $return .='</tr><tr>';
        $return .='<th colspan ="4" style="text-align:center;">Product ID</th>';
        $return .='<th colspan ="4" style="text-align:left;padding:10px;">Product Name</th>';
        $return .='<th style="text-align:right;padding:10px;">Rata-rata</th>';
        $return .='<th style="text-align:right;padding:10px;">Akhir</th>';
        $return .='<th style="text-align:right;padding:10px;">Saran Order</th>';
        $return .='<th style="text-align:right;padding:10px;">Fix Order</th>';
        $return .='</tr></thead><tbody>';

        foreach ($data as $value) {
           	$return .= '<tr><td colspan ="4" style="text-align:center;">'.$value['productid'].'</td>';
            $return .= '<td colspan ="4" style="text-align:left;padding:10px;">'.$value['nama_invoice'].'</td>';
            
                $return .= '<td style="text-align:right;padding:10px;">'.$value['r1'].'</td>';
                $return .= '<td style="text-align:right;padding:10px;">'.$value['a1'].'</td>';
                $return .= '<td style="text-align:right;padding:10px;">'.$value['s1'].'</td>';
                $return .= '<td style="text-align:right;padding:10px;">'.$value['f1'].'</td>';			
            
                $return .= '</tr>';
                    
        }

        $return .='</tbody></table>';	

        return $return;
    }

	function get_node($sid,$get_date) {
		
		//$siteid = $this->get_siteid(); 
		
		$q = $this->db->query(" select z.* from (
								select case when b.customerid is null then 'ExtraCall' 
									   when b.customerid is not null then 'EffectiveCall' end as flag,
									   a.siteid, e.nama_site , a.salesmanid,  a.customerid , f.nama_customer, f.alamat, f.latitude, f.longitude, 
									   a.latitude_cell, a.longitude_cell, d.nama_salesman,a.check_in
								from t_sales_rrk_trans a left join t_sales_rrk b on 
								a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
								left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
								left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
								left join m_setup_site e ON a.siteid = e.siteid
								left JOIN m_customer_ob custob ON a.salesmanid = custob.salesmanid and a.customerid = custob.customerid
								left join m_customer f on a.customerid=f.customerid
								where a.periode='".$get_date."' and a.salesmanid='".$sid."'
								group by a.periode,a.salesmanid,a.customerid
								UNION ALL
								select 'Jadwal' as flag, rrk.siteid, site.nama_site , rrk.salesmanid,  rrk.customerid , cust.nama_customer, cust.alamat, cust.latitude, 
								cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, sls.nama_salesman, '' check_in
								from  t_sales_rrk rrk								
								INNER JOIN  m_sales_salesman sls ON rrk.siteid = sls.siteid and rrk.salesmanid = sls.salesmanid
								JOIN m_customer_ob custob ON rrk.salesmanid = custob.salesmanid and rrk.customerid = custob.customerid
								INNER JOIN  m_customer cust ON rrk.siteid = cust.siteid and rrk.customerid = cust.customerid
								INNER JOIN  m_setup_site site ON rrk.siteid = site.siteid
								where rrk.periode = '".$get_date."' and rrk.salesmanid = '".$sid."' 
								UNION ALL
								select 'Noo' as flag, cust.siteid, site.nama_site , cust.salesmanid,  cust.customerid , cust.nama_customer, 
										cust.alamat, cust.latitude, cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, 
										sls.nama_salesman, '' check_in
								from  m_customer as cust
								INNER JOIN  m_sales_salesman sls ON cust.siteid = sls.siteid and cust.salesmanid = sls.salesmanid
								INNER JOIN  m_setup_site site ON cust.siteid = site.siteid
								where cust.salesmanid = '".$sid."' AND cust.createdate = '".$get_date."') z
								order by z.check_in asc
								");
		//$this->db->order_by('check_in', 'ASC');
		//$q=$this->db->get();
		return $q->result_array();
	}

	function get_detail_rrk($siteid,$customerid,$salesmanid,$get_date) {
	
		$this->db->select("DATE_FORMAT(a.check_in, '%H:%i:%s') check_in, DATE_FORMAT(a.check_out, '%H:%i:%s') check_out, DATE_FORMAT(a.order_time, '%H:%i:%s') order_time, 
						DATE_FORMAT(a.crc_time, '%H:%i:%s') crc_time, DATE_FORMAT(a.ink_time, '%H:%i:%s') tagihan_time, 
						(select reason from t_sales_rrk_reason where call_reasonid=a.call_reasonid) alasan,
						timediff(DATE_FORMAT(a.check_out, '%H:%i:%s'),DATE_FORMAT(a.check_in, '%H:%i:%s')) lama_kunjungan, a.keterangan");
		$this->db->from("t_sales_rrk_trans a");
		$this->db->where("a.customerid",$customerid);
		$this->db->where("a.siteid",$siteid);
		$this->db->where("a.salesmanid",$salesmanid);
		$this->db->where("a.periode",$get_date);
		$data = $this->db->get()->row();
		return $data;
    }

	function get_image_cust($siteid,$customerid,$salesmanid) {
	
		$this->db->select("image");
		$this->db->from("m_customer_image");
		$this->db->where("customerid",$customerid);
		$this->db->where("siteid",$siteid);
		$this->db->where("salesmanid",$salesmanid);
		$this->db->where("image_type","IMG_OUTLET");
		$this->db->order_by("created_date","desc");
		$this->db->limit(1, 0);
		$data = $this->db->get()->row();
		return $data;
	}

	function get_image_checkin($siteid,$periode,$salesmanid,$customerid) {
	$this->db->select("image,image_type");
	$this->db->from("m_customer_image");
	$this->db->where("siteid",$siteid);
	$this->db->where("periode",$periode);
	$this->db->where("salesmanid",$salesmanid);
	$this->db->where("customerid",$customerid);
	$this->db->where("image_type","IMG_CHECKIN");
	$data = $this->db->get()->row();
	return $data;
	}

	function get_image_before($siteid,$periode,$salesmanid,$customerid) {
	$this->db->select("image,description");
	$this->db->from("m_customer_image");
	$this->db->where("periode",$periode);
	$this->db->where("siteid",$siteid);
	$this->db->where("salesmanid",$salesmanid);
	$this->db->where("customerid",$customerid);
	$this->db->where("image_type","IMG_CHECKIN_CRC");
	$data = $this->db->get()->row();
	return $data;
	}

	function get_image_after($siteid,$periode,$salesmanid,$customerid) {
	$this->db->select("image,description");
	$this->db->from("m_customer_image");
	$this->db->where("periode",$periode);
	$this->db->where("siteid",$siteid);
	$this->db->where("salesmanid",$salesmanid);
	$this->db->where("customerid",$customerid);
	$this->db->where("image_type","IMG_CHECKOUT_CRC");
	$data = $this->db->get()->row();
	return $data;
	}

	function get_image_dokumentasi($siteid,$periode,$salesmanid,$customerid,$imgtype) {
		$this->db->select("image,description");
		$this->db->from("m_customer_image");
		$this->db->where("periode",$periode);
		$this->db->where("siteid",$siteid);
		$this->db->where("salesmanid",$salesmanid);
		$this->db->where("customerid",$customerid);
		$this->db->where("image_type",$imgtype);
		$data = $this->db->get()->row();
		return $data;
	}

		
	function get_image_sos($siteid,$periode,$salesmanid,$customerid) {
		$q = $this->db->query(" select a.qty_sos_gsk, a.qty_sos_competitor, 
								case when a.type_sos='B' then 'Toothbrush' when a.type_sos='P' then 'Toothpaste' else '' end type_sos,
								a.sos, (select group_concat(image SEPARATOR ',')from m_customer_image where transaction_id=a.transaction_id)  image
								from t_activity_sos a 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
									  and a.salesmanid='".$salesmanid."' and a.customerid='".$customerid."' 
								order by a.created_date desc; "); 
		$data = $q->result_array();
		return $data;
	}

	function get_image_competitor($siteid,$periode,$salesmanid,$customerid) {
		$q = $this->db->query(" select a.productid, b.nama_invoice, c.image, a.harga_normal, a.harga_promo, a.sewa, a.tipesewa, a.description
								from t_activity_competitor a join m_product_competitor b on a.productid=b.productid 
								left join m_customer_image c on c.transaction_id=a.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesmanid."' and a.customerid='".$customerid."' 
								order by a.created_date desc; ");
		$data = $q->result_array();
		return $data;
	}

	function get_image_npd($siteid,$periode,$salesmanid,$customerid) {
		$q = $this->db->query(" select a.product_name, b.image, a.harga_normal, a.description
								from t_activity_npd_competitor a left join m_customer_image b on b.transaction_id=a.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesmanid."' and a.customerid='".$customerid."' 
								order by a.created_date desc; ");
		$data = $q->result_array();
		return $data;
	}

	function get_image_promo_gsk($siteid,$periode,$salesmanid,$customerid) {
		$q = $this->db->query(" select a.idpromo, b.promo, c.image, a.tipepromo, a.display, a.harga_normal, a.harga_promo, a.description
								from t_activity_promo_gsk a join mapping_promo_active b on a.idpromo=b.idpromo
								left join m_customer_image c on c.transaction_id=a.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesmanid."' and a.customerid='".$customerid."' 
								union all
								select a.idpromo, b.promo, concat(a.image,',',a.image_st) image, 'Gimmick' tipepromo, '' display, a.stock_awal, a.qty_pasang, a.description
								from t_activity_promo_gsk_gimmick a join mapping_promo_active b on a.idpromo=b.idpromo
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesmanid."' and a.customerid='".$customerid."'
								; ");
		$data = $q->result_array();
		return $data;
	}


	function get_siteid() {
		$this->db->select("siteid");
		$this->db->from("m_setup_site");
		$site = $this->db->get()->row()->siteid;
		return $site;
	}

	function get_tracking($sid,$get_date) {
		$siteid = $this->get_siteid(); 

		$q = $this->db->query(" select ifnull(latitude_cell,0) latitude_cell, ifnull(longitude_cell,0) longitude_cell,
										DATE_FORMAT(createdate,'%H:%i') waktu
								from t_tracker_salesman
								where siteid = '".$siteid."'  AND salesmanid = '".$sid."' AND periode = '".$get_date."'
								order by DATE_FORMAT(createdate,'%H:%i') asc
								");

		return $q->result_array();
	}

	function get_fancy() {
	
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
		
		$sql = "select image from m_customer_image where customerid = '".$customerid."' and salesmanid = '".$salesid."' and image_type='IMG_OUTLET' order by created_date desc";
        $result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
        return $response;	
	
	}

	function get_fancy_checkin() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "select image,image_type from m_customer_image where siteid = '".$siteid."' and periode = '".$periode."' and customerid = '".$customerid."' and salesmanid = '".$salesid."' and image_type='IMG_CHECKIN' ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;	
	}

	function get_fancy_before() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "select image,image_type,description from m_customer_image where siteid = '".$siteid."' and periode = '".$periode."' and customerid = '".$customerid."' and salesmanid = '".$salesid."' and image_type='IMG_CHECKIN_CRC' ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;	
	}

	function get_fancy_after() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "select image,image_type,description from m_customer_image where siteid = '".$siteid."' and periode = '".$periode."' and customerid = '".$customerid."' and salesmanid = '".$salesid."' and image_type='IMG_CHECKOUT_CRC' ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;
	}

	function get_fancy_sellout() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "select image,image_type,description from m_customer_image where siteid = '".$siteid."' and periode = '".$periode."' and customerid = '".$customerid."' and salesmanid = '".$salesid."' and image_type like 'IMG_SELL_OUT_%' ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;
	}

	function get_fancy_sos() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = " select a.qty_sos_gsk, a.qty_sos_competitor, a.sos, b.image from t_activity_sos a join m_customer_image b on a.transaction_id=b.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
									  and a.salesmanid='".$salesid."' and a.customerid='".$customerid."'
								order by a.created_date desc; ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;	
	}

	function get_fancy_competitor() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "select a.productid, b.nama_invoice, c.image, a.harga_normal, a.harga_promo, a.sewa, a.tipesewa, a.description
								from t_activity_competitor a join m_product_competitor b on a.productid=b.productid 
								left join m_customer_image c on c.transaction_id=a.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesid."' and a.customerid='".$customerid."' 
								order by a.created_date desc;  ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;	
	}

	function get_fancy_npd() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = " select a.product_name, b.image, a.harga_normal, a.description
						from t_activity_npd_competitor a join m_customer_image b on a.transaction_id=b.transaction_id 
						where a.siteid='".$siteid."' and a.periode='".$periode."'
								and a.salesmanid='".$salesid."' and a.customerid='".$customerid."' 
						order by a.created_date desc; ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;
	}

	function get_fancy_promo_gsk() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "  select a.idpromo, b.promo, c.image, a.tipepromo, a.display, a.harga_normal, a.harga_promo, a.description
								from t_activity_promo_gsk a join mapping_promo_active b on a.idpromo=b.idpromo
								join m_customer_image c on c.transaction_id=a.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesid."' and a.customerid='".$customerid."' 
								order by a.created_date desc; ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;
	}

	function get_fancy_promo_gsk_gimmick() {
		$siteid = $_POST['siteid'];
		$periode = $_POST['periode'];
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
	
		$sql = "  select a.idpromo, b.promo, c.image, a.description
								from t_activity_promo_gsk_gimmick a join mapping_promo_active b on a.idpromo=b.idpromo
								join m_customer_image c on c.transaction_id=a.transaction_id 
								where a.siteid='".$siteid."' and a.periode='".$periode."'
										and a.salesmanid='".$salesid."' and a.customerid='".$customerid."' 
								order by a.created_date desc; ";
		$result_array = $this->db->query($sql);
		$response['images'] = $result_array->result();
		return $response;
	}

	function replace_evaluation($data)
	{

		return $this->db->query("replace into t_sales_rrk_trans_evaluation(periode,salesmanid,username,nilai,evaluation,created_by,created_date,modified_by,modified_date) 
			values('".$data['periode']."','".$data['salesmanid']."','".$data['username']."','".$data['nilai']."','".$data['evaluation']."','".$data['username']."',now(),'".$data['username']."',now())");
	}

	function get_rating($custIdArr, $date)
	{

		$ids = join(",",$custIdArr);

		$sql = 'select customerid, round(avg(rating_star),2) as rating_star from rating_review where customerid in ('.$ids.') and periode between "'.$date.'" and "'.$date.'" group by customerid';

        $query = $this->db->query($sql);

        return $query->result_array();
	}

}
