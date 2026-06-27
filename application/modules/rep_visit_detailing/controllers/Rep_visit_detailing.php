<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Rep_visit_detailing extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_visit_detailing_model', 'visit_detailing');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
		echo $this->db->last_query();
        responseJSON($this->visit_detailing->load($data));
    } 

    public function survei()
    {
        $data = param_input();
		echo $this->db->last_query();
        responseJSON($this->visit_detailing->get_survei($data));
    } 

	function get_gmap()
    {		
		$html 		= '';
		$sid 		= $this->input->post("sid");
		$periode 	= $this->input->post("periode");
		
		$total = 0 ;
		$marker 	= "";
		$i = 0;
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

		$numpos = 0;
		$d_posisisales = $this->visit_detailing->get_tracking($sid,$periode);
		foreach ($d_posisisales as $d_position) {
			$latpos = $d_position['latitude_cell'];
			$longpos = $d_position['longitude_cell'];
			$waktu = $d_position['lamakunjungan'];
			$start_detailing = $d_position['start_detailing'];
			$end_detailing = $d_position['end_detailing'];
			$outletid = $d_position['customerid'];
			$name = $d_position['nama_customer'];
			$add = $d_position['alamat'];
			$account = $d_position['account'];
			$pic = $d_position['professional_name'];
			$spesialisasi = $d_position['spesialisasi'];
			$keterangan = $d_position['keterangan'];
			$reason = $d_position['reason'];
			$img = '';
			if ($d_position['url_img_detailing']) {
				$img = '<br/><img src="'.$d_position['url_img_detailing'].'" alt="foto" width="100">';
			}
			$products = "";
			if (!empty($d_position['products'])) {
				$p = explode('||',$d_position['products']);
				foreach ($p as $k => $pr) {
					$products .= ($k+1).'. '.$pr.'<br/>';
				}
			}
			if ($d_position['tipe_pic']) $pic .= ' ('.$d_position['tipe_pic'].')';
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
						\'<div style="z-index: -1;" id="siteNotice"><h3 style="margin-top: 0px;">Outlet</h3>'.$img.'\'+
						\'<table cellspacing="1" cellpadding="1">\'+
						\'<tbody>\'+
							\'<tr>\'+									
									\'<td>\'+
										\'<div class="well bg-info" style="min-width:355px; border: none !important;">\'+
											\'<table cellspacing="1" cellpadding="1" >\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">OutletId</td>\'+
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$outletid.'</td>\'+	
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
													\'<td class="text-muted" style="white-space: nowrap;">User</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$pic.'</td>\'+
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Spesialisasi</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$spesialisasi.'</td>\'+
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;vertical-align: top;">Produk</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;vertical-align: top;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$products.'</td>\'+
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Keterangan</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$keterangan.'</td>\'+
												\'</tr>\'+
												\'<tr>\'+
													\'<td class="text-muted" style="white-space: nowrap;">Reason</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td>\'+	
													\'<td class="text-muted" style="white-space: nowrap;">'.@$reason.'</td>\'+
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

		$get_latlong = $this->visit_detailing->get_lat_long();
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
    
    public function savetoxlsx($data)
    {
		ini_set('memory_limit', '512M');

        $data = [];
        $data['get_date1'] = $this->uri->segment('3');
        $data['get_date2'] = $this->uri->segment('4');

        $detailing = $this->visit_detailing->savetoxlsx($data);
        $filename = "report_visit_detailing_" . date('Y-m-d_His');
        $spreadsheet = new Spreadsheet();
        $header = [
            'No',
            'Periode',
            'User Login',
            'Salesman',
            'Outlet ID',
            'Outlet',
            'User',
            'Spesialisasi',
            'Produk',
            'Status',
            'Keterangan',
            'Reason',
			'Foto',
			'Signature',
        ];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Report Visit Detailing')->mergeCells('A1:M1');
        $sheet->fromArray($header,NULL,'A2');

        $i = 1;
        $row = 3;
        foreach ($detailing as $value) {
            $content = [
                $i,
				$value['periode'],
				$value['salesmanid'],
				$value['nama_salesman'],
				$value['customerid'],
				$value['nama_customer'],
				$value['professional_name'],
				$value['spesialisasi'],
				$value['products'],
				$value['status_label'],
				$value['keterangan'],
				$value['reason'],
				'',
				'',
			];
			// img_url_to_sheet($sheet, $value['url_img_detailing'], 'N'.$row);
            // $sheet->getStyle('N'.$row)->getAlignment()->setWrapText(true);
            $sheet->fromArray($content,NULL,'A'.$row);

			if (!empty($value['url_img_detailing'])) {
				$sheet->setCellValue('L'.$row, 'Foto Detailing');
				$sheet->getCell('L'.$row)
					->getHyperlink()
					->setUrl($value['url_img_detailing']);

				$sheet->getStyle('L'.$row)->applyFromArray([
					'font' => [
						'color' => ['rgb' => '0000FF'],
						'underline' => 'single'
					]
				]);
			}

			if (!empty($value['url_file_signature'])) {
				$sheet->setCellValue('M'.$row, 'Foto Signature');
				$sheet->getCell('M'.$row)
					->getHyperlink()
					->setUrl($value['url_file_signature']);

				$sheet->getStyle('M'.$row)->applyFromArray([
					'font' => [
						'color' => ['rgb' => '0000FF'],
						'underline' => 'single'
					]
				]);
			}

            $i++;
            $row++;
        }
		// Ambil range seluruh worksheet
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$fullRange = 'A1:' . $highestColumn . $highestRow;
		$sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
 
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
