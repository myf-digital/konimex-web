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
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and c.subareaid in (select distinct b.subareaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";

			}else{
				$strquery1 = " where a.periode = '$periode' and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
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
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$usersession."')
				) ";
				$strpengali = "*(date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end))";
				$strqueryprodgff = " and a.periode between '".$tahun."-".$bulan."-01' and LAST_DAY('".$tahun."-".$bulan."-01') 
									 and d.areaid in (select distinct b.areaid from app_resource a 
									 left join app_restrict_location b on a.resource_id=b.resource_id where a.username='".$usersession."')";
			}else{
				$strquery1 = " where a.periode = '$periode' and a.status='H' and 
				b.salesmanid in (select salesmanid from m_sales_salesman where$querybu regionalid in (select distinct b.regionalid from  
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
												else 'HF' end tipe, '' checkin, '' checkout, 
												concat(a.description_in,'-', a.description_out) keterangan, 0 _pjp, 0 _call, 0 _extra_call, 0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos  
										from s_absensi a 
										where a.date = (select tanggal from m_setup_site)
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
										from t_sales_rrk_trans a where a.periode = (select tanggal from m_setup_site)
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
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
									$strquery1
									) x;
									");
		//echo $this->db->last_query();
		$dataatt = $qattnat->result_array();

		##query get performance MEDREP national
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
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($hcsfgt, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($hcspg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($hcmd, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($hcsfmt, 0, '.', ',').' %</td>';
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
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($attsfgt, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($attspg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($attmd, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($attsfmt, 0, '.', ',').' %</td>';
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
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format(@$acallsfgt, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format(@$acallspg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format(@$acallmd, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format(@$acallsfmt, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			//$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';		
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
					$html .= '<td style="white-space: nowrap;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_hc_detail_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
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
		
		$qattreg = $this->db->query(" 
									select x.regionalid, x.nama_regional, 'MEDREP Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
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
									select x.regionalid, x.nama_regional, 'MEDREP Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
									(select e.regionalid, e.nama_regional,
									case when b.tipe_sales='MERCHANDISER' then 1 else 0 end md,
									case when b.tipe_sales='SPG' then 1 else 0 end spg,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfmt,
									case when b.tipe_sales='MEDREP' then 1 else 0 end sfgt
									from 
									t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
									left join m_area_subarea c on b.subareaid=c.subareaid
									left join m_area_areasite d on c.areaid=d.areaid
									left join m_area_regional e on e.regionalid=d.regionalid
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';		
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
					$html .= '<td style="white-space: nowrap;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='MEDREP Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_att_perregional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='MEDREP Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
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
		
		##query get performance MEDREP regional
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';		
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
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';				
				}
			}
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_act_call_regional_area('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\');">'.$vreg['nama_regional'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
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
					$html .= '<td style="white-space: nowrap;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_hc_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
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

		$qattreg = $this->db->query(" 
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'MEDREP Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
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
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, 'MEDREP Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">'.$nama_regional.'</th>';
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
					$html .= '<td style="white-space: nowrap;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='MEDREP Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="attnational" onclick="open_preview_att_perregional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='MEDREP Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
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
		
		##query get performance MEDREP regional
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
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
				if ($vreg['nama_area']!=$regional){
					if ($pjpmdreg!=0){ $actcallmdreg = @$callmdreg/@$pjpmdreg *100; } else {$actcallmdreg=0;}
					if ($pjpspgreg!=0){ $actcallspgreg = @$callspgreg/@$pjpspgreg *100;}else {$actcallspgreg=0;}
					if ($pjpsfmtreg!=0){ $actcallsfmtreg = @$callsfmtreg/@$pjpsfmtreg *100;}else {$actcallsfmtreg=0;}
					if ($pjpsfgtreg!=0){ $actcallsfgtreg = @$callsfgtreg/@$pjpsfgtreg *100;}else {$actcallsfgtreg=0;}
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">% Call On PJP</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';				
				}
			}
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_act_call_regional_area_city('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\');">'.$vreg['nama_area'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">'.$nama_area.'</th>';
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
					$html .= '<td style="white-space: nowrap;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='Quota'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_hc_detail_regional_area_city_gff('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['subarea'].'\');">'.$vreg['subarea'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($hcsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($hcspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($hcmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($hcsfmtreg, 0, '.', ',').' %</td>';
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

		$qattreg = $this->db->query(" 
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city, 'MEDREP Aktif' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
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
									select x.regionalid, x.nama_regional, x.areaid, x.nama_area, x.subareaid, x.city, 'MEDREP Hadir' item, sum(x.md) jmlmd, sum(x.spg) jmlspg, sum(x.sfmt) jmlsfmt, sum(x.sfgt) jmlsfgt from
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;" rowspan="2">'.$nama_area.'</th>';
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
					$html .= '<td style="white-space: nowrap;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
				}else{
					$html .= '<td style="white-space: nowrap;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
					$html .= '<td style="white-space: nowrap;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
				}
				$html .= '</tr>';
				}
			}
			if ($vreg['item']=='MEDREP Aktif'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="attnational" onclick="open_att_detail_regional_area_city_gff('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['city'].'\');">'.$vreg['city'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
					}
					$html .= '</tr>';
					$quotamdreg=$vreg['jmlmd'];
					$quotaspgreg=$vreg['jmlspg'];
					$quotasfmtreg=$vreg['jmlsfmt'];
					$quotasfgtreg=$vreg['jmlsfgt'];

				}else if ($vreg['item']=='MEDREP Hadir'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfgt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlspg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlmd'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['jmlsfmt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($attsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($attspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($attmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($attsfmtreg, 0, '.', ',').' %</td>';
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
		
		##query get performance MEDREP regional
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
										where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
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
										where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
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
										where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
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
												where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
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
												where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid $strqueryprodgff
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
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
					}
					$html .= '</tr>';				
				}
			}
			if ($vreg['header']=='PJP'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_preview_act_call_regional_area_city_gff('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['city'].'\');">'.$vreg['city'].'</a></td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['header'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_gt'], 0, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['spg'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['md'], 0, '.', ',').' </td>';
						$html .= '<td style="white-space: nowrap;">'.number_format($vreg['sls_mt'], 0, '.', ',').' </td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($actcallsfmtreg, 0, '.', ',').' %</td>';
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
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfgtreg, 0, '.', ',').' %</td>';
			}else{
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallspgreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallmdreg, 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;">'.number_format($totactcallsfmtreg, 0, '.', ',').' %</td>';
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
			$querybu = " b.tipe_sales<>'MEDREP' and";
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
		
		##query get performance MEDREP
		/*$qpfgffreg = $this->db->query(" select * from (
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city, '1' as _order,
											b.salesmanid,b.nama_salesman,b.tipe_sales,sum(a.pjp) PJP, 0 CallOnPJP
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area,b.salesmanid,b.nama_salesman,b.tipe_sales
										union all
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city, '2' as _order,
												b.salesmanid,b.nama_salesman,b.tipe_sales,0 PJP, sum(a.call) CallOnPJP
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area,b.salesmanid,b.nama_salesman,b.tipe_sales
										union all
										select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city, '3' as _order,
												b.salesmanid,b.nama_salesman,b.tipe_sales,0 PJP, 0 CallOnPJP, sum(a.extra_call) EXT_CALL
										from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
										left join m_area_subarea c on b.subareaid=c.subareaid
										left join m_area_areasite d on b.areaid=d.areaid
										left join m_area_regional e on b.regionalid=e.regionalid
										where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
										group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area,b.salesmanid,b.nama_salesman,b.tipe_sales
										union all
										select y.regionalid,y.nama_regional,y.areaid,y.nama_area,y.subareaid,y.city,'Total Call' header, '4' as _order,
												y.salesmanid,y.nama_salesman,y.tipe_sales,
												sum(y._call_MD) _call_MD,
												sum(y._call_SPG) _call_SPG,
												sum(y._call_SLS_MT) _call_SLS_MT,
												sum(y._call_SLS_GT) _call_SLS_GT
										from 
											(
												select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'Call On PJP' header,
													b.salesmanid,b.nama_salesman,b.tipe_sales,
													sum(case when b.tipe_sales='MERCHANDISER' then a.call else 0 end) _call_MD,
													sum(case when b.tipe_sales='SPG' then a.call else 0 end) _call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.call else 0 end) _call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
												group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area,b.salesmanid,b.nama_salesman,b.tipe_sales
												union all
												select e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area city,'EXT_CALL' header,
													b.salesmanid,b.nama_salesman,b.tipe_sales,
													sum(case when b.tipe_sales='MERCHANDISER' then a.extra_call else 0 end) _ext_call_MD,
													sum(case when b.tipe_sales='SPG' then a.extra_call else 0 end)  _ext_call_SPG,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_MT,
													sum(case when b.tipe_sales='MEDREP' then a.extra_call else 0 end)  _ext_call_SLS_GT
												from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid
												left join m_area_subarea c on b.subareaid=c.subareaid
												left join m_area_areasite d on b.areaid=d.areaid
												left join m_area_regional e on b.regionalid=e.regionalid
												where b.aktif=1 and e.regionalid=$regionalid and d.areaid=$areaid and c.subareaid=$subareaid $strqueryprodgff
												group by e.regionalid,e.nama_regional,d.areaid,d.nama_area,c.subareaid,c.nama_area,b.salesmanid,b.nama_salesman,b.tipe_sales
											) y group by y.regionalid,y.nama_regional,y.areaid,y.nama_area,y.subareaid,y.city,y.salesmanid,y.nama_salesman,y.tipe_sales
										) x
										order by x.regionalid asc, x.areaid asc, x.subareaid asc, x.tipe_sales asc, x.salesmanid asc, x._order asc
									");
		echo $this->db->last_query();*/
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
		$html ='<div class="box-body" id="regional"><h3>Summary Actual Call By MEDREP City '.$city.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_act_call_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-9"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;width:40px;" rowspan=2>No.</th>';
        $html .= '<th style="white-space: nowrap;width:200px;" rowspan=2>Position</th>';
        $html .= '<th style="white-space: nowrap;width:300px;" rowspan=2">User MEDREP</th>';
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
				$html .= '<td style="white-space: nowrap;">'.$vreg['salesmanid'].'-'.$vreg['nama_salesman'].'</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['PJP'], 0, '.', ',').'</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['CallOnPJP'], 0, '.', ',').' </td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['TOTAL_CALL'], 0, '.', ',').' </td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['%CallOnPJP'], 0, '.', ',').' %</td>';
				$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['%TotalCall'], 0, '.', ',').' %</td>';
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
		}else{
			$strqueryarea1 = " where $querybu a.periode = '$periode' and a.status='H' and e.regionalid = $regionalid and d.areaid=$areaid and c.subareaid=$subareaid";
		}												

		$qattreg = $this->db->query(" 
									select e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area city,b.salesmanid,b.nama_salesman,b.tipe_sales, 
											date_format('$periodedate','%d')-FLOOR(date_format('$periodedate','%d')/7)-(case when date_format('$periodedate','%d') > 25 then (select jml_libur from setup_jumlah_harilibur where tahun='$tahun' and bulan='$bulan') else 0 end) as 'MEDREP Aktif', count(1) as 'MEDREP Hadir', 
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
		$html ='<div class="box-body" id="regional"><h3>Summary Man Power Attendance By MEDREP City '.$city.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_preview_att_perregional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<div class="table-responsive col-md-9"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;width:40px;" rowspan=2>No.</th>';
        $html .= '<th style="white-space: nowrap;width:200px;" rowspan=2>Position</th>';
        $html .= '<th style="white-space: nowrap;width:300px;" rowspan=2">User MEDREP</th>';
        $html .= '<th style="white-space: nowrap;text-align:center;" colspan="5">'.date("M-Y",strtotime($periodedate)).'</th>';
		$html .= '</tr>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">MEDREP Aktif</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">MEDREP Hadir</th>';
		$html .= '<th style="white-space: nowrap; width:150px;text-align:center;">%</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($datarg as $vreg) {
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;text-align:right;">'.$i.'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['tipe_sales'].'</td>';
					$html .= '<td style="white-space: nowrap;">'.$vreg['salesmanid'].'-'.$vreg['nama_salesman'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['MEDREP Aktif'], 0, '.', ',').' </td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['MEDREP Hadir'], 0, '.', ',').' </td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['persentasi'], 0, '.', ',').' %</td>';
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
		$html ='<div class="box-body col-md-6" id="regional"><h3>Summary Man Power Fulfillment By City '.$city.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_preview_national();" class="btn btn-success fa fa-home"> Home</a>
				 <a id="btn-cancel-form" href="javascript:void(0)" onclick="open_hc_detail_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
        $html .= '<tr><th style="white-space: nowrap;;" colspan="3">'.date_format($periodedate,"M-Y").'</th></tr>';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;width:40px;" >No.</th>';
        $html .= '<th style="white-space: nowrap;width:100px;" >Position</th>';
        $html .= '<th style="white-space: nowrap;width:300px;" ">User MEDREP</th>';
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
		$html .= '</table></div>';
				
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

		$qhcreg = $this->db->query(" 
										select case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, (sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid'
										$strqueryarea
										group by case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end
										order by item desc
										;
								");
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
		foreach ($datarg as $vreg) {
			if ($vreg['item']=='Toothpaste'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_sos_detail_regional();">National</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
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

					function open_sos_detail_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var classid = uiSelectClassSos.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
						common.loading();
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_sos_regional"),
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

		$qhcreg = $this->db->query(" 
										select f.regionalid, f.nama_regional, case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										sum(a.qty_sos_gsk) qtygsk, sum(a.qty_sos_competitor) qtykategori, (sum(a.qty_sos_gsk)/sum(a.qty_sos_competitor))*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid'
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
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
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
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid'
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
		$html .='<div class="box-header"><a id="btn-home-form" href="javascript:void(0)" onclick="open_sos_detail_national();" class="btn btn-success fa fa-home"> Home</a>
		<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_sos_detail_regional();" class="btn btn-warning fa fa-backward"> Back</a></div>';
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
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' %</td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
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
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid'
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
					$html .= '<td style="white-space: nowrap;"><a href="#" id="hcnational" onclick="open_sos_detail_regional_area_city_outlet('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['kota'].'\');">'.$vreg['kota'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
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
										b.customerid, b.kode_outlet, b.nama_customer,b.alamat,
										case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										a.qty_sos_gsk qtygsk, a.qty_sos_competitor qtykategori, 
										(a.qty_sos_gsk/a.qty_sos_competitor)*100 sos
										from rekap_sos_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid'
												and f.regionalid='$regionalid' and e.areaid='$areaid' and d.subareaid='$subareaid' $strqueryarea
										order by b.customerid asc, item desc
										;
								");
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS Regional '.$nama_regional.' - Area '.$nama_area.' - City '.$kota.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_sos_detail_national();" class="btn btn-success fa fa-home"> Home</a>
		<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_sos_detail_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">IdOutlet</th>';
        $html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
        $html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
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
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtygsk'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtykategori'].'</a></td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtygsk'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtykategori'].'</a></td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
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
									select 'National' area, z.productid,z.product_name, count(1) coverage, sum(z._available) _available,sum(z.fr) fr, sum(z.oos) oos from (
										select distinct a.customerid,b.kode_outlet,b.nama_customer,b.classid, a.productid,c.nama_invoice product_name,c.brandid,d.brand, 
													b.regionalid, e.nama_regional, b.areaid, f.nama_area, b.subareaid,g.nama_area city, a.fr, a.oos, 
													case when a.fr<>a.oos then 1 else 0 end _available  
										from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid
										left join m_product c on a.productid=c.productid
										left join ref_brand d on c.brandid=d.brandid
										left join m_area_regional e on b.regionalid=e.regionalid
										left join m_area_areasite f on b.areaid=f.areaid
										left join m_area_subarea g on b.subareaid=g.subareaid
										where a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid' 
											  and a.productid in (select productid from mapping_sku_active where idaccount=b.classid)
											  and a.productid like '$productid' and c.brandid like '$brandid'
											  and b.nama_customer is not null and e.nama_regional is not null $strqueryarea
										) z 
										group by z.productid,z.product_name
										order by z.product_name asc;
								");
		$datarg = $qhcreg->result_array();
		//echo $this->db->last_query();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>Product Availability</h3>';
		$html .= '<div class="table-responsive col-md-6"><table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;"></th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Product</th>';
		$html .= '<th style="white-space: nowrap;text-align:left;">Item</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;">Value</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
		$regional='';
		$header=1;
		foreach ($datarg as $vreg) {
				$html .= '<tr>';
				if ($header==1){
					$html .= '<td style="white-space: nowrap;" rowspan="4"><a href="#" id="hcnational" onclick="open_product_available_detail_regional();">National</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['product_name'].'('.$vreg['productid'].')</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['coverage'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['_available'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
				$html .= '</tr>';
				$header++;
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

					function open_product_available_detail_regional() {
					
						var tahun = uiSelectTahun.val();
						var bulan = uiSelectBulan.val();
						var classid = uiSelectClassPa.val();
						var productid = uiSelectProd.val();
						var brandid = uiSelectBrand.val();
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
						
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
											where a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid' 
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
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['coverage'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['_available'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
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
										where a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid' 
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
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['coverage'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['_available'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
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
										where a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid' 
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
					$html .= '<td style="white-space: nowrap;" rowspan = "4" ><a href="#" id="hcnational" onclick="open_product_available_detail_regional_area_city_outlet('.$vreg['regionalid'].',\''.$vreg['nama_regional'].'\','.$vreg['areaid'].',\''.$vreg['nama_area'].'\','.$vreg['subareaid'].',\''.$vreg['city'].'\');">'.$vreg['city'].'</a></td>';
				}else{
					$html .= '<td style="white-space: nowrap;" rowspan="4"></td>';
				}
				$html .= '<td style="white-space: nowrap;text-align:left;" rowspan="4">'.$vreg['product_name'].'('.$vreg['productid'].')</td>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['coverage'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">Store Available</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.$vreg['_available'].'</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% Vs Coverage</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['_available']/$vreg['coverage']*100),2).' %</td>';
				$html .= '</tr>';
				$html .= '<tr>';
				$html .= '<td style="white-space: nowrap;text-align:left;">% OOS</td>';
				$html .= '<td style="white-space: nowrap;text-align:right;">'.round(($vreg['oos']/$vreg['fr']*100),2).' %</td>';
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

					function open_product_available_detail_regional_area_city_xls(regionalid,nama_regional,areaid,nama_area,subareaid,kota) {
							common.loading();
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional_area_city_outlet"),
								data : "regionalid="+regionalid+"&nama_regional="+nama_regional+"&areaid="+areaid+"&nama_area="+nama_area+"&subareaid="+subareaid+"&kota="+kota+"&brandid="+brandid+"&productid="+productid+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
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

	function open_detail_product_available_regional_area_city_outlet() {
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
										b.customerid, b.kode_outlet, b.nama_customer,b.alamat,
										case when type_sos='P' then 'Toothpaste' else 'Toothbrush' end item, 
										a.qty_product_available_gsk qtygsk, a.qty_product_available_competitor qtykategori, 
										(a.qty_product_available_gsk/a.qty_product_available_competitor)*100 sos
										from rekap_product_available_detail a left join m_customer b on a.customerid=b.customerid
										left join m_sales_salesman c on a.salesmanid=c.salesmanid
										left join m_area_subarea d on b.subareaid=d.subareaid
										left join m_area_areasite e on e.areaid=d.areaid
										left join m_area_regional f on f.regionalid=e.regionalid
										where f.nama_regional is not null and a.tahun='$tahun' and a.bulan='$bulan' and b.classid='$classid'
												and f.regionalid='$regionalid' and e.areaid='$areaid' and d.subareaid='$subareaid' $strqueryarea
										order by f.regionalid asc, item desc
										;
								");
		$datarg = $qhcreg->result_array();
		
		##generate div per regional
		$html ='<div class="box-body" id="regional"><h3>SOS Regional '.$nama_regional.' - Area '.$nama_area.'</h3>';
		$html .='<div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="open_product_available_detail_regional();" class="btn btn-success fa fa-home"> Home</a>
		<a id="btn-cancel-form" href="javascript:void(0)" onclick="open_product_available_detail_regional_area_city('.$regionalid.',\''.$nama_regional.'\','.$areaid.',\''.$nama_area.'\');" class="btn btn-warning fa fa-backward"> Back</a></div>';		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">IdOutlet</th>';
        $html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
        $html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
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
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtygsk'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtykategori'].'</a></td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}else if ($vreg['item']=='Toothbrush'){
					$html .= '<tr>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;"></a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['item'].'</td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtygsk'].'</a></td>';
					$html .= '<td style="white-space: nowrap;text-align:center;">'.$vreg['qtykategori'].'</a></td>';
					if ($restrict_bu=='GT'){
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format('0', 2, '.', ',').' </td>';
					}else{
						$html .= '<td style="white-space: nowrap;text-align:center;">'.number_format($vreg['sos'], 2, '.', ',').' %</td>';
					}
					$html .= '</tr>';
				}
				$regional=$vreg['nama_regional'];
			}

		$html .= '</tbody>';
		$html .= '</table></div>';
		$html .='<script>
					const common = new Common();
					let uiSelectTahun = $("#tahun-id-pa");
					let uiSelectBulan = $("#bln-id-pa");
					let uiSelectClassSos = $("#classid-id-pa");   
					let paramsession = common.getCookie("session");
					var tahun = uiSelectTahun.val();
					var bulan = uiSelectBulan.val();
					var classid = uiSelectClassSos.val();

					function open_product_available_detail_regional() {
					
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional"),
								data : "tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
								success:function(res){
									response = res;
									$(\'#tbl-content\').html(response);
								},
								error:function(){
									alert("Load failed");
								}
							});
					}

					function open_product_available_detail_regional_area_city(regionalid,nama_regional,areaid,nama_area) {
					
						var idjabatan = paramsession.idjabatan;
						var usersession = paramsession.username;
						var restrict_level = paramsession.restrict_level;
						var restrict_bu = paramsession.restrict_bu;
				
							$.ajax({
								type:"POST",
								dataType: "html",
								url: common.baseURL("drc_dashboard/open_detail_product_available_regional_area_city"),
								data : "areaid="+areaid+"&nama_area="+nama_area+"&regionalid="+regionalid+"&nama_regional="+nama_regional+"&tahun="+tahun+"&bulan="+bulan+"&classid="+classid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu,
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


}
