<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mon_outlet extends CI_Controller {
	
	var $gparam = array();
	
	public function __construct() {
        parent::__construct();		
		
		$this->gparam['controller'] = $this->router->fetch_class();
		$this->load->model('mon_outlet_model', 'model');
		$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
		//$this->load->library('googlemaps');
    }
	
	public function index()	{
		
		if ($this->gparam['privilage']->privilage_view == 'Y') {
		
			$gridopt = $this->input->get(array('psize', 'pnumber'));
			header('Content-Type: text/html');
			
			$get_latlong = $this->model->get_lat_long();

			// pnumber, psize
			$segment_options = '';
			$get_segment = $this->model->get_data_segment();
			foreach ($get_segment as $value_segment) {
				$segment_options .= '<option value="'.$value_segment['segmentid'].'">'.$value_segment['nama_segment'].'('.$value_segment['segmentid'].')</option>';
			}

			$class_options = '';
			$get_class = $this->model->get_data_class();
			foreach ($get_class as $value_class) {
				$class_options .= '<option value="'.$value_class['classid'].'">'.$value_class['nama_class'].'('.$value_class['classid'].')</option>';
			}
			
			$type_options = '';
			$get_type = $this->model->get_data_type();
			foreach ($get_type as $value_type) {
				$type_options .= '<option value="'.$value_type['typeid'].'">'.$value_type['nama_type'].'('.$value_type['typeid'].')</option>';
			}

			$data = array(
					'controller'  	=> $this->gparam['controller'],
					'psize'			=> (empty($gridopt['psize'])?10:$gridopt['psize']),
					'pnumber'  		=> (empty($gridopt['pnumber'])?1:$gridopt['pnumber']),
					'lat'			=> $get_latlong->latitude,
					'long'			=> $get_latlong->longitude,
					'segment_options' => $segment_options,
					'class_options' => $class_options,
					'type_options' => $type_options
			);
			
			$this->load->view('mon_outlet_view',$data);
		} else {
			echo $this->gparam['restrict'];
		}
	}
	
	
	function load_data() {
		header('Content-Type: application/jsonp');
        $list = $this->model->get_list_data();
		
        echo json_encode($list);
	}
	
	function get_gmap() {
		
		$html 		= '';
		$segmentid 	= $this->input->post("segmentid");
		$classid 	= $this->input->post("classid");
		$typeid 	= $this->input->post("typeid");

		$get_map 	= $this->model->get_outlet($segmentid,$classid,$typeid);
		
		
		//print_r($get_map); die();
		$marker_outlet 	= "";
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
					url:"'.site_url().'/mon_outlet/open_crc",
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
			
			$vidoutlet = $map['customerid'];
			$voutlet = $map['nama_customer'];
			$valamat = $map['alamat'];
				
			$icon = '';
			$icon = base_url().'assets/mapIcon/res/drawable-hdpi/jadwal.png';//"https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png";
			$urlimage = base_url().DIR_IMAGE;
			
			$lat = $map['latitude_cell'];
			$long = $map['longitude_cell'];			
			
				if (($lat != "0") and ($long !="0")) {

					$marker_outlet .=' var latlng = new google.maps.LatLng('.$lat.','.$long.');
									   var contentString_'.$i.' = 
										\'<div class="well bg-info" style="border: none !important;">\'+
											\'<table cellspacing="1" cellpadding="1" >\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Customerid</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.$map['customerid'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Nama Customer</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.$map['nama_customer'].'</td>\'+	
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Alamat</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted">'.$map['alamat'].'</td>\'+	
												\'</tr>\'+											
											\'</table>\'+
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
						  
							marker_'.$i.'.addListener(\'mouseover\', function() {
							infowindow_'.$i.'.open(map, marker_'.$i.');
						   });
							marker_'.$i.'.addListener(\'mouseout\', function() {
							infowindow_'.$i.'.close(map, marker_'.$i.');
						   });

						  ';
				}
				$i++;			

		}

		$get_latlong = $this->model->get_lat_long();
		$html .='<script type="text/javascript">
					var directionDisplay;
					var directionsService = new google.maps.DirectionsService();
					var map;
					function initialize() {
					  directionsDisplay = new google.maps.DirectionsRenderer({
						  suppressMarkers : true
					  });


					  var myOptions = {
						  zoom: 10,
						  center: new google.maps.LatLng('.$get_latlong->latitude.','.$get_latlong->longitude.'),  
						  mapTypeId: google.maps.MapTypeId.ROADMAP,
						  zoomControl: true,
					  }


					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
					/*map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById("filter"));*/
					'.$marker_outlet.'
					directionsDisplay.setMap(map);
					
					}
					
					initialize();					
	
			</script>';
		
		echo $html;
			
				
	}

	function get_fancy_image() {
		header('Content-Type: application/json'); // parsing json
        $list = $this->model->get_fancy();
        echo json_encode($list);
	}
	
}
