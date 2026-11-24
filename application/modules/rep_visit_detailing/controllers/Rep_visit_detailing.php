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
		$jsJsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
		$escHtml = function ($val) {
			return htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8');
		};
		$iconJs = json_encode(base_url() . 'assets/mapIcon/tracking.png', $jsJsonFlags);

		$d_posisisales = $this->visit_detailing->get_tracking($sid, $periode);
		foreach ($d_posisisales as $d_position) {
			$latpos = $d_position['latitude_cell'];
			$longpos = $d_position['longitude_cell'];
			$waktu = $d_position['lamakunjungan'];
			$outletid = $d_position['customerid'] ?? '';
			$name = $d_position['nama_customer'] ?? '';
			$add = $d_position['alamat'] ?? '';
			$account = $d_position['account'] ?? '';
			$pic = $d_position['professional_name'] ?? '';
			$spesialisasi = $d_position['spesialisasi'] ?? '';
			$keterangan = $d_position['keterangan'] ?? '';
			$reason = $d_position['reason'] ?? '';

			$img = '';
			if (!empty($d_position['url_img_detailing'])) {
				$img = '<br/><img src="' . $escHtml($d_position['url_img_detailing']) . '" alt="foto" width="100">';
			}

			$products = '';
			if (!empty($d_position['products'])) {
				$p = explode('||', $d_position['products']);
				foreach ($p as $k => $pr) {
					$products .= ($k + 1) . '. ' . $escHtml($pr) . '<br/>';
				}
			}

			if (!empty($d_position['tipe_pic'])) {
				$pic .= ' (' . $d_position['tipe_pic'] . ')';
			}

			if (is_numeric($latpos) && is_numeric($longpos) && (float) $latpos != 0.0 && (float) $longpos != 0.0) {
				$latNum = (float) $latpos;
				$longNum = (float) $longpos;
				$nameJs = json_encode((string) $name, $jsJsonFlags);
				$outletLabel = $escHtml($name) . ($account !== '' ? ' (' . $escHtml($account) . ')' : '');

				$contentHtml = '<div id="content" style="max-width:1000px;">'
					. '<div style="z-index: -1;" id="siteNotice"><h3 style="margin-top: 0px;">Outlet</h3>' . $img
					. '<table cellspacing="1" cellpadding="1"><tbody><tr><td>'
					. '<div class="well bg-info" style="min-width:355px; border: none !important;">'
					. '<table cellspacing="1" cellpadding="1">'
					. '<tr><td class="text-muted" style="white-space: nowrap;">OutletId</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted" style="white-space: nowrap;">' . $escHtml($outletid) . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;">Nama Outlet</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted" style="white-space: nowrap;">' . $outletLabel . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;">Alamat</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted">' . nl2br($escHtml($add)) . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;">User</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted" style="white-space: nowrap;">' . $escHtml($pic) . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;">Spesialisasi</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted" style="white-space: nowrap;">' . $escHtml($spesialisasi) . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;vertical-align: top;">Produk</td><td class="text-muted" style="white-space: nowrap;vertical-align: top;">&nbsp;:&nbsp;</td><td class="text-muted" style="white-space: nowrap;">' . $products . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;">Keterangan</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted">' . nl2br($escHtml($keterangan)) . '</td></tr>'
					. '<tr><td class="text-muted" style="white-space: nowrap;">Reason</td><td class="text-muted" style="white-space: nowrap;">&nbsp;:&nbsp;</td><td class="text-muted">' . nl2br($escHtml($reason)) . '</td></tr>'
					. '</table></div></td></tr></tbody></table></div></div>';

				$contentJs = json_encode($contentHtml, $jsJsonFlags);

				$marker .= '
					var latlng = new google.maps.LatLng(' . $latNum . ', ' . $longNum . ');
					var markerOptions = {
						map: map,
						position: latlng,
						icon: {
							url: ' . $iconJs . ',
							labelOrigin: { x: 17, y: 40 }
						},
						title: ' . $nameJs . ',
						label: {
							text: ' . $nameJs . ',
							color: "#222222",
							fontSize: "10px"
						}
					};
					var contentString_' . $numpos . ' = ' . $contentJs . ';
					var infowindow_' . $numpos . ' = new google.maps.InfoWindow({
						content: contentString_' . $numpos . '
					});
					var marker_' . $numpos . ' = new google.maps.Marker(markerOptions);
					marker_' . $numpos . '.addListener("click", function() {
						infowindow_' . $numpos . '.open(map, marker_' . $numpos . ');
					});
				';
			}

			$numpos++;
		}

		$get_latlong = $this->visit_detailing->get_lat_long();
		$centerLat = (isset($get_latlong->latitude) && is_numeric($get_latlong->latitude)) ? (float) $get_latlong->latitude : -6.200000;
		$centerLng = (isset($get_latlong->longitude) && is_numeric($get_latlong->longitude)) ? (float) $get_latlong->longitude : 106.816666;

		$html .= '<script type="text/javascript">
					var directionsDisplay;
					var directionsService = new google.maps.DirectionsService();
					var map;
					function initialize() {
					  directionsDisplay = new google.maps.DirectionsRenderer({
						  suppressMarkers : true
					  });

					  var myOptions = {
						zoom: 7,
						center: new google.maps.LatLng(' . $centerLat . ', ' . $centerLng . '),
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						zoomControl: true,
					  };

					  map = new google.maps.Map(document.getElementById("maps"), myOptions);
					  ' . $marker . '

					  directionsDisplay.setMap(map);

					  calcRoute();
					}

					function calcRoute() {
						var waypts = [];
						var start = "";
						var end = "";
						';

						$vstart = "";
						$vstop = "";
						$numtrans = 0;
						foreach ($d_posisisales as $mapRow) {
							$lat = $mapRow['latitude_cell'];
							$long = $mapRow['longitude_cell'];
							if (is_numeric($lat) && is_numeric($long) && (float) $lat != 0.0 && (float) $long != 0.0) {
								$latNum = (float) $lat;
								$longNum = (float) $long;
								$numtrans++;

								if ($vstart === "") {
									$vstart = $latNum . ',' . $longNum;
									$html .= 'start = new google.maps.LatLng(' . $latNum . ', ' . $longNum . ');';
								}

								if ($numtrans >= 2 && $numtrans <= 10) {
									$html .= 'waypts.push({ location: ' . json_encode($latNum . ',' . $longNum, $jsJsonFlags) . ', stopover: true });';
								}

								$vstop = $latNum . ',' . $longNum;
							}
						}

					if ($vstop !== "") {
						$html .= 'end = new google.maps.LatLng(' . $vstop . ');';
					}
					$html .= '
					if (start !== "" && end !== "") {
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
            'MEDREP',
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
