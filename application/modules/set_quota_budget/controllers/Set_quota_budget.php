<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Set_quota_budget extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Set_quota_budget_model', 'set_quota_budget');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

	public function set_quota()
    {
		$data = param_input();
        response($this->set_quota_budget->set_quota($data));
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function delete()
    {
        $data = param_input();
        response($this->set_quota_budget->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->set_quota_budget->load($data));
    }

    public function load_promo()
    {
        $data = param_input();
        responseJSON($this->set_quota_budget->load_promo($data));
    }

	function open_detail() {
		
		$start = $this->input->post("start");
		$idjabatan = $this->input->post("idjabatan");
		$usersession = $this->input->post("usersession");

        if ($idjabatan=='2' or $idjabatan=='3')
			$strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
		else if($idjabatan=='16' or $idjabatan=='17'){
			$strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
		}else{
            $strquery = "";
        }

        if ($idpromo!='null'){$addquery=" and a.idpromo in (".$idpromo.") ";} else { $addquery="";}

        $q = $this->db->query(" 
                                select b.promo,d.nama_class,a.*,e.nama_salesman,e.tipe_sales, c.kode_outlet, c.nama_customer,c.alamat,
                                       f.nama_regional, g.nama_area, h.nama_area as city 
                                from t_activity_promo_gsk a
                                join mapping_promo_active b on a.idpromo=b.idpromo 
                                left join m_customer c on a.customerid=c.customerid
                                left join m_customer_class d on c.classid=d.classid
                                left join m_sales_salesman e on a.salesmanid=e.salesmanid
                                left join m_area_regional f on c.regionalid=f.regionalid
                                left join m_area_areasite g on c.areaid = g.areaid
                                left join m_area_subarea h on c.subareaid = h.subareaid
                                where a.periode between '".$start."' and '".$end."' ".$addquery.$strquery.";
                            ");
		//echo $this->db->last_query();
		$data = $q->result_array();
        $urlimage = URL_IMAGE;
		
		$html ='<div class="box-body"><h3>List Promo</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed ">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th style="white-space: nowrap;">No</th>';
		$html .= '<th style="white-space: nowrap;">Tanggal</th>';
		$html .= '<th style="white-space: nowrap;">Promo</th>';
		$html .= '<th style="white-space: nowrap;">Account</th>';
		$html .= '<th style="white-space: nowrap;">User MEDREP</th>';
		$html .= '<th style="white-space: nowrap;">Outlet ID</th>';
		$html .= '<th style="white-space: nowrap;">Kode Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Alamat</th>';
		$html .= '<th style="white-space: nowrap;">Kota</th>';
		$html .= '<th style="white-space: nowrap;">Tipe Promo</th>';
		$html .= '<th style="white-space: nowrap;">Display</th>';
		$html .= '<th style="white-space: nowrap;">Harga Normal</th>';
		$html .= '<th style="white-space: nowrap;">Harga Promo</th>';
		$html .= '<th style="white-space: nowrap;">Deskripsi</th>';
		$html .= '<th style="white-space: nowrap;">Foto</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';
        $i=1;
		foreach ($data as $value) {
			$html .= '<tr>';
			$html .= '<td>'.$i.'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['periode'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['promo'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_class'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['salesmanid'].'-'.$value['nama_salesman'].'</td>';
            $html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['kode_outlet'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['alamat'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['city'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['tipepromo'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['display'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.number_format($value['harga_normal'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;">'.number_format($value['harga_promo'], 0, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['description'].'</td>';
			$html .= '<td style="white-space: nowrap;"><img class="img-rounded" style="width:100px; height:100px;" src="'.$urlimage.@$value['image'].'"></td>';
			
            //$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			//$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			//$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			//$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'">'.$flag.'</td>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table></div>';
				
		echo $html;

	}

	function export_excel() {
		
		$periode 	= $this->uri->segment(3);
		$until 	= $this->uri->segment(4);
		
		$filename = "Report_Salesman_Aktif.xls";
		$html ='<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:300px;
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
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th rowspan="2" style="text-align:center;white-space:nowrap;">Salesman ID</th>';
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Nama Medrep</th>';

		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;white-space:nowrap;color:red;">'.date_format($start,"d").'</th>';
			}else{
			$html .='<th style="text-align:center;white-space:nowrap;">'.date_format($start,"d").'</th>';
			}
			$start->modify('+1 day');
		}
		$html .='<th rowspan="2" style="text-align:left;white-space:nowrap;">Total</th>';
		$html .= '</tr><tr>';
		$start = date_create($periode);
		$end = date_create($until);
		while($start <= $end)
		{
			$namahari=date_format($start,"D");
			if ($namahari=='Sun'){
			$html .='<th style="text-align:center;color:red;">'.$namahari.'</th>';
			}else{
			$html .='<th style="text-align:center;">'.$namahari.'</th>';
			}
			$start->modify('+1 day');
		}

		$html .= '</tr></thead>';

		$html .= '<tbody>';
		/*Close Header*/
		$get_salesman = $this->model->get_salesman();

						
		foreach ($get_salesman as $v_salesman) {
			
			/*Get Detail Siswa*/
			$html .='<tr><td style="text-align:center;white-space:nowrap;">'.$v_salesman['salesmanid'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_salesman['nama_salesman'].'</td>';
			
			/******************/
			$start = date_create($periode);
			$end = date_create($until);
			while($start <= $end)
			{
				$vdate=date_format($start,"Y-m-d");
				$vsalesmanid=$v_salesman['salesmanid'];
				$get_salesman_aktif = $this->model->get_salesman_aktif($vsalesmanid,$vdate);
				if (!empty($get_salesman_aktif)){
					foreach ($get_salesman_aktif as $val_aktif) {
						$namahari=date_format($start,"D");
						if ($namahari=='Sun'){
						$html .='<th style="text-align:center;color:red;font-size:11px;">'.$val_aktif['aktif'].'</th>';
						}else{
						$html .='<th style="text-align:center;font-size:11px;">'.$val_aktif['aktif'].'</th>';
						}
					}
				}else{
					$namahari=date_format($start,"D");
					if ($namahari=='Sun'){
					$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
					}else{
					$html .='<th style="text-align:center;font-size:11px;">0</th>';
					}
				}

				$start->modify('+1 day');
			}
			
			$get_salesman_aktif_sum = $this->model->get_salesman_aktif_sum($vsalesmanid,$periode,$until);
			if (!empty($get_salesman_aktif_sum)){
				foreach ($get_salesman_aktif_sum as $val_sumaktif) {
					$html .='<th style="text-align:center;font-size:11px;">'.$val_sumaktif['sumaktif'].'</th>';
				}
			}else{
				$html .='<th style="text-align:center;color:red;font-size:11px;">0</th>';
			}
			
			
			/******************/
			
		}
			$html .='</tr>';
		
		$html .= '</tbody>';
		$html .= '</table>';
		
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		 
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
		$html .= '<div id="table-wrapper"><div id="table-scroll"><table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th style="text-align:center;white-space:nowrap;width:25px;">No.</th>';
		$html .='<th style="text-align:left;white-space:nowrap;width:100px;">Regional</th>';
		$html .='<th style="text-align:left;white-space:nowrap;width:100px;">Area</th>';
		// $html .='<th style="text-align:left;white-space:nowrap;width:100px;">City</th>';
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
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_city['nama_regional'].'</td>';
			$html .='<td style="text-align:left;white-space:nowrap;">'.$v_city['nama_area'].'</td>';
			// $html .='<td style="text-align:left;white-space:nowrap;">'.$v_city['city'].'<input id="listcity" type="hidden" name="listcity[]"  value="'.$v_city['subareaid'].'" /><input id="listcitynm" type="hidden" name="listcitynm[]"  value="'.$v_city['city'].'" /></td>';
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

}
