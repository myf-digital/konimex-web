<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_dashboard extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('app_dashboard_model', 'dashboard');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
		responseJSON($this->dashboard->load($data));
    }

	function open_detail() {
		
		$sid = $this->input->post("sid");
		$date = $this->input->post("get_date");
		$siteid = $this->input->post("siteid");
		//$target_detail = $this->dashboard->target_detail($sid,$date);
		//$html = $target_detail;

		//$html ='<div><h3>Summary</h3>';
		/*$qtotorder = $this->db->query("
					select 
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
					   sum(case when dtl.flag_bonus = 0 then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
					   sum(case when dtl.flag_bonus = 0 then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
					from 
					t_sales_master sls left join
					t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales
					where sls.retur=0 and 
						  sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$sid."' AND
						  sls.tanggal >= '".$date."' AND
						  sls.tanggal <= '".$date."'
				");
		$datatotorder = $qtotorder->result_array();
		if ($datatotorder){
		foreach ($datatotorder as $vorder) {
			$totbruto		= number_format($vorder['total_bruto'], 2, '.', ',');
			$totdisc		= number_format($vorder['total_discount'], 2, '.', ',');
			$totnetto		= number_format($vorder['total_netto'], 2, '.', ',');
		}
		}else{
			$totbruto		= 0;
			$totdisc		= 0;
			$totnetto		= 0;
		}
		
		
		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:400px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="white-space: nowrap;text-align:left; padding-left:15px">Order</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';		
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Netto</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totnetto.'</td>';
		$html .= '</tr>';
		$html .= '</tbody>';		
		$html .= '</table>';		
		*/
		$q = $this->db->query("
								select * from (
								select salesamn.nama_salesman,salesamn.salesmanid,
								   rrk_trans.customerid,
								   cst.nama_customer,
								   cst.alamat,
								   sum(ifnull(dtl.netto,0)) as total_netto,
								   DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
								   case when rrk.customerid is null then 'ExtraCall' 
									   when rrk.customerid is not null then 'Call' 
									   when rrk.customerid is not null and rrk_trans.check_in is null then 'Jadwal' end as flag
									,ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) as jarak 
								from 
								t_sales_rrk_trans rrk_trans left join t_sales_rrk rrk on 
								rrk_trans.siteid = rrk.siteid and rrk_trans.periode = rrk.periode and rrk_trans.salesmanid = rrk.salesmanid and rrk_trans.customerid = rrk.customerid
								left join t_sales_master sls 
								on rrk_trans.siteid = sls.siteid and rrk_trans.periode = sls.tanggal and rrk_trans.salesmanid = sls.salesmanid 
								and rrk_trans.customerid = sls.customerid and rrk_trans.salesmanid = sls.salesmanid and rrk_trans.customerid = sls.customerid
								left JOIN t_sales_detail dtl 
								on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po 
								left JOIN m_customer_ob custob ON rrk_trans.salesmanid = custob.salesmanid and rrk_trans.customerid = custob.customerid
								left JOIN m_customer cst 
								on rrk_trans.siteid = cst.siteid and rrk_trans.customerid = cst.customerid 
								left JOIN m_sales_salesman salesamn 
								on rrk_trans.siteid = salesamn.siteid and rrk_trans.salesmanid = salesamn.salesmanid 
								left JOIN m_product product 
								on dtl.productid = product.productid 
								where rrk_trans.siteid = '".$siteid."' AND 
									  rrk_trans.salesmanid = '".$sid."' AND
									  rrk_trans.periode = '".$date."' 
								group by 
									   rrk_trans.customerid,
									   cst.nama_customer,
									   cst.alamat
							union all
								select 
								   rrk.customerid,
								   cst.nama_customer,
								   cst.alamat,
								   0 as total_netto,
								   DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
								   'Jadwal' as flag,
								   ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) as jarak
								from 
						  t_sales_rrk rrk left join t_sales_rrk_trans rrk_trans 
						  on rrk.siteid = rrk_trans.siteid and rrk.periode = rrk_trans.periode and rrk.salesmanid = rrk_trans.salesmanid 
						  and rrk.customerid = rrk_trans.customerid
						  left JOIN m_customer_ob custob ON rrk.salesmanid = custob.salesmanid and rrk.customerid = custob.customerid
						  left JOIN m_customer cst 
								on rrk.siteid = cst.siteid and rrk.customerid = cst.customerid
								where rrk.siteid = '".$siteid."' AND 
									  rrk.salesmanid = '".$sid."' AND
									  rrk.periode = '".$date."' AND
									  rrk_trans.check_in is null          
								group by 
									   rrk.customerid,
									   cst.nama_customer,
									   cst.alamat
					) fnl order by fnl.check_in desc
		");
		
		$i = 1;

		$data = $q->result_array();
		
		$html ='<div><h3>Outlet</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">No</th>';
		$html .= '<th style="white-space: nowrap;">Action</th>';
		$html .= '<th style="white-space: nowrap;">Outlet ID</th>';
		$html .= '<th style="white-space: nowrap;">Nama Outlet</th>';
		$html .= '<th style="white-space: nowrap;">Alamat</th>';
		$html .= '<th style="white-space: nowrap;">Check In</th>';
		$html .= '<th style="white-space: nowrap;">Akurasi(Km)</th>';
		$html .= '<th style="white-space: nowrap;">Total Netto</th>';
		$html .= '<th style="white-space: nowrap;">Status</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$icon = '';
		$urlimage = URL_IMAGE;

		foreach ($data as $value) {
			$flag = $value['flag'];
			if ($flag == 'Call') {
				$icon = base_url().'assets/images/ic_call.png';
			} else if ($flag == 'ExtraCall') {
				$icon = base_url().'assets/images/ic_extra_call.png';
			} else if ($flag == 'Jadwal') {
				$icon = base_url().'assets/mapIcon/kiosk/png/store_35.png';
			}
			
			/*$flag = $value['flag'];
			if ($flag == 'EffectiveCall') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/eff_call.png';
			} else if ($flag == 'ExtraCall') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/ext_call.png';
			} else if ($flag == 'Call') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/call.png';			
			} else if ($flag == 'InvalidCall') { 
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/inv_call.png';
			} else if ($flag == 'Noo') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/noo.png';
			} else if ($flag == 'Schedule') {
				$icon = base_url().'assets/mapIcon/map-pin-31.png';
			} */
			
			$html .= '<tr>';
			$html .= '<td>'.$i.'</td>';
			$html .= '<td style="white-space: nowrap;"><a class="btn btn-primary btn-xs" href="#" onclick="toggle_visibility(\'tr_detail_'.$i.'\'); return false;">Detail</a></td>';
			$html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
			$html .= '<td>'.$value['alamat'].'</td>';
			$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'"> '.$flag.'</td>';
			
			$html .= '</tr>';	
			$html .= '<tr id="tr_detail_'.$i.'" style="display: none;">';
			$html .= '<td colspan="6">';
			
			$customerid = $value['customerid'];
			$html .= '<div id="detail_product_"'.$i.' style="overflow-y: auto; max-height: 300px; max-width: 900px; white-space: nowrap; ">';
			
			//$get_tagihan = $this->dashboard->get_tagihan_sales($siteid,$sid,$date,$customerid);
			$d_rrk = $this->dashboard->get_detail_rrk($siteid,$customerid,$sid,$date);
			$d_img_checkin = $this->dashboard->get_image_checkin($siteid,$date,$sid,$customerid);
			$d_detailing = $this->dashboard->get_detailing($siteid,$date,$sid,$customerid);
								/*<th width="70">Order Time</th>
								<th width="70">Tagihan Time</th>*/

								/*<td class="active" style="text-align:center;">'.@$d_rrk->order_time.'</td>
								<td class="active" style="text-align:center;">'.@$d_rrk->tagihan_time.'</td>*/

			$html .='<table class="table table-striped table-bordered table-condensed" style="width:1000px;">
						<thead>
							<tr style="align:center;">
								<th width="70">User PARMA</th>
								<th width="70">Foto Checkin</th>
								<th width="70">Check IN</th>
								<th width="70">Check OUT</th>
								<th width="70">Lama Kunjungan</th>
								<th>Keterangan</th>
								<th>Catatan</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td class="success" style="text-align:center;">'.@$value['salesmanid'].'-'.@$value['nama_salesman'].'</td>
								<td valign="top" style="text-align:center;"><img class="img-rounded" onclick="preview_image_checkin(\'+param_link_image+\'); " alt="Image CheckIn" style="width:100px; height:100px;" src="'.$urlimage.@$d_img_checkin->image.'"></td>
								<td class="success" style="text-align:center;">'.@$d_rrk->check_in.'</td>
								<td class="success" style="text-align:center;">'.@$d_rrk->check_out.'</td>
								<td class="danger" style="text-align:center;">'.@$d_rrk->lama_kunjungan.'</td>
								<td class="info" style="text-align:left;padding-left:10px;">'.@$d_rrk->alasan.'</td>
								<td class="info" style="text-align:left;padding-left:10px;">'.str_replace(array("\n","\r"),"",str_replace("'","`", @$d_rrk->keterangan)).'</td>
							</tr>
						</tbody>
					</table>';
			
			$html .='<div><h3>Detailing</h3>
							<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">
								<thead>
									<tr style="align:center;">
										<th style="text-align:center;">Foto Detailing</th>
										<th style="text-align:left;">Keterangan</th>
									</tr>
								</thead>
								<tbody>';
								foreach ($d_detailing as $rowsdetailing){
									$html .=' <tr>
												<td valign="top" style="text-align:center;"><img class="img-rounded" onclick="preview_image(\'+param_link_image+\'); " alt="Image Detailing" style="width:100px; height:100px;" src="'.$urlimage.@$rowsdetailing['url_img_detailing'].'"></td>
												<td valign="center" style="text-align:left;">
												<p>CustomerID : '.@$rowsdetailing['customerid'].'</p>
												<p>Latest JJID : '.@$rowsdetailing['latest_jjid'].'</p>
												<p>Nama Customer : '.@$rowsdetailing['nama_customer'].'</p>
												<p>Channel - Class :'.@$rowsdetailing['typeid'].' - '.@$rowsdetailing['nama_account'].'</p>
												<p>PIC :'.@$rowsdetailing['professional_name'].'('.@$rowsdetailing['tipe_pic'].')</p>
												<p>Detailing Product :'.@$rowsdetailing['brands'].'</p>
												<p>Description :'.str_replace(array("\n","\r"),"",str_replace("'","`", @$rowsdetailing['keterangan'])).'</p>
												</td>
												</tr>';
								}
								$html .= '</tbody>
							</table>
							</div>';
				$html .= '<h4>Order</h4>'; 
				$html .= '<table class="table table-striped table-bordered table-condensed">';
				$html .= '<thead>';
				$html .= '<tr>';
					$html .= '<th style="white-space: nowrap;padding-left:10px">Product ID </th>';
					$html .= '<th style="white-space: nowrap;padding-left:10px" >Product</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Harga</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Total Neto</th>';	
				$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';
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
					   sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
					   dtl.h_jual,
					   sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
					from 
					t_sales_master sls left join
					t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po left JOIN
					m_customer_ob cstob on sls.customerid = cstob.customerid and sls.salesmanid = cstob.salesmanid left JOIN
					m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid left JOIN
					m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
					m_product product on dtl.productid = product.productid 
					where sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$sid."' AND
						  sls.tanggal = '".$date."'AND
						  sls.customerid = '".$customerid."'
					group by sls.siteid, 
						   sls.salesmanid,
						   salesamn.nama_salesman,
						   sls.customerid,
						   cst.nama_customer,
						   cst.alamat,
						   dtl.productid,
						   product.nama_invoice,dtl.h_jual
				");
				$totnetto = 0;
				$k_detail = $q_detail->result_array();
				foreach ($k_detail as $v_detail) {
					$html .= '<tr>';						
						$html .= '<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['productid'].'</td>';
						$html .= '<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['nama_invoice'].'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>';
					$html .= '</tr>';
					$totnetto = $totnetto+$v_detail['total_netto'];
				}
				$html .= '<tr>';
				$html .= '<th colspan="4" style="white-space: nowrap;padding-left:10px">Total </th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totnetto, 2, '.', ',').'</th>';	
				$html .= '</tr>';

				$html .= '</tbody>';
				$html .= '</table>';

				$html .= '<h4>Daily CRC (Quantity in pcs)</h4>';
				$get_crecord = $this->dashboard->get_record($siteid,$customerid,$sid,$date);
				$html .= str_replace("'","",str_replace("'+","",$get_crecord));

			$html .= '</div>';		
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '<script type="text/javascript">
							function toggle_visibility (id) {
							   var e = document.getElementById(id);
							   if(e.style.display == \'\') {
								  e.style.display = \'none\';
							   } else {
								  e.style.display = \'\';
							   }  
							}						
					  </script>';
			$i++;
		}
		$html .= '</tbody>';
		$html .= '</table></div>';
				
		echo $html;

	}
    
	function get_gmaptracking() {
		
		$html 		= '';
		$sid 		= $this->input->post("sid");
		$get_date 	= $this->input->post("get_date");
		
		//print_r($get_map); die();
		$total = 0 ;
		$marker 	= "";
		$marker_sales = "";
		$i = 0;
		$jum = $total - 1; 
		$outlet = '';
		$html .= '<script>
	
			function TxtOverlay(pos, txt, cls, map) {

			  // Now initialize all properties.
			  this.pos = pos;
			  this.txt_ = txt;
			  this.cls_ = cls;
			  this.map_ = map;
			  
			  this.div_ = null;

			  // Explicitly call setMap() on this overlay
			  this.setMap(map);
			}

			TxtOverlay.prototype = new google.maps.OverlayView();



			TxtOverlay.prototype.onAdd = function() {

			 
			  var div = document.createElement(\'DIV\');
			  div.className = this.cls_;

			  div.innerHTML = this.txt_;

			  
			  this.div_ = div;
			  var overlayProjection = this.getProjection();
			  var position = overlayProjection.fromLatLngToDivPixel(this.pos);
			  div.style.left = position.x + \'px\';
			  div.style.top = position.y + \'px\';
			 
			  var panes = this.getPanes();
			  panes.floatPane.appendChild(div);
			}
			
			TxtOverlay.prototype.draw = function() {

				var overlayProjection = this.getProjection();
				var position = overlayProjection.fromLatLngToDivPixel(this.pos);


				var div = this.div_;
				div.style.left = (position.x-15) + \'px\';
				div.style.top = (position.y-10) + \'px\';

			 }
			 
			TxtOverlay.prototype.onRemove = function() {
			  this.div_.parentNode.removeChild(this.div_);
			  this.div_ = null;
			}
			
			TxtOverlay.prototype.hide = function() {
			  if (this.div_) {
				this.div_.style.visibility = "hidden";
			  }
			}

			TxtOverlay.prototype.show = function() {
			  if (this.div_) {
				this.div_.style.visibility = "visible";
			  }
			}

			TxtOverlay.prototype.toggle = function() {
			  if (this.div_) {
				if (this.div_.style.visibility == "hidden") {
				  this.show();
				} else {
				  this.hide();
				}
			  }
			}

			TxtOverlay.prototype.toggleDOM = function() {
			  if (this.getMap()) {
				this.setMap(null);
			  } else {
				this.setMap(this.map_);
			  }
			}
			
			function MakeControl(controlDiv, label, outletList) {
			  
			  // Set up the control border.
			  var controlUI = document.createElement(\'div\');
			  controlUI.title = label;
			  controlUI.className = \'controlUI\';			  
			  controlDiv.appendChild(controlUI);

			  // Set up the inner control.
			  var controlText = document.createElement(\'div\');
			  controlText.innerHTML = outletList;
			  controlText.className = \'controlText\';
			  controlUI.appendChild(controlText);
			  
			}

		</script>';
		$numtrans = 0;
		$num = 1;

		$numpos = 0;
		$d_posisisales = $this->dashboard->get_tracking($sid,$get_date);
		$cnttracking = count($d_posisisales); 
		$starttime = '';
		foreach ($d_posisisales as $d_position) {
			if($starttime =='') { $starttime = strtotime($d_position['waktu'].':00'); }
			$latpos = $d_position['latitude_cell'];
			$longpos = $d_position['longitude_cell'];
			$waktu = $d_position['waktu'];
			if ($numpos==0){
				$icon = base_url().'assets/mapIcon/tracking.png';
			} else if ($numpos==$cnttracking){
				$icon = base_url().'assets/mapIcon/tracking.png';
			}else{
				$icon = base_url().'assets/mapIcon/tracking.png';
			}
			
			if (($latpos) and ($longpos !="0")) {
			$marker .=' var latlng = new google.maps.LatLng('.$latpos.','.$longpos.');
						var markerOptions = {  
							map: map,  
							position: new google.maps.LatLng('.$latpos.','.$longpos.'),
							icon: {
								url: \''.$icon.'\',
								labelOrigin: { x: 17, y: 40}
							  },
							  title: \''.@$waktu.'\',
							  label: {
								text: \''.@$waktu.'\',
								color: "#222222",
								fontSize: "10px"
							  }
						};
					  
						marker_'.$i.' = new google.maps.Marker(markerOptions);
					  
						marker_'.$i.'.addListener(\'click\', function() {
						infowindow_'.$i.'.open(map, marker_'.$i.');
					   });


					  //customTxt_'.$i.' = "<div>'.$waktu.'</div>";
					  //txt = new TxtOverlay(latlng, customTxt_'.$i.', "customBox2", map);
					  
					  ';
			}
			
			$numpos++;
		}
		//echo $numpos."|".$cnttracking; 
		//echo $marker;
		$get_latlong = $this->dashboard->get_lat_long();
		$html .='<script type="text/javascript">
					var directionDisplay;
					var directionsService = new google.maps.DirectionsService();
					var map;
					function initialize() {
					  directionsDisplay = new google.maps.DirectionsRenderer({
						  suppressMarkers : true
					  });


					  var myOptions = {
						  zoom: 7,
						  center: new google.maps.LatLng('.$get_latlong->latitude.','.$get_latlong->longitude.'),  
						  mapTypeId: google.maps.MapTypeId.ROADMAP,
						  zoomControl: true,
					  }


					map = new google.maps.Map(document.getElementById("maps"), myOptions);
					'.$marker.'

					directionsDisplay.setMap(map);
						
					calcRoute();

					}
					

					function calcRoute() {

						var waypts=[];
						var start="";
						var end=""
						';
						
						$vstart="";
						$vstop="";
						$i=1;
						$numtrans=0;
						foreach ($d_posisisales as $map) {
							$lat = $map['latitude_cell'];
							$long = $map['longitude_cell'];
							$waktu = $map['waktu'];
							//$flag = $map['flag'];
							//$sales_lat = $d_posisisales['latitude_cell'];
							//$sales_long = $d_posisisales['longitude_cell'];
							
								$numtrans++;
								if (($lat != "0") and ($long !="0")) {
									
									if ($vstart== "")
									{
									$vstart=$lat.','.$long;
									$html .='start = new google.maps.LatLng('.$lat.','.$long.');';
									}
									
									if ($numtrans>=2 && $numtrans<=10){
									$html .='waypts.push({ location: \''.$lat.','.$long.'\',stopover: true});';
									}
									
								$vstop=$lat.','.$long;
							}
							$i++;
						}
					$html .='end = new google.maps.LatLng('.$vstop.');';
					$html .='
					if(start!=""){	
						  var request = {
							  origin: start,
							  destination: end,
							  waypoints: waypts,
							  optimizeWaypoints: true,
							  travelMode: google.maps.DirectionsTravelMode.DRIVING
						  };
						  
						  directionsService.route(request, function(response, status) {
							  if (status == google.maps.DirectionsStatus.OK) {
								  directionsDisplay.setDirections(response);
								  var route = response.routes[0];

							  }
						  });
						}
					}
					initialize();					
	
			</script>';
		
		echo $html;
			
				
	}

	function open_crc() {
	
		$customerid = $this->input->post('cusid');
		$salesmanid = $this->input->post('salesid');
		$date = $this->input->post('dateTime');
		$dateTime = explode('-',$date);
		$month = $dateTime[1];
		$year = $dateTime[0];
		$html = $this->dashboard->get_record_month($customerid,$salesmanid,$month,$year);
		echo $html;
		
	}
	
	function get_gmap() {
		
		$html 		= '';
		$html .= '<script>
	
			function TxtOverlay(pos, txt, cls, map) {

			  // Now initialize all properties.
			  this.pos = pos;
			  this.txt_ = txt;
			  this.cls_ = cls;
			  this.map_ = map;
			  
			  this.div_ = null;

			  // Explicitly call setMap() on this overlay
			  this.setMap(map);
			}

			TxtOverlay.prototype = new google.maps.OverlayView();



			TxtOverlay.prototype.onAdd = function() {

			 
			  var div = document.createElement(\'DIV\');
			  div.className = this.cls_;

			  div.innerHTML = this.txt_;

			  
			  this.div_ = div;
			  var overlayProjection = this.getProjection();
			  var position = overlayProjection.fromLatLngToDivPixel(this.pos);
			  div.style.left = position.x + \'px\';
			  div.style.top = position.y + \'px\';
			 
			  var panes = this.getPanes();
			  panes.floatPane.appendChild(div);
			}
			
			TxtOverlay.prototype.draw = function() {

				var overlayProjection = this.getProjection();
				var position = overlayProjection.fromLatLngToDivPixel(this.pos);


				var div = this.div_;
				div.style.left = (position.x-15) + \'px\';
				div.style.top = (position.y-10) + \'px\';

			 }
			 
			TxtOverlay.prototype.onRemove = function() {
			  this.div_.parentNode.removeChild(this.div_);
			  this.div_ = null;
			}
			
			TxtOverlay.prototype.hide = function() {
			  if (this.div_) {
				this.div_.style.visibility = "hidden";
			  }
			}

			TxtOverlay.prototype.show = function() {
			  if (this.div_) {
				this.div_.style.visibility = "visible";
			  }
			}

			TxtOverlay.prototype.toggle = function() {
			  if (this.div_) {
				if (this.div_.style.visibility == "hidden") {
				  this.show();
				} else {
				  this.hide();
				}
			  }
			}

			TxtOverlay.prototype.toggleDOM = function() {
			  if (this.getMap()) {
				this.setMap(null);
			  } else {
				this.setMap(this.map_);
			  }
			}
			
			function MakeControl(controlDiv, label, outletList) {
			  
			  // Set up the control border.
			  var controlUI = document.createElement(\'div\');
			  controlUI.title = label;
			  controlUI.className = \'controlUI\';			  
			  controlDiv.appendChild(controlUI);

			  // Set up the inner control.
			  var controlText = document.createElement(\'div\');
			  controlText.innerHTML = outletList;
			  controlText.className = \'controlText\';
			  controlUI.appendChild(controlText);
			  
			}
			
			function open_modal(cusid,salesid) {
				
				var dateTime = document.getElementsByName("get_date")[0].value;
				
				$.ajax({
					type: "POST",
					url:"'.site_url().'/app_dashboard/open_crc",
					data:"cusid="+cusid+"&salesid="+salesid+"&dateTime="+dateTime,
					async: false,
					success: function(res) {
						response = res;
						$(\'div .modal-header .modal-title\').text(\'CRC Sales Reporting\');			
						$(\'#crc_modal\').find(\'.modal-body\').html(response);
						$("#crc_modal").modal(\'show\');
						
					}
					
				});
			}

			
		</script>';

		$sid 		= $this->input->post("sid");
		$get_date 	= $this->input->post("get_date");
		$get_map 	= $this->dashboard->get_node($sid,$get_date);
		
		//print_r($get_map); die();
		$marker 	= "";
		$marker_sales = "";
		$i = 0;
		$total = count($get_map); 
		$jum = $total - 1; 
		$outlet = '';

		$numicon = 0;
		$numtrans = 1;
		$num = 1;
		
		$numpos = 0;
		foreach ($get_map as $map) {
			
			$siteid = $map['siteid'];
			$periode = $get_date;
			$salesmanid = $map['salesmanid'];
			$customerid = $map['customerid'];
			$customerid_m = $map['customerid_m'];
				
			$get_crecord = $this->dashboard->get_record($siteid,$customerid,$salesmanid,$get_date);
			$get_order = $this->dashboard->get_order($siteid,$customerid,$salesmanid,$get_date);
			$d_rrk = $this->dashboard->get_detail_rrk($siteid,$customerid,$salesmanid,$get_date);
			$d_img = $this->dashboard->get_image_cust($siteid,$customerid,$customerid_m,$salesmanid);
			$d_img_checkin = $this->dashboard->get_image_checkin($siteid,$get_date,$salesmanid,$customerid);
			$d_detailing = $this->dashboard->get_detailing($siteid,$get_date,$salesmanid,$customerid);
			//$d_img_before = $this->dashboard->get_image_before($siteid,$get_date,$salesmanid,$customerid);
			//$d_img_after = $this->dashboard->get_image_after($siteid,$get_date,$salesmanid,$customerid);
			//$d_img_sell_1 = $this->dashboard->get_image_dokumentasi($siteid,$get_date,$salesmanid,$customerid,'IMG_SELL_OUT_I');
			//$d_img_sell_2 = $this->dashboard->get_image_dokumentasi($siteid,$get_date,$salesmanid,$customerid,'IMG_SELL_OUT_II');
			//$d_img_sell_3 = $this->dashboard->get_image_dokumentasi($siteid,$get_date,$salesmanid,$customerid,'IMG_SELL_OUT_III');
			//$d_img_sos = $this->dashboard->get_image_sos($siteid,$get_date,$salesmanid,$customerid);
			//$d_img_competitor = $this->dashboard->get_image_competitor($siteid,$get_date,$salesmanid,$customerid);
			//$d_img_npd = $this->dashboard->get_image_npd($siteid,$get_date,$salesmanid,$customerid);
			//$d_img_promo_gsk = $this->dashboard->get_image_promo_gsk($siteid,$get_date,$salesmanid,$customerid);
			
			$icon = '';
			//$icon_sales = $icon = base_url().'assets/mapIcon/res/drawable-hdpi/jadwal.png';//"https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png";
			$urlimage = URL_IMAGE;
			//$urlimageoutlet = URL_IMAGE."imageoutlet/";
			
			$lat = $map['latitude_cell'];
			$long = $map['longitude_cell'];			
			
			$lat_sales = $map['latitude'];			
			$long_sales = $map['longitude'];
			$flag = $map['flag'];
			if ($flag == 'EffectiveCall') {
				$icon = base_url().'assets/images/ic_call_48.png';
				//$icon = base_url().'assets/mapIcon/res/drawable-hdpi/eff_call.png';
			} else if ($flag == 'ExtraCall') {
				$icon = base_url().'assets/images/ic_extra_call_48.png';
				//$icon = base_url().'assets/mapIcon/numbers/png/extra_call.png';
			} else if ($flag == 'Jadwal') {
				$icon = base_url().'assets/images/ic_store_48.png';
				//$icon = base_url().'assets/mapIcon/res/drawable-hdpi/jadwal.png';
				//$icon = 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png';
			} 
					
			if ($flag != 'Jadwal') {
				if (($lat != "0") and ($long !="0")) {
				$marker .=' var latlng = new google.maps.LatLng('.$lat.','.$long.');
							var link_image = "\''.$customerid.'\',\''.$salesmanid.'\'";
							var param_link_image = "\''.$siteid.'\',\''.$periode.'\',\''.$customerid.'\',\''.$salesmanid.'\'";	
							var contentString_'.$i.' = \'<div id="content" style="max-width:1000px;" >\'+
							\'<div id="siteNotice"><h3>Outlet</h3>\'+
							\'<table cellspacing="1" cellpadding="1">\'+
							\'<tbody>\'+
								\'<tr>\'+
									\'<td>\'+
										\'<div class="well bg-info" style="min-width:355px; border:none !important;">\'+
											\'<table cellspacing="1" cellpadding="1">\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">OutletId</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['customerid'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Outlet</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_customer'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Alamat</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted">'.@$map['alamat'].'</td>\'+	
												\'</tr>\'+											
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Salesman</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_salesman'].' ('.@$map['salesmanid'].')</td>\'+	
												\'</tr>\'+
											\'</table>\'+
										\'<\div>\'+
									\'</td>\'+										
								\'</tr>\'+														
							\'</tbody>\'+
							\'</table>\'+							
							\'</div>\'+	
							\'<hr>\'+
							\'<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">\'+
								\'<thead>\'+
									\'<tr style="align:center;">\'+
										\'<th style="text-align:center;">Foto CheckIn</th>\'+
										\'<th style="text-align:center;">Check IN</th>\'+
										\'<th style="text-align:center;">Check OUT</th>\'+
										\'<th style="text-align:center;">Lama Kunjungan</th>\'+
										\'<th style="text-align:left;">Keterangan</th>\'+
										\'<th style="text-align:left;">Catatan</th>\'+
									\'</tr>\'+
								\'</thead>\'+
								\'<tbody>\'+
									\'<tr>\'+
										\'<td valign="top" style="text-align:center;"><img class="img-rounded" onclick="preview_image_checkin(\'+param_link_image+\'); " alt="Image CheckIn" style="width:100px; height:100px;" src="'.$urlimage.@$d_img_checkin->image.'"></td>\'+
										\'<td class="success" style="text-align:center;">'.$d_rrk->check_in.'</td>\'+	
										\'<td class="success" style="text-align:center;">'.$d_rrk->check_out.'</td>\'+	
										\'<td class="danger" style="text-align:center;">'.$d_rrk->lama_kunjungan.'</td>\'+
										\'<td class="info" style="text-align:left;">'.$d_rrk->alasan.'</td>\'+	
										\'<td class="info" style="text-align:left;">'.str_replace(array("\n","\r"),"",str_replace("'","`", @$d_rrk->keterangan)).'</td>\'+	
									\'</tr>\'+
								\'</tbody>\'+
							\'</table>\'+
							\'<hr>\'+
							\'<div><h3>Detailing</h3>\'+
							\'<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">\'+
								\'<thead>\'+
									\'<tr style="align:center;">\'+
										\'<th style="text-align:center;">Foto Detailing</th>\'+
										\'<th style="text-align:left;">Keterangan</th>\'+
									\'</tr>\'+
								\'</thead>\'+
								\'<tbody>';
								foreach ($d_detailing as $rowsdetailing){
									$marker .=' <tr>\'+
												\'<td valign="top" style="text-align:center;"><img class="img-rounded" onclick="preview_image(\'+param_link_image+\'); " alt="Image Detailing" style="width:100px; height:100px;" src="'.$urlimage.@$rowsdetailing['url_img_detailing'].'"></td>\'+
												\'<td valign="center" style="text-align:left;">\'+
												\'<p>CustomerID : '.@$rowsdetailing['customerid'].'</p>\'+
												\'<p>Latest JJID : '.@$rowsdetailing['latest_jjid'].'</p>\'+
												\'<p>Nama Customer : '.@$rowsdetailing['nama_customer'].'</p>\'+
												\'<p>Channel - Class :'.@$rowsdetailing['typeid'].' - '.@$rowsdetailing['nama_account'].'</p>\'+
												\'<p>PIC :'.@$rowsdetailing['professional_name'].'('.@$rowsdetailing['tipe_pic'].')</p>\'+
												\'<p>Detailing Product :'.@$rowsdetailing['brands'].'</p>\'+
												\'<p>Description :'.str_replace(array("\n","\r"),"",str_replace("'","`", @$rowsdetailing['keterangan'])).'</p>\'+
												\'</td>\'+
												\'</tr>';
								}
								$marker .= '</tbody>\'+
							\'</table>\'+
							\'</div>\'+	
							\'<hr>\'+
							\'<div><h3>Order</h3>\'+
							'.$get_order.'
							\'</div>\'+							
							\'<hr>\'+
							\'<div><h3>Daily CRC (Quantity in pcs)</h3>\'+
							'.$get_crecord.'
							\'</div>\'+							
						  \'</div>\';
				
						  
						  var infowindow_'.$i.' = new google.maps.InfoWindow({
							content: contentString_'.$i.'
						  });

						
							
						  var markerOptions = {  
								map: map,  
								position: new google.maps.LatLng('.$lat.','.$long.'),
								icon: {
									url: \''.$icon.'\',
									labelOrigin: { x: 17, y: 45}
								  },
								  title: \''.@$numtrans.' - '.@$map['nama_customer'].'\',
								  label: {
									text: \''.@$numtrans.' - '.@$map['nama_customer'].'\',
									color: "#222222",
									fontSize: "10px"
								  }
						  };
						  
						  marker_'.$i.' = new google.maps.Marker(markerOptions);
						  
							marker_'.$i.'.addListener(\'click\', function() {
							infowindow_'.$i.'.open(map, marker_'.$i.');
						   });


						  //customTxt_'.$i.' = "<div style=\'font-size:8px;color:black;background:white;\'>'.$numtrans.' - '.$map['nama_customer'].'</div>";
						  //txt = new TxtOverlay(latlng, customTxt_'.$i.', "customBox2", map);
						  
						  ';
						  $numtrans++;
				}
			}
			
			if ($flag == 'Jadwal') {
				
				if (($lat_sales != "0") and ($long_sales !="0")) {

					$marker_sales .=' var latlng = new google.maps.LatLng('.$lat.','.$long.');
							var link_image = "\''.$customerid.'\',\''.$salesmanid.'\'";	
							var param_link_image = "\''.$siteid.'\',\''.$periode.'\',\''.$customerid.'\',\''.$salesmanid.'\'";	
							var contentString_'.$i.' = \'<div id="content" style="max-width:1000px;">\'+
							\'<div style="z-index: -1;" id="siteNotice"><h3>Outlet</h3>\'+
							\'<table cellspacing="1" cellpadding="1">\'+
							\'<tbody>\'+
								\'<tr>\'+									
									\'<td>\'+
										\'<div class="well bg-info" style="min-width:355px; border: none !important;">\'+
											\'<table cellspacing="1" cellpadding="1" >\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">OutletId</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['customerid'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Outlet</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_customer'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Alamat</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted">'.@$map['alamat'].'</td>\'+	
												\'</tr>\'+											
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Salesman</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_salesman'].' ('.@$map['salesmanid'].')</td>\'+	
												\'</tr>\'+
											\'</table>\'+
										\'</div>\'+											
									\'</td>\'+							
								\'</tr>\'+														
							\'</tbody>\'+
							\'</table>\'+
							\'<hr>\'+	
							\'<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">\'+
								\'<thead>\'+
									\'<tr style="align:center;">\'+
										\'<th style="text-align:center;">Foto CheckIn</th>\'+
										\'<th style="text-align:center;">Check IN</th>\'+
										\'<th style="text-align:center;">Check OUT</th>\'+
										\'<th style="text-align:center;">Lama Kunjungan</th>\'+
										\'<th style="text-align:left;">Keterangan</th>\'+
										\'<th style="text-align:left;">Catatan</th>\'+
									\'</tr>\'+
								\'</thead>\'+
								\'<tbody>\'+
									\'<tr>\'+
										\'<td valign="top" style="text-align:center;"><img class="img-rounded" onclick="preview_image_checkin(\'+param_link_image+\'); " alt="Image CheckIn" style="width:100px; height:100px;" src="'.$urlimage.@$d_img_checkin->image.'"></td>\'+
										\'<td class="success" style="text-align:center;">'.$d_rrk->check_in.'</td>\'+	
										\'<td class="success" style="text-align:center;">'.$d_rrk->check_out.'</td>\'+	
										\'<td class="danger" style="text-align:center;">'.$d_rrk->lama_kunjungan.'</td>\'+
										\'<td class="info" style="text-align:left;">'.$d_rrk->alasan.'</td>\'+	
										\'<td class="info" style="text-align:left;">'.str_replace(array("\n","\r"),"",str_replace("'","`", @$d_rrk->keterangan)).'</td>\'+	
									\'</tr>\'+
								\'</tbody>\'+
							\'</table>\'+
							\'<hr>\'+
							\'<div><h3>Detailing</h3>\'+
							\'<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">\'+
								\'<thead>\'+
									\'<tr style="align:center;">\'+
										\'<th style="text-align:center;">Foto Detailing</th>\'+
										\'<th style="text-align:left;">Keterangan</th>\'+
									\'</tr>\'+
								\'</thead>\'+
								\'<tbody>';
								foreach ($d_detailing as $rowsdetailing){
									$marker .=' <tr>\'+
												\'<td valign="top" style="text-align:center;"><img class="img-rounded" onclick="preview_image(\'+param_link_image+\'); " alt="Image Detailing" style="width:100px; height:100px;" src="'.$urlimage.@$rowsdetailing['url_img_detailing'].'"></td>\'+
												\'<td valign="center" style="text-align:left;">\'+
												\'<p>CustomerID : '.@$rowsdetailing['customerid'].'</p>\'+
												\'<p>Latest JJID : '.@$rowsdetailing['latest_jjid'].'</p>\'+
												\'<p>Nama Customer : '.@$rowsdetailing['nama_customer'].'</p>\'+
												\'<p>Channel - Class :'.@$rowsdetailing['typeid'].' - '.@$rowsdetailing['nama_account'].'</p>\'+
												\'<p>PIC :'.@$rowsdetailing['professional_name'].'('.@$rowsdetailing['tipe_pic'].')</p>\'+
												\'<p>Detailing Product :'.@$rowsdetailing['brands'].'</p>\'+
												\'<p>Description :'.str_replace(array("\n","\r"),"",str_replace("'","`", @$rowsdetailing['keterangan'])).'</p>\'+
												\'</td>\'+
												\'</tr>';
								}
								$marker .= '</tbody>\'+
							\'</table>\'+
							\'</div>\'+	
							\'<hr>\'+
							\'<div><h3>Order</h3>\'+
							'.$get_order.'
							\'</div>\'+
							\'<hr>\'+
							\'<div><h3>Daily CRC (Quantity in pcs)</h3>\'+
							'.$get_crecord.'
							\'</div>\'+							
						  \'</div>\';
				
						  
						  var infowindow_'.$i.' = new google.maps.InfoWindow({
							content: contentString_'.$i.'
						  });

						
							
						  var markerOptions = {  
								map: map,  
								position: new google.maps.LatLng('.@$lat_sales.','.@$long_sales.'),
								icon: {
									url: \''.@$icon.'\',
									labelOrigin: { x: 17, y: 45}
								  },
								  title: \''.@$num.' - '.@$map['nama_customer'].'\',
								  label: {
									text: \''.@$num.' - '.@$map['nama_customer'].'\',
									color: "#222222",
									fontSize: "10px"
								  }
						  };
						  
						  marker_'.$i.' = new google.maps.Marker(markerOptions);
						  
							marker_'.$i.'.addListener(\'click\', function() {
							infowindow_'.$i.'.open(map, marker_'.$i.');
						   });


						  //customTxt_'.$i.' = "<div style=\'font-size:8px;color:black;background:white;\'>'.$num.' - '.$map['nama_customer'].'</div>";
						  //txt = new TxtOverlay(latlng, customTxt_'.$i.', "customBox2", map);
						  
						  ';
						  $num++;			
				}
			}
			//$outlet .='<a href=\"javascript:void(0)\" onclick=\"open_modal('.$customerid.','.$salesmanid.');\">'.$map['nama_customer'].'</a><br>';
			
			$i++;
		}
		$get_latlong = $this->dashboard->get_lat_long();
		$html .='<script type="text/javascript">
					var directionDisplay;
					var directionsService = new google.maps.DirectionsService();
					var map;
					function initialize() {
					  directionsDisplay = new google.maps.DirectionsRenderer({
						  suppressMarkers : true
					  });


					  var myOptions = {
						  zoom: 7,
						  center: new google.maps.LatLng('.@$get_latlong->latitude.','.@$get_latlong->longitude.'),  
						  mapTypeId: google.maps.MapTypeId.ROADMAP,
						  zoomControl: true,
					  }


					map = new google.maps.Map(document.getElementById("maps"), myOptions);
					'.$marker.'
					'.$marker_sales.'		

					directionsDisplay.setMap(map);
						
					calcRoute();

					}
					

					function calcRoute() {

						var waypts=[];
						var start="";
						var end=""
						';
						
						$vstart="";
						$vstop="";
						$i=1;
						$numtrans=0;
						foreach ($get_map as $map) {
							$lat = $map['latitude_cell'];
							$long = $map['longitude_cell'];
							$flag = $map['flag'];
							//$sales_lat = $d_posisisales['latitude_cell'];
							//$sales_long = $d_posisisales['longitude_cell'];
							
							if ($flag != 'Jadwal' or $flag != 'Noo') {
								$numtrans++;
								if (($lat != "0") and ($long !="0")) {
									
									if ($vstart== "")
									{
									$vstart=$lat.','.$long;
									$html .='start = new google.maps.LatLng('.@$lat.','.@$long.');';
									}
									
									if ($numtrans>=2 && $numtrans<=9){
									$html .='waypts.push({ location: \''.@$lat.','.@$long.'\',stopover: true});';
									}
									
								}
								$vstop=$lat.','.$long;
							}
							$i++;
						}
					$html .='end = new google.maps.LatLng('.$vstop.');';
					$html .='
					if(start!=""){	
						  var request = {
							  origin: start,
							  destination: end,
							  waypoints: waypts,
							  optimizeWaypoints: true,
							  travelMode: google.maps.DirectionsTravelMode.DRIVING
						  };
						  /*
							posisi = new google.maps.LatLng(-6.9452394,107.6160295);
							var marker = new google.maps.Marker({
								position: posisi,
								label:\'S\',
								map: map
							});
							
							var marker = new google.maps.Marker({
								position: end,
								label:\'E\',
								map: map
							});*/
						  directionsService.route(request, function(response, status) {
							  if (status == google.maps.DirectionsStatus.OK) {
								  directionsDisplay.setDirections(response);
								  var route = response.routes[0];

							  }
						  });
						}
					}
					initialize();					
					
					function preview_image(cusid,sales) {
						$.ajax({
						   url: "'.site_url().'/app_dashboard/get_fancy_image",
						   type: "POST",
						   async: false,
						   dataType: "json",
						   data: {cusid: cusid, sales: sales},
						   success: function (result) {
							   var tempFile = [];
							   $.each(result.images, function (index, value) {
								   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Image Outlet"};
								   tempFile.push(tempFileElemet);
							   });
							  
							   
							   $.fancybox.open(tempFile, {
								   helpers: {
									   thumbs: {
										   width: 75,
										   height: 50
									   }
								   }
							   });
						   }
					   });
				   }

				   function preview_image_checkin(siteid,periode,cusid,sales) {
					$.ajax({
					   url: "'.site_url().'/app_dashboard/get_fancy_checkin",
					   type: "POST",
					   async: false,
					   dataType: "json",
					   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
					   success: function (result) {
						   // var p = result.images;
						   var tempFile = [];
						   $.each(result.images, function (index, value) {
							   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Image Check In"};
							   tempFile.push(tempFileElemet);
						   });
						  
						   
						   $.fancybox.open(tempFile, {
							   helpers: {
								   thumbs: {
									   width: 75,
									   height: 50
								   }
							   }
						   });
					   }
				   });
			   }

			   function preview_image_before(siteid,periode,cusid,sales) {
				$.ajax({
				   url: "'.site_url().'/app_dashboard/get_fancy_before",
				   type: "POST",
				   async: false,
				   dataType: "json",
				   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
				   success: function (result) {
					   // var p = result.images;
					   var tempFile = [];
					   $.each(result.images, function (index, value) {
						   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Keterangan : "+value.description};
						   tempFile.push(tempFileElemet);
					   });
					  
					   
					   $.fancybox.open(tempFile, {
						   helpers: {
							   thumbs: {
								   width: 75,
								   height: 50
							   }
						   }
					   });
				   }
			   });
		   }

		   function preview_image_after(siteid,periode,cusid,sales) {
			$.ajax({
			   url: "'.site_url().'/app_dashboard/get_fancy_after",
			   type: "POST",
			   async: false,
			   dataType: "json",
			   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
			   success: function (result) {
				   // var p = result.images;
				   var tempFile = [];
				   $.each(result.images, function (index, value) {
					   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Keterangan : "+value.description};
					   tempFile.push(tempFileElemet);
				   });
				  
				   
				   $.fancybox.open(tempFile, {
					   helpers: {
						   thumbs: {
							   width: 75,
							   height: 50
						   }
					   }
				   });
			   }
		   });
	   }

	   function preview_image_dokumentasi(siteid,periode,cusid,sales) {
		$.ajax({
		   url: "'.site_url().'/app_dashboard/get_fancy_sellout",
		   type: "POST",
		   async: false,
		   dataType: "json",
		   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
		   success: function (result) {
			   // var p = result.images;
			   var tempFile = [];
			   $.each(result.images, function (index, value) {
				   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Keterangan : "+value.description};
				   tempFile.push(tempFileElemet);
			   });
			  
			   
			   $.fancybox.open(tempFile, {
				   helpers: {
					   thumbs: {
						   width: 75,
						   height: 50
					   }
				   }
			   });
		   }
	   });
   }

	function preview_image_promo_gsk(siteid,periode,cusid,sales) {
		$.ajax({
		   url: "'.site_url().'/app_dashboard/get_fancy_promo_gsk",
		   type: "POST",
		   async: false,
		   dataType: "json",
		   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
		   success: function (result) {
			   // var p = result.images;
			   var tempFile = [];
			   $.each(result.images, function (index, value) {
				   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Promo : "+value.promo};
				   tempFile.push(tempFileElemet);
			   });
			  
			   
			   $.fancybox.open(tempFile, {
				   helpers: {
					   thumbs: {
						   width: 75,
						   height: 50
					   }
				   }
			   });
		   }
	   	});
   	}

	   function preview_image_promo_gsk_gimmick(siteid,periode,cusid,sales) {
		$.ajax({
		   url: "'.site_url().'/app_dashboard/get_fancy_promo_gsk_gimmick",
		   type: "POST",
		   async: false,
		   dataType: "json",
		   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
		   success: function (result) {
			   // var p = result.images;
			   var tempFile = [];
			   $.each(result.images, function (index, value) {
				   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "Promo : "+value.promo};
				   tempFile.push(tempFileElemet);
			   });
			  
			   
			   $.fancybox.open(tempFile, {
				   helpers: {
					   thumbs: {
						   width: 75,
						   height: 50
					   }
				   }
			   });
		   }
	   	});
   	}

	   function preview_image_sos(siteid,periode,cusid,sales) {
		$.ajax({
		   url: "'.site_url().'/app_dashboard/get_fancy_sos",
		   type: "POST",
		   async: false,
		   dataType: "json",
		   data: { siteid: siteid, periode:periode, cusid: cusid, sales: sales },
		   success: function (result) {
			   // var p = result.images;
			   var tempFile = [];
			   $.each(result.images, function (index, value) {
				   var tempFileElemet = {href: "'.URL_IMAGE.'" + value.image, title: "SOS : "+value.sos+" %"};
				   tempFile.push(tempFileElemet);
			   });
			  
			   
			   $.fancybox.open(tempFile, {
				   helpers: {
					   thumbs: {
						   width: 75,
						   height: 50
					   }
				   }
			   });
		   }
	   });
   }

</script>';
		
		echo $html;
	}

	function get_fancy_image() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy();
        echo json_encode($list);
	}

	function get_fancy_checkin() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_checkin();
        echo json_encode($list);
	}

	function get_fancy_before() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_before();
        echo json_encode($list);
	}

	function get_fancy_after() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_after();
        echo json_encode($list);
	}

	function get_fancy_sos() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_sos();
        echo json_encode($list);
	}

	function get_fancy_promo_gsk() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_promo_gsk();
        echo json_encode($list);
	}

	function get_fancy_promo_gsk_gimmick() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_promo_gsk_gimmick();
        echo json_encode($list);
	}

	function get_fancy_competitor() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_competitor();
        echo json_encode($list);
	}
	
	function get_fancy_npd() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_npd();
        echo json_encode($list);
	}

	function get_fancy_sellout() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->dashboard->get_fancy_sellout();
        echo json_encode($list);
	}

}
