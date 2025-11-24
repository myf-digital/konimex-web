<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_visit extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_visit_model', 'visit');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function create()
    {
		$message = 'Success';
        $data = param_input();

        //file upload		
        if (isset($_FILES["fileupload"])) {
			if ($_FILES['fileupload']['size'] > (2 * 1024 * 1024)) {
				return response("File terlalu besar. Maksimal 2MB.");
			}

			$path = FCPATH.'uploads/absence/'.date('Ym').'/';
			if (!is_dir($path)) {
				mkdir($path, 0777, true);
			}

			$file_name = $_FILES["fileupload"]["name"];
			$tmp = explode('.', $file_name);
			$file_extension = end($tmp);

	        $fileName = date('YmdHis').'-'.$data['salesmanid'].'.'.$file_extension; 
			$config['upload_path']   = $path;
			$config['allowed_types'] = 'jpg|jpeg|png|webp|heic|heif';
			$config['max_size']      = 2048;
			$this->load->library('upload', $config);

			$message = 'Success';
			if ($this->upload->do_upload('fileupload')) {
				$dataUpload = $this->upload->data();

				$source = $dataUpload['full_path'];
				$dest   = $dataUpload['file_path'] . $fileName;

				$this->load->helper('image');

				$compressed = compress_image($source, $dest);
				if ($compressed) {
	        		$data['image'] = $compressed;
				} else {
					$message = "Gagal compress.";
				}

			} else {
				$message = $this->upload->display_errors();
			}
        }

		if ($message != 'Success') {
			return response($message);
		}

        response($this->visit->create($data), 200, $message);
    }

    public function load()
    {
        $data = param_input();
		echo $this->db->last_query();
        responseJSON($this->visit->load($data));
    } 

	function get_gmap() {
		
		$html 		= '';
		$sid 		= $this->input->post("sid");
		$periode 	= $this->input->post("periode");
		
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
		$d_posisisales = $this->visit->get_tracking($sid,$periode);
		$cnttracking = count($d_posisisales); 
		$starttime = '';
		foreach ($d_posisisales as $d_position) {
			$latpos = $d_position['latitude_cell'];
			$longpos = $d_position['longitude_cell'];
			$waktu = $d_position['lamakunjungan'];
			$checkin = $d_position['checkin'];
			$checkout = $d_position['checkout'];
			$outletid = $d_position['customerid'];
			$name = $d_position['nama_customer'];
			$add = $d_position['alamat'];
			$account = $d_position['account'];
			$icon = base_url().'assets/mapIcon/tracking.png';
			
			if (($latpos) and ($longpos !="0")) {
			$marker .=' var latlng = new google.maps.LatLng('.$latpos.','.$longpos.');
						var markerOptions = {  
							map: map,  
							position: new google.maps.LatLng('.$latpos.','.$longpos.'),
							icon: {
								url: \''.$icon.'\',
								labelOrigin: { x: 17, y: 40}
							  },
							  title: \''.@$name.'\',
							  label: {
								text: \''.@$name.'\',
								color: "#222222",
								fontSize: "10px"
							  }
						};

						var contentString_'.$numpos.' = \'<div id="content" style="max-width:1000px;">\'+
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
													\'<td class="text-muted" style="white-space: nowrap;">'.$outletid.'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Outlet</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$name.' ('.@$account.')</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Alamat</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted">'.@$add.'</td>\'+	
												\'</tr>\'+											
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">CheckIn</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$checkin.'</td>\'+
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">CheckOut</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$checkout.'</td>\'+
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Lama Kunjungan</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$waktu.'</td>\'+
												\'</tr>\'+
											\'</table>\'+
										\'</div>\'+											
									\'</td>\'+							
								\'</tr>\'+														
							\'</tbody>\'+
							\'</table>\'+
							\'</div>\'+
						  \'</div>\';
						  
					  var infowindow_'.$numpos.' = new google.maps.InfoWindow({
						content: contentString_'.$numpos.'
					  });
					  
						marker_'.$numpos.' = new google.maps.Marker(markerOptions);
					  
						marker_'.$numpos.'.addListener(\'click\', function() {
						infowindow_'.$numpos.'.open(map, marker_'.$numpos.');
					   });


					  //customTxt_'.$numpos.' = "<div>'.$waktu.'</div>";
					  //txt = new TxtOverlay(latlng, customTxt_'.$numpos.', "customBox2", map);
					  
					  ';
			}
			
			$numpos++;
		}
		//echo $numpos."|".$cnttracking; 
		//echo $marker;
		$get_latlong = $this->visit->get_lat_long();
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

}
