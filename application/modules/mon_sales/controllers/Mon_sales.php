<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mon_sales extends CI_Controller {
	
	var $gparam = array();
	
	public function __construct() { 
        parent::__construct();		
		
		$this->gparam['controller'] = $this->router->fetch_class();
		$this->load->model('mon_sales_model', 'sales');
		//$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
		//$this->load->library('googlemaps');
    }
	
	public function index()	{
		
		//if ($this->gparam['privilage']->privilage_view == 'Y') {
		
			$gridopt = $this->input->get(array('psize', 'pnumber'));
			header('Content-Type: text/html');
			
			$get_latlong = $this->sales->get_lat_long();
			$bmap = "<input type=\"radio\" name=\"radio\" onchange=\"get_map(\''+row.salesmanid+'\')\" ><i></i>";
			$bmaptracking = "<input type=\"radio\" name=\"radio\" onchange=\"get_maptracking(\''+row.salesmanid+'\')\" ><i></i>";
			$bmap_detail = "<a style=\"margin:4px;\" class=\"btn btn-success btn-xs\" onclick=\"open_detail(\''+row.salesmanid+'\')\" href=\"javascript:void(0)\"\
									group=\"\" data-toggle=\"tooltip\" title=\"Detail\"><i class=\"fa fa-edit\"></i> Detail\
								  </a>";
			// pnumber, psize
			$data = array(
					'controller'  	=> $this->gparam['controller'],
					'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
					'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),
					'bmap' 			=> $bmap,
					'bmaptracking' 	=> $bmaptracking,
					'bmap_detail' 	=> $bmap_detail,
					'lat'			=> $get_latlong->latitude,
					'long'			=> $get_latlong->longitude
			);
			
			$this->load->view('mon_sales_view',$data);
		/*} else {
			echo $this->gparam['restrict'];
		}*/
	}
	
	
	function load_data() {
		header('Content-Type: application/jsonp');
        $list = $this->sales->get_list_data();
		
        echo json_encode($list);
	}

	function load_data_summary() {
		header('Content-Type: application/jsonp');
        $list = $this->sales->get_list_data_summary();
		
        echo json_encode($list);
	}
	
	function open_crc() {
	
		$customerid = $this->input->post('cusid');
		$salesmanid = $this->input->post('salesid');
		$date = $this->input->post('dateTime');
		$dateTime = explode('-',$date);
		$month = $dateTime[1];
		$year = $dateTime[0];
		$html = $this->sales->get_record_month($customerid,$salesmanid,$month,$year);
		echo $html;
		
	}
	
	function get_gmap() {
		
		$html 		= '';
		$sid 		= $this->input->post("sid");
		$get_date 	= $this->input->post("get_date");
		$get_map 	= $this->sales->get_node($sid,$get_date);
		
		
		//print_r($get_map); die();
		$marker 	= "";
		$marker_sales = "";
		$i = 0;
		$total = count($get_map); 
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
			
			function open_modal(cusid,salesid) {
				
				var dateTime = document.getElementsByName("get_date")[0].value;
				
				$.ajax({
					type: "POST",
					url:"'.site_url().'/mon_sales/open_crc",
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
		$numtrans = 0;
		$num = 1;
		
		$numpos = 0;
		foreach ($get_map as $map) {
			
			$siteid = $map['siteid'];
			$salesmanid = $map['salesmanid'];
			$customerid = $map['customerid'];
				
			$get_crecord = $this->sales->get_record($siteid,$customerid,$salesmanid,$get_date);
			$get_tagihan = $this->sales->get_tagihan($siteid,$customerid,$salesmanid,$get_date);
			$get_order = $this->sales->get_order($siteid,$customerid,$salesmanid,$get_date);
			$get_retur = $this->sales->get_retur($siteid,$customerid,$salesmanid,$get_date);
			$d_rrk = $this->sales->get_detail_rrk($siteid,$customerid,$salesmanid,$get_date);
			$d_img = $this->sales->get_image_cust($siteid,$customerid,$salesmanid);
			
			$icon = '';
			$icon_sales = $icon = base_url().'assets/mapIcon/res/drawable-hdpi/jadwal.png';//"https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png";
			$urlimage = base_url().DIR_IMAGE;
			
			$lat = $map['latitude_cell'];
			$long = $map['longitude_cell'];			
			
			$lat_sales = $map['latitude'];			
			$long_sales = $map['longitude'];
			$flag = $map['flag'];
			if ($flag == 'EffectiveCall') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/eff_call.png';
			} else if ($flag == 'ExtraCall') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/ext_call.png';
				//$icon = base_url().'assets/mapIcon/icon_2.png';
			} else if ($flag == 'Cal') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/call.png';			
				//$icon = base_url().'assets/mapIcon/icon_3.png';			
			} else if ($flag == 'InvalidCall') { 
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/inv_call.png';
				//$icon = base_url().'assets/mapIcon/icon_4.png';
			} else if ($flag == 'Noo') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/noo.png';
				//$icon = base_url().'assets/mapIcon/icon_5.png';
			} else if ($flag == 'Jadwal') {
				$icon = base_url().'assets/mapIcon/res/drawable-hdpi/jadwal.png';
				//$icon = 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png';
			} 
					
			if ($flag != 'Jadwal' && $flag != 'Noo') {
				if (($lat != "0") and ($long !="0")) {
				$numtrans++;
				$marker .=' var latlng = new google.maps.LatLng('.$lat.','.$long.');
							var link_image = "\''.$customerid.'\',\''.$salesmanid.'\'";
							var contentString_'.$i.' = \'<div id="content" style="max-width:1000px;" >\'+
							\'<div id="siteNotice"><h3>Deskripsi Customer</h3>\'+
							\'<table cellspacing="1" cellpadding="1">\'+
							\'<tbody>\'+
								\'<tr>\'+
									\'<td valign="top"><img class="img-rounded" onclick="preview_image(\'+link_image+\'); " alt="Image Outlet" style="width:100px; height:100px;" src="'.$urlimage.@$d_img->image.'"></td>\'+
									\'<td>\'+
										\'<div class="well bg-info" style="min-width:355px; border:none !important;">\'+
											\'<table cellspacing="1" cellpadding="1">\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama TPE</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_salesman'].' ('.@$map['salesmanid'].')</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Customerid</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['customerid'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Customer</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_customer'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Alamat</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted">'.@$map['alamat'].'</td>\'+	
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
										\'<th>Check IN</th>\'+
										\'<th>CRC Time</th>\'+
										\'<th>Order Time</th>\'+
										\'<th>Tagihan Time</th>\'+
										\'<th>Check OUT</th>\'+
										\'<th>Lama Kunjungan</th>\'+
										\'<th>Alasan</th>\'+
									\'</tr>\'+
								\'</thead>\'+
								\'<tbody>\'+
									\'<tr>\'+
										\'<td class="success" style="text-align:center;">'.$d_rrk->check_in.'</td>\'+	
										\'<td class="active" style="text-align:center;">'.$d_rrk->crc_time.'</td>\'+
										\'<td class="active" style="text-align:center;">'.$d_rrk->order_time.'</td>\'+
										\'<td class="active" style="text-align:center;">'.$d_rrk->tagihan_time.'</td>\'+
										\'<td class="success" style="text-align:center;">'.$d_rrk->check_out.'</td>\'+	
										\'<td class="danger" style="text-align:center;">'.$d_rrk->lama_kunjungan.'</td>\'+
										\'<td class="info" style="text-align:left;">'.$d_rrk->alasan.'</td>\'+	
									\'</tr>\'+
								\'</tbody>\'+
							\'</table>\'+
							\'<div><h3>Tagihan</h3>\'+
							'.$get_tagihan.'
							\'</div>\'+
							\'<div><h3>Order</h3>\'+
							'.$get_order.'
							\'</div>\'+
							\'<div><h3>Retur</h3>\'+
							'.$get_retur.'
							\'</div>\'+
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
								icon: \''.$icon.'\'
						  };
						  
						  marker_'.$i.' = new google.maps.Marker(markerOptions);
						  
							marker_'.$i.'.addListener(\'click\', function() {
							infowindow_'.$i.'.open(map, marker_'.$i.');
						   });


						  customTxt_'.$i.' = "<div style=\'font-size:8px;color:black;background:white;\'>'.$numtrans.' - '.$map['nama_customer'].'</div>"
						  txt = new TxtOverlay(latlng, customTxt_'.$i.', "customBox2", map)
						  
						  ';
				}
			}
			
			if ($flag == 'Jadwal') {
				
				if (($lat_sales != "0") and ($long_sales !="0")) {

					$marker_sales .=' var latlng = new google.maps.LatLng('.$lat.','.$long.');
									var link_image = "\''.$customerid.'\',\''.$salesmanid.'\'";	
							var contentString_'.$i.' = \'<div id="content" style="max-width:1000px;">\'+
							\'<div id="siteNotice"><h3>Deskripsi Customer</h3>\'+
							\'<table cellspacing="1" cellpadding="1">\'+							
							\'<tbody>\'+
								\'<tr>\'+									
									\'<td valign="top"><img class="img-rounded" onclick="preview_image(\'+link_image+\');" alt="Image Outlet" style="width:100px; height:100px;" src="'.$urlimage.@$d_img->image.'"></td>\'+
									\'<td>\'+
										\'<div class="well bg-info" style="min-width:355px; border: none !important;">\'+
											\'<table cellspacing="1" cellpadding="1" >\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama TPE</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_salesman'].' ('.@$map['salesmanid'].')</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Customerid</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['customerid'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Customer</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$map['nama_customer'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Alamat</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted">'.@$map['alamat'].'</td>\'+	
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
										\'<th>Check IN</th>\'+
										\'<th>CRC Time</th>\'+
										\'<th>Order Time</th>\'+
										\'<th>Tagihan Time</th>\'+
										\'<th>Check OUT</th>\'+
										\'<th>Lama Kunjungan</th>\'+
										\'<th>Alasan</th>\'+
									\'</tr>\'+
								\'</thead>\'+
								\'<tbody>\'+
									\'<tr>\'+
										\'<td class="success" style="text-align:center;">'.@$d_rrk->check_in.'</td>\'+	
										\'<td class="active" style="text-align:center;">'.@$d_rrk->crc_time.'</td>\'+
										\'<td class="active" style="text-align:center;">'.@$d_rrk->order_time.'</td>\'+
										\'<td class="active" style="text-align:center;">'.@$d_rrk->tagihan_time.'</td>\'+
										\'<td class="success" style="text-align:center;">'.@$d_rrk->check_out.'</td>\'+	
										\'<td class="danger" style="text-align:center;">'.@$d_rrk->lama_kunjungan.'</td>\'+
										\'<td class="info" style="text-align:left;padding-left:10px;">'.@$d_rrk->alasan.'</td>\'+	
									\'</tr>\'+
								\'</tbody>\'+
							\'</table>\'+
							\'</div>\'+	
							\'<div><h3>Tagihan</h3>\'+
							'.$get_tagihan.'
							\'</div>\'+							
							\'<div><h3>Order</h3>\'+
							'.$get_order.'
							\'</div>\'+							
							\'<div><h3>Retur</h3>\'+
							'.$get_retur.'
							\'</div>\'+
							\'<div><h3>Daily CRC (Quantity in pcs)</h3>\'+
							'.$get_crecord.'
							\'</div>\'+
						  \'</div>\';
				
						  
						  var infowindow_'.$i.' = new google.maps.InfoWindow({
							content: contentString_'.$i.'
						  });

						
							
						  var markerOptions = {  
								map: map,  
								position: new google.maps.LatLng('.$lat_sales.','.$long_sales.'),
								icon: \''.$icon_sales.'\'
						  };
						  
						  marker_'.$i.' = new google.maps.Marker(markerOptions);
						  
							marker_'.$i.'.addListener(\'click\', function() {
							infowindow_'.$i.'.open(map, marker_'.$i.');
						   });


						  customTxt_'.$i.' = "<div style=\'font-size:8px;color:black;background:white;\'>'.$num.' - '.$map['nama_customer'].'</div>"
						  txt = new TxtOverlay(latlng, customTxt_'.$i.', "customBox2", map)
						  
						  ';
				}
				$num++;			
			}
			//$outlet .='<a href=\"javascript:void(0)\" onclick=\"open_modal('.$customerid.','.$salesmanid.');\">'.$map['nama_customer'].'</a><br>';
			
			$i++;
		}	
		$get_latlong = $this->sales->get_lat_long();
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


					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
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
									$html .='start = new google.maps.LatLng('.$lat.','.$long.');';
									}
									
									if ($numtrans>=2 && $numtrans<=9){
									$html .='waypts.push({ location: \''.$lat.','.$long.'\',stopover: true});';
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
	
			</script>';
		
		echo $html;
			
				
	}

	function get_gmaptracking() {
		
		$html 		= '';
		$sid 		= $this->input->post("sid");
		$get_date 	= $this->input->post("get_date");
		
		//print_r($get_map); die();
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
		$d_posisisales = $this->sales->get_tracking($sid,$get_date);
		$cnttracking = count($d_posisisales); 
		foreach ($d_posisisales as $d_position) {
			$latpos = $d_position['latitude_cell'];
			$longpos = $d_position['longitude_cell'];
			$waktu = $d_position['waktu'];
			if ($numpos==0){
				$icon = base_url().'assets/mapIcon/tracking_stop.png';
			} else if ($numpos==$cnttracking){
				$icon = base_url().'assets/mapIcon/tracking_run.png';
			}else{
				$icon = base_url().'assets/mapIcon/tracking_transit.png';
			}
			
			if (($latpos) and ($longpos !="0")) {
			$marker .=' var latlng = new google.maps.LatLng('.$latpos.','.$longpos.');
						var markerOptions = {  
							map: map,  
							position: new google.maps.LatLng('.$latpos.','.$longpos.'),
							icon: \''.$icon.'\'
							};
					  
						marker_'.$i.' = new google.maps.Marker(markerOptions);
					  
						marker_'.$i.'.addListener(\'click\', function() {
						infowindow_'.$i.'.open(map, marker_'.$i.');
					   });


					  customTxt_'.$i.' = "<div>'.$waktu.'</div>"
					  txt = new TxtOverlay(latlng, customTxt_'.$i.', "customBox2", map)
					  
					  ';
			}
			
			$numpos++;
		}
		echo $numpos."|".$cnttracking; 
		echo $marker;
		$get_latlong = $this->sales->get_lat_long();
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


					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
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
							$flag = $map['flag'];
							//$sales_lat = $d_posisisales['latitude_cell'];
							//$sales_long = $d_posisisales['longitude_cell'];
							
								$numtrans++;
								if (($lat != "0") and ($long !="0")) {
									
									if ($vstart== "")
									{
									$vstart=$lat.','.$long;
									$html .='start = new google.maps.LatLng('.$lat.','.$long.');';
									}
									
									if ($numtrans>=2 && $numtrans<=9){
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
	
			</script>';
		
		echo $html;
			
				
	}
	
	function open_detail() {
		
		$sid = $this->input->post("sid");
		$date = $this->input->post("get_date");
		$siteid = $this->sales->get_siteid();

		$target_detail = $this->sales->target_detail($sid,$date);
		$html = $target_detail;

		$html .='<div><h3>Summary</h3>';
		$qtotorder = $this->db->query("
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

		$qtotretur = $this->db->query("
					select 
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
					   sum(case when dtl.flag_bonus = 0 then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
					   sum(case when dtl.flag_bonus = 0 then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
					from 
					t_sales_master sls left join
					t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales
					where sls.retur=1 and 
						  sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$sid."' AND
						  sls.tanggal >= '".$date."' AND
						  sls.tanggal <= '".$date."'
				");
		$datatotretur = $qtotretur->result_array();
		if ($datatotretur){
		foreach ($datatotretur as $vretur) {
			$totbrutoretur		= number_format($vretur['total_bruto'], 2, '.', ',');
			$totdiscretur		= number_format($vretur['total_discount'], 2, '.', ',');
			$totnettoretur		= number_format($vretur['total_netto'], 2, '.', ',');
		}
		}else{
			$totbrutoretur		= 0;
			$totdiscretur		= 0;
			$totnettoretur		= 0;
		}
		
		$qtottagihan = $this->db->query("
			select sum(ifnull(bayar_tunai,0)) bayar_tunai,sum(bayar_transfer) bayar_transfer,sum(bayar_giro) bayar_giro, sum(bayar) bayar 
					from t_ar_ink_detail
			where siteid = '".$siteid."' AND 
				  tgl_ink = '".$date."' AND
				  salesmanid = '".$sid."'
		");
		
		$datatottagihan = $qtottagihan->result_array();
		if ($datatottagihan){
		foreach ($datatottagihan as $vtagihan) {
			$bayar			= number_format($vtagihan['bayar'], 2, '.', ',');
			$bayartunai		= number_format($vtagihan['bayar_tunai'], 2, '.', ',');
			$bayartransfer	= number_format($vtagihan['bayar_transfer'], 2, '.', ',');
			$bayargiro		= number_format($vtagihan['bayar_giro'], 2, '.', ',');
			$totalbayar		= number_format($vtagihan['bayar_tunai']+$vtagihan['bayar_transfer']+$vtagihan['bayar_giro'], 2, '.', ',');
		}
		}else{
			$bayar			= 0;
			$bayartunai		= 0;
			$bayartransfer	= 0;
			$bayargiro		= 0;
			$totalbayar		= 0;
		}
		
		
		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:400px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th colspan="2" style="white-space: nowrap;text-align:left; padding-left:15px">Order</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';		
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Bruto</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totbruto.'</td>';
		$html .= '</tr><tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Discount</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totdisc.'</td>';
		$html .= '</tr><tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Netto</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totnetto.'</td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<th colspan="2" style="white-space: nowrap;text-align:left; padding-left:15px" >Retur</th>';
		$html .= '</tr><tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Bruto</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totbrutoretur.'</td>';
		$html .= '</tr><tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Discount</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totdiscretur.'</td>';
		$html .= '</tr><tr>';
		$html .= '<tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Total Netto</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totnettoretur.'</td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<th colspan="2" style="white-space: nowrap;text-align:left; padding-left:15px" >Tagihan</th>';
		$html .= '</tr><tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Nilai Tagihan</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$bayar.'</td>';
		$html .= '</tr><tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Bayar Tunai</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$bayartunai.'</td>';
		$html .= '</tr><tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Bayar Transfer</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$bayartransfer.'</td>';
		$html .= '</tr><tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Bayar Giro</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$bayargiro.'</td>';
		$html .= '</tr><tr>';
		$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">Bayar Total</td>';
		$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.$totalbayar.'</td>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		
		// $q = $this->db->query("
			// select 
			   // rrk_trans.customerid,
			   // cst.nama_customer,
			   // cst.alamat,
			   // sum(ifnull(dtl.netto,0)) as total_netto,
			   // DATE_FORMAT(check_in, '%H:%i:%s') check_in
			// from 
			// t_sales_rrk_trans rrk_trans left join t_sales_master sls 
			// on rrk_trans.siteid = sls.siteid and rrk_trans.periode = sls.tgl_periode and rrk_trans.salesmanid = sls.salesmanid 
			// and rrk_trans.customerid = sls.customerid and rrk_trans.salesmanid = sls.salesmanid and rrk_trans.customerid = sls.customerid
			// left JOIN t_sales_detail dtl 
			// on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales 
			// left JOIN m_customer cst 
			// on rrk_trans.siteid = cst.siteid and rrk_trans.customerid = cst.customerid and rrk_trans.salesmanid = cst.salesmanid 
			// left JOIN m_sales_salesman salesamn 
			// on rrk_trans.siteid = salesamn.siteid and rrk_trans.salesmanid = salesamn.salesmanid 
			// left JOIN m_product product 
			// on dtl.productid = product.productid 
			// where rrk_trans.siteid = '".$siteid."' AND 
				  // rrk_trans.salesmanid = '".$sid."' AND
				  // date(rrk_trans.tgl_proses) = '".$date."' 
			// group by 
				   // rrk_trans.customerid,
				   // cst.nama_customer,
				   // cst.alamat
			// order by rrk_trans.check_in asc
		// ");
		
		$q = $this->db->query("
								select * from (
								select 
								   rrk_trans.customerid,
								   cst.nama_customer,
								   cst.alamat,
								   sum(ifnull(dtl.netto,0)) as total_netto,
								   DATE_FORMAT(rrk_trans.check_in, '%H:%i:%s') check_in,
								   case when rrk.customerid is null and sls.no_sales is null then 'InvalidCall' 
										when rrk.customerid is null and sls.no_sales is not null then 'ExtraCall' 
										when rrk.customerid is not null and sls.no_sales is null or (sls.no_sales is not null and sls.retur=1) then 'Call'
										when rrk.customerid is not null and sls.no_sales is not null and sls.retur=0 then 'EffectiveCall' 
										when rrk.customerid is not null and rrk_trans.check_in is null then 'Schedule' 
									end as flag,ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) as jarak, 
									cst.mcc fjp,(select count(1) from t_sales_rrk_trans where customerid=cst.customerid and salesmanid=rrk_trans.salesmanid 
												and periode between DATE_ADD(rrk_trans.periode, INTERVAL - DAYOFMONTH(rrk_trans.periode)+1 day) and rrk_trans.periode
												and order_time is not null)as co
								from 
								t_sales_rrk_trans rrk_trans left join t_sales_rrk rrk on 
								rrk_trans.siteid = rrk.siteid and rrk_trans.periode = rrk.periode and rrk_trans.salesmanid = rrk.salesmanid and rrk_trans.customerid = rrk.customerid
								left join t_sales_master sls 
								on rrk_trans.siteid = sls.siteid and rrk_trans.periode = sls.tgl_periode and rrk_trans.salesmanid = sls.salesmanid 
								and rrk_trans.customerid = sls.customerid and rrk_trans.salesmanid = sls.salesmanid and rrk_trans.customerid = sls.customerid
								left JOIN t_sales_detail dtl 
								on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales 
								left JOIN m_customer cst 
								on rrk_trans.siteid = cst.siteid and rrk_trans.customerid = cst.customerid and rrk_trans.salesmanid = cst.salesmanid 
								left JOIN m_sales_salesman salesamn 
								on rrk_trans.siteid = salesamn.siteid and rrk_trans.salesmanid = salesamn.salesmanid 
								left JOIN m_product product 
								on dtl.productid = product.productid 
								where rrk_trans.siteid = '".$siteid."' AND 
									  rrk_trans.salesmanid = '".$sid."' AND
									  date(rrk_trans.tgl_proses) = '".$date."' 
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
								   'Schedule' as flag,
								   ROUND(CALCULATE_DISTANCE(cst.latitude,cst.longitude,rrk_trans.latitude_cell,rrk_trans.longitude_cell),2) as jarak,
								   cst.mcc fjp, (select count(1) from t_sales_rrk_trans where customerid=cst.customerid and salesmanid=rrk_trans.salesmanid 
												and periode between DATE_ADD(rrk_trans.periode, INTERVAL - DAYOFMONTH(rrk_trans.periode)+1 day) and rrk_trans.periode
												and order_time is not null)as co
								from 
						  t_sales_rrk rrk left join t_sales_rrk_trans rrk_trans 
						  on rrk.siteid = rrk_trans.siteid and rrk.periode = rrk_trans.periode and rrk.salesmanid = rrk_trans.salesmanid 
						  and rrk.customerid = rrk_trans.customerid
						  left JOIN m_customer cst 
								on rrk.siteid = cst.siteid and rrk.customerid = cst.customerid and rrk.salesmanid = cst.salesmanid 
								where rrk.siteid = '".$siteid."' AND 
									  rrk.salesmanid = '".$sid."' AND
									  date(rrk.tgl_proses) = '".$date."' AND
									  rrk_trans.check_in is null          
								group by 
									   rrk.customerid,
									   cst.nama_customer,
									   cst.alamat
					) fnl order by fnl.check_in asc
		");
		
		$i = 1;

		$data = $q->result_array();
		
		$html .='<div><h3>Customer</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;">No</th>';
		$html .= '<th style="white-space: nowrap;">Action</th>';
		$html .= '<th style="white-space: nowrap;">Customer ID</th>';
		$html .= '<th style="white-space: nowrap;">Nama Customer</th>';
		$html .= '<th style="white-space: nowrap;">Alamat</th>';
		$html .= '<th style="white-space: nowrap;">Check In</th>';
		$html .= '<th style="white-space: nowrap;">Akurasi(Km)</th>';
		$html .= '<th style="white-space: nowrap;">FJP</th>';
		$html .= '<th style="white-space: nowrap;">CO</th>';
		$html .= '<th style="white-space: nowrap;">Total Netto</th>';
		$html .= '<th style="white-space: nowrap;">Flag</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$icon = '';
		foreach ($data as $value) {
			$flag = $value['flag'];
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
			} 

			$html .= '<tr>';
			$html .= '<td>'.$i.'</td>';
			$html .= '<td style="white-space: nowrap;"><a class="btn btn-primary btn-xs" href="#" onclick="toggle_visibility(\'tr_detail_'.$i.'\'); return false;">Detail</a></td>';
			$html .= '<td style="white-space: nowrap;">'.$value['customerid'].'</td>';
			$html .= '<td style="white-space: nowrap;">'.$value['nama_customer'].'</td>';
			$html .= '<td>'.$value['alamat'].'</td>';
			$html .= '<td class="success" style="text-align:center;">'.$value['check_in'].'</td>';
			$html .= '<td class="success" style="text-align:center;">'.$value['jarak'].'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;">'.$value['fjp'].'</td>';
			$html .= '<td style="white-space: nowrap;text-align:center;">'.$value['co'].'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($value['total_netto'], 0, '.', ',').'</td>';
			$html .= '<td class="success" style="text-align:center;"><img class="img-rounded" alt="Image Outlet" style="width:20px; height:20px;" src="'.$icon.'">'.$flag.'</td>';
			
			$html .= '</tr>';	
			$html .= '<tr id="tr_detail_'.$i.'" style="display: none;">';
			$html .= '<td colspan="6">';
			
			$customerid = $value['customerid'];
			$html .= '<div id="detail_product_"'.$i.' style="overflow-y: auto; max-height: 300px; max-width: 900px; white-space: nowrap; ">';
			
			$get_tagihan = $this->sales->get_tagihan_sales($siteid,$sid,$date,$customerid);
			$d_rrk = $this->sales->get_detail_rrk($siteid,$customerid,$sid,$date);

			$html .='<table class="table table-striped table-bordered table-condensed" style="width:700px;">
						<thead>
							<tr style="align:center;">
								<th width="70">Check IN</th>
								<th width="70">CRC Time</th>
								<th width="70">Order Time</th>
								<th width="70">Tagihan Time</th>
								<th width="70">Check OUT</th>
								<th width="70">Lama Kunjungan</th>
								<th>Alasan</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td class="success" style="text-align:center;">'.@$d_rrk->check_in.'</td>
								<td class="active" style="text-align:center;">'.@$d_rrk->crc_time.'</td>
								<td class="active" style="text-align:center;">'.@$d_rrk->order_time.'</td>
								<td class="active" style="text-align:center;">'.@$d_rrk->tagihan_time.'</td>
								<td class="success" style="text-align:center;">'.@$d_rrk->check_out.'</td>
								<td class="danger" style="text-align:center;">'.@$d_rrk->lama_kunjungan.'</td>
								<td class="info" style="text-align:left;padding-left:10px;">'.@$d_rrk->alasan.'</td>
							</tr>
						</tbody>
					</table>';
			
			$html .= $get_tagihan;

				$html .= '<h4>Order</h4>'; 
				$html .= '<table class="table table-striped table-bordered table-condensed">';
				$html .= '<thead>';
				$html .= '<tr>';
					$html .= '<th style="white-space: nowrap;padding-left:10px">Product ID </th>';
					$html .= '<th style="white-space: nowrap;padding-left:10px" >Nama Invoice</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Harga Jual</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Total Bruto</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Total Diskon</th>';
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
					m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid left JOIN
					m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
					m_product product on dtl.productid = product.productid 
					where sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$sid."' AND
						  sls.retur=0 AND
						  sls.tanggal >= '".$date."' AND
						  sls.tanggal <= '".$date."' AND
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
					$html .= '<tr>';						
						$html .= '<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['productid'].'</td>';
						$html .= '<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['nama_invoice'].'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_bruto'], 2, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_discount'], 2, '.', ',').'</td>';			
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>';
					$html .= '</tr>';
					$totbruto = $totbruto+$v_detail['total_bruto'];
					$totdisc = $totdisc+$v_detail['total_discount'];
					$totnetto = $totnetto+$v_detail['total_netto'];
				}
				$html .= '<tr>';
				$html .= '<th colspan="4" style="white-space: nowrap;padding-left:10px">Total </th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totbruto, 2, '.', ',').'</th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totdisc, 2, '.', ',').'</th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totnetto, 2, '.', ',').'</th>';	
				$html .= '</tr>';

				$html .= '</tbody>';
				$html .= '</table>';

				$html .= '<h4>Retur</h4>'; 
				$html .= '<table class="table table-striped table-bordered table-condensed">';
				$html .= '<thead>';
				$html .= '<tr>';
					$html .= '<th style="white-space: nowrap;padding-left:10px">Product ID </th>';
					$html .= '<th style="white-space: nowrap;padding-left:10px" >Nama Invoice</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Harga Jual</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Total Bruto</th>';
					$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Total Diskon</th>';
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
					m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid left JOIN
					m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
					m_product product on dtl.productid = product.productid 
					where sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$sid."' AND
						  sls.retur=1 AND
						  sls.tanggal >= '".$date."' AND
						  sls.tanggal <= '".$date."' AND
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
					$html .= '<tr>';						
						$html .= '<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['productid'].'</td>';
						$html .= '<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['nama_invoice'].'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_bruto'], 2, '.', ',').'</td>';
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_discount'], 2, '.', ',').'</td>';			
						$html .= '<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>';
					$html .= '</tr>';
					$totbruto = $totbruto+$v_detail['total_bruto'];
					$totdisc = $totdisc+$v_detail['total_discount'];
					$totnetto = $totnetto+$v_detail['total_netto'];
				}
				$html .= '<tr>';
				$html .= '<th colspan="4" style="white-space: nowrap;padding-left:10px">Total </th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totbruto, 2, '.', ',').'</th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totdisc, 2, '.', ',').'</th>';
				$html .= '<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totnetto, 2, '.', ',').'</th>';	
				$html .= '</tr>';
				$html .= '</tbody>';
				$html .= '</table>';
				
				$html .= '<h4>Daily CRC (Quantity in pcs)</h4>';
				$get_crc = $this->sales->get_crc($siteid,$sid,$date,$customerid);
				$html .= $get_crc;

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
	
	function get_fancy_image() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->sales->get_fancy();
        echo json_encode($list);
	}
	
}
