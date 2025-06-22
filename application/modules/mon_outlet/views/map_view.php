			<script type="text/javascript">
			//<![CDATA[
			
			var map; // Global declaration of the map
			var lat_longs_map = new Array();
			var markers_map = new Array();
            var iw_map;
			
			iw_map = new google.maps.InfoWindow();
				
				 function initialize_map() {
				
				var myLatlng = new google.maps.LatLng(-6.33731,109.125592);
				var myOptions = {
			  		zoom: 5,
					center: myLatlng,
			  		mapTypeId: google.maps.MapTypeId.ROADMAP}
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				
			var myLatlng = new google.maps.LatLng(-6.33731,109.125592);
			
				var marker_icon = {
					url: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|9999FF|000000"};
				
			var markerOptions = {
				map: map,
				position: myLatlng,
				icon: marker_icon		
			};
			marker_0 = createMarker_map(markerOptions);
			
			marker_0.set("content", "1 - Hello World!");
			
			google.maps.event.addListener(marker_0, "click", function(event) {
				iw_map.setContent(this.get("content"));
				iw_map.open(map, this);
			
			});
			
			var myLatlng = new google.maps.LatLng(-6.26712,106.83051);
				
			var markerOptions = {
				map: map,
				position: myLatlng,
				draggable: true,
				animation:  google.maps.Animation.DROP		
			};
			marker_1 = createMarker_map(markerOptions);
			
			var myLatlng = new google.maps.LatLng(-6.26455,106.81727);
				
			var markerOptions = {
				map: map,
				position: myLatlng		
			};
			marker_2 = createMarker_map(markerOptions);
			
				google.maps.event.addListener(marker_2, "click", function(event) {
					alert("You just clicked me!!")
				});
				
			var myLatlng = new google.maps.LatLng(-6.26536,106.82074);
				
			var markerOptions = {
				map: map,
				position: myLatlng,
				draggable: true,
				animation:  google.maps.Animation.DROP		
			};
			marker_3 = createMarker_map(markerOptions);
			
			var myLatlng = new google.maps.LatLng(-6.26865,106.82117);
				
			var markerOptions = {
				map: map,
				position: myLatlng		
			};
			marker_4 = createMarker_map(markerOptions);
			
				google.maps.event.addListener(marker_4, "click", function(event) {
					alert("You just clicked me!!")
				});
				
			fitMapToBounds_map();
			
			
			}
		
		
		function createMarker_map(markerOptions) {
			var marker = new google.maps.Marker(markerOptions);
			markers_map.push(marker);
			lat_longs_map.push(marker.getPosition());
			return marker;
		}
		
			function fitMapToBounds_map() {
				var bounds = new google.maps.LatLngBounds();
				if (lat_longs_map.length>0) {
					for (var i=0; i<lat_longs_map.length; i++) {
						bounds.extend(lat_longs_map[i]);
					}
					map.fitBounds(bounds);
				}
			}
			
			google.maps.event.addDomListener(window, "load", initialize_map);
			
			//]]>
			/* function preview_image() {
				 $.ajax({
					url: '<?php echo site_url('/') . $controller . "/get_fancy_image"; ?>',
					type: 'POST',
					async: false,
					data: {id: id},
					dataType: 'json',
					success: function (result) {
						// var p = result.images;
						var tempFile = [];
						$.each(result.images, function (index, value) {
							var tempFileElemet = {href: '<?php echo base_url() . DIR_IMAGE; ?>' + value.image_name, title: value.image_name};
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
			} */
			</script>

			<div class="row">
		<div id="table-content" class="col-lg-12">
			<div id="map_canvas" style="width:100%; height:450px;"></div>
		</div>
	</div>

	<div class="row">
		<div class="extra-footer col-lg-12" style="height:25px;"></div>
		<div class="col-lg-12 page-footer-table"></div>
		<!-- only space 
		-->
	</div>
			
