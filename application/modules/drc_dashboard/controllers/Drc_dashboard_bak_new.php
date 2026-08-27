<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drc_dashboard extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Drc_dashboard_model', 'drc_dashboard');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

	public function set_quota()
    {
		$data = param_input();
        response($this->drc_dashboard->set_quota($data));
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }


    public function load()
    {
        $data = param_input();
        responseJSON($this->drc_dashboard->load($data));
    }

	function open_detail_national() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");

		if (intval(date("Ym")) == intval($tahun.$bulan)){
			if ($tanggal=='All'){
				$periodedate= date("Y-m-d",strtotime($tahun.'-'.$bulan.'-'.date("d")));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}else{
			if ($tanggal=='All'){
				$periode= $tahun.'-'.$bulan.'-01';
				$periodedate= date("Y-m-t",strtotime($periode));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " where c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												)";
			if ($tanggal=='All'){
				$strquery1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";

			}else{
				$strquery1 = " where a.periode = '$periode' and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "";
				$strqueryprodgff = " and a.periode = '$periode'
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " where b.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu areaid in (select distinct b.areaid from  
											app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
											where a.username='".$usersession."')
												)";
			if ($tanggal=='All'){
				$strquery1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}else{
				$strquery1 = " where a.periode = '$periode' and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "";
				$strqueryprodgff = " and a.periode = '$periode'
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " where a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												) ";
			if ($tanggal=='All'){
				$strquery1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and e.regionalid in (select distinct b.regionalid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}else{
				$strquery1 = " where a.periode = '$periode' and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "";
				$strqueryprodgff = " and a.periode = '$periode'
									 and e.regionalid in (select distinct b.regionalid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else {
			$strqueryarea ="";
			$strquery = "";
			if ($tanggal=='All'){
				$strquery1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H'";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01')";
			}else{
				$strquery1 = " where a.periode = '$periode' and a.status='H'";
				$strpengali = "";
				$strqueryprodgff = " and a.periode = '$periode' ";
			}
		}

		$qexecabsensi = $this->db->query("replace into t_sales_absensi (periode, salesmanid, status, checkin, checkout, keterangan, pjp, `call`, extra_call, crc, promo, competitor, `order`, sos)
										select a.date, a.salesman_id, 
												case when a.type='IST' then 'S' 
												when a.type='ICT' then 'C' 
												when a.type='AHR' then 'H' 
												else 'HF' end tipe, check_in checkin, check_out checkout, 
												concat(a.description_in,'-', a.description_out) keterangan, 0 _pjp, 0 _call, 0 _extra_call, 0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos  
										from s_absensi a 
										where a.date between DATE_ADD((select tanggal from m_setup_site), INTERVAL -2 DAY) and DATE_ADD((select tanggal from m_setup_site), INTERVAL -1 DAY)
										union
										select a.periode,a.salesmanid,'H' status, min(a.check_in) checkin, max(a.check_out) checkout, '' keterangan, 
										(select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) _pjp, 
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)) _call,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) ) _extra_call,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and crc_time is not null) _crc,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and promo_time is not null) _promo,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and competitor_time is not null) _competitor,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and order_time is not null) _order,
										(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and sos_time is not null) _sos
										from t_sales_rrk_trans a where a.periode between DATE_ADD((select tanggal from m_setup_site), INTERVAL -2 DAY) and DATE_ADD((select tanggal from m_setup_site), INTERVAL -1 DAY)
										group by a.periode,a.salesmanid
										;");

		##query get quota national
       	$qhcnat = $this->db->query(" 
									select 'quota' hcff, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
									$strqueryarea
									union all
									select 'actual' hcff, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select 
									(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid $strquery) md,
										(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid $strquery) spg,
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfmt,  
										(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									$strqueryarea
									group by a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									) x;
							");
		$data = $qhcnat->result_array();
		//echo $this->db->last_query();die();
		
		##query get quota national
		
		$qattnat = $this->db->query(" 
									select 'board' att, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select 
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid $strquery) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid $strquery) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1
									group by a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									) x
									union all
									select 'field' att, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select 
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strquery1
									) x;
									");
		//echo $this->db->last_query();
		$dataatt = $qattnat->result_array();

		##query get performance TPE national
		$qpfgffnat = $this->db->query(" 
										select '_PJP' header, 
												sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) MD,
											sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) SPG,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 $strqueryprodgff
										union all
										select '_CALL' header, 
												sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
											sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 $strqueryprodgff
										union all
										select '_EXT_CALL' header, 
												sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
											sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 $strqueryprodgff
									");
		//echo $this->db->last_query();
		$dataprfgff = $qpfgffnat->result_array();
		
		$html ='<div class="box-header" id="national"><h3>Man Power</h3>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">National</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;text-align:center;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;text-align:center;">SPG</th>';
			$html .= '<th style="white-space: nowrap;text-align:center;">MD</th>';
			$html .= '<th style="white-space: nowrap;text-align:center;">SR MT</th>';
		}

		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			if ($value['hcff']=='quota'){
				$quotamd=$value['jmlmd'];
				$quotaspg=$value['jmlspg'];
				$quotasfmt=$value['jmlsfmt'];
				$quotasfgt=$value['jmlsfgt'];
			}else if ($value['hcff']=='actual'){
				$actualmd=$value['jmlmd'];
				$actualspg=$value['jmlspg'];
				$actualsfmt=$value['jmlsfmt'];
				$actualsfgt=$value['jmlsfgt'];
			}
		}
			if ($quotamd!=0){ $hcmd = $actualmd/$quotamd *100; } else {$hcmd=0;}
			if ($quotaspg!=0){ $hcspg = $actualspg/$quotaspg *100;}else {$hcspg=0;}
			if ($quotasfmt!=0){ $hcsfmt = $actualsfmt/$quotasfmt *100;}else {$hcsfmt=0;}
			if ($quotasfgt!=0){ $hcsfgt = $actualsfgt/$quotasfgt *100;}else {$hcsfgt=0;}

			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_hc_regional();">Fulfillment</a></td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($hcsfgt, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($hcspg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($hcmd, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($hcsfmt, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			foreach ($dataatt as $value) {
				if ($value['att']=='board'){
					$boardmd=$value['jmlmd'];
					$boardspg=$value['jmlspg'];
					$boardsfmt=$value['jmlsfmt'];
					$boardsfgt=$value['jmlsfgt'];
				}else if ($value['att']=='field'){
					$fieldmd=$value['jmlmd'];
					$fieldspg=$value['jmlspg'];
					$fieldsfmt=$value['jmlsfmt'];
					$fieldsfgt=$value['jmlsfgt'];
				}
			}
				if ($boardmd!=0){ $attmd = $fieldmd/$boardmd *100; } else {$attmd=0;}
				if ($boardspg!=0){ $attspg = $fieldspg/$boardspg *100;}else {$attspg=0;}
				if ($boardsfmt!=0){ $attsfmt = $fieldsfmt/$boardsfmt *100;}else {$attsfmt=0;}
				if ($boardsfgt!=0){ $attsfgt = $fieldsfgt/$boardsfgt *100;}else {$attsfgt=0;}

				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"><a href="#" id="attnational" onclick="open_preview_att_regional();">Absensi</a></td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($attsfgt, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($attspg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($attmd, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($attsfmt, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';

				foreach ($dataprfgff as $value) {
					if ($value['header']=='_PJP'){
						$pjpmd=$value['MD'];
						$pjpspg=$value['SPG'];
						$pjpsfmt=$value['SLS_MT'];
						$pjpsfgt=$value['SLS_GT'];
					}else if ($value['header']=='_CALL'){
						$callmd=$value['MD'];
						$callspg=$value['SPG'];
						$callsfmt=$value['SLS_MT'];
						$callsfgt=$value['SLS_GT'];
					}
				}
					if ($pjpmd!=0){ $acallmd = $callmd/$pjpmd *100; } else {$acallmd=0;}
					if ($pjpspg!=0){ $acallspg = $callspg/$pjpspg *100;}else {$acallspg=0;}
					if ($pjpsfmt!=0){ $acallsfmt = $callsfmt/$pjpsfmt *100;}else {$acallsfmt=0;}
					if ($pjpsfgt!=0){ $acallsfgt = $callsfgt/$pjpsfgt *100;}else {$acallsfgt=0;}
	
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"><a href="#" id="attnational" onclick="open_preview_act_call_regional();">Actual Call</a></td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format(@$acallsfgt, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format(@$acallspg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format(@$acallmd, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format(@$acallsfmt, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			//$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;text-align:right;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			//$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'">'.$flag.'</td>';
		
		$html .= '</tbody>';
		$html .= '</table></div></div>';

		$html .='<script>';
		$html .='const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_hc_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
								
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_att_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
								
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_act_call_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
								
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}
					
				';
		$html .='</script>';
		echo $html;

	}

	/*function open_detail_productivity() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;

		$html ='<div class="box-header" id="productivity"><h3>Productivity</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Report</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;

		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_hc_regional();">Product Availability</a></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_hc_regional();">SOS</a></td>';
		$html .= '</tr>';


		$html .= '</tbody>';
		$html .= '</table></div>';

		$html .='<script>';
		$html .='const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_hc_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
								
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_att_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
								
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_act_call_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
								
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}
					
				';
		$html .='</script>';
		echo $html;

	}*/

	function open_hc_detail_regional() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " where c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												)";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " where b.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu areaid in (select distinct b.areaid from  
											app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
											where a.username='".$usersession."')
												)";
			$strquery1 = " and b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												) ";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " where a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qhcnat = $this->db->query(" 
										select 'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
										from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
										left join m_area_subarea c on c.areaid=b.areaid
										left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
										$strqueryarea
										union all
										select 'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
										(select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea,
										(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid $strquery) md,
										(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid $strquery) spg,
										(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfmt,  
										(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfgt
										from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
										left join m_area_subarea c on c.areaid=b.areaid
										left join m_sales_salesman d on d.subareaid = c.subareaid
										$strqueryarea
										group by a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
										) x 
										order by item desc;
										;
								");
		$datanat = $qhcnat->result_array();
		
		$qhcreg = $this->db->query(" 
										select a.regionalid,a.nama_regional,'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
										from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
										left join m_area_subarea c on c.areaid=b.areaid
										left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
										$strqueryarea
										group by a.regionalid,a.nama_regional
										union all
										select x.regionalid, x.nama_regional,'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
										(select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea,
										(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid $strquery) md,
										(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid $strquery) spg,
										(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfmt,  
										(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid $strquery) sfgt
										from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
										left join m_area_subarea c on c.areaid=b.areaid
										left join m_sales_salesman d on d.subareaid = c.subareaid
										$strqueryarea
										group by a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
										) x group by x.regionalid, x.nama_regional
										order by regionalid asc, item desc;
										;
								");
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Fulfillment By Region</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2"></th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datanat as $vnat) {
			if ($vnat['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">National</td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vnat['jmlmd'];
					$quotaspgreg=$vnat['jmlspg'];
					$quotasfmtreg=$vnat['jmlsfmt'];
					$quotasfgtreg=$vnat['jmlsfgt'];

				}else if ($vnat['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vnat['jmlmd'];
					$actualspgreg=$vnat['jmlspg'];
					$actualsfmtreg=$vnat['jmlsfmt'];
					$actualsfgtreg=$vnat['jmlsfgt'];
				}
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($regional!=''){
				if ($vreg['nama_regional']!=$regional){
				if ($quotamdreg!=0){ $hcmdreg = $actualmdreg/$quotamdreg *100; } else {$hcmdreg=0;}
				if ($quotaspgreg!=0){ $hcspgreg = $actualspgreg/$quotaspgreg *100;}else {$hcspgreg=0;}
				if ($quotasfmtreg!=0){ $hcsfmtreg = $actualsfmtreg/$quotasfmtreg *100;}else {$hcsfmtreg=0;}
				if ($quotasfgtreg!=0){ $hcsfgtreg = $actualsfgtreg/$quotasfgtreg *100;}else {$hcsfgtreg=0;}
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"></td>';
				$html .= '<td style="white-space: nowrap;">%</td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_hc_detail_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$regional=$vreg['nama_regional'];
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_hc_detail_regional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
					
							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}

				</script>
				';
				
		echo $html;

	}

	function open_att_detail_regional() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if (intval(date("Ym")) == intval($tahun.$bulan)){
			if ($tanggal=='All'){
				$periodedate= date("Y-m-d",strtotime($tahun.'-'.$bulan.'-'.date("d")));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}else{
			if ($tanggal=='All'){
				$periode= $tahun.'-'.$bulan.'-01';
				$periodedate= date("Y-m-t",strtotime($periode));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}
		
		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												)";
            $strqueryarea1 = " and c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
												)";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H' and c.subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and c.subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')";
				$strpengali = "";
			}												
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and b.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu areaid in (select distinct b.areaid from  
											app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
											where a.username='".$usersession."')
												)";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H' and d.areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."'
				)";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and d.areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."'
				)";
				$strpengali = "";
			}
												
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
			$strquery = " and salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$usersession."')
												) ";
            $strqueryarea1 = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
												) ";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H' and e.regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."'
				) ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and e.regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."'
				)";
				$strpengali = "";
			}
		}
		else {
			$strqueryarea ="";
			$strquery = "";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H'";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H'";
				$strpengali = "";
			}
		}

		$qattnat = $this->db->query(" 
									select 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid$strquery) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid$strquery) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid$strquery) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid$strquery) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1$strqueryarea
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area 
									) x 
									union all
									select 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on d.areaid=b.areaid
									left join m_area_regional e on e.regionalid=b.regionalid
									$strqueryarea1
									) x 
									order by item asc
									;
								");
		$datanat = $qattnat->result_array();
		//echo $this->db->last_query();
		
		$qattreg = $this->db->query(" 
									select x.regionalid, x.nama_regional, 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid$strquery) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid$strquery) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid$strquery) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid$strquery) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1$strqueryarea
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area 
									) x group by x.regionalid, x.nama_regional
									union all
									select x.regionalid, x.nama_regional, 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on d.areaid=b.areaid
									left join m_area_regional e on e.regionalid=b.regionalid
									$strqueryarea1
									) x group by x.regionalid, x.nama_regional
									order by regionalid asc, item asc
									;
								");
		$datarg = $qattreg->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Attendance By Region</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2"></th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datanat as $vnat) {
			if ($vnat['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">National</td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vnat['jmlmd'];
					$quotaspgreg=$vnat['jmlspg'];
					$quotasfmtreg=$vnat['jmlsfmt'];
					$quotasfgtreg=$vnat['jmlsfgt'];

				}else if ($vnat['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vnat['jmlmd'];
					$actualspgreg=$vnat['jmlspg'];
					$actualsfmtreg=$vnat['jmlsfmt'];
					$actualsfgtreg=$vnat['jmlsfgt'];
				}
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($regional!=''){
				if ($vreg['nama_regional']!=$regional){
				if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
				if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
				if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
				if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"></td>';
				$html .= '<td style="white-space: nowrap;">%</td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_att_perregional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$regional=$vreg['nama_regional'];
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_att_perregional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;

							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}

				</script>
				';
				
		echo $html;

	}

	function open_preview_act_call_regional() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}
		
		if ($restrict_level=='4'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";

			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else if ($restrict_level=='3'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else if ($restrict_level=='2'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and e.regionalid in (select distinct b.regionalid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and e.regionalid in (select distinct b.regionalid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else {
			$strqueryarea ="";
			$strquery = "";
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode' ";
			}
		}

		$qpfgffnat = $this->db->query(" select * from (
			select 'PJP' header, '1' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
				sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 $strqueryprodgff
			union all
			select 'Call On PJP' header, '2' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
				sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 $strqueryprodgff
			union all
			select 'EXT_CALL' header, '3' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
				sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 $strqueryprodgff
			union all
			select 'Total Call' header, '4' as _order,
				sum(y._call_MD) _call_MD,
				sum(y._call_SPG) _call_SPG,
				sum(y._call_SLS_MT) _call_SLS_MT,
				sum(y._call_SLS_GT) _call_SLS_GT
			from 
				(
					select e.regionalid,e.nama_regional,'Call On PJP' header,
						sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
						sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 $strqueryprodgff
					group by e.regionalid,e.nama_regional
					union all
					select e.regionalid,e.nama_regional,'EXT_CALL' header,
							sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
						sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 $strqueryprodgff
					group by e.regionalid,e.nama_regional											
				) y 
			) x
			order by x._order asc
		");
		//echo $this->db->last_query();
		$datanat = $qpfgffnat->result_array();

		##query get performance TPE regional
		$qpfgffreg = $this->db->query(" select * from (
										select e.regionalid,e.nama_regional,'PJP' header, '1' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
											sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 $strqueryprodgff
										group by e.regionalid,e.nama_regional
										union all
										select e.regionalid,e.nama_regional,'Call On PJP' header, '2' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
											sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 $strqueryprodgff
										group by e.regionalid,e.nama_regional
										union all
										select e.regionalid,e.nama_regional,'EXT_CALL' header, '3' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
											sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 $strqueryprodgff
										group by e.regionalid,e.nama_regional
										union all
										select y.regionalid,y.nama_regional,'Total Call' header, '4' as _order,
											sum(y._call_MD) _call_MD,
											sum(y._call_SPG) _call_SPG,
											sum(y._call_SLS_MT) _call_SLS_MT,
											sum(y._call_SLS_GT) _call_SLS_GT
										from 
											(
												select e.regionalid,e.nama_regional,'Call On PJP' header,
													sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
													sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 $strqueryprodgff
												group by e.regionalid,e.nama_regional
												union all
												select e.regionalid,e.nama_regional,'EXT_CALL' header,
														sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
													sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 $strqueryprodgff
												group by e.regionalid,e.nama_regional											
											) y group by y.regionalid,y.nama_regional
										) x
										order by x.regionalid asc, x._order asc
									");
		//echo $this->db->last_query();
		$dataprfgff = $qpfgffreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Actual Call By Region</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2"></th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datanat as $vnat) {
			if ($vnat['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">National &nbsp;<button id="btn-savexls-pjp" onclick="download_detail_actual_call_national_xls();" type="button" class="btn btn-success fa fa-download btn-xs"></button></td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpmdreg=$vnat['md'];
					$pjpspgreg=$vnat['spg'];
					$pjpsfmtreg=$vnat['sls_mt'];
					$pjpsfgtreg=$vnat['sls_gt'];

				}else if ($vnat['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callmdreg=$vnat['md'];
					$callspgreg=$vnat['spg'];
					$callsfmtreg=$vnat['sls_mt'];
					$callsfgtreg=$vnat['sls_gt'];
				}else if ($vnat['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vnat['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vnat['md'];
					$totcallspgreg=$vnat['spg'];
					$totcallsfmtreg=$vnat['sls_mt'];
					$totcallsfgtreg=$vnat['sls_gt'];
				}
			}

			if (@$pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if (@$pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if (@$pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if (@$pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if (@$pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if (@$pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if (@$pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if (@$pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';		
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($dataprfgff as $vreg) {
			if ($regional!=''){
				if ($vreg['nama_regional']!=$regional){

					if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
					if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';

					if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
					if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Total Call</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';				
				}
			}
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_act_call_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpmdreg=$vreg['md'];
					$pjpspgreg=$vreg['spg'];
					$pjpsfmtreg=$vreg['sls_mt'];
					$pjpsfgtreg=$vreg['sls_gt'];

				}else if ($vreg['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callmdreg=$vreg['md'];
					$callspgreg=$vreg['spg'];
					$callsfmtreg=$vreg['sls_mt'];
					$callsfgtreg=$vreg['sls_gt'];
				}else if ($vreg['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vreg['md'];
					$totcallspgreg=$vreg['spg'];
					$totcallsfmtreg=$vreg['sls_mt'];
					$totcallsfgtreg=$vreg['sls_gt'];
				}
				$regional=$vreg['nama_regional'];
			}

			if (@$pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if (@$pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if (@$pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if (@$pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if (@$pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if (@$pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if (@$pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if (@$pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");
					let tahun = uiSelectTahun.val();
					let bulan = uiSelectBulan.val();
					let tanggal = uiSelectTanggal.val();
					let usersession = paramsession.username;
					let restrict_level = paramsession.restrict_level;
					let restrict_bu = paramsession.restrict_bu;

					function open_preview_act_call_regional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;

							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}
						
						function download_detail_actual_call_national_xls() {
							common.loading();
							common.direct("drc_dashboard/save_detail_actual_call_national_xls/"+tahun+"/"+bulan+"/"+tanggal+"/"+usersession+"/"+restrict_level+"/"+restrict_bu);
							common.loadingClose();
						}


				</script>
				';
				
		echo $html;

	}

	function open_hc_detail_regional_area() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");

		if ($restrict_level=='4'){
            $strqueryarea = " and c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and b.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else {
			$strqueryarea ="";
		}

		$qhchead = $this->db->query(" 
									select a.regionalid,a.nama_regional, 'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
									where a.regionalid=$regionalid $strqueryarea
									group by a.regionalid,a.nama_regional
									union all
									select x.regionalid, x.nama_regional,'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea,
									(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,  
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where a.regionalid=$regionalid $strqueryarea
									group by a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									) x group by x.regionalid, x.nama_regional
									order by regionalid asc, item desc;
								");
		$datahead = $qhchead->result_array();

		$qhcreg = $this->db->query(" 
									select a.regionalid,a.nama_regional, b.areaid, b.nama_area, 'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
									where a.regionalid=$regionalid $strqueryarea
									group by a.regionalid,a.nama_regional, b.areaid, b.nama_area
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area,'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea,
									(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,  
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where a.regionalid=$regionalid $strqueryarea
									group by a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area
									order by regionalid asc, areaid asc, item desc;
								");
		$dataarea = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Fulfillment By Area</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_hc_regional();" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">'.$nama_regional.'</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($datahead as $vreg) {
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_regional'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataarea as $vreg) {
			if ($area!=''){
				if ($vreg['nama_area']!=$area){
				if ($quotamdreg!=0){ $hcmdreg = $actualmdreg/$quotamdreg *100; } else {$hcmdreg=0;}
				if ($quotaspgreg!=0){ $hcspgreg = $actualspgreg/$quotaspgreg *100;}else {$hcspgreg=0;}
				if ($quotasfmtreg!=0){ $hcsfmtreg = $actualsfmtreg/$quotasfmtreg *100;}else {$hcsfmtreg=0;}
				if ($quotasfgtreg!=0){ $hcsfgtreg = $actualsfgtreg/$quotasfgtreg *100;}else {$hcsfgtreg=0;}
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"></td>';
				$html .= '<td style="white-space: nowrap;">%</td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_hc_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$area=$vreg['nama_area'];
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_hc_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
					
							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}
		
					function open_preview_hc_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

				</script>
				';
				
		echo $html;

	}

	function open_att_detail_regional_area() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;

		if (intval(date("Ym")) == intval($tahun.$bulan)){
			if ($tanggal=='All'){
				$periodedate= date("Y-m-d",strtotime($tahun.'-'.$bulan.'-'.date("d")));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}else{
			if ($tanggal=='All'){
				$periode= $tahun.'-'.$bulan.'-01';
				$periodedate= date("Y-m-t",strtotime($periode));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		
		if ($restrict_level=='4'){
            $strqueryarea = " and c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01')
				and e.regionalid = $regionalid and a.status='H' and c.subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid 
				and c.subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')";
				$strpengali = "";
			}												
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and b.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H'
				and e.regionalid = $regionalid and d.areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."'
				)";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid 
				and d.areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."'
				)";
				$strpengali = "";
			}
												
		}
		else {
			$strqueryarea ="";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and a.status='H'
				and e.regionalid = $regionalid ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid ";
				$strpengali = "";
			}
		}

		$qattheader = $this->db->query(" 
									select x.regionalid, x.nama_regional, 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1 and a.regionalid = $regionalid $strqueryarea
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area 
									) x group by x.regionalid, x.nama_regional
									union all
									select x.regionalid, x.nama_regional, 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional, d.areaid, d.nama_area,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strqueryarea1
									) x group by x.regionalid, x.nama_regional
									order by regionalid asc, item asc
									;
								");
		$dataheader = $qattheader->result_array();

		$qattreg = $this->db->query(" 
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1 and a.regionalid = $regionalid $strqueryarea
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area 
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional, d.areaid, d.nama_area,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strqueryarea1
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area
									order by regionalid asc, areaid asc, item asc
									;
								");
		$datarg = $qattreg->result_array();
		//echo $this->db->last_query();
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Attendance By Area</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_att_regional();" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataheader as $vreg) {
			if ($vreg['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_regional'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($datarg as $vreg) {
			if ($area!=''){
				if ($vreg['nama_area']!=$area){
				if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
				if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
				if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
				if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"></td>';
				$html .= '<td style="white-space: nowrap;">%</td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="attnational" onclick="open_preview_att_perregional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$area=$vreg['nama_area'];
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_att_perregional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}


					function open_preview_national() {
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var tanggal = uiSelectTanggal.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
			
						if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
						{
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;			
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});	
						}
					}

					function open_preview_att_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}
				</script>';
				
		echo $html;

	}
	
	function open_preview_act_call_regional_area() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");

		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";

			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		}
		else if ($restrict_level=='3'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		} else {
			$strqueryarea ="";
			$strquery = "";
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode' ";
			}
		}

		$qheader = $this->db->query(" select * from (
			select e.regionalid,e.nama_regional,'PJP' header, '1' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
				sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
			group by e.regionalid,e.nama_regional
			union all
			select e.regionalid,e.nama_regional,'Call On PJP' header, '2' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
				sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
			group by e.regionalid,e.nama_regional
			union all
			select e.regionalid,e.nama_regional,'EXT_CALL' header, '3' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
				sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
			group by e.regionalid,e.nama_regional
			union all
			select y.regionalid,y.nama_regional,'Total Call' header, '4' as _order,
				sum(y._call_MD) _call_MD,
				sum(y._call_SPG) _call_SPG,
				sum(y._call_SLS_MT) _call_SLS_MT,
				sum(y._call_SLS_GT) _call_SLS_GT
			from 
				(
					select e.regionalid,e.nama_regional,'Call On PJP' header,
						sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
						sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
					group by e.regionalid,e.nama_regional
					union all
					select e.regionalid,e.nama_regional,'EXT_CALL' header,
							sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
						sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
					group by e.regionalid,e.nama_regional
				) y group by y.regionalid,y.nama_regional
			) x
			order by x.regionalid asc, x._order asc
		");
		//echo $this->db->last_query();
		$dataheader = $qheader->result_array();		
		##query get performance TPE regional
		$qpfgffreg = $this->db->query(" select * from (
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'PJP' header, '1' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
											sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
										union all
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'Call On PJP' header, '2' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
											sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
										union all
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'EXT_CALL' header, '3' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
											sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
										union all
										select y.regionalid,y.nama_regional,y.areaid,y.nama_area,'Total Call' header, '4' as _order,
											sum(y._call_MD) _call_MD,
											sum(y._call_SPG) _call_SPG,
											sum(y._call_SLS_MT) _call_SLS_MT,
											sum(y._call_SLS_GT) _call_SLS_GT
										from 
											(
												select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'Call On PJP' header,
													sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
													sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
												group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
												union all
												select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'EXT_CALL' header,
														sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
													sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 and e.regionalid=$regionalid $strqueryprodgff
												group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
											) y group by y.regionalid,y.nama_regional,y.areaid,y.nama_area
										) x
										order by x.regionalid asc, x.areaid asc, x._order asc
									");
		//echo $this->db->last_query();
		$dataprfgff = $qpfgffreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Actual Call By Area</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_act_call_regional();" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($dataheader as $vreg) {
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_regional'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpspgreg=$vreg['spg'];
					$pjpmdreg=$vreg['md'];
					$pjpsfmtreg=$vreg['sls_mt'];
					$pjpsfgtreg=$vreg['sls_gt'];

				}else if ($vreg['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callspgreg=$vreg['spg'];
					$callmdreg=$vreg['md'];
					$callsfmtreg=$vreg['sls_mt'];
					$callsfgtreg=$vreg['sls_gt'];
				}else if ($vreg['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vreg['md'];
					$totcallspgreg=$vreg['spg'];
					$totcallsfmtreg=$vreg['sls_mt'];
					$totcallsfgtreg=$vreg['sls_gt'];
				}
			}

			if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($dataprfgff as $vreg) {
			if ($regional!=''){
				if ($vreg['nama_area']!=$regional){
					if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
					if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';

					if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
					if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Total Call</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';				
				}
			}
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_act_call_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpspgreg=$vreg['spg'];
					$pjpmdreg=$vreg['md'];
					$pjpsfmtreg=$vreg['sls_mt'];
					$pjpsfgtreg=$vreg['sls_gt'];

				}else if ($vreg['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callspgreg=$vreg['spg'];
					$callmdreg=$vreg['md'];
					$callsfmtreg=$vreg['sls_mt'];
					$callsfgtreg=$vreg['sls_gt'];
				}else if ($vreg['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vreg['md'];
					$totcallspgreg=$vreg['spg'];
					$totcallsfmtreg=$vreg['sls_mt'];
					$totcallsfgtreg=$vreg['sls_gt'];
				}				
				$regional=$vreg['nama_area'];
			}

			if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_act_call_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;

							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}

						function open_preview_act_call_regional() {
					
							var tahun = uiSelectTahun.val();
							var bulan = uiSelectBulan.val();
							var tanggal = uiSelectTanggal.val();
							var idjabatan = paramsession.idjabatan;
							var usersession = paramsession.username;
							var restrict_level = paramsession.restrict_level;
							var restrict_bu = paramsession.restrict_bu;
									
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_preview_act_call_regional"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
									success:function(res){
										response = res;
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});
						}

				</script>
				';
				
		echo $html;

	}

	function open_hc_detail_regional_area_city() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		$areaid=$this->input->post("areaid");
		$nama_area=$this->input->post("nama_area");

		if ($restrict_level=='4'){
            $strqueryarea = " and c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		} else {
			$strqueryarea ="";
		}

		$qheader = $this->db->query(" 
									select a.regionalid,a.nama_regional, b.areaid, b.nama_area, 'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
									where a.regionalid=$regionalid and b.areaid=$areaid $strqueryarea
									group by a.regionalid,a.nama_regional, b.areaid, b.nama_area
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid, a.nama_regional, b.areaid, b.nama_area,
									(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,  
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where a.regionalid=$regionalid and b.areaid=$areaid $strqueryarea
									group by a.regionalid, a.nama_regional, b.areaid, b.nama_area
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area
									order by regionalid asc, areaid asc, item desc;
								");
		$dataheader = $qheader->result_array();

		$qhcreg = $this->db->query(" 
									select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea, 'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
									where a.regionalid=$regionalid and b.areaid=$areaid $strqueryarea
									group by a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.subarea ,'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea,
									(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,  
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where a.regionalid=$regionalid and b.areaid=$areaid $strqueryarea
									group by a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.subarea
									order by regionalid asc, areaid asc, subareaid asc, item desc;
								");
		$dataarea = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Fulfillment By City</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_hc_detail_regional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataheader as $vreg) {
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_area'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataarea as $vreg) {
			if ($area!=''){
				if ($vreg['subarea']!=$area){
				if ($quotamdreg!=0){ $hcmdreg = $actualmdreg/$quotamdreg *100; } else {$hcmdreg=0;}
				if ($quotaspgreg!=0){ $hcspgreg = $actualspgreg/$quotaspgreg *100;}else {$hcspgreg=0;}
				if ($quotasfmtreg!=0){ $hcsfmtreg = $actualsfmtreg/$quotasfmtreg *100;}else {$hcsfmtreg=0;}
				if ($quotasfgtreg!=0){ $hcsfgtreg = $actualsfgtreg/$quotasfgtreg *100;}else {$hcsfgtreg=0;}
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"></td>';
				$html .= '<td style="white-space: nowrap;">%</td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_hc_detail_regional_area_city_gff('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['subarea'].'\');">'.$vreg['subarea'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$area=$vreg['subarea'];
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .= '<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_hc_detail_regional_area_city_gff(regionalid,nama_regional,areaid,nama_area,subareaid,city) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional_area_city_gff"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&subareaid="+subareaid+"&city="+city+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var tanggal = uiSelectTanggal.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
			
						if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
						{
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;			
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});	
						}
					}

					function open_hc_detail_regional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

				</script>';
		
				
		echo $html;

	}

	function open_att_detail_regional_area_city() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		$areaid=$this->input->post("areaid");
		$nama_area=$this->input->post("nama_area");

		if (intval(date("Ym")) == intval($tahun.$bulan)){
			if ($tanggal=='All'){
				$periodedate= date("Y-m-d",strtotime($tahun.'-'.$bulan.'-'.date("d")));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}else{
			if ($tanggal=='All'){
				$periode= $tahun.'-'.$bulan.'-01';
				$periodedate= date("Y-m-t",strtotime($periode));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}
		
		if ($restrict_level=='4'){
            $strqueryarea = " and c.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and d.areaid=$areaid
				and e.regionalid = $regionalid and a.status='H' and c.subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid and d.areaid=$areaid
				and c.subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')";
				$strpengali = "";
			}												
		}
		else {
			$strqueryarea ="";
			if ($tanggal=='All'){
				$strqueryarea1 = " where a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and d.areaid=$areaid
				and e.regionalid = $regionalid and a.status='H'";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
			}else{
				$strqueryarea1 = " where a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid and d.areaid=$areaid";
				$strpengali = "";
			}												
		}

		$qheader = $this->db->query(" 
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, 
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and areaid=c.areaid) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and areaid=c.areaid) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and areaid=c.areaid) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and areaid=c.areaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1 and a.regionalid=$regionalid and b.areaid=$areaid $strqueryarea
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional, d.areaid, d.nama_area,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strqueryarea1
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area
									order by regionalid asc, areaid asc, item asc
									;
									");
		$dataheader = $qheader->result_array();

		$qattreg = $this->db->query(" 
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city, 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1 and a.regionalid=$regionalid and b.areaid=$areaid $strqueryarea
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area 
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city, 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area city,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strqueryarea1
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city
									order by regionalid asc, areaid asc, subareaid asc, item asc
									;								
									");
		$datarg = $qattreg->result_array();
		//echo $this->db->last_query();
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Attendance By City</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_att_perregional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataheader as $vreg) {
			if ($vreg['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_area'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$area=$vreg['city'];
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($datarg as $vreg) {
			if ($area!=''){
				if ($vreg['city']!=$area){
				if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
				if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
				if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
				if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;"></td>';
				$html .= '<td style="white-space: nowrap;">%</td>';
				if ($restrict_bu=='GT'){
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="attnational" onclick="open_att_detail_regional_area_city_gff('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['city'].'\');">'.$vreg['city'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$area=$vreg['city'];
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
				
		$html .= '<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_att_detail_regional_area_city_gff(regionalid,nama_regional,areaid,nama_area,subareaid,city) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional_area_city_gff"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&subareaid="+subareaid+"&city="+city+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var tanggal = uiSelectTanggal.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
			
						if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
						{
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;			
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});	
						}
					}

					function open_preview_att_perregional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
					
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}
				</script>';
		
		echo $html;

	}

	function open_preview_act_call_regional_area_city() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		$areaid=$this->input->post("areaid");
		$nama_area=$this->input->post("nama_area");

		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}
		
		if ($restrict_level=='4'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";

			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		} else {
			$strqueryarea ="";
			$strquery = "";
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode' ";
			}
		}
		
		$qheader = $this->db->query(" select * from (
			select e.regionalid,e.nama_regional,d.areaid,d.nama_area, 'PJP' header, '1' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
				sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
			group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
			union all
			select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'Call On PJP' header, '2' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
				sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
			group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
			union all
			select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'EXT_CALL' header, '3' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
				sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
			group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
			union all
			select y.regionalid,y.nama_regional,y.areaid,y.nama_area,'Total Call' header, '4' as _order,
				sum(y._call_MD) _call_MD,
				sum(y._call_SPG) _call_SPG,
				sum(y._call_SLS_MT) _call_SLS_MT,
				sum(y._call_SLS_GT) _call_SLS_GT
			from 
				(
					select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'Call On PJP' header,
						sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
						sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
					group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
					union all
					select e.regionalid,e.nama_regional,d.areaid,d.nama_area,'EXT_CALL' header,
							sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
						sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
					group by e.regionalid,e.nama_regional,d.areaid,d.nama_area
				) y group by y.regionalid,y.nama_regional,y.areaid,y.nama_area
			) x
			order by x.regionalid asc, x.areaid asc, x._order asc
		");
		//echo $this->db->last_query();
		$dataheader = $qheader->result_array();

		##query get performance TPE regional
		$qpfgffreg = $this->db->query(" select * from (
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city, 'PJP' header, '1' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
											sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
											sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
										union all
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'Call On PJP' header, '2' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
											sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
										union all
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'EXT_CALL' header, '3' as _order,
												sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
											sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
											sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
										union all
										select y.regionalid,y.nama_regional,y.areaid,y.nama_area,y.subareaid,y.city,'Total Call' header, '4' as _order,
											sum(y._call_MD) _call_MD,
											sum(y._call_SPG) _call_SPG,
											sum(y._call_SLS_MT) _call_SLS_MT,
											sum(y._call_SLS_GT) _call_SLS_GT
										from 
											(
												select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'Call On PJP' header,
													sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
													sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
												group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
												union all
												select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'EXT_CALL' header,
														sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
													sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 and b.tipe_sales not in ('ADMIN','FC') and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
												group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
											) y group by y.regionalid,y.nama_regional,y.areaid,y.nama_area,y.subareaid,y.city 
										) x
										order by x.regionalid asc, x.areaid asc, x.subareaid asc, x._order asc
									");
		//echo $this->db->last_query();
		$dataprfgff = $qpfgffreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Actual Call By City</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_act_call_regional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($dataheader as $vreg) {
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpspgreg=$vreg['spg'];
					$pjpmdreg=$vreg['md'];
					$pjpsfmtreg=$vreg['sls_mt'];
					$pjpsfgtreg=$vreg['sls_gt'];

				}else if ($vreg['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callspgreg=$vreg['spg'];
					$callmdreg=$vreg['md'];
					$callsfmtreg=$vreg['sls_mt'];
					$callsfgtreg=$vreg['sls_gt'];
				}else if ($vreg['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vreg['md'];
					$totcallspgreg=$vreg['spg'];
					$totcallsfmtreg=$vreg['sls_mt'];
					$totcallsfgtreg=$vreg['sls_gt'];
				}				
				$regional=$vreg['city'];
			}

			if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($dataprfgff as $vreg) {
			if ($regional!=''){
				if ($vreg['city']!=$regional){
					if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
					if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';

					if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
					if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Total Call</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';				
				}
			}
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_act_call_regional_area_city_gff('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['city'].'\');">'.$vreg['city'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpspgreg=$vreg['spg'];
					$pjpmdreg=$vreg['md'];
					$pjpsfmtreg=$vreg['sls_mt'];
					$pjpsfgtreg=$vreg['sls_gt'];

				}else if ($vreg['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callspgreg=$vreg['spg'];
					$callmdreg=$vreg['md'];
					$callsfmtreg=$vreg['sls_mt'];
					$callsfgtreg=$vreg['sls_gt'];
				}else if ($vreg['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vreg['md'];
					$totcallspgreg=$vreg['spg'];
					$totcallsfmtreg=$vreg['sls_mt'];
					$totcallsfgtreg=$vreg['sls_gt'];
				}				
				$regional=$vreg['city'];
		}

			if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_act_call_regional_area_city_gff(regionalid,nama_regional,areaid,nama_area,subareaid,city) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional_area_city_gff"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&subareaid="+subareaid+"&city="+city+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_act_call_regional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;

							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}

				</script>
				';
				
		echo $html;

	}

	function open_preview_act_call_regional_area_city_gff() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		$areaid=$this->input->post("areaid");
		$nama_area=$this->input->post("nama_area");
		$subareaid=$this->input->post("subareaid");
		$city=$this->input->post("city");

		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}

		if ($restrict_bu=='GT'){
			$querybu = " b.tipe_sales='MEDREP' and";
		}else{
			$querybu = " b.tipe_sales not in ('MEDREP','FC','ADMIN') and";
		}
		
		if ($restrict_level=='4'){
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";

			}else{
				$strqueryprodgff = " and a.periode = '$periode'
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}
		} else {
			$strqueryarea ="";
			$strquery = "";
			if ($tanggal=='All'){
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01')";
			}else{
				$strqueryprodgff = " and a.periode = '$periode' ";
			}
		}
		$qheader = $this->db->query(" select * from (
			select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city, 'PJP' header, '1' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.pjp else 0 end) md,
				sum(case when b.tipe_sales='SPG' then a.pjp else 0 end) spg,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_mt,
				sum(case when b.tipe_sales='MEDREP' then a.pjp else 0 end) sls_gt
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
			group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
			union all
			select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'Call On PJP' header, '2' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
				sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
			group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
			union all
			select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'EXT_CALL' header, '3' as _order,
					sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
				sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
				sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
			from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
			left join m_area_subarea c on b.subareaid=c.subareaid
			left join m_area_areasite d on b.areaid=d.areaid
			left join m_area_regional e on b.regionalid=e.regionalid
			where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
			group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
			union all
			select y.regionalid,y.nama_regional,y.areaid,y.nama_area,y.subareaid,y.city,'Total Call' header, '4' as _order,
				sum(y._call_MD) _call_MD,
				sum(y._call_SPG) _call_SPG,
				sum(y._call_SLS_MT) _call_SLS_MT,
				sum(y._call_SLS_GT) _call_SLS_GT
			from 
				(
					select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'Call On PJP' header,
						sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
						sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
					group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
					union all
					select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'EXT_CALL' header,
							sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
						sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
						sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
					left join m_area_subarea c on b.subareaid=c.subareaid
					left join m_area_areasite d on b.areaid=d.areaid
					left join m_area_regional e on b.regionalid=e.regionalid
					where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
					group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area
				) y group by y.regionalid,y.nama_regional,y.areaid,y.nama_area,y.subareaid,y.city 
			) x
			order by x.regionalid asc, x.areaid asc, x.subareaid asc, x._order asc
		");
		//echo $this->db->last_query();
		$dataheader = $qheader->result_array();


		$qpfgffreg = $this->db->query(" select * from (
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city, b.salesmanid,b.nama_salesman,b.tipe_sales,
													sum(a.pjp) PJP,sum(a.call) CallOnPJP,sum(a.extra_call) EXT_CALL,sum(a.call)+sum(a.extra_call) TOTAL_CALL, 
													round((sum(a.call)/sum(a.pjp)*100),0) '%CallOnPJP',
													round(((sum(a.call)+sum(a.extra_call))/(sum(a.pjp)+sum(a.extra_call))*100),0) '%TotalCall'
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid 
										left join m_area_subarea c on b.subareaid=c.subareaid left join m_area_areasite d on b.areaid=d.areaid 
										left join m_area_regional e on b.regionalid=e.regionalid 
										where $querybu b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area,b.salesmanid,b.nama_salesman,b.tipe_sales
										) x
										order by x.regionalid asc, x.areaid asc, x.subareaid asc, x.tipe_sales asc, x.salesmanid asc
									");
		$dataprfgff = $qpfgffreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Actual Call By TPE City '.$city.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_act_call_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($dataheader as $vreg) {
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['city'].'&nbsp;<button id="btn-savexls" onclick="open_detail_actual_call_city_xls(\''.$subareaid.'\');" type="button" class="btn btn-success fa fa-download btn-xs"></button></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$pjpspgreg=$vreg['spg'];
					$pjpmdreg=$vreg['md'];
					$pjpsfmtreg=$vreg['sls_mt'];
					$pjpsfgtreg=$vreg['sls_gt'];

				}else if ($vreg['header']=='Call On PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$callspgreg=$vreg['spg'];
					$callmdreg=$vreg['md'];
					$callsfmtreg=$vreg['sls_mt'];
					$callsfgtreg=$vreg['sls_gt'];
				}else if ($vreg['header']=='Total Call'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$totcallmdreg=$vreg['md'];
					$totcallspgreg=$vreg['spg'];
					$totcallsfmtreg=$vreg['sls_mt'];
					$totcallsfgtreg=$vreg['sls_gt'];
				}				
				$regional=$vreg['city'];
			}

			if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
			if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

			if ($pjpmdreg!=0){ $totactcallmdreg = @$totcallmdreg/@$pjpmdreg *100; } else {$totactcallmdreg=0;}
			if ($pjpspgreg!=0){ $totactcallspgreg = @$totcallspgreg/@$pjpspgreg *100;}else {$totactcallspgreg=0;}
			if ($pjpsfmtreg!=0){ $totactcallsfmtreg = @$totcallsfmtreg/@$pjpsfmtreg *100;}else {$totactcallsfmtreg=0;}
			if ($pjpsfgtreg!=0){ $totactcallsfgtreg = @$totcallsfgtreg/@$pjpsfgtreg *100;}else {$totactcallsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">% Total Call</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';		
		$html .= '</div>';

		$html .= '<div class="table-responsive col-md-9">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;width:40px;" rowspan=2>No.</th>';
        $html .= '<th style="white-space: nowrap;width:200px;" rowspan=2>Position</th>';
        $html .= '<th style="white-space: nowrap;width:300px;" rowspan=2">User TPE</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">PJP</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">Call On PJP</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">Total Call</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">% Call On PJP</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">% Total Call</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$salesmanid='';
		$i=1;
		foreach ($dataprfgff as $vreg) {
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$i.'</td>';
				$html .= '<td style="white-space: nowrap;">'.$vreg['tipe_sales'].'</td>';
				$html .= '<td style="white-space: nowrap;"><button id="btn-savexls" onclick="open_detail_actual_call_xls(\''.$vreg['salesmanid'].'\');" type="button" class="btn btn-success fa fa-download btn-xs"></button>&nbsp;'.$vreg['salesmanid'].'-'.$vreg['nama_salesman'].'</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['PJP'], 0, '.', ',').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['CallOnPJP'], 0, '.', ',').' </td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['TOTAL_CALL'], 0, '.', ',').' </td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['%CallOnPJP'], 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['%TotalCall'], 0, '.', ',').' %</td>';
				$html .= '</tr>';
				$i++;
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var tanggal = uiSelectTanggal.val();

					function open_preview_act_call_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_preview_act_call_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_preview_national() {
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;

							if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
							{
								$.ajax({
									type:"POST",
									dataType: "html",
									url: common.baseURL("drc_dashboard/open_detail_national"),
									data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
									success:function(res){
										response = res;			
										$(\'#tbl-content\').html(response);
									},
									error:function(){
										alert("Load failed");
									}
								});	
							}
						}

						function open_detail_actual_call_xls(salesmanid) {
							common.loading();
							common.direct("drc_dashboard/save_detail_actual_call_xls/"+salesmanid+"/"+tahun+"/"+bulan+"/"+tanggal);
							common.loadingClose();
						}

						function open_detail_actual_call_city_xls(subareaid) {
							common.loading();
							common.direct("drc_dashboard/save_detail_actual_call_city_xls/"+subareaid+"/"+tahun+"/"+bulan+"/"+tanggal);
							common.loadingClose();
						}
	
				</script>
				';
				
		echo $html;

	}

	function open_att_detail_regional_area_city_gff() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		$areaid=$this->input->post("areaid");
		$nama_area=$this->input->post("nama_area");
		$subareaid=$this->input->post("subareaid");
		$city=$this->input->post("city");

		if (intval(date("Ym")) == intval($tahun.$bulan)){
			if ($tanggal=='All'){
				$periodedate= date("Y-m-d",strtotime($tahun.'-'.$bulan.'-'.date("d")));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}else{
			if ($tanggal=='All'){
				$periode= $tahun.'-'.$bulan.'-01';
				$periodedate= date("Y-m-t",strtotime($periode));
			}else{
				$periode = $tahun.'-'.$bulan.'-'.$tanggal;
				$periodedate=date("Y-m-d",strtotime($periode));
			}
		}

		if ($restrict_bu=='GT'){
			$querybu = " b.tipe_sales='MEDREP' and";
		}else{
			$querybu = " b.tipe_sales<>'MEDREP' and";
		}

		if ($tanggal=='All'){
			$strqueryarea1 = " where $querybu a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') and c.subareaid=$subareaid 
								and d.areaid=$areaid and e.regionalid = $regionalid and a.status='H'";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
		}else{
			$strqueryarea1 = " where $querybu a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid and d.areaid=$areaid and c.subareaid=$subareaid";
			$strpengali = "";
		}												

		$qheader = $this->db->query(" 
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city, 'TPE Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,
									(select if(count(1)>0,count(1)$strpengali,count(1)) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where d.aktif=1 and a.regionalid=$regionalid and b.areaid=$areaid and c.subareaid=$subareaid
									group by  a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area 
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city, 'TPE Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area city,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strqueryarea1
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city
									order by regionalid asc, areaid asc, subareaid asc, item asc
									;								
									");
		$dataheader = $qheader->result_array();

		$qattreg = $this->db->query(" 
									select e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area city,b.salesmanid,b.nama_salesman,b.tipe_sales, 
											date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end) as 'TPE Aktif', 
											count(1) as 'TPE Hadir',
											round((count(1)/(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end)))*100,0) as persentasi
									from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1 left join m_area_subarea c on b.subareaid=c.subareaid 
									left join m_area_areasite d on c.areaid=d.areaid left join m_area_regional e on e.regionalid=d.regionalid 
									$strqueryarea1
									group by e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area, b.salesmanid,b.nama_salesman,b.tipe_sales
									order by regionalid asc, areaid asc, subareaid asc, tipe_sales asc, nama_salesman asc;								
									");
		$datarg = $qattreg->result_array();
		//echo $this->db->last_query();
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Attendance By TPE City '.$city.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_att_perregional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataheader as $vreg) {
			if ($vreg['item']=='TPE Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['city'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='TPE Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
				$area=$vreg['city'];
			}

			if ($quotamdreg!=0){ $attmdreg = @$actualmdreg/@$quotamdreg *100; } else {$attmdreg=0;}
			if ($quotaspgreg!=0){ $attspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$attspgreg=0;}
			if ($quotasfmtreg!=0){ $attsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$attsfmtreg=0;}
			if ($quotasfgtreg!=0){ $attsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$attsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		$html .= '<div class="table-responsive col-md-9"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;width:40px;" rowspan=2>No.</th>';
        $html .= '<th style="white-space: nowrap;width:200px;" rowspan=2>Position</th>';
        $html .= '<th style="white-space: nowrap;width:300px;" rowspan=2">User TPE</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">TPE Aktif</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">TPE Hadir</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">%</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">Cuti</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">Sakit</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($datarg as $vreg) {
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.$i.'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['tipe_sales'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['salesmanid'].'-'.$vreg['nama_salesman'].'
								&nbsp;<button id="btn-savexls" onclick="save_absensi_detail(\''.$vreg['salesmanid'].'\');" type="button" class="btn btn-success fa fa-download btn-xs"></button>
								</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['TPE Aktif'], 0, '.', ',').' </td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['TPE Hadir'], 0, '.', ',').' </td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['persentasi'], 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['cuti'], 0, '.', ',').' </td>';
					$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sakit'], 0, '.', ',').' </td>';
					$html .= '</tr>';
					$i++;
			}

		$html .= '</tbody>';
		$html .= '</table></div><div>';
				
		$html .= '<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var tanggal = uiSelectTanggal.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_preview_national() {
			
						if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
						{
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;			
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});	
						}
					}

					function open_preview_att_perregional_area_city(regionalid,nama_regional,areaid,nama_area) {
									
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_att_detail_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function save_absensi_detail(salesmanid) {
						common.loading();
						common.direct("drc_dashboard/save_absensi_detail/"+salesmanid+"/"+tahun+"/"+bulan+"/"+tanggal);
						common.loadingClose();
					}

				</script>';
		
		echo $html;

	}

	function open_hc_detail_regional_area_city_gff() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid=$this->input->post("regionalid");
		$nama_regional=$this->input->post("nama_regional");
		$areaid=$this->input->post("areaid");
		$nama_area=$this->input->post("nama_area");
		$subareaid=$this->input->post("subareaid");
		$city=$this->input->post("city");

		if ($tanggal=='All'){
			$periodedate=date_create($tahun.'-'.$bulan.'-01');
		}else{
			$periodedate=date_create($periode);
		}

		if ($restrict_bu=='GT'){
			$querybu = " d.tipe_sales='MEDREP' and";
		}else{
			$querybu = " d.tipe_sales<>'MEDREP' and";
		}

		$qheader = $this->db->query(" 
									select a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea, 'Quota' item, sum(d.md) jmlmd, sum(d.spg) jmlspg, sum(d.sfmt) jmlsfmt, sum(d.sfgt) jmlsfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join set_quota_budget d on d.idcity = c.subareaid and d.periode=(select max(periode) from set_quota_budget)
									where a.regionalid=$regionalid and b.areaid=$areaid and c.subareaid=$subareaid
									group by a.regionalid,a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									union all
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.subarea ,'Actual' item,sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area subarea,
									(select count(1) from m_sales_salesman where tipe_sales='MERCHANDISER' and aktif=1 and subareaid=c.subareaid) md,
									(select count(1) from m_sales_salesman where tipe_sales='SPG' and aktif=1 and subareaid=c.subareaid) spg,
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfmt,  
									(select count(1) from m_sales_salesman where tipe_sales='MEDREP' and aktif=1 and subareaid=c.subareaid) sfgt
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where a.regionalid=$regionalid and b.areaid=$areaid and c.subareaid=$subareaid
									group by a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area
									) x group by x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.subarea
									order by regionalid asc, areaid asc, subareaid asc, item desc;
								");
		$dataheader = $qheader->result_array();

		$qattreg = $this->db->query(" 
									select a.regionalid, a.nama_regional, b.areaid, b.nama_area, c.subareaid, c.nama_area city,
											d.salesmanid,d.nama_salesman,d.tipe_sales
									from m_area_regional a left join m_area_areasite b on a.regionalid=b.regionalid
									left join m_area_subarea c on c.areaid=b.areaid
									left join m_sales_salesman d on d.subareaid = c.subareaid
									where $querybu d.aktif='1' and a.regionalid=$regionalid and b.areaid=$areaid and c.subareaid=$subareaid
									order by a.regionalid asc, b.areaid asc, c.subareaid asc, d.tipe_sales asc, d.nama_salesman asc;
									");
		$datarg = $qattreg->result_array();
		//echo $this->db->last_query();
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Fulfillment By City '.$city.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_hc_detail_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date_format($periodedate,"M-Y").'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;">Item</th>';
		if ($restrict_bu=='GT'){
			$html .= '<th style="white-space: nowrap;">SR GT</th>';
		}else{
			$html .= '<th style="white-space: nowrap;"># of SPG</th>';
			$html .= '<th style="white-space: nowrap;"># of MD</th>';
			$html .= '<th style="white-space: nowrap;">SR MT</th>';
		}
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($dataheader as $vreg) {
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['subarea'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='Actual'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$actualmdreg=$vreg['jmlmd'];
					$actualspgreg=$vreg['jmlspg'];
					$actualsfmtreg=$vreg['jmlsfmt'];
					$actualsfgtreg=$vreg['jmlsfgt'];
				}
			}

			if ($quotamdreg!=0){ $hcmdreg = @$actualmdreg/@$quotamdreg *100; } else {$hcmdreg=0;}
			if ($quotaspgreg!=0){ $hcspgreg = @$actualspgreg/@$quotaspgreg *100;}else {$hcspgreg=0;}
			if ($quotasfmtreg!=0){ $hcsfmtreg = @$actualsfmtreg/@$quotasfmtreg *100;}else {$hcsfmtreg=0;}
			if ($quotasfgtreg!=0){ $hcsfgtreg = @$actualsfgtreg/@$quotasfgtreg *100;}else {$hcsfgtreg=0;}
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;"></td>';
			$html .= '<td style="white-space: nowrap;">%</td>';
			if ($restrict_bu=='GT'){
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
			}
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
        $html .= '<tr><th style="white-space: nowrap;;" colspan="3">'.date_format($periodedate,"M-Y").'</th></tr>';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;width:40px;" >No.</th>';
        $html .= '<th style="white-space: nowrap;width:100px;" >Position</th>';
        $html .= '<th style="white-space: nowrap;width:300px;" ">User TPE</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($datarg as $vreg) {
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.$i.'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['tipe_sales'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['salesmanid'].'-'.$vreg['nama_salesman'].'</td>';
					$html .= '</tr>';
					$i++;
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
				
		$html .= '<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id");
					let uiSelectBulan = $("#bln-id");
					let uiSelectTanggal = $("#tgl-id");
					let paramsession = common.getCookie("session");

					function open_preview_national() {
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var tanggal = uiSelectTanggal.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
			
						if (uiSelectBulan.val()!=\'All\' || uiSelectTanggal.val()!=\'All\')
						{
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;			
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});	
						}
					}

					function open_hc_detail_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var tanggal = uiSelectTanggal.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_hc_detail_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

				</script>';
		
		echo $html;

	}

	function load_data_city() {
	
		$periode = $this->input->post("start");	
		//$until = $this->input->post("end");
        $idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");

        /*Header*/
		$html ='<script>
				function isNumber(evt) {
					var iKeyCode = (evt.which) ? evt.which : evt.keyCode
					if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
						return false;
			
					return true;
				}  
				</script>
				<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:600px;
					overflow:auto;  
					margin-top:20px;
				}

				#table-wrapper table {
					width:100%;
				}

				#table-wrapper table thead th .text {
					position:absolute;   
					top:-20px;
					z-index:2;
					height:20px;
					width:35%;
					border:1px solid;
				}
				</style>';
		$html .= '<div id="table-wrapper"><div id="table-scroll"><table id="activity_table" border="1" class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th style="text-align:center;white-space:nowrap;width:25px;">No.</th>';
		$html .='<th style="text-align:left;white-space:nowrap;width:100px;">City</th>';
		$html .='<th style="text-align:left;white-space:nowrap;width:100px;">SPG</th>';
        $html .='<th style="text-align:left;white-space:nowrap;width:100px;">MERCHANDISER</th>';
        $html .='<th style="text-align:left;white-space:nowrap;width:100px;">MEDREP</th>';
		$html .='<th style="text-align:left;white-space:nowrap;width:100px;">MEDREP</th>';
		$html .='<th style="text-align:left;white-space:nowrap;width:100px;">FC</th>';

		$start = date_create($periode);
		//$end = date_create($until);
		$html .= '</tr></thead>';
        $html .= '<tbody>';

		/*Close Header*/
		$get_city = $this->set_quota_budget->get_city();
        $no = 1;

		foreach ($get_city as $v_city) {
			
			/*Get Detail City*/
			$html .='<tr><td style="text-align:center;white-space:nowrap;">'.$no.'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_city['city'].'<input id="listcity" type="hidden" name="listcity[]"  value="'.$v_city['subareaid'].'" /><input id="listcitynm" type="hidden" name="listcitynm[]"  value="'.$v_city['city'].'" /></td>';
			$html .='<td style="text-align:left;white-space:nowrap;"><input id="listspg" type="text" name="listspg[]"  value="'.$v_city['spg'].'" autocomplete="off" onkeypress="javascript:return isNumber(event)" /></td>';
			$html .='<td style="text-align:left;white-space:nowrap;"><input id="listmd" type="text" name="listmd[]"  value="'.$v_city['md'].'" autocomplete="off" onkeypress="javascript:return isNumber(event)" /></td>';
			$html .='<td style="text-align:left;white-space:nowrap;"><input id="listsfmt" type="text" name="listsfmt[]"  value="'.$v_city['sfmt'].'" autocomplete="off" onkeypress="javascript:return isNumber(event)" /></td>';
			$html .='<td style="text-align:left;white-space:nowrap;"><input id="listsfgt" type="text" name="listsfgt[]"  value="'.$v_city['sfgt'].'" autocomplete="off" onkeypress="javascript:return isNumber(event)" /></td>';
			$html .='<td style="text-align:left;white-space:nowrap;"><input id="listfc" type="text" name="listfc[]"  value="'.$v_city['fc'].'" autocomplete="off" onkeypress="javascript:return isNumber(event)" /></td>';
			/******************/
			$no++;
		}
			$html .='</tr>';
		
		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .= '<div class="box-footer"><button type="submit" class="btn btn-primary">Save</button></div>';

		
		echo $html;
	}

	function open_detail_sos_national() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and d.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and e.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and f.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qexecsosperiod = $this->db->query("replace into rekap_sos_detail (tahun, bulan, periode, salesmanid, customerid, customerid_m, type_sos, image, 
										qty_sos_gsk, qty_sos_competitor, sos, datecreate, created_by, created_date, modified_by, modified_date, transaction_id)
										select date_format(a.periode,'%Y'), date_format(a.periode,'%m'), a.periode, a.salesmanid, a.customerid, a.customerid_m, 
										a.type_sos, a.image, a.qty_sos_gsk, a.qty_sos_competitor, a.sos, a.datecreate, a.created_by, a.created_date, a.modified_by,
										a.modified_date, a.transaction_id
										from t_activity_sos a 
										where a.periode = (select tanggal from m_setup_site)
										;");

		$qsosnat = $this->db->query(" 
										select case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, (sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
										$strqueryarea
										group by case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end
										order by item desc
										;
								");
		$datanat = $qsosnat->result_array();

		$qhcreg = $this->db->query(" 
									select f.regionalid, f.nama_regional, case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
									sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, (sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
									from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
									left join m_sales_salesman c on a.salesmanid=c.salesmanid
									left join m_area_subarea d on b.subareaid=d.subareaid
									left join m_area_areasite e on e.areaid=d.areaid
									left join m_area_regional f on f.regionalid=e.regionalid
									where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
									$strqueryarea
									group by f.regionalid, f.nama_regional, case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end
									order by f.regionalid asc, item desc
									;");
		$datarg = $qhcreg->result_array();

		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS</h3>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2"></th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="2">SOS</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">% SOS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datanat as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">National</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=@$vreg['nama_regional'];
			}


		$html .= '</tbody>';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="2">SOS</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">% SOS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_sos_detail_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=$vreg['nama_regional'];
			}


		$html .= '</tbody>';
		$html .= '</table></div></div>';
		
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-sos");
					let uiSelectBulan = $("#bln-id-sos");
					let uiSelectClassSos = $("#classid-id-sos");   
					let paramsession = common.getCookie("session");

					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassSos.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
				
					function open_sos_detail_regional_area(regionalid,nama_regional) {
						common.loadingClose();
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("drc_dashboard/open_detail_sos_regional_area"),
							data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);
								common.loadingClose();
							},
							error:function(){
								alert("Load failed");
								common.loadingClose();
							}
						});
				}



				</script>
				';
				
		echo $html;

	}

	function open_detail_sos_regional() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and d.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and e.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and f.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qhcreg = $this->db->query(" 
										select f.regionalid, f.nama_regional, case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, (sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
										$strqueryarea
										group by f.regionalid, f.nama_regional, case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end
										order by f.regionalid asc, item desc
										;
								");
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_sos_detail_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="2">SOS</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">% SOS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_sos_detail_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=$vreg['nama_regional'];
			}


		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-sos");
					let uiSelectBulan = $("#bln-id-sos");
					let uiSelectClassSos = $("#classid-id-sos");   
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassSos.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
			
					function open_sos_detail_national() {
							common.loadingClose();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_sos_detail_regional_area(regionalid,nama_regional) {
							common.loadingClose();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_detail_sos_regional_area() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");

		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and d.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and e.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and f.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}


		$qhcreg = $this->db->query(" 
										select f.regionalid, f.nama_regional, e.areaid, e.nama_area,
										case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, (sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
												and f.regionalid='$regionalid' $strqueryarea
										group by f.regionalid, f.nama_regional, e.areaid, e.nama_area, 
												case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end
										order by e.areaid asc, item desc
										;
								");
		//echo $this->db->last_query();
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS Regional '.$nama_regional.'</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_sos_detail_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="2">SOS</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">% SOS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_sos_detail_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=$vreg['nama_regional'];
			}


		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-sos");
					let uiSelectBulan = $("#bln-id-sos");
					let uiSelectClassSos = $("#classid-id-sos");   
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassSos.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
				
					function open_sos_detail_national() {
						common.loadingClose();
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("drc_dashboard/open_detail_sos_national"),
							data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);
								common.loadingClose();
							},
							error:function(){
								alert("Load failed");
								common.loadingClose();
							}
						});
					}

				function open_sos_detail_regional() {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional"),
								data : "classid="+classid+"&tahun="+tahun+"&bulan="+bulan+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_sos_detail_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional_area_city"),
								data : "areaid="+areaid+"&nama_area="+nama_area+"&regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_detail_sos_regional_area_city() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		$areaid = $this->input->post("areaid");
		$nama_area = $this->input->post("nama_area");

		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and d.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and e.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and f.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}


		$qhcreg = $this->db->query(" 
										select f.regionalid, f.nama_regional, e.areaid, e.nama_area, d.subareaid, d.nama_area kota,
										case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, 
										(sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
												and f.regionalid='$regionalid' and e.areaid='$areaid' $strqueryarea
										group by f.regionalid, f.nama_regional, e.areaid, e.nama_area, d.subareaid, d.nama_area,
												case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end
										order by d.subareaid asc, item desc
										;
								");
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS Regional '.$nama_regional.' - Area '.$nama_area.'</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_sos_detail_national();" class="btn btn-success fa fa-home"> Home</a>
		<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_sos_detail_regional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';		
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Kota</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="2">SOS</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">% SOS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_sos_detail_regional_area_city_outlet('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['kota'].'\');">'.$vreg['kota'].'</a>&nbsp;
								<button id="btn-savexls" onclick="save_xls_sos_by_city(\''.$vreg['subareaid'].'\');" type="button" class="btn btn-success fa fa-download btn-xs"></button></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=$vreg['nama_regional'];
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-sos");
					let uiSelectBulan = $("#bln-id-sos");
					let uiSelectClassSos = $("#classid-id-sos");   
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassSos.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
				
					function open_sos_detail_national() {
						common.loadingClose();
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("drc_dashboard/open_detail_sos_national"),
							data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);
								common.loadingClose();
							},
							error:function(){
								alert("Load failed");
								common.loadingClose();
							}
						});
					}

					function open_sos_detail_regional_area(regionalid,nama_regional) {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_sos_detail_regional_area_city_outlet(regionalid,nama_regional,areaid,nama_area,subareaid,kota) {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional_area_city_outlet"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&subareaid="+subareaid+"&kota="+kota+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function save_xls_sos_by_city(city) {
						common.loading();
						common.direct("drc_dashboard/save_xls_sos_by_city/"+city+"/"+tahun+"/"+bulan+"/"+classid);
						common.loadingClose();
					}

				</script>
				';
				
		echo $html;

	}

	function open_detail_sos_regional_area_city_outlet() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		$areaid = $this->input->post("areaid");
		$nama_area = $this->input->post("nama_area");
		$subareaid = $this->input->post("subareaid");
		$kota = $this->input->post("kota");

		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and d.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and e.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and f.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}


		$qhcreg = $this->db->query(" 
										select f.regionalid, f.nama_regional, e.areaid, e.nama_area, d.subareaid, d.nama_area kota, 
										b.customerid, b.kode_outlet, b.nama_customer, g.nama_class as account, b.alamat,
										case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										a.qty_sos_gsk qtygsk, a.qty_sos_competitor qtykategori, 
										(a.qty_sos_gsk/a.qty_sos_competitor)*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										left join m_customer_class g on b.classid=g.classid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
												and f.regionalid='$regionalid' and e.areaid='$areaid' and d.subareaid='$subareaid' $strqueryarea
										order by b.customerid asc, item desc
										;
								");
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS Regional '.$nama_regional.' - Area '.$nama_area.' - City '.$kota.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_sos_detail_national();" class="btn btn-success fa fa-home"> Home</a>
		<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_sos_detail_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';		
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Outlet Id</th>';
        $html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
        $html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
        $html .= '<th style="white-space: nowrap;">Account</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Qty GSK</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">Qty Kategori</th>';
		$html .= '<th style="white-space: nowrap;text-align:center;">% SOS</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['customerid'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['kode_outlet'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['nama_customer'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['account'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtygsk'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtykategori'].'</a></td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtygsk'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtykategori'].'</a></td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;text-align:right;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=$vreg['nama_regional'];
			}

		$html .= '</tbody>';
		$html .= '</table></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-sos");
					let uiSelectBulan = $("#bln-id-sos");
					let uiSelectClassSos = $("#classid-id-sos");   
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassSos.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_sos_detail_national() {
						common.loadingClose();
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("drc_dashboard/open_detail_sos_national"),
							data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);
								common.loadingClose();
							},
							error:function(){
								alert("Load failed");
								common.loadingClose();
							}
						});
					}

					function open_sos_detail_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional_area_city"),
								data : "areaid="+areaid+"&nama_area="+nama_area+"&regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function save_xls_sos_by_city() {
		$this->load->library('excel');
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', '3600');
		
		$kota = $this->uri->segment('3');
		$tahun = $this->uri->segment('4');
		$bulan = $this->uri->segment('5');
		$classid = $this->uri->segment('6');
		if($classid=='null'){$classid='%';}

		$qhcreg = $this->db->query(" 
										select a.periode,f.regionalid, f.nama_regional, e.areaid, e.nama_area, d.subareaid, d.nama_area kota, 
										b.customerid, b.kode_outlet, b.nama_customer, g.nama_class as account, b.alamat, a.salesmanid, c.nama_salesman, c.tipe_sales,
										case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										a.qty_sos_gsk qtygsk, a.qty_sos_competitor qtykategori, 
										(a.qty_sos_gsk/a.qty_sos_competitor)*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										left join m_customer_class g on b.classid=g.classid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
												and d.subareaid='$kota'
										order by b.customerid asc, item desc
										;
								");
		$datarg = $qhcreg->result_array();
		echo $tempsql;
		$filename = "Data_SOS_BY_CITY_".$tahun.$bulan.".xlsx";
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Data SOS By City')
                    ->setCellValue('A2', 'Outlet ID.')
                    ->setCellValue('B2', 'Kode Outlet')
                    ->setCellValue('C2', 'Nama Outlet')
                    ->setCellValue('D2', 'Kota')
                    ->setCellValue('E2', 'Account')
                    ->setCellValue('F2', 'Item')
                    ->setCellValue('G2', 'Qty GSK')
                    ->setCellValue('H2', 'Qty Kategori')
                    ->setCellValue('I2', '% SOS')
                    ->setCellValue('J2', 'USER TPE')
                    ->setCellValue('K2', 'POSITION')
                    ->setCellValue('L2', 'PERIODE INPUT')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($datarg as $vnat) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $vnat['customerid'])
                                    ->setCellValue('B'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('C'.$i, $vnat['nama_customer'])
                                    ->setCellValue('D'.$i, $vnat['kota'])
                                    ->setCellValue('E'.$i, $vnat['account'])
                                    ->setCellValue('F'.$i, $vnat['item'])
                                    ->setCellValue('G'.$i, $vnat['qtygsk'])
                                    ->setCellValue('H'.$i, $vnat['qtykategori'])
									->setCellValue('I'.$i, $vnat['sos'])
									->setCellValue('J'.$i, $vnat['salesmanid'].'-'.$vnat['nama_salesman'])
									->setCellValue('K'.$i, $vnat['tipe_sales'])
									->setCellValue('L'.$i, $vnat['periode'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;
	}

	function save_xls_sos_national_row_data() {
			$tahun = $this->input->get("tahun");
			$bulan = $this->input->get("bulan");
			$classid = $this->input->get("classid");

			$idjabatan = $this->input->get("idjabatan");
			$usersession = $this->input->get("usersession");
			$restrict_level = $this->input->get("restrict_level");
			$restrict_bu = $this->input->get("restrict_bu");
			$periode = $tahun.'-'.$bulan.'-01';
	
			if($classid=='null'){$classid='%';}
	
			if ($restrict_bu=='GT'){
				$querybu = " tipe_sales='MEDREP' and";
			}elseif ($restrict_bu=='MT'){
				$querybu = " tipe_sales<>'MEDREP' and";
			}else{
				$querybu = "";
			}
	
			if ($restrict_level=='4'){
				$strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
													app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
													where a.username='".$usersession."'
													)";
				
			}
			else if ($restrict_level=='3'){
				$strqueryarea = " and f.areaid in (select distinct b.areaid from  
													app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
													where a.username='".$usersession."'
													)";
			}
			else if ($restrict_level=='2'){
				$strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
													app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
													where a.username='".$usersession."'
													) ";
			}
			else {
				$strqueryarea ="";
				$strquery = "";
			}
	
			$this->load->library('excel');
			ini_set("memory_limit","1024M");
			ini_set('max_execution_time', '3600');
			$filename = "Row_data_SOS_".$tahun.$bulan.".xlsx";
	
			$qhcreg = $this->db->query(" 
										select a.periode, a.customerid, a.customerid,b.kode_outlet,b.nama_customer, f.regionalid, f.nama_regional, e.areaid, e.nama_area, d.subareaid, d.nama_area kota, b.kode_outlet, b.nama_customer, 
										b.classid, x.nama_class, case when a.type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, a.qty_sos_gsk qtygsk, a.qty_sos_competitor qtykategori, 
										(a.qty_sos_gsk/a.qty_sos_competitor)*100 sos, a.salesmanid, y.nama_salesman, y. tipe_sales
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid left join m_sales_salesman c on a.salesmanid=c.salesmanid 
										left join m_customer_class x on b.classid=x.classid
										left join m_area_subarea d on b.subareaid=d.subareaid 
										left join m_area_areasite e on e.areaid=d.areaid 
										left join m_area_regional f on f.regionalid=e.regionalid 
										left join m_sales_salesman y on a.salesmanid=y.salesmanid										
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid'
										$strqueryarea
										order by f.regionalid asc, item desc 
										;");
			$datarg = $qhcreg->result_array();
			//echo $this->db->last_query();
	
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'Report SOS National')
						->setCellValue('A2', 'Periode')
						->setCellValue('B2', 'OutletID')
						->setCellValue('C2', 'Kode Outlet')
						->setCellValue('D2', 'Outlet')
						->setCellValue('E2', 'Account')
						->setCellValue('F2', 'Regional')
						->setCellValue('G2', 'Area')
						->setCellValue('H2', 'City')
						->setCellValue('I2', 'Kategori')
						->setCellValue('J2', 'QTY GSK')
						->setCellValue('K2', 'QTY ALL Kategori')
						->setCellValue('L2', '%SOS')
						->setCellValue('M2', 'MEDREP')
						->setCellValue('N2', 'Position')
						;
						$i = 3;
						$no = 1;
						
						foreach ($datarg as $vnat) {
							if($vnat['qtygsk']==0) { $sos= 0; }
							else if ($vnat['qtykategori']==0) { $sos= 0; }
							else { $sos=$vnat['qtygsk']/$vnat['qtykategori']; }
	
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('A'.$i, $vnat['periode'])
										->setCellValue('B'.$i, $vnat['customerid'])
										->setCellValue('C'.$i, $vnat['kode_outlet'])
										->setCellValue('D'.$i, $vnat['nama_customer'])
										->setCellValue('E'.$i, $vnat['nama_class'])
										->setCellValue('F'.$i, $vnat['nama_regional'])
										->setCellValue('G'.$i, $vnat['nama_area'])
										->setCellValue('H'.$i, $vnat['kota'])
										->setCellValue('I'.$i, $vnat['item'])
										->setCellValue('J'.$i, $vnat['qtygsk'])
										->setCellValue('K'.$i, $vnat['qtykategori'])
										->setCellValue('L'.$i, $vnat['sos'])
										->setCellValue('M'.$i, $vnat['salesmanid'].'-'.$vnat['nama_salesman'])
										->setCellValue('N'.$i, $vnat['tipe_sales'])
										;
							$i++;
							$no++;
						}
			// Redirect output to a client's web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment;filename=$filename");
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=0');
			// If you're serving to IE over SSL, then the following may be needed
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			unset($objPHPExcel);
			return true;
			
	}

	function open_detail_product_available_national() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		/*$qexecpaperiod = $this->db->query("replace into rekap_product_available_detail (tahun, bulan, periode, salesmanid, customerid, customerid_m, type_sos, image, 
										qty_product_available_gsk, qty_product_available_competitor, sos, datecreate, created_by, created_date, modified_by, modified_date, transaction_id)
										select date_format(a.periode,'%Y'), date_format(a.periode,'%m'), a.periode, a.salesmanid, a.customerid, a.customerid_m, 
										a.type_sos, a.image, a.qty_product_available_gsk, a.qty_product_available_competitor, a.sos, a.datecreate, a.created_by, a.created_date, a.modified_by,
										a.modified_date, a.transaction_id
										from t_activity_sos a 
										where a.periode = (select tanggal from m_setup_site)
										;");
		*/
		$qhcreg = $this->db->query(" 
		select 'National' area, z.brandid, z.brand, z.categoryid, count(1) coverage, sum(z._available) _available, sum(z.fr) fr, sum(z.oos) oos from ( 
				select distinct z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid, z.brand, z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.categoryid,
						sum(z.fr) fr, sum(z.oos) oos, case when sum(z.fr)<>sum(z.oos) then 1 else 0 end _available from 
						( 
						select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, c.brandid,d.brand, b.regionalid, e.nama_regional, 
								b.areaid, f.nama_area, b.subareaid,g.nama_area city, a.fr, a.oos, c.categoryid
						from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
							left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
							left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
						where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' and a.productid in (select productid from mapping_sku_active where idaccount=b.classid) 
							and a.productid like '$productid' and c.brandid like '$brandid'
							and b.nama_customer is not null and e.nama_regional is not null $strqueryarea
						) z 
				group by z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid,z.brand,z.regionalid, z.nama_regional,z.areaid, z.nama_area, z.categoryid 
				order by z.areaid, z.brandid asc) z 
		group by z.brandid, z.brand, z.categoryid
		order by z.brandid asc;
								");
		$datarg = $qhcreg->result_array();

		$qchild = $this->db->query(" 
								select 'National' area, z.brandid, z.brand, z.categoryid, z.regionalid, z.nama_regional, count(1) coverage, sum(z._available) _available, sum(z.fr) fr, sum(z.oos) oos from ( 
									select distinct z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid, z.categoryid, z.brand, z.regionalid, z.nama_regional, z.areaid, z.nama_area,
											sum(z.fr) fr, sum(z.oos) oos, case when sum(z.fr)<>sum(z.oos) then 1 else 0 end _available from 
											( 
											select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, c.brandid, c.categoryid, d.brand, b.regionalid, e.nama_regional, 
													b.areaid, f.nama_area, b.subareaid,g.nama_area city, a.fr, a.oos
											from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
												left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
												left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
											where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' and a.productid in (select productid from mapping_sku_active where idaccount=b.classid) 
												and a.productid like '$productid' and c.brandid like '$brandid'
												and b.nama_customer is not null and e.nama_regional is not null $strqueryarea
											) z 
									group by z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid,z.brand, z.categoryid, z.regionalid, z.nama_regional,z.areaid, z.nama_area 
									order by z.areaid, z.brandid asc) z 
								group by z.brandid, z.brand, z.categoryid, z.regionalid, z.nama_regional
								order by z.regionalid, z.brandid asc;
								");
		$datachild = $qchild->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability</h3>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;"></th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Brand</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$regional='';
		$header=1;
		foreach ($datarg as $vreg) {
				if ($vreg['oos']==0){$persentasioos=0;}
				else if ($vreg['fr']==0){$persentasioos=0;}
				else{$persentasioos=$vreg['oos']/$vreg['fr'];}

				if ($vreg['_available']==0){$persentasicoverage=0;}
				else if ($vreg['coverage']==0){$persentasicoverage=0;}
				else{$persentasicoverage=$vreg['_available']/$vreg['coverage'];}

				$html .= '<tr>';
				if ($header==1){
					$html .= '<td style="white-space: nowrap;" rowspan="4"><a href="#" id="hcnational" onclick="open_product_available_detail_regional();">National</a>&nbsp;
					<button id="btn-savexls-nat" onclick="save_xls_product_availability_national();" type="button" class="btn btn-success fa fa-download btn-xs"></button>
					</td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				
				if($vreg['categoryid']=='TB'){$catsensodyne='-Toothbrush';}else if($vreg['categoryid']=='TP'){$catsensodyne='-Toothpaste';}else{$catsensodyne='';}
				
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['brand'].$catsensodyne.'</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($persentasicoverage*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($persentasioos*100),2).' %</td>';
				$html .= '</tr>';
				$header++;
			}
		$html .= '</tbody>';
		$html .= '</table></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Regional</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Brand</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datachild as $vreg) {
				if ($vreg['oos']==0){$persentasioos=0;}
				else if ($vreg['fr']==0){$persentasioos=0;}
				else{$persentasioos=$vreg['oos']/$vreg['fr'];}

				if ($vreg['_available']==0){$persentasicoverage=0;}
				else if ($vreg['coverage']==0){$persentasicoverage=0;}
				else{$persentasicoverage=$vreg['_available']/$vreg['coverage'];}

				$html .= '<tr>';
				if ($regional!=$vreg['nama_regional']){
					$html .= '<td style="white-space: nowrap;" rowspan = "4" ><a href="#" id="hcnational" onclick="open_brand_product_available_detail_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}

				if($vreg['categoryid']=='TB'){$catsensodyne='-Toothbrush';}else if($vreg['categoryid']=='TP'){$catsensodyne='-Toothpaste';}else{$catsensodyne='';}

				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['brand'].$catsensodyne.'</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(@$vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($persentasicoverage*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($persentasioos*100),2).' %</td>';
				$html .= '</tr>';
				$regional=$vreg['nama_regional'];
			}
		$html .= '</tbody>';
		$html .= '</table></div>';		
		$html .= '</div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassPa = $("#classid-id-pa");
					let uiSelectBrand = $("#brandid-id");
					let uiSelectProd = $("#productid-id");
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassPa.val();
					var productid = uiSelectProd.val();
					var brandid = uiSelectBrand.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_product_available_detail_regional() {
					
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_brand_product_available_detail_regional_area(regionalid,nama_regional) {
					
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_brand_detail_product_available_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function save_xls_product_availability_national() {
						common.loading();
						common.direct("drc_dashboard/save_xls_product_availability_national?tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu);
						common.loadingClose();
					}

				</script>
				';
				
		echo $html;

	}

	function open_detail_product_available_regional() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		/*$qexecpaperiod = $this->db->query("replace into rekap_product_available_detail (tahun, bulan, periode, salesmanid, customerid, customerid_m, type_sos, image, 
										qty_product_available_gsk, qty_product_available_competitor, sos, datecreate, created_by, created_date, modified_by, modified_date, transaction_id)
										select date_format(a.periode,'%Y'), date_format(a.periode,'%m'), a.periode, a.salesmanid, a.customerid, a.customerid_m, 
										a.type_sos, a.image, a.qty_product_available_gsk, a.qty_product_available_competitor, a.sos, a.datecreate, a.created_by, a.created_date, a.modified_by,
										a.modified_date, a.transaction_id
										from t_activity_sos a 
										where a.periode = (select tanggal from m_setup_site)
										;");
		*/
		$qhcreg = $this->db->query(" 
									select z.regionalid, z.nama_regional, z.productid,z.product_name, count(1) coverage, sum(z._available) _available,
										sum(z.fr) fr, sum(z.oos) oos from (
											select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, a.productid,c.nama_invoice product_name,c.brandid,d.brand,b.regionalid,e.nama_regional,b.areaid, f.nama_area,
													b.subareaid, g.nama_area city, a.fr, a.oos, case when a.fr<>a.oos then 1 else 0 end _available  
											from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
											left join m_product c on a.productid=c.productid
											left join ref_brand d on c.brandid=d.brandid
											left join m_area_regional e on b.regionalid=e.regionalid
											left join m_area_areasite f on b.areaid=f.areaid
											left join m_area_subarea g on b.subareaid=g.subareaid
											where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' 
												and a.productid in (select productid from mapping_sku_active where idaccount=b.classid)
												and a.productid like '$productid' and c.brandid like '$brandid'
												and b.nama_customer is not null and e.nama_regional is not null $strqueryarea
										) z 
										group by z.regionalid, z.nama_regional, z.productid, z.product_name
										order by z.regionalid asc, z.product_name asc;
								");
		$datarg = $qhcreg->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability Regional</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_product_available_detail_national();" class="btn btn-success fa fa-home"> Home</a></div>';		
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Regional</th>';
        $html .= '<th style="white-space: nowrap;text-align:left;">Product</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $regional='';
		foreach ($datarg as $vreg) {
				$html .= '<tr>';
				if ($regional!=$vreg['nama_regional']){
					$html .= '<td style="white-space: nowrap;" rowspan = "4" ><a href="#" id="hcnational" onclick="open_product_available_detail_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['product_name'].'('.$vreg['productid'].')</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
				$html .= '</tr>';
				$regional=$vreg['nama_regional'];

			}


		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassPa = $("#classid-id-pa");
					let uiSelectBrand = $("#brandid-id");
					let uiSelectProd = $("#productid-id");
					let paramsession = common.getCookie("session");

					function open_product_available_detail_national() {
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var classid = uiSelectClassPa.val();
						var brandid = uiSelectBrand.val();
						var productid = uiSelectProd.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_national"),
								data : "classid="+classid+"&brandid="+brandid+"&productid="+productid+"&tahun="+tahun+"&bulan="+bulan+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_product_available_detail_regional_area(regionalid,nama_regional) {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var classid = uiSelectClassPa.val();
						var brandid = uiSelectBrand.val();
						var productid = uiSelectProd.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
						common.loading();

							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_detail_product_available_regional_area() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}


		$qhcreg = $this->db->query(" 
									select z.regionalid, z.nama_regional, z.areaid,z.nama_area, z.productid, z.product_name, count(1) coverage, 
										sum(z._available) _available, sum(z.fr) fr, sum(z.oos) oos from (
										select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, a.productid,c.nama_invoice product_name,
												c.brandid,d.brand,b.regionalid,e.nama_regional,b.areaid,f.nama_area,
												b.subareaid,g.nama_area city, a.fr, a.oos, case when a.fr<>a.oos then 1 else 0 end _available  
										from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
										left join m_product c on a.productid=c.productid
										left join ref_brand d on c.brandid=d.brandid
										left join m_area_regional e on b.regionalid=e.regionalid
										left join m_area_areasite f on b.areaid=f.areaid
										left join m_area_subarea g on b.subareaid=g.subareaid
										where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' 
											and a.productid in (select productid from mapping_sku_active where idaccount=b.classid)
											and a.productid like '$productid' and c.brandid like '$brandid'
											and b.nama_customer is not null and e.nama_regional is not null 
											and b.regionalid='$regionalid' $strqueryarea
									) z 
									group by z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.productid,z.product_name
									order by z.regionalid asc, z.areaid asc, z.product_name asc;
								");
		$datarg = $qhcreg->result_array();
		//echo $this->db->last_query();
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability Regional '.$nama_regional.'</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_product_available_detail_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_product_available_detail_regional('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">City</th>';
		$html .= '<th style="white-space: nowrap;">Product</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
				 $area='';
		foreach ($datarg as $vreg) {
				$html .= '<tr>';
				if ($area!=$vreg['nama_area']){
					$html .= '<td style="white-space: nowrap;"  rowspan="4"><a href="#" id="hcnational" onclick="open_product_available_detail_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['product_name'].'('.$vreg['productid'].')</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
				$html .= '</tr>';
				$area=$vreg['nama_area'];
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassPa = $("#classid-id-pa");
					let uiSelectBrand = $("#brandid-id");
					let uiSelectProd = $("#productid-id");
					let paramsession = common.getCookie("session");

					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassPa.val();
					var brandid = uiSelectBrand.val();
					var productid = uiSelectProd.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
				
					function open_product_available_detail_national() {

						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_national"),
								data : "classid="+classid+"&brandid="+brandid+"&productid="+productid+"&tahun="+tahun+"&bulan="+bulan+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_product_available_detail_regional() {
					
						common.loading();
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional"),
								data : "classid="+classid+"&brandid="+brandid+"&productid="+productid+"&tahun="+tahun+"&bulan="+bulan+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_product_available_detail_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						common.loading();
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional_area_city"),
								data : "areaid="+areaid+"&nama_area="+nama_area+"&regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_brand_detail_product_available_regional_area() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qchild = $this->db->query(" 
									select distinct z.brandid, z.brand, z.regionalid, z.nama_regional, z.areaid, z.nama_area, count(1) coverage, sum(z._available) _available,
											sum(z.fr) fr, sum(z.oos) oos from ( 
									select distinct z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid, z.brand, z.regionalid, z.nama_regional, z.areaid, z.nama_area,
											sum(z.fr) fr, sum(z.oos) oos, case when 	sum(z.fr)<>sum(z.oos) then 1 else 0 end _available from ( 
												select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, c.brandid,d.brand, b.regionalid, e.nama_regional, 
											b.areaid, f.nama_area, b.subareaid,g.nama_area city, a.fr, a.oos
									from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
										left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
										left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
									where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' and a.productid in (select productid from mapping_sku_active where idaccount=b.classid) 
										and a.productid like '$productid' and c.brandid like '$brandid' and b.regionalid='$regionalid' 
										and b.nama_customer is not null and e.nama_regional is not null $strqueryarea
										) z 
									group by z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid,z.brand,z.regionalid, z.nama_regional,z.areaid, z.nama_area 
									order by z.areaid, z.brandid asc) z 
									group by z.brandid,z.brand,z.regionalid, z.nama_regional,z.areaid, z.nama_area 
									order by z.areaid, z.brandid asc;
								");
		$datachild = $qchild->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability Regional '.$nama_regional.'</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_product_available_detail_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">Area</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Brand</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($datachild as $vreg) {
				$html .= '<tr>';
				if ($area!=$vreg['nama_area']){
					$html .= '<td style="white-space: nowrap;" rowspan = "4" ><a href="#" id="hcnational" onclick="open_brand_product_available_detail_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['brand'].'</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
				$html .= '</tr>';
				$area=$vreg['nama_area'];
			}
		$html .= '</tbody>';
		$html .= '</table></div>';		
		$html .= '</div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassPa = $("#classid-id-pa");
					let uiSelectBrand = $("#brandid-id");
					let uiSelectProd = $("#productid-id");
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassPa.val();
					var productid = uiSelectProd.val();
					var brandid = uiSelectBrand.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_product_available_detail_national() {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_brand_product_available_detail_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_brand_detail_product_available_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_brand_detail_product_available_regional_area_city() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		$areaid = $this->input->post("areaid");
		$nama_area = $this->input->post("nama_area");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qchild = $this->db->query(" 
									select distinct z.brandid, z.brand, z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city, count(1) coverage, sum(z._available) _available,
											sum(z.fr) fr, sum(z.oos) oos from ( 
											select distinct z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid, z.brand, z.regionalid, z.nama_regional, z.areaid, z.nama_area,
													z.subareaid, z.city, sum(z.fr) fr, sum(z.oos) oos, case when 	sum(z.fr)<>sum(z.oos) then 1 else 0 end _available from ( 
														select distinct a.customerid,b.kode_outlet,b.nama_customer, b.classid, c.brandid,d.brand, b.regionalid, e.nama_regional, 
													b.areaid, f.nama_area, b.subareaid, g.nama_area city, a.fr, a.oos
											from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
												left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
												left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
											where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' and a.productid in (select productid from mapping_sku_active where idaccount=b.classid) 
												and a.productid like '$productid' and c.brandid like '$brandid' and b.regionalid='$regionalid'  and b.areaid='$areaid'
												and b.nama_customer is not null and e.nama_regional is not null $strqueryarea
												) z 
											group by z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brandid,z.brand,z.regionalid, z.nama_regional,z.areaid, z.nama_area 
											order by z.areaid, z.brandid asc) z 
									group by z.brandid,z.brand,z.regionalid, z.nama_regional,z.areaid, z.nama_area, z.subareaid, z.city
									order by z.subareaid, z.brandid asc;
								");
		$datachild = $qchild->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability Regional '.$nama_regional.' - Area '.$nama_area.'</h3>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_product_available_detail_national();" class="btn btn-success fa fa-home"> Home</a>
		<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_brand_product_available_detail_regional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		
		$html .= '<div class="table-responsive col-md-6">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">City</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Brand</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $area='';
		foreach ($datachild as $vreg) {
				$html .= '<tr>';
				if ($area!=$vreg['city']){
					$html .= '<td style="white-space: nowrap;" rowspan = "4" ><a href="#" id="hcnational" onclick="save_xls_product_availability_city('.$vreg['regionalid'].','.$vreg['areaid'].','.$vreg['subareaid'].');">'.$vreg['city'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['brand'].'</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
				$html .= '</tr>';
				$area=$vreg['city'];
			}
		$html .= '</tbody>';
		$html .= '</table></div>';		
		$html .= '</div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassPa = $("#classid-id-pa");
					let uiSelectBrand = $("#brandid-id");
					let uiSelectProd = $("#productid-id");
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassPa.val();
					var productid = uiSelectProd.val();
					var brandid = uiSelectBrand.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_product_available_detail_national() {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_national"),
								data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_brand_product_available_detail_regional_area(regionalid,nama_regional) {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_brand_detail_product_available_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function save_xls_product_availability_city(regionalid,areaid,subareaid) {
						common.loading();
						common.direct("drc_dashboard/save_xls_product_availability_city?regionalid="+regionalid+"&areaid="+areaid+"&subareaid="+subareaid+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu);
						common.loadingClose();
					}

				</script>
				';
				
		echo $html;

	}

	function open_detail_product_available_regional_area_city() {
		$tahun = $this->input->post("tahun");
		$bulan = $this->input->post("bulan");
		$classid = $this->input->post("classid");
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		$areaid = $this->input->post("areaid");
		$nama_area = $this->input->post("nama_area");

		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;

		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qhcreg = $this->db->query(" 
									select z.regionalid, z.nama_regional, z.areaid,z.nama_area, z.subareaid, z.city, z.productid, z.product_name, count(1) coverage, 
										sum(z._available) _available, sum(z.fr) fr, sum(z.oos) oos from (
										select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, a.productid,c.nama_invoice product_name,
												c.brandid,d.brand,b.regionalid,e.nama_regional,b.areaid,f.nama_area,
												b.subareaid, g.nama_area city, a.fr, a.oos, case when a.fr<>a.oos then 1 else 0 end _available  
										from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
										left join m_product c on a.productid=c.productid
										left join ref_brand d on c.brandid=d.brandid
										left join m_area_regional e on b.regionalid=e.regionalid
										left join m_area_areasite f on b.areaid=f.areaid
										left join m_area_subarea g on b.subareaid=g.subareaid
										where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' 
											and a.productid in (select productid from mapping_sku_active where idaccount=b.classid)
											and a.productid like '$productid' and c.brandid like '$brandid'
											and b.nama_customer is not null and e.nama_regional is not null 
											and b.regionalid='$regionalid' and b.areaid='$areaid' $strqueryarea
									) z 
									group by z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city, z.productid,z.product_name
									order by z.regionalid asc, z.areaid asc, z.subareaid asc, z.product_name asc;
								");
		$datarg = $qhcreg->result_array();
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability Regional '.$nama_regional.' - Area '.$nama_area.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_product_available_detail_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_product_available_detail_regional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">City</th>';
        $html .= '<th style="white-space: nowrap;">Product</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $city='';
		foreach ($datarg as $vreg) {
				$html .= '<tr>';
				if ($city!=$vreg['city']){
					$html .= '<td style="white-space: nowrap;" rowspan = "4" ><a href="#" id="hcnational" onclick="save_xls_product_availability_city('.$vreg['regionalid'].','.$vreg['areaid'].','.$vreg['subareaid'].');">'.$vreg['city'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['product_name'].'('.$vreg['productid'].')</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['coverage'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vreg['_available'], 0, ',', '.').'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
				$html .= '</tr>';
				$city=$vreg['city'];
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassPa = $("#classid-id-pa");
					let uiSelectBrand = $("#brandid-id");
					let uiSelectProd = $("#productid-id");
					let paramsession = common.getCookie("session");

					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassPa.val();
					var brandid = uiSelectBrand.val();
					var productid = uiSelectProd.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
				
					function open_product_available_detail_national() {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_national"),
								data : "classid="+classid+"&brandid="+brandid+"&productid="+productid+"&tahun="+tahun+"&bulan="+bulan+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_product_available_detail_regional_area(regionalid,nama_regional) {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function save_xls_product_availability_city(regionalid,areaid,subareaid) {
						common.loading();
						common.direct("drc_dashboard/save_xls_product_availability_city?regionalid="+regionalid+"&areaid="+areaid+"&subareaid="+subareaid+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu);
						common.loadingClose();
					}

				</script>
				';
				
		echo $html;

	}

	function save_xls_product_availability_national() {
		$tahun = $this->input->get("tahun");
		$bulan = $this->input->get("bulan");
		$classid = $this->input->get("classid");
		$brandid = $this->input->get("brandid");
		$productid = $this->input->get("productid");
		$idjabatan = $this->input->get("idjabatan");
		$usersession = $this->input->get("usersession");
		$restrict_level = $this->input->get("restrict_level");
		$restrict_bu = $this->input->get("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-01';

		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$this->load->library('excel');
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '36000');

		if ($classid=='%') {
			$dataaccount = 'All Account';
		}else{
			$qgetaccount = $this->db->query("select nama_class from m_customer_class where classid like '$classid';");
			$dataaccount = $qgetaccount->row()->nama_class;
		}
		
		$filename = "Data_Stock_".$tahun.$bulan."_".$dataaccount.".xlsx";
		
		$qhcreg = $this->db->query(" 
									select z.regionalid, z.nama_regional,z.areaid, z.nama_area, z.subareaid, z.city, z.brand, z.productid, z.nama_invoice product_name, count(1) coverage, sum(z._available) _available, sum(z.fr) fr, sum(z.oos) oos from 
												( 
												select distinct z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brand, z.productid, z.nama_invoice, 
														z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city,
														sum(z.fr) fr, sum(z.oos) oos, case when sum(z.fr)<>sum(z.oos) then 1 else 0 end _available from 
														( 
														select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, d.brand, c.productid, c.nama_invoice, b.regionalid, e.nama_regional, 
																b.areaid, f.nama_area, b.subareaid,g.nama_area city, a.fr, a.oos
														from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
															left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
															left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
														where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' 
															and a.productid in (select productid from mapping_sku_active where idaccount = b.classid) 
															and a.productid like '$productid' and c.brandid like '$brandid'
															$strqueryarea
															and b.nama_customer is not null and e.nama_regional is not null
														) z 
												group by z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brand, z.productid, z.nama_invoice, z.regionalid, z.nama_regional,z.areaid, z.nama_area, z.subareaid, z.city 
												) z 
									group by z.regionalid, z.nama_regional,z.areaid, z.nama_area, z.subareaid, z.city, z.brand, z.productid, z.nama_invoice;
		");
		/* query national 		
		select 'National' area,z.brand, z.productid, z.nama_invoice product_name, count(1) coverage, sum(z._available) _available, sum(z.fr) fr, sum(z.oos) oos from ( 
			select distinct z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brand, z.productid, z.nama_invoice, z.regionalid, z.nama_regional, z.areaid, z.nama_area,
					sum(z.fr) fr, sum(z.oos) oos, case when sum(z.fr)<>sum(z.oos) then 1 else 0 end _available from 
					( 
					select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, d.brand, c.productid, c.nama_invoice, b.regionalid, e.nama_regional, 
							b.areaid, f.nama_area, b.subareaid,g.nama_area city, a.fr, a.oos
					from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
						left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
						left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
					where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' 
						and a.productid in (select productid from mapping_sku_active where idaccount = b.classid) 
						and a.productid like '$productid' and c.brandid like '$brandid'
						$strqueryarea
						and b.nama_customer is not null and e.nama_regional is not null
					) z 
				group by z.customerid, z.kode_outlet, z.nama_customer, z.classid, z.brand, z.productid, z.nama_invoice, z.regionalid, z.nama_regional,z.areaid, z.nama_area ) z 
		group by z.brand, z.productid, z.nama_invoice;
		*/
		$datarg = $qhcreg->result_array();
		//echo $this->db->last_query();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Report Product Availability')
                    ->setCellValue('A2', 'Regional')
                    ->setCellValue('B2', 'Area')
                    ->setCellValue('C2', 'City')
                    ->setCellValue('D2', 'Brand')
                    ->setCellValue('E2', 'Kode Product')
                    ->setCellValue('F2', 'Product')
                    ->setCellValue('G2', '%OOS')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($datarg as $vnat) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $vnat['nama_regional'])
                                    ->setCellValue('B'.$i, $vnat['nama_area'])
                                    ->setCellValue('C'.$i, $vnat['city'])
                                    ->setCellValue('D'.$i, $vnat['brand'])
                                    ->setCellValue('E'.$i, $vnat['productid'])
                                    ->setCellValue('F'.$i, $vnat['product_name'])
                                    ->setCellValue('G'.$i, round(($vnat['oos']/$vnat['fr']),2))->getStyle('G'.$i)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
									;
						$i++;
						$no++;
					}
		$objPHPExcel->getActiveSheet()->setTitle($dataaccount);
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;
		
	}

	function save_xls_product_availability_national_row_data() {
		$tahun = $this->input->get("tahun");
		$bulan = $this->input->get("bulan");
		$classid = $this->input->get("classid");
		$brandid = $this->input->get("brandid");
		$productid = $this->input->get("productid");
		$idjabatan = $this->input->get("idjabatan");
		$usersession = $this->input->get("usersession");
		$restrict_level = $this->input->get("restrict_level");
		$restrict_bu = $this->input->get("restrict_bu");
		$periode = $tahun.'-'.$bulan.'-01';

		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$this->load->library('excel');
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', '36000');
		$filename = "Row_data_OOS_".$tahun.$bulan.".xlsx";

		$qhcreg = $this->db->query(" 
									select distinct a.salesmanid,y.nama_salesman,y.tipe_sales, a.customerid,b.kode_outlet,b.nama_customer,b.classid,x.nama_class account_name, 
											d.brand, c.productid, c.nama_invoice, b.regionalid, e.nama_regional, b.areaid, f.nama_area, b.subareaid,g.nama_area city, 
											a.fr, a.oos, a.last_update periode
										from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
										left join m_customer_class x on b.classid=x.classid		 
										left join m_product c on a.productid=c.productid 
											left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
											left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
											left join m_sales_salesman y on a.salesmanid=y.salesmanid
										where a.tahun='$tahun' and a.bulan='$bulan' and b.classid like '$classid' 
											and a.productid in (select productid from mapping_sku_active where idaccount=b.classid) 
											and c.brandid like '$brandid' and a.productid like '$productid'
											and b.nama_customer is not null and e.nama_regional is not null
											$strqueryarea
										;");
		$datarg = $qhcreg->result_array();
		//echo $this->db->last_query();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Report OOS National')
                    ->setCellValue('A2', 'Tahun')
                    ->setCellValue('B2', 'Bulan')
                    ->setCellValue('C2', 'OutletID')
                    ->setCellValue('D2', 'Kode Outlet')
                    ->setCellValue('E2', 'Outlet')
                    ->setCellValue('F2', 'Account')
                    ->setCellValue('G2', 'Regional')
                    ->setCellValue('H2', 'Area')
                    ->setCellValue('I2', 'City')
                    ->setCellValue('J2', 'Brand')
                    ->setCellValue('K2', 'Kode Product')
                    ->setCellValue('L2', 'Product')
                    ->setCellValue('M2', 'Fr')
                    ->setCellValue('N2', 'OOS')
                    ->setCellValue('O2', '%OOS')
                    ->setCellValue('P2', 'MEDREP')
                    ->setCellValue('Q2', 'POSITION')
                    ->setCellValue('R2', 'Period')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($datarg as $vnat) {
						if($vnat['oos']==0) { $oospersentasi= 0; }
						else if ($vnat['fr']==0) { $oospersentasi= 0; }
						else { $oospersentasi=$vnat['oos']/$vnat['fr']; }

                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $tahun)
                                    ->setCellValue('B'.$i, $bulan)
                                    ->setCellValue('C'.$i, $vnat['customerid'])
                                    ->setCellValue('D'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('E'.$i, $vnat['nama_customer'])
                                    ->setCellValue('F'.$i, $vnat['account_name'])
                                    ->setCellValue('G'.$i, $vnat['nama_regional'])
                                    ->setCellValue('H'.$i, $vnat['nama_area'])
                                    ->setCellValue('I'.$i, $vnat['city'])
                                    ->setCellValue('J'.$i, $vnat['brand'])
									->setCellValue('K'.$i, $vnat['productid'])
                                    ->setCellValue('L'.$i, $vnat['nama_invoice'])
                                    ->setCellValue('M'.$i, $vnat['fr'])
                                    ->setCellValue('N'.$i, $vnat['oos'])
                                    ->setCellValue('O'.$i, $oospersentasi)
                                    ->setCellValue('P'.$i, $vnat['salesmanid'].'-'.$vnat['nama_salesman'])
                                    ->setCellValue('Q'.$i, $vnat['tipe_sales'])
                                    ->setCellValue('R'.$i, $vnat['period'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;
		
	}

	function save_xls_product_availability_city() {
		$tahun = $this->input->get("tahun");
		$bulan = $this->input->get("bulan");
		$classid = $this->input->get("classid");
		$brandid = $this->input->get("brandid");
		$productid = $this->input->get("productid");
		$regionalid = $this->input->get("regionalid");
		$nama_regional = $this->input->get("nama_regional");
		$areaid = $this->input->get("areaid");
		$nama_area = $this->input->get("nama_area");
		$subareaid = $this->input->get("subareaid");
		$city = $this->input->get("city");
		$periode = $tahun.'-'.$bulan.'-01';

		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->get("idjabatan");
		$usersession = $this->input->get("usersession");
		$restrict_level = $this->input->get("restrict_level");
		$restrict_bu = $this->input->get("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}
		if($classid=='null'){$classid='%';}

		$this->load->library('excel');
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', '36000');
		$filename = "Data_Stock_".$tahun.$bulan.".xlsx";

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}


		$qhcreg = $this->db->query(" 
									select a.periode, a.salesmanid, h.nama_salesman, a.customerid,b.kode_outlet,b.nama_customer,b.classid,i.nama_class,b.typeid,
											a.productid,c.nama_invoice product_name,c.brandid,d.brand,
											b.regionalid,e.nama_regional,b.areaid,f.nama_area,
											b.subareaid, g.nama_area city, case when a.date_update is null then 'NA' else a.qty_akhir end qty_akhir, a.exp_date, att.status status_att,
											CONCAT((select reason from t_sales_rrk_reason where call_reasonid=rrktrans.call_reasonid),' - ',rrktrans.keterangan) as keterangan
									from t_sales_crc a left join m_customer b on a.customerid=b.customerid
									left join m_product c on a.productid=c.productid
									left join ref_brand d on c.brandid=d.brandid
									left join m_area_regional e on b.regionalid=e.regionalid
									left join m_area_areasite f on b.areaid=f.areaid
									left join m_area_subarea g on b.subareaid=g.subareaid
									left join m_sales_salesman h on a.salesmanid=h.salesmanid
									left join m_customer_class i on b.classid=i.classid
									left join t_sales_absensi att on att.periode=a.periode and att.salesmanid=a.salesmanid
									left join t_sales_rrk_trans rrktrans on a.periode=rrktrans.periode and a.customerid=rrktrans.customerid
									where a.periode between '$periode' and last_day('$periode') and b.classid like '$classid' 
										and a.productid in (select productid from mapping_sku_active where idaccount=b.classid)
										and a.productid like '$productid' and c.brandid like '$brandid'
										and b.regionalid='$regionalid' and b.areaid='$areaid' and b.subareaid='$subareaid'
										and b.nama_customer is not null and e.nama_regional is not null 
										and a.customerid in (select customerid from t_stock_all_outlet where tahun='$tahun' and bulan='$bulan')
										and att.status='H'
										;");
		$datarg = $qhcreg->result_array();
		//echo $this->db->last_query();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Report Stock')
                    ->setCellValue('A2', 'Periode Input')
                    ->setCellValue('B2', 'OutletID')
                    ->setCellValue('C2', 'Kode Outlet')
                    ->setCellValue('D2', 'Outlet')
                    ->setCellValue('E2', 'Channel')
                    ->setCellValue('F2', 'Account')
                    ->setCellValue('G2', 'Brand')
                    ->setCellValue('H2', 'Kode Product')
                    ->setCellValue('I2', 'Product')
                    ->setCellValue('J2', 'Stock')
                    ->setCellValue('K2', 'Expired date')
                    ->setCellValue('L2', 'MEDREP')
                    ->setCellValue('M2', 'Keterangan')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($datarg as $vnat) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $vnat['periode'])
                                    ->setCellValue('B'.$i, $vnat['customerid'])
                                    ->setCellValue('C'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('D'.$i, $vnat['nama_customer'])
                                    ->setCellValue('E'.$i, $vnat['typeid'])
                                    ->setCellValue('F'.$i, $vnat['nama_class'])
                                    ->setCellValue('G'.$i, $vnat['brand'])
                                    ->setCellValue('H'.$i, $vnat['productid'])
                                    ->setCellValue('I'.$i, $vnat['product_name'])
                                    ->setCellValue('J'.$i, $vnat['qty_akhir'])
                                    ->setCellValue('K'.$i, $vnat['exp_date'])
									->setCellValue('L'.$i, $vnat['nama_salesman'].'-'.$vnat['salesmanid'])
                                    ->setCellValue('M'.$i, $vnat['keterangan'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;
		
	}

	function open_detail_slob_national() {
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($brandid=='null'){$brandid='%';}
		if($productid=='null'){$productid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qslob = $this->db->query(" 
		select 'current month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
			sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
		  	sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(now(),'%Y') and a.bulan=date_format(now(),'%m') and b.nama_customer is not null and e.nama_regional is not null 
							and c.brandid like '$brandid' and a.productid like '$productid' 
							$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
				$strqueryarea
			) z
		union all
		select 'Last month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
				sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
				sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
				$strqueryarea
				) z
		union all
		select 'Last 3 months' item, ifnull(sum(z.mm),0) as MM, ifnull(sum(z.mp),0) as MP, ifnull(sum(z.MTI),0) as MTI, ifnull(sum(z.HYPER),0) as HYPER, ifnull(sum(SUPER),0) as SUPER, 
				ifnull(sum(z.mm1),0) as MM1, ifnull(sum(z.mp1),0) as MP1, ifnull(sum(z.MTI1),0) as MTI1, ifnull(sum(z.HYPER1),0) as HYPER1, ifnull(sum(SUPER1),0) as SUPER1,
				ifnull(sum(z.mm2),0) as MM2, ifnull(sum(z.mp2),0) as MP2, ifnull(sum(z.MTI2),0) as MTI2, ifnull(sum(z.HYPER2),0) as HYPER2, ifnull(sum(SUPER2),0) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
				$strqueryarea
		 ) z ; ");
		$datanat = $qslob->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SLOB</h3><li>Keterangan : Satuan dalam Juta</a></li>';
		$html .= '<div class="table-responsive col-md-12"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">National</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y').'</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-1 month")).'</th>';
		$html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-2 month")).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$regional='';
		$header=1;
		foreach ($datanat as $vnat) {
				$html .= '<tr>';
				if ($vnat['item']=='current month'){
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_slob_detail_regional();">National</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
				}
				if ($vnat['item']=='Last 3 months'){
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm+$vnat['MM'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp+$vnat['MP'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti+$vnat['MTI'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper+$vnat['HYPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super+$vnat['SUPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1+$vnat['MM1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1+$vnat['MP1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1+$vnat['MTI1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1+$vnat['HYPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1+$vnat['SUPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2+$vnat['MM2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2+$vnat['MP2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2+$vnat['MTI2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2+$vnat['HYPER2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2+$vnat['SUPER2'])/1000000, 2, ',', '.').'</td>';
				}else{
					$mm = $mm + $vnat['MM']; $mp = $mp + $vnat['MP']; $mti = $mti + $vnat['MTI']; $hyper = $hyper + $vnat['HYPER']; $super = $super+$vnat['SUPER'];
					$mm1 = $mm1 + $vnat['MM1']; $mp1 = $mp1 + $vnat['MP1']; $mti1 = $mti1 + $vnat['MTI1']; $hyper1 = $hyper1 + $vnat['HYPER1']; $super1 = $super1+$vnat['SUPER1'];
					$mm2 = $mm2 + $vnat['MM2']; $mp2 = $mp2 + $vnat['MP2']; $mti2 = $mti2 + $vnat['MTI2']; $hyper2 = $hyper2 + $vnat['HYPER2']; $super2 = $super2+$vnat['SUPER2'];

					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER2']/1000000, 2, ',', '.').'</td>';
				}
				$html .= '</tr>';
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectBrand = $("#brandid-slob-id");
					let uiSelectProd = $("#productid-slob-id");
					let paramsession = common.getCookie("session");

					function open_slob_detail_regional() {
					
						var brandid = uiSelectBrand.val();
						var productid = uiSelectProd.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
						
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_regional"),
								data : "brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_detail_slob_regional() {
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($brandid=='null'){$brandid='%';}
		if($productid=='null'){$productid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qslob = $this->db->query(" 
		select * from (
		select  z.regionalid, z.nama_regional,'current month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
			sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
		  	sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(now(),'%Y') and a.bulan=date_format(now(),'%m') and b.nama_customer is not null and e.nama_regional is not null 
							and c.brandid like '$brandid' and a.productid like '$productid' 
							$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
				$strqueryarea
			) z group by z.regionalid, z.nama_regional
		union all
		select  z.regionalid, z.nama_regional,'Last month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
				sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
				sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
				$strqueryarea
				) z group by z.regionalid, z.nama_regional
		union all
		select  z.regionalid, z.nama_regional,'Last3months' item, ifnull(sum(z.mm),0) as MM, ifnull(sum(z.mp),0) as MP, ifnull(sum(z.MTI),0) as MTI, ifnull(sum(z.HYPER),0) as HYPER, ifnull(sum(SUPER),0) as SUPER, 
				ifnull(sum(z.mm1),0) as MM1, ifnull(sum(z.mp1),0) as MP1, ifnull(sum(z.MTI1),0) as MTI1, ifnull(sum(z.HYPER1),0) as HYPER1, ifnull(sum(SUPER1),0) as SUPER1,
				ifnull(sum(z.mm2),0) as MM2, ifnull(sum(z.mp2),0) as MP2, ifnull(sum(z.MTI2),0) as MTI2, ifnull(sum(z.HYPER2),0) as HYPER2, ifnull(sum(SUPER2),0) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid'
				$strqueryarea
			 ) z group by z.regionalid, z.nama_regional
		) x	order by x.regionalid asc, x.item asc; ");
		$datanat = $qslob->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SLOB</h3><li>Keterangan : Satuan dalam Juta</a></li>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_slob_detail_national();" class="btn btn-success fa fa-home"> Home</a></div>';
		$html .= '<div class="table-responsive col-md-12"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Regional</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y').'</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-1 month")).'</th>';
		$html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-2 month")).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$regional='';
		$cntitem=0;
		foreach ($datanat as $vnat) {
				if ($vnat['item']=='current month'){
					if($item=='Last month'){
						$html .= '<tr>';
						$html .= '<td style="white-space: nowrap;">Last 3 Month</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2)/1000000, 2, ',', '.').'</td>';
						$html .= '</tr>';
						}

					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_detail_slob_regional_area('.$vnat['regionalid'].',\''.$vnat['nama_regional'].'\');">'.$vnat['nama_regional'].'</a></td>';
					$mm = 0; $mp = 0; $mti = 0; $hyper = 0; $super = 0;
					$mm1 = 0; $mp1 = 0; $mti1 = 0; $hyper1 = 0; $super1 = 0;
					$mm2 = 0; $mp2 = 0; $mti2 = 0; $hyper2 = 0; $super2 = 0;
				}else{
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
				}

				if ($vnat['item']=='Last3months'){
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm+$vnat['MM'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp+$vnat['MP'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti+$vnat['MTI'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper+$vnat['HYPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super+$vnat['SUPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1+$vnat['MM1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1+$vnat['MP1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1+$vnat['MTI1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1+$vnat['HYPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1+$vnat['SUPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2+$vnat['MM2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2+$vnat['MP2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2+$vnat['MTI2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2+$vnat['HYPER2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2+$vnat['SUPER2'])/1000000, 2, ',', '.').'</td>';
					$cntitem=0;
				}else{
					$mm = $mm + $vnat['MM']; $mp = $mp + $vnat['MP']; $mti = $mti + $vnat['MTI']; $hyper = $hyper + $vnat['HYPER']; $super = $super+$vnat['SUPER'];
					$mm1 = $mm1 + $vnat['MM1']; $mp1 = $mp1 + $vnat['MP1']; $mti1 = $mti1 + $vnat['MTI1']; $hyper1 = $hyper1 + $vnat['HYPER1']; $super1 = $super1+$vnat['SUPER1'];
					$mm2 = $mm2 + $vnat['MM2']; $mp2 = $mp2 + $vnat['MP2']; $mti2 = $mti2 + $vnat['MTI2']; $hyper2 = $hyper2 + $vnat['HYPER2']; $super2 = $super2+$vnat['SUPER2'];

					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER2']/1000000, 2, ',', '.').'</td>';
					$cntitem++;				
				}
				$html .= '</tr>';
				$item=$vnat['item'];
			}
			if($item=='Last month'){
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;">Last 3 Month</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2)/1000000, 2, ',', '.').'</td>';
				$html .= '</tr>';
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectBrand = $("#brandid-slob-id");
					let uiSelectProd = $("#productid-slob-id");
					let paramsession = common.getCookie("session");
					var brandid = uiSelectBrand.val();
					var productid = uiSelectProd.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_slob_detail_national() {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_national"),
								data : "brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_detail_slob_regional_area(regionalid,nama_regional) {
					
						common.loading();

							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}


				</script>
				';
				
		echo $html;

	}

	function open_detail_slob_regional_area() {
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");

		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if($brandid=='null'){$brandid='%';}
		if($productid=='null'){$productid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}elseif ($restrict_bu=='MT'){
			$querybu = " tipe_sales<>'MEDREP' and";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qslob = $this->db->query(" 
		select * from (
		select  z.regionalid, z.nama_regional, z.areaid, z.nama_area,'current month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
			sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
		  	sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(now(),'%Y') and a.bulan=date_format(now(),'%m') and b.nama_customer is not null and e.nama_regional is not null 
							and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
							$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
				$strqueryarea
			) z group by z.regionalid, z.nama_regional, z.areaid, z.nama_area
		union all
		select  z.regionalid, z.nama_regional, z.areaid, z.nama_area,'Last month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
				sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
				sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
				$strqueryarea
				) z group by z.regionalid, z.nama_regional, z.areaid, z.nama_area
		union all
		select  z.regionalid, z.nama_regional, z.areaid, z.nama_area,'Last3months' item, ifnull(sum(z.mm),0) as MM, ifnull(sum(z.mp),0) as MP, ifnull(sum(z.MTI),0) as MTI, ifnull(sum(z.HYPER),0) as HYPER, ifnull(sum(SUPER),0) as SUPER, 
				ifnull(sum(z.mm1),0) as MM1, ifnull(sum(z.mp1),0) as MP1, ifnull(sum(z.MTI1),0) as MTI1, ifnull(sum(z.HYPER1),0) as HYPER1, ifnull(sum(SUPER1),0) as SUPER1,
				ifnull(sum(z.mm2),0) as MM2, ifnull(sum(z.mp2),0) as MP2, ifnull(sum(z.MTI2),0) as MTI2, ifnull(sum(z.HYPER2),0) as HYPER2, ifnull(sum(SUPER2),0) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						$strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						$strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
				$strqueryarea
			 ) z group by z.regionalid, z.nama_regional, z.areaid, z.nama_area
		) x	order by x.regionalid asc, x.nama_area asc, x.item asc; ");
		$datanat = $qslob->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SLOB Regional '.$nama_regional.'</h3><li>Keterangan : Satuan dalam Juta</a></li>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_detail_slob_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_detail_slob_regional();" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-12"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">Area</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y').'</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-1 month")).'</th>';
		$html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-2 month")).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$regional='';
		$cntitem=0;
		foreach ($datanat as $vnat) {
				if ($vnat['item']=='current month'){
					if($item=='Last month'){
						$html .= '<tr>';
						$html .= '<td style="white-space: nowrap;">Last 3 Month</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2)/1000000, 2, ',', '.').'</td>';
						$html .= '</tr>';
						}

					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_detail_slob_regional_area_city('.$vnat['regionalid'].',\''.$vnat['nama_regional'].'\','.$vnat['areaid'].',\''.$vnat['nama_area'].'\');">'.$vnat['nama_area'].'</a></td>';
					$mm = 0; $mp = 0; $mti = 0; $hyper = 0; $super = 0;
					$mm1 = 0; $mp1 = 0; $mti1 = 0; $hyper1 = 0; $super1 = 0;
					$mm2 = 0; $mp2 = 0; $mti2 = 0; $hyper2 = 0; $super2 = 0;
				}else{
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
				}

				if ($vnat['item']=='Last3months'){
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm+$vnat['MM'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp+$vnat['MP'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti+$vnat['MTI'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper+$vnat['HYPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super+$vnat['SUPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1+$vnat['MM1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1+$vnat['MP1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1+$vnat['MTI1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1+$vnat['HYPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1+$vnat['SUPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2+$vnat['MM2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2+$vnat['MP2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2+$vnat['MTI2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2+$vnat['HYPER2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2+$vnat['SUPER2'])/1000000, 2, ',', '.').'</td>';
					$cntitem=0;
				}else{
					$mm = $mm + $vnat['MM']; $mp = $mp + $vnat['MP']; $mti = $mti + $vnat['MTI']; $hyper = $hyper + $vnat['HYPER']; $super = $super+$vnat['SUPER'];
					$mm1 = $mm1 + $vnat['MM1']; $mp1 = $mp1 + $vnat['MP1']; $mti1 = $mti1 + $vnat['MTI1']; $hyper1 = $hyper1 + $vnat['HYPER1']; $super1 = $super1+$vnat['SUPER1'];
					$mm2 = $mm2 + $vnat['MM2']; $mp2 = $mp2 + $vnat['MP2']; $mti2 = $mti2 + $vnat['MTI2']; $hyper2 = $hyper2 + $vnat['HYPER2']; $super2 = $super2+$vnat['SUPER2'];

					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER2']/1000000, 2, ',', '.').'</td>';
					$cntitem++;				
				}
				$html .= '</tr>';
				$item=$vnat['item'];
			}
			if($item=='Last month'){
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;">Last 3 Month</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2)/1000000, 2, ',', '.').'</td>';
				$html .= '</tr>';
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectBrand = $("#brandid-slob-id");
					let uiSelectProd = $("#productid-slob-id");
					let paramsession = common.getCookie("session");
					var brandid = uiSelectBrand.val();
					var productid = uiSelectProd.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;

					function open_detail_slob_national() {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_national"),
								data : "brandid="+brandid+"&productid="+productid+"&tahun="+tahun+"&bulan="+bulan+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_detail_slob_regional() {
					
						common.loading();

							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_regional"),
								data : "brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_detail_slob_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						common.loading();

							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_regional_area_city"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

				</script>
				';
				
		echo $html;

	}

	function open_detail_slob_regional_area_city() {
		$brandid = $this->input->post("brandid");
		$productid = $this->input->post("productid");
		$regionalid = $this->input->post("regionalid");
		$nama_regional = $this->input->post("nama_regional");
		$areaid = $this->input->post("areaid");
		$nama_area = $this->input->post("nama_area");

		//$tanggal = $this->input->post("tanggal");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");
		$restrict_level = $this->input->post("restrict_level");
		$restrict_bu = $this->input->post("restrict_bu");
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;

		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}

		if ($restrict_bu=='GT'){
			$querybu = " tipe_sales='MEDREP' and";
		}else{
			$querybu = " tipe_sales<>'MEDREP' and";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and g.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and f.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and e.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
			$strquery = "";
		}

		$qslob = $this->db->query(" 
		select * from (
		select  z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city,'current month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
			sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
		  	sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(now(),'%Y') and a.bulan=date_format(now(),'%m') and b.nama_customer is not null and e.nama_regional is not null 
							and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
							and b.areaid='$areaid' $strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						and b.areaid='$areaid' $strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
				and b.areaid='$areaid' $strqueryarea
			) z group by z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city
		union all
		select  z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city,'Last month' item, sum(z.mm) as MM, sum(z.mp) as MP, sum(z.MTI) as MTI, sum(z.HYPER) as HYPER, sum(SUPER) as SUPER, 
				sum(z.mm1) as MM1, sum(z.mp1) as MP1, sum(z.MTI1) as MTI1, sum(z.HYPER1) as HYPER1, sum(SUPER1) as SUPER1,
				sum(z.mm2) as MM2, sum(z.mp2) as MP2, sum(z.MTI2) as MTI2, sum(z.HYPER2) as HYPER2, sum(SUPER2) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 1 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						and b.areaid='$areaid' $strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						and b.areaid='$areaid' $strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
				and b.areaid='$areaid' $strqueryarea
				) z group by z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city
		union all
		select  z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city,'Last3months' item, ifnull(sum(z.mm),0) as MM, ifnull(sum(z.mp),0) as MP, ifnull(sum(z.MTI),0) as MTI, ifnull(sum(z.HYPER),0) as HYPER, ifnull(sum(SUPER),0) as SUPER, 
				ifnull(sum(z.mm1),0) as MM1, ifnull(sum(z.mp1),0) as MP1, ifnull(sum(z.MTI1),0) as MTI1, ifnull(sum(z.HYPER1),0) as HYPER1, ifnull(sum(SUPER1),0) as SUPER1,
				ifnull(sum(z.mm2),0) as MM2, ifnull(sum(z.mp2),0) as MP2, ifnull(sum(z.MTI2),0) as MTI2, ifnull(sum(z.HYPER2),0) as HYPER2, ifnull(sum(SUPER2),0) as SUPER2
		from (
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER,
						0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2					
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 2 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						and b.areaid='$areaid' $strqueryarea
			union all
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 0 mm, 0 mp, 0 mti, 0 hyper, 0 super,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM1,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP1,
					case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI1,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER1,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER1,
						0 mm2, 0 mp2, 0 mti2, 0 hyper2, 0 super2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 3 MONTH),'%m')
						and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
						and b.areaid='$areaid' $strqueryarea
			union all									
			select a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed, 
						0 mm, 0 mp, 0 mti, 0 hyper, 0 super,0 mm1, 0 mp1, 0 mti1, 0 hyper1, 0 super1,
						case when typeid='MINIMARKET' then a.qty_ed*c.h_grosir else 0 end as MM2,
						case when typeid='MODERN PHARMA' then a.qty_ed*c.h_grosir else 0 end as MP2, 
						case when typeid='MTI' then a.qty_ed*c.h_grosir else 0 end as MTI2,
						case when typeid='HYPERMARKET' then a.qty_ed*c.h_grosir else 0 end as HYPER2,
						case when typeid='SUPERMARKET' then a.qty_ed*c.h_grosir else 0 end as SUPER2
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%Y') and a.bulan=date_format(DATE_SUB(now(), INTERVAL 4 MONTH),'%m')
				and b.nama_customer is not null and e.nama_regional is not null and c.brandid like '$brandid' and a.productid like '$productid' and b.regionalid='$regionalid'
				and b.areaid='$areaid' $strqueryarea
			 ) z group by z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city
		) x	order by x.regionalid asc, x.nama_area asc, x.city asc, x.item asc; ");
		$datanat = $qslob->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SLOB Regional '.$nama_regional.' - Area '.$nama_area.'</h3><li>Keterangan : Satuan dalam Juta</a></li>';
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_detail_slob_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_detail_slob_regional_area('.$regionalid.',\''.$nama_regional.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-12"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">City</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y').'</th>';
        $html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-1 month")).'</th>';
		$html .= '<th style="white-space: nowrap;" colspan="5">'.date('F Y',  strtotime("-2 month")).'</th>';
		$html .= '</tr><tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MM</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">MP</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">MTI</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">HYPER</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">SUPER</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$regional='';
		$cntitem=0;
		foreach ($datanat as $vnat) {
				if ($vnat['item']=='current month'){
					if($item=='Last month'){
						$html .= '<tr>';
						$html .= '<td style="white-space: nowrap;">Last 3 Month</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2)/1000000, 2, ',', '.').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2)/1000000, 2, ',', '.').'</td>';
						$html .= '</tr>';
						}

					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_detail_slob_regional_area_city_xls('.$vnat['subareaid'].');">'.$vnat['city'].'</a></td>';
					$mm = 0; $mp = 0; $mti = 0; $hyper = 0; $super = 0;
					$mm1 = 0; $mp1 = 0; $mti1 = 0; $hyper1 = 0; $super1 = 0;
					$mm2 = 0; $mp2 = 0; $mti2 = 0; $hyper2 = 0; $super2 = 0;
				}else{
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;">'.$vnat['item'].'</td>';
				}

				if ($vnat['item']=='Last3months'){
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm+$vnat['MM'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp+$vnat['MP'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti+$vnat['MTI'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper+$vnat['HYPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super+$vnat['SUPER'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1+$vnat['MM1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1+$vnat['MP1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1+$vnat['MTI1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1+$vnat['HYPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1+$vnat['SUPER1'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2+$vnat['MM2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2+$vnat['MP2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2+$vnat['MTI2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2+$vnat['HYPER2'])/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2+$vnat['SUPER2'])/1000000, 2, ',', '.').'</td>';
					$cntitem=0;
				}else{
					$mm = $mm + $vnat['MM']; $mp = $mp + $vnat['MP']; $mti = $mti + $vnat['MTI']; $hyper = $hyper + $vnat['HYPER']; $super = $super+$vnat['SUPER'];
					$mm1 = $mm1 + $vnat['MM1']; $mp1 = $mp1 + $vnat['MP1']; $mti1 = $mti1 + $vnat['MTI1']; $hyper1 = $hyper1 + $vnat['HYPER1']; $super1 = $super1+$vnat['SUPER1'];
					$mm2 = $mm2 + $vnat['MM2']; $mp2 = $mp2 + $vnat['MP2']; $mti2 = $mti2 + $vnat['MTI2']; $hyper2 = $hyper2 + $vnat['HYPER2']; $super2 = $super2+$vnat['SUPER2'];

					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER1']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MM2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MP2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['MTI2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['HYPER2']/1000000, 2, ',', '.').'</td>';
					$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format($vnat['SUPER2']/1000000, 2, ',', '.').'</td>';
					$cntitem++;				
				}
				$html .= '</tr>';
				$item=$vnat['item'];
			}
			if($item=='Last month'){
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;">Last 3 Month</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super1)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mm2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mp2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($mti2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($hyper2)/1000000, 2, ',', '.').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;text-align:right;">'.number_format(($super2)/1000000, 2, ',', '.').'</td>';
				$html .= '</tr>';
			}

		$html .= '</tbody>';
		$html .= '</table></div></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectBrand = $("#brandid-slob-id");
					let uiSelectProd = $("#productid-slob-id");
					let paramsession = common.getCookie("session");

					var brandid = uiSelectBrand.val();
					var productid = uiSelectProd.val();
					var idjabatan = paramsession.idjabatan;
					var usersession = paramsession.username;
					var restrict_level = paramsession.restrict_level;
					var restrict_bu = paramsession.restrict_bu;
				
					function open_detail_slob_national() {
						common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_national"),
								data : "brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}

					function open_detail_slob_regional_area(regionalid,nama_regional) {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_slob_regional_area"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
									common.loadingClose();
								},
								error:function(){
									alert("Load failed");
									common.loadingClose();
								}
							});
					}
										
					function open_detail_slob_regional_area_city_xls(subareaid) {
						common.loading();
						common.direct("drc_dashboard/save_detail_slob_regional_area_city_xls/"+subareaid+"/"+brandid+"/"+productid);
						common.loadingClose();
					}


				</script>
				';
				
		echo $html;

	}

	function save_detail_slob_regional_area_city_xls() {
		$this->load->library('excel');
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', '3600');

		$subareaid = $this->uri->segment('3');
		$brandid = $this->uri->segment('4');
		$productid = $this->uri->segment('5');

		$filename = "Data_SLOB_".date('F Y').".xlsx";

		if($productid=='null'){$productid='%';}
		if($brandid=='null'){$brandid='%';}

		$qslob = $this->db->query(" 
			select a.last_update periode, a.customerid,b.kode_outlet,b.nama_customer,b.classid, b.typeid, a.productid,c.nama_invoice product_name, c.h_grosir hjp, c.brandid, d.brand, 
						b.regionalid,e.nama_regional,b.areaid,f.nama_area, b.subareaid, g.nama_area city, a.qty_ed,b.typeid as channel, h.nama_class
			from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_customer_class h on b.classid=h.classid 
				left join m_product c on a.productid=c.productid left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
				left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
				where a.qty_ed>0 and a.tahun=date_format(now(),'%Y') and a.bulan=date_format(now(),'%m') and b.nama_customer is not null and e.nama_regional is not null 
							and c.brandid like '$brandid' and a.productid like '$productid' and b.subareaid=$subareaid
			; ");
		$datanat = $qslob->result_array();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'SLOB')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Periode')
                    ->setCellValue('C2', 'Regional')
                    ->setCellValue('D2', 'Area')
                    ->setCellValue('E2', 'City')
                    ->setCellValue('F2', 'Channel')
                    ->setCellValue('G2', 'Account')
                    ->setCellValue('H2', 'OutletID')
                    ->setCellValue('I2', 'Kode Outlet')
                    ->setCellValue('J2', 'Outlet')
                    ->setCellValue('K2', 'Brand')
                    ->setCellValue('L2', 'Kode Product')
                    ->setCellValue('M2', 'Product')
                    ->setCellValue('N2', 'Qty Ed')
                    ->setCellValue('O2', 'Value')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($datanat as $vnat) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vnat['periode'])
                                    ->setCellValue('C'.$i, $vnat['nama_regional'])
                                    ->setCellValue('D'.$i, $vnat['nama_area'])
                                    ->setCellValue('E'.$i, $vnat['city'])
                                    ->setCellValue('F'.$i, $vnat['channel'])
                                    ->setCellValue('G'.$i, $vnat['nama_class'])
                                    ->setCellValue('H'.$i, $vnat['customerid'])
                                    ->setCellValue('I'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('J'.$i, $vnat['nama_customer'])
                                    ->setCellValue('K'.$i, $vnat['brand'])
                                    ->setCellValue('L'.$i, $vnat['productid'])
									->setCellValue('M'.$i, $vnat['product_name'])
									->setCellValue('N'.$i, $vnat['qty_ed'])
									->setCellValue('O'.$i, ($vnat['hjp']*$vnat['qty_ed']));
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

	}

	function save_absensi_detail() {
		$this->load->library('excel');
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', '3600');
		
		$salesmanid = $this->uri->segment('3');
		$tahun = $this->uri->segment('4');
		$bulan = $this->uri->segment('5');
		$tanggal = $this->uri->segment('6');
		
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$tempsql="between '$tahun-$bulan-01' and LAST_DAY('$tahun-$bulan-01')";
		}else{
			$tempsql="= '$tahun-$bulan-$tanggal'";
		}
		//echo $tempsql;
		$filename = "Data_Absensi_".$salesmanid."_".$bulan.$tahun.".xlsx";
		//echo $salesmanid.'/'.$tahun.'/'.$bulan.'/'.$tanggal.'/'.$filename.'/'.$querystr;
		
		$sqlactcall = $this->db->query(" 
										select tsrt.periode, tsrt.salesmanid ,tsrt.nama_salesman, DATE_FORMAT((select min(check_in) from t_sales_rrk_trans where salesmanid=tsrt.salesmanid and periode=tsrt.periode), '%H:%i:%s') check_in,
										DATE_FORMAT((select max(check_out) from t_sales_rrk_trans where salesmanid=tsrt.salesmanid and periode=tsrt.periode), '%H:%i:%s') check_out,
										timediff(DATE_FORMAT((select max(check_out) from t_sales_rrk_trans where salesmanid=tsrt.salesmanid and periode=tsrt.periode), '%H:%i:%s'),DATE_FORMAT((select min(check_in) from t_sales_rrk_trans where salesmanid=tsrt.salesmanid and periode=tsrt.periode), '%H:%i:%s')) lama_bekerja,
										COUNT(1) jml_visit 
										from t_sales_rrk_trans tsrt
										where tsrt.salesmanid = '$salesmanid' and tsrt.periode $tempsql
										group by tsrt.periode, tsrt.salesmanid ,tsrt.nama_salesman
										;");

		$resdata = $sqlactcall->result_array();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Absensi TPE')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Periode')
                    ->setCellValue('C2', 'Kode TPE')
                    ->setCellValue('D2', 'Nama TPE')
                    ->setCellValue('E2', 'Check In')
                    ->setCellValue('F2', 'Check Out')
                    ->setCellValue('G2', 'Jam Kerja')
                    ->setCellValue('H2', 'Jumlah Visit')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($resdata as $vnat) {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vnat['periode'])
                                    ->setCellValue('C'.$i, $vnat['salesmanid'])
                                    ->setCellValue('D'.$i, $vnat['nama_salesman'])
                                    ->setCellValue('E'.$i, $vnat['check_in'])
                                    ->setCellValue('F'.$i, $vnat['check_out'])
                                    ->setCellValue('G'.$i, $vnat['lama_bekerja'])
                                    ->setCellValue('H'.$i, $vnat['jml_visit'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

	}

	function save_detail_actual_call_xls() {
		$this->load->library('excel');
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', '3600');
		
		$salesmanid = $this->uri->segment('3');
		$tahun = $this->uri->segment('4');
		$bulan = $this->uri->segment('5');
		$tanggal = $this->uri->segment('6');
		//echo $salesmanid.'/'.$tahun.'/'.$bulan.'/'.$tanggal;
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$tempsql="between '$tahun-$bulan-01' and LAST_DAY('$tahun-$bulan-01')";
		}else{
			$tempsql="= '$tahun-$bulan-$tanggal'";
		}
		//echo $tempsql;
		$sqlactcall = $this->db->query(" 
										select * from (
											select 
											rrk_trans.periode,
											rrk_trans.customerid, cst.kode_outlet,
											cst.nama_customer,
											cst.alamat, cst.typeid channel, (select nama_class from m_customer_class where classid=cst.classid) account, 
											r.nama_regional, ar.nama_area, sa.nama_area city, rrk_trans.keterangan,
											sum(ifnull(dtl.netto,0)) as total_netto,
											DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
											DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s'),DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s')) lama_kunjungan,
											case when rrk.customerid is null then 'ExtraCall' 
												when rrk.customerid is not null then 'Call' 
												when rrk.customerid is not null and rrk_trans.check_in is null then 'Jadwal' end as flag,
											case when cst.longitude=0 or rrk_trans.longitude_cell=0 or cst.longitude is null or rrk_trans.longitude_cell is null then 'NA' 
											else ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) 
											end as jarak, salesamn.salesmanid kode_gff, salesamn.nama_salesman nama_gff
											from 
											t_sales_rrk_trans rrk_trans left join t_sales_rrk rrk on 
											rrk_trans.siteid = rrk.siteid and rrk_trans.periode = rrk.periode and rrk_trans.salesmanid = rrk.salesmanid and rrk_trans.customerid = rrk.customerid
											left join t_sales_master sls 
											on rrk_trans.siteid = sls.siteid and rrk_trans.periode = sls.tanggal and rrk_trans.salesmanid = sls.salesmanid 
											and rrk_trans.customerid = sls.customerid and rrk_trans.salesmanid = sls.salesmanid and rrk_trans.customerid = sls.customerid
											left JOIN t_sales_detail dtl 
											on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales 
											left JOIN m_customer_ob custob ON rrk_trans.salesmanid = custob.salesmanid and rrk_trans.customerid = custob.customerid
											left JOIN m_customer cst on rrk_trans.siteid = cst.siteid and rrk_trans.customerid = cst.customerid 
											left JOIN m_sales_salesman salesamn on rrk_trans.siteid = salesamn.siteid and rrk_trans.salesmanid = salesamn.salesmanid 
											left JOIN m_product product on dtl.productid = product.productid 
											left join m_area_regional r on cst.regionalid=r.regionalid
											left join m_area_areasite ar on cst.areaid = ar.areaid
											left join m_area_subarea sa on cst.subareaid=sa.subareaid
											where rrk_trans.salesmanid = '$salesmanid' AND
												rrk_trans.periode $tempsql
											group by 
												rrk_trans.periode,
												rrk_trans.customerid,
												cst.nama_customer,
												cst.alamat
										union all
											select 
											rrk.periode,
											rrk.customerid, cst.kode_outlet,
											cst.nama_customer,
											cst.alamat, cst.typeid channel, (select nama_class from m_customer_class where classid=cst.classid) account, 
											r.nama_regional, ar.nama_area, sa.nama_area city, rrk_trans.keterangan,
											0 as total_netto,
											DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
											DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s'),DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s')) lama_kunjungan,
											'Jadwal' as flag,
											case when cst.longitude=0 or rrk_trans.longitude_cell=0 or cst.longitude is null or rrk_trans.longitude_cell is null then 'NA' 
											else ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) 
											end as jarak, salesamn.salesmanid kode_gff, salesamn.nama_salesman nama_gff
											from 
											t_sales_rrk rrk left join t_sales_rrk_trans rrk_trans on rrk.siteid = rrk_trans.siteid and rrk.periode = rrk_trans.periode and rrk.salesmanid = rrk_trans.salesmanid 
											and rrk.customerid = rrk_trans.customerid left JOIN m_customer_ob custob ON rrk.salesmanid = custob.salesmanid and rrk.customerid = custob.customerid
											left JOIN m_customer cst on rrk.siteid = cst.siteid and rrk.customerid = cst.customerid
											left join m_area_regional r on cst.regionalid=r.regionalid
											left join m_area_areasite ar on cst.areaid = ar.areaid
											left join m_area_subarea sa on cst.subareaid=sa.subareaid
											left JOIN m_sales_salesman salesamn on rrk.siteid = salesamn.siteid and rrk.salesmanid = salesamn.salesmanid
											where rrk.salesmanid = '$salesmanid' AND
												rrk.periode in (select periode from t_sales_absensi where salesmanid = '$salesmanid' and status in ('H','C') and periode $tempsql ) AND rrk_trans.check_in is null         
											group by 
												rrk.periode,
												rrk.customerid,
												cst.nama_customer,
												cst.alamat
										) fnl order by fnl.check_in desc
										;");

										//echo $sqlactcall;
		$resdata = $sqlactcall->result_array();
		//echo $this->db->last_query();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Actual Call')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Periode')
                    ->setCellValue('C2', 'Outlet ID')
                    ->setCellValue('D2', 'Kode Outlet')
                    ->setCellValue('E2', 'Nama Outlet')
                    ->setCellValue('F2', 'Alamat')
                    ->setCellValue('G2', 'Channel')
                    ->setCellValue('H2', 'Account')
                    ->setCellValue('I2', 'Regional')
                    ->setCellValue('J2', 'Area')
                    ->setCellValue('K2', 'City')
                    ->setCellValue('L2', 'Check In')
                    ->setCellValue('M2', 'Check Out')
                    ->setCellValue('N2', 'Lama Kunjungan')
                    ->setCellValue('O2', 'Akurasi(Km)')
                    ->setCellValue('P2', 'Keterangan')
                    ->setCellValue('Q2', 'Status')
                    ->setCellValue('R2', 'Kode TPE')
                    ->setCellValue('S2', 'Nama TPE')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($resdata as $vnat) {
						$namagff = $vnat['nama_gff'];
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vnat['periode'])
                                    ->setCellValue('C'.$i, $vnat['customerid'])
                                    ->setCellValue('D'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('E'.$i, $vnat['nama_customer'])
                                    ->setCellValue('F'.$i, $vnat['alamat'])
                                    ->setCellValue('G'.$i, $vnat['channel'])
                                    ->setCellValue('H'.$i, $vnat['account'])
                                    ->setCellValue('I'.$i, $vnat['nama_regional'])
                                    ->setCellValue('J'.$i, $vnat['nama_area'])
                                    ->setCellValue('K'.$i, $vnat['city'])
                                    ->setCellValue('L'.$i, $vnat['check_in'])
                                    ->setCellValue('M'.$i, $vnat['check_out'])
									->setCellValue('N'.$i, $vnat['lama_kunjungan'])
									->setCellValue('O'.$i, $vnat['jarak'])
									->setCellValue('P'.$i, $vnat['keterangan'])
									->setCellValue('Q'.$i, $vnat['flag'])
									->setCellValue('R'.$i, $vnat['kode_gff'])
									->setCellValue('S'.$i, $vnat['nama_gff'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
		$filename = "Actual_Call_".$bulan.$tahun."_".$salesmanid."-".$namagff.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

	}

	function save_detail_actual_call_city_xls() {
		$this->load->library('excel');
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', '3600');
		
		$subareaid = $this->uri->segment('3');
		$tahun = $this->uri->segment('4');
		$bulan = $this->uri->segment('5');
		$tanggal = $this->uri->segment('6');
		//echo $subareaid.'/'.$salesmanid.'/'.$tahun.'/'.$bulan.'/'.$tanggal;
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$tempsql="between '$tahun-$bulan-01' and LAST_DAY('$tahun-$bulan-01')";
		}else{
			$tempsql="= '$tahun-$bulan-$tanggal'";
		}
		//echo $tempsql;
		$sqlactcall = $this->db->query(" 
										select * from (
											select 
											rrk_trans.periode,
											rrk_trans.customerid, cst.kode_outlet,
											cst.nama_customer,
											cst.alamat, cst.typeid channel, (select nama_class from m_customer_class where classid=cst.classid) account, 
											r.nama_regional, ar.nama_area, sa.nama_area city, rrk_trans.keterangan,
											sum(ifnull(dtl.netto,0)) as total_netto,
											DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
											DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s'),DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s')) lama_kunjungan,
											case when rrk.customerid is null then 'ExtraCall' 
												when rrk.customerid is not null then 'Call' 
												when rrk.customerid is not null and rrk_trans.check_in is null then 'Jadwal' end as flag,
											case when cst.longitude=0 or rrk_trans.longitude_cell=0 or cst.longitude is null or rrk_trans.longitude_cell is null then 'NA' 
											else ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) 
											end as jarak, salesamn.salesmanid kode_gff, salesamn.nama_salesman nama_gff, 'H' status_att
											from 
											t_sales_rrk_trans rrk_trans left join t_sales_rrk rrk on 
											rrk_trans.siteid = rrk.siteid and rrk_trans.periode = rrk.periode and rrk_trans.salesmanid = rrk.salesmanid and rrk_trans.customerid = rrk.customerid
											left join t_sales_master sls 
											on rrk_trans.siteid = sls.siteid and rrk_trans.periode = sls.tanggal and rrk_trans.salesmanid = sls.salesmanid 
											and rrk_trans.customerid = sls.customerid and rrk_trans.salesmanid = sls.salesmanid and rrk_trans.customerid = sls.customerid
											left JOIN t_sales_detail dtl 
											on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales 
											left JOIN m_customer_ob custob ON rrk_trans.salesmanid = custob.salesmanid and rrk_trans.customerid = custob.customerid
											left JOIN m_customer cst on rrk_trans.siteid = cst.siteid and rrk_trans.customerid = cst.customerid 
											left JOIN m_sales_salesman salesamn on rrk_trans.siteid = salesamn.siteid and rrk_trans.salesmanid = salesamn.salesmanid 
											left JOIN m_product product on dtl.productid = product.productid 
											left join m_area_regional r on cst.regionalid=r.regionalid
											left join m_area_areasite ar on cst.areaid = ar.areaid
											left join m_area_subarea sa on cst.subareaid=sa.subareaid
											where rrk_trans.salesmanid in (select salesmanid from m_sales_salesman where aktif=1 and subareaid=$subareaid) AND salesamn.tipe_sales not in ('MEDREP','FC','ADMIN') AND
												rrk_trans.periode $tempsql
											group by 
												rrk_trans.periode,
												rrk_trans.customerid,
												cst.nama_customer,
												cst.alamat
										union all
											select 
											rrk.periode,
											rrk.customerid, cst.kode_outlet,
											cst.nama_customer,
											cst.alamat, cst.typeid channel, (select nama_class from m_customer_class where classid=cst.classid) account, 
											r.nama_regional, ar.nama_area, sa.nama_area city, rrk_trans.keterangan,
											0 as total_netto,
											DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
											DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s'),DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s')) lama_kunjungan,
											'Jadwal' as flag,
											case when cst.longitude=0 or rrk_trans.longitude_cell=0 or cst.longitude is null or rrk_trans.longitude_cell is null then 'NA' 
											else ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) 
											end as jarak,
											salesamn.salesmanid kode_gff, salesamn.nama_salesman nama_gff, att.status status_att
											from 
											t_sales_rrk rrk left join t_sales_rrk_trans rrk_trans on rrk.siteid = rrk_trans.siteid and rrk.periode = rrk_trans.periode and rrk.salesmanid = rrk_trans.salesmanid 
											and rrk.customerid = rrk_trans.customerid left JOIN m_customer_ob custob ON rrk.salesmanid = custob.salesmanid and rrk.customerid = custob.customerid
											left JOIN m_customer cst on rrk.siteid = cst.siteid and rrk.customerid = cst.customerid
											left join m_area_regional r on cst.regionalid=r.regionalid
											left join m_area_areasite ar on cst.areaid = ar.areaid
											left join m_area_subarea sa on cst.subareaid=sa.subareaid
											left JOIN m_sales_salesman salesamn on rrk.siteid = salesamn.siteid and rrk.salesmanid = salesamn.salesmanid
											left join t_sales_absensi att on att.salesmanid=rrk.salesmanid and att.periode=rrk.periode
											where att.status in ('H','C') and rrk.salesmanid in (select salesmanid from m_sales_salesman where aktif=1 and subareaid=$subareaid) AND salesamn.tipe_sales not in ('MEDREP','FC','ADMIN') AND 
												rrk.periode in (select periode from t_sales_absensi where salesmanid in (select salesmanid from m_sales_salesman where aktif=1 and subareaid=$subareaid) and status in ('H','C') and periode $tempsql ) AND rrk_trans.check_in is null
											group by
												rrk.periode,
												rrk.customerid,
												cst.nama_customer,
												cst.alamat
										) fnl order by fnl.check_in desc
										;");

		//echo $sqlactcall;
		$resdata = $sqlactcall->result_array();
		//echo $this->db->last_query();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Actual Call')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Periode')
                    ->setCellValue('C2', 'Outlet ID')
                    ->setCellValue('D2', 'Kode Outlet')
                    ->setCellValue('E2', 'Nama Outlet')
                    ->setCellValue('F2', 'Alamat')
                    ->setCellValue('G2', 'Channel')
                    ->setCellValue('H2', 'Account')
                    ->setCellValue('I2', 'Regional')
                    ->setCellValue('J2', 'Area')
                    ->setCellValue('K2', 'City')
                    ->setCellValue('L2', 'Check In')
                    ->setCellValue('M2', 'Check Out')
                    ->setCellValue('N2', 'Lama Kunjungan')
                    ->setCellValue('O2', 'Akurasi(Km)')
                    ->setCellValue('P2', 'Keterangan')
                    ->setCellValue('Q2', 'Status')
                    ->setCellValue('R2', 'Kode TPE')
                    ->setCellValue('S2', 'Nama TPE')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($resdata as $vnat) {
						$namacity = $vnat['city'];
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vnat['periode'])
                                    ->setCellValue('C'.$i, $vnat['customerid'])
                                    ->setCellValue('D'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('E'.$i, $vnat['nama_customer'])
                                    ->setCellValue('F'.$i, $vnat['alamat'])
                                    ->setCellValue('G'.$i, $vnat['channel'])
                                    ->setCellValue('H'.$i, $vnat['account'])
                                    ->setCellValue('I'.$i, $vnat['nama_regional'])
                                    ->setCellValue('J'.$i, $vnat['nama_area'])
                                    ->setCellValue('K'.$i, $vnat['city'])
                                    ->setCellValue('L'.$i, $vnat['check_in'])
                                    ->setCellValue('M'.$i, $vnat['check_out'])
									->setCellValue('N'.$i, $vnat['lama_kunjungan'])
									->setCellValue('O'.$i, $vnat['jarak'])
									->setCellValue('P'.$i, $vnat['keterangan'])
									->setCellValue('Q'.$i, $vnat['flag'])
									->setCellValue('R'.$i, $vnat['kode_gff'])
									->setCellValue('S'.$i, $vnat['nama_gff'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
		$filename = "Actual_Call_".$bulan.$tahun."_".$namacity.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

	}
	
	function save_detail_actual_call_national_xls() {
		$this->load->library('excel');
		ini_set('memory_limit', '3052M');
		ini_set('max_execution_time', '3600');
		
		$tahun = $this->uri->segment('3');
		$bulan = $this->uri->segment('4');
		$tanggal = $this->uri->segment('5');
		$usersession = $this->uri->segment('6');
		$restrict_level = $this->uri->segment('7');
		$restrict_bu = $this->uri->segment('8');

		//echo $subareaid.'/'.$salesmanid.'/'.$tahun.'/'.$bulan.'/'.$tanggal;
		//$periode = $tahun.'-'.$bulan.'-'.$tanggal;
		if ($tanggal=='All'){
			$tempsql="between '$tahun-$bulan-01' and LAST_DAY('$tahun-$bulan-01')";
		}else{
			$tempsql="= '$tahun-$bulan-$tanggal'";
		}
		
		if ($restrict_bu=='GT'){
			$querybu = " and tipe_sales='MEDREP'";
		}elseif ($restrict_bu=='MT'){
			$querybu = " and tipe_sales<>'MEDREP'";
		}else{
			$querybu = "";
		}

		if ($restrict_level=='4'){
            $strqueryarea = " and subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
			
		}
		else if ($restrict_level=='3'){
            $strqueryarea = " and areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                )";
		}
		else if ($restrict_level=='2'){
            $strqueryarea = " and regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
		}
		
		//echo $tempsql;
		$sqlactcall = $this->db->query(" 
										select * from (
											select 
											rrk_trans.periode,
											rrk_trans.customerid, cst.kode_outlet,
											cst.nama_customer,
											cst.alamat, cst.typeid channel, (select nama_class from m_customer_class where classid=cst.classid) account, 
											r.nama_regional, ar.nama_area, sa.nama_area city, rrk_trans.keterangan,
											sum(ifnull(dtl.netto,0)) as total_netto,
											DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
											DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s'),DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s')) lama_kunjungan,
											case when rrk.customerid is null then 'ExtraCall' 
												when rrk.customerid is not null then 'Call' 
												when rrk.customerid is not null and rrk_trans.check_in is null then 'Jadwal' end as flag,
											case when cst.longitude=0 or rrk_trans.longitude_cell=0 or cst.longitude is null or rrk_trans.longitude_cell is null then 'NA' 
											else ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) 
											end as jarak, salesamn.salesmanid kode_gff, salesamn.nama_salesman nama_gff, 'H' status_att
											from 
											t_sales_rrk_trans rrk_trans left join t_sales_rrk rrk on 
											rrk_trans.siteid = rrk.siteid and rrk_trans.periode = rrk.periode and rrk_trans.salesmanid = rrk.salesmanid and rrk_trans.customerid = rrk.customerid
											left join t_sales_master sls 
											on rrk_trans.siteid = sls.siteid and rrk_trans.periode = sls.tanggal and rrk_trans.salesmanid = sls.salesmanid 
											and rrk_trans.customerid = sls.customerid and rrk_trans.salesmanid = sls.salesmanid and rrk_trans.customerid = sls.customerid
											left JOIN t_sales_detail dtl 
											on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales 
											left JOIN m_customer_ob custob ON rrk_trans.salesmanid = custob.salesmanid and rrk_trans.customerid = custob.customerid
											left JOIN m_customer cst on rrk_trans.siteid = cst.siteid and rrk_trans.customerid = cst.customerid 
											left JOIN m_sales_salesman salesamn on rrk_trans.siteid = salesamn.siteid and rrk_trans.salesmanid = salesamn.salesmanid 
											left JOIN m_product product on dtl.productid = product.productid 
											left join m_area_regional r on cst.regionalid=r.regionalid
											left join m_area_areasite ar on cst.areaid = ar.areaid
											left join m_area_subarea sa on cst.subareaid=sa.subareaid
											where rrk_trans.salesmanid in (select salesmanid from m_sales_salesman where aktif=1 $strqueryarea) AND
												rrk_trans.periode $tempsql
											group by 
												rrk_trans.periode,
												rrk_trans.customerid,
												cst.nama_customer,
												cst.alamat
										union all
											select 
											rrk.periode,
											rrk.customerid, cst.kode_outlet,
											cst.nama_customer,
											cst.alamat, cst.typeid channel, (select nama_class from m_customer_class where classid=cst.classid) account, 
											r.nama_regional, ar.nama_area, sa.nama_area city, rrk_trans.keterangan,
											0 as total_netto,
											DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
											DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(rrk_trans.check_out, '%H:%i:%s'),DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s')) lama_kunjungan,
											'Jadwal' as flag,
											case when cst.longitude=0 or rrk_trans.longitude_cell=0 or cst.longitude is null or rrk_trans.longitude_cell is null then 'NA' 
											else ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) 
											end as jarak,
											salesamn.salesmanid kode_gff, salesamn.nama_salesman nama_gff, att.status status_att
											from 
											t_sales_rrk rrk left join t_sales_rrk_trans rrk_trans on rrk.siteid = rrk_trans.siteid and rrk.periode = rrk_trans.periode and rrk.salesmanid = rrk_trans.salesmanid 
											and rrk.customerid = rrk_trans.customerid left JOIN m_customer_ob custob ON rrk.salesmanid = custob.salesmanid and rrk.customerid = custob.customerid
											left JOIN m_customer cst on rrk.siteid = cst.siteid and rrk.customerid = cst.customerid
											left join m_area_regional r on cst.regionalid=r.regionalid
											left join m_area_areasite ar on cst.areaid = ar.areaid
											left join m_area_subarea sa on cst.subareaid=sa.subareaid
											left JOIN m_sales_salesman salesamn on rrk.siteid = salesamn.siteid and rrk.salesmanid = salesamn.salesmanid
											left join t_sales_absensi att on att.salesmanid=rrk.salesmanid and att.periode=rrk.periode
											where att.status in ('H','C') and rrk.salesmanid in (select salesmanid from m_sales_salesman where aktif=1 $strqueryarea) AND
												rrk.periode in (select periode from t_sales_absensi where salesmanid in (select salesmanid from m_sales_salesman where aktif=1 $strqueryarea) 
												and status in ('H','C') and periode $tempsql ) AND rrk_trans.check_in is null
											group by
												rrk.periode,
												rrk.customerid,
												cst.nama_customer,
												cst.alamat
										) fnl order by fnl.check_in desc
										;");

		//echo $sqlactcall;
		$resdata = $sqlactcall->result_array();
		//echo $this->db->last_query();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Actual Call')
                    ->setCellValue('A2', 'No.')
                    ->setCellValue('B2', 'Periode')
                    ->setCellValue('C2', 'Outlet ID')
                    ->setCellValue('D2', 'Kode Outlet')
                    ->setCellValue('E2', 'Nama Outlet')
                    ->setCellValue('F2', 'Alamat')
                    ->setCellValue('G2', 'Channel')
                    ->setCellValue('H2', 'Account')
                    ->setCellValue('I2', 'Regional')
                    ->setCellValue('J2', 'Area')
                    ->setCellValue('K2', 'City')
                    ->setCellValue('L2', 'Check In')
                    ->setCellValue('M2', 'Check Out')
                    ->setCellValue('N2', 'Lama Kunjungan')
                    ->setCellValue('O2', 'Akurasi(Km)')
                    ->setCellValue('P2', 'Keterangan')
                    ->setCellValue('Q2', 'Status')
                    ->setCellValue('R2', 'Kode TPE')
                    ->setCellValue('S2', 'Nama TPE')
					;
                    $i = 3;
					$no = 1;
					
					foreach ($resdata as $vnat) {
						$namacity = $vnat['city'];
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.$i, $no)
                                    ->setCellValue('B'.$i, $vnat['periode'])
                                    ->setCellValue('C'.$i, $vnat['customerid'])
                                    ->setCellValue('D'.$i, $vnat['kode_outlet'])
                                    ->setCellValue('E'.$i, $vnat['nama_customer'])
                                    ->setCellValue('F'.$i, $vnat['alamat'])
                                    ->setCellValue('G'.$i, $vnat['channel'])
                                    ->setCellValue('H'.$i, $vnat['account'])
                                    ->setCellValue('I'.$i, $vnat['nama_regional'])
                                    ->setCellValue('J'.$i, $vnat['nama_area'])
                                    ->setCellValue('K'.$i, $vnat['city'])
                                    ->setCellValue('L'.$i, $vnat['check_in'])
                                    ->setCellValue('M'.$i, $vnat['check_out'])
									->setCellValue('N'.$i, $vnat['lama_kunjungan'])
									->setCellValue('O'.$i, $vnat['jarak'])
									->setCellValue('P'.$i, $vnat['keterangan'])
									->setCellValue('Q'.$i, $vnat['flag'])
									->setCellValue('R'.$i, $vnat['kode_gff'])
									->setCellValue('S'.$i, $vnat['nama_gff'])
									;
						$i++;
						$no++;
					}
        // Redirect output to a client's web browser (Excel2007)
		$filename = "Actual_Call_".$bulan.$tahun."_National.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;

	}

}
