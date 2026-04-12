<style>
  <style>
    .customBox {
      background: yellow;
      position: absolute;
	  padding:2px;
	  min-width:10px;
	  position-y:+30px;
	  color:white;
    }
	
	.customBox2 {
      background: blue;      
      position: absolute;
	  padding:2px;
	  min-width:10px;
	  position-y:+15px;
	  color:white;
    }
	
	.controlUI {
      background-color: #fff;
      border: 2px solid #fff;
      border-radius: 3px;
      box-shadow: 0 2px 6px rgba(0,0,0,.3);
      margin-top: 22px;
      margin-right: 22px;
      text-align: center;
	  min-width:200px;
	  height:450px;
    }
    .controlText {
      color: rgb(25,25,25);
      font-family: Roboto,Arial,sans-serif;
      font-size: 12px;
      line-height: 18px;
      padding-left: 5px;
      padding-right: 5px;
    }
</style>


<!-- EasyUI CSS -->
<link href="<?php echo base_url() . "assets/" ?>easyui/themes/bootstrap/easyui.css" rel="stylesheet">
<link href="<?php echo base_url() . "assets/" ?>easyui/themes/icon.css" rel="stylesheet">

<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- START ROW -->
	<div class="row">
		<!-- NEW COL START -->
		<article class="col-sm-12 col-md-12 col-lg-12 ">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget" id="dash_map" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
				<!-- widget options:
				usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

				data-widget-colorbutton="false"
				data-widget-editbutton="false"
				data-widget-togglebutton="false"
				data-widget-deletebutton="false"
				data-widget-fullscreenbutton="false"
				data-widget-custombutton="false"
				data-widget-collapsed="true"
				data-widget-sortable="false"

				-->
				<header>
					<span class="widget-icon"> <i class="fa fa-map-marker"></i> </span>
					<h2>Dashboard Monitoring Sales </h2>

				</header>

				<!-- widget div-->
				<div>

					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->

					</div>
					<!-- end widget edit box -->

					<!-- widget content -->
					<div class="widget-body no-padding">
							
							<div id="map_canvas" style="height:500px; width:100%;">
							</div>

							<div id="tb" style="padding:5px;height:auto">
								<input type="text" id="get_date" name="get_date" value="<?php echo date("Y-m-d"); ?>" />	
								<a href="javascript:void(0)"  class="btn btn-primary" onclick="search_data();" ><i class="fa fa-lg fa-fw fa-search"></i></a>	
							</div>	
							<div id="table-content">							
								<table id="tbl">
								</table>
							</div>
							
							<div id="tb1" style="padding:5px;height:auto">
								<input type="text" id="get_date1" name="get_date1" value="<?php echo date("Y-m-d"); ?>" />	
								<input type="text" id="get_date2" name="get_date2" value="<?php echo date("Y-m-d"); ?>" />	
								<a href="javascript:void(0)"  class="btn btn-primary" onclick="search_data_summary();" ><i class="fa fa-lg fa-fw fa-search"></i></a>
							</div>	
							<div id="table-content">							
								<table id="tbl1">
								</table>
							</div>
					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

			</div>
			<!-- end widget -->

		</article>
		<!-- END COL -->

	</div>
</section>

<div class="modal fade" id="crc_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">List Data</h4>
      </div>
	  <div class="modal-body">
	 
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>       
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" >
    <div class="modal-content" style="overflow-y: auto; width:1000px; max-height: 500px;" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">List Data</h4>
      </div>
		<div class="modal-body" style=" max-height: 75%; width: 100%;">
	 
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>      
      </div>
    </div>
  </div>
</div>


<script>
	
	var map; // Global declaration of the map  
    var iw = new google.maps.InfoWindow(); // Global declaration of the infowindow  
	
    function initialize()   
    {  
        var myOptions = {  
            zoom: 12,  
            //center: new google.maps.LatLng(-6.26149,106.81060),
			center: new google.maps.LatLng(<?php echo $lat;?>,<?php echo $long;?>),  
            mapTypeId: google.maps.MapTypeId.ROADMAP  
        }  
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);  
		
		// Create the legend and display on the map
        //var legend = document.createElement('div');
        //legend.id = 'legend';
        //var content = [];
        //content.push('<h4>Legend</h4><hr>');
        //content.push('<p><img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_1.png"></img>Eff Call</p>');
        //content.push('<p><img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_2.png"></img>Ex Call</p>');
        //content.push('<p><img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_3.png"></img>Call</p>');
        //content.push('<p><img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_4.png"></img>Invalid Call</p>');
        //content.push('<p><img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_5.png"></img>Noo</p>');
        //legend.innerHTML = content.join('');
        //legend.index = 1;
        //map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);

          
        /*var markerOptions = {  
            map: map,  
            position: new google.maps.LatLng(-6.26149,106.807)       
        };  
        marker = new google.maps.Marker(markerOptions);  
          
        google.maps.event.addListener(marker, "click", function()  
        {  
            iw.setContent("This is an infowindow");  
            iw.open(map, this);  
        });*/   
    }
	
	var tbl, tbl1, setup; // setup is flag
    
	$(document).ready(function () {

    	//initialize();
		/* $.fn.datebox.defaults.formatter = function(date) {
			var y = date.getFullYear();
			var m = date.getMonth() + 1;
			var d = date.getDate();
			return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
		}

		// required
		$.fn.datebox.defaults.parser = function(s) {
			if (!s)
				return new Date();
			var ss = (s.split('-'));
			var y = parseInt(ss[0], 10);
			var m = parseInt(ss[1], 10);
			var d = parseInt(ss[2], 10);
			if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
				return new Date(y, m - 1, d);
			} else {
				return new Date();
			}
		} */
		
		$('#get_date').datebox({
			 formatter : function(date){
				var y = date.getFullYear();
				var m = date.getMonth()+1;
				var d = date.getDate();
				return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
			},
			
			parser : function(s){
				if (!s) return new Date();
				var ss = s.split('-');
				var y = parseInt(ss[0], 10);
				var m = parseInt(ss[1], 10);
				var d = parseInt(ss[2], 10);
				if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
					return new Date(y,m-1,d)
				} else {
					return new Date();
				}
			}
		});
		
		$('#get_date1').datebox({
			 formatter : function(date){
				var y = date.getFullYear();
				var m = date.getMonth()+1;
				var d = date.getDate();
				return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
			},
			
			parser : function(s){
				if (!s) return new Date();
				var ss = s.split('-');
				var y = parseInt(ss[0], 10);
				var m = parseInt(ss[1], 10);
				var d = parseInt(ss[2], 10);
				if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
					return new Date(y,m-1,d)
				} else {
					return new Date();
				}
			}
		});
		
		$('#get_date2').datebox({
			 formatter : function(date){
				var y = date.getFullYear();
				var m = date.getMonth()+1;
				var d = date.getDate();
				return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
			},
			
			parser : function(s){
				if (!s) return new Date();
				var ss = s.split('-');
				var y = parseInt(ss[0], 10);
				var m = parseInt(ss[1], 10);
				var d = parseInt(ss[2], 10);
				if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
					return new Date(y,m-1,d)
				} else {
					return new Date();
				}
			}
		});
		
		
		initialize();
		setup = 0;
		tbl = $('#tbl').datagrid({width: '100%'});
		tbl1 = $('#tbl1').datagrid({width: '100%'});
        // toolbar action
		var toolbarCostum = $('#tb');
		var toolbarCostum1 = $('#tb1');
		
		// handle when direct update or create
		tbl.datagrid(gridConfigGeneral);
		tbl1.datagrid(gridConfigGeneral);
		
		tbl.datagrid({
			pageNumber: '<?php echo $pnumber?>',
			pageSize: '<?php echo $psize?>',
		});
		
		tbl1.datagrid({
			pageNumber: '<?php echo $pnumber?>',
			pageSize: '<?php echo $psize?>',
		});
		
        tbl.datagrid({
			url: '<?php echo site_url('/') . $controller . '/load_data'; ?>',
            title: "List Data Sales",
            toolbar: toolbarCostum,
            onLoadSuccess: loadAction,
			height:400,
			frozenColumns:[[
				
			]],	
            columns: [[
                   
                    {field: 'options', title: 'ACTION', width: 170, halign: 'center', align: 'center', formatter: formatButton},
					{field: 'nama_salesman', title: 'Nama Medrep', width: 220, sortable: 'true', halign: 'center', align: 'center'},
					{field: 'jadwal', title: 'Scedule', width: 70, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'effectivecall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_1.png"></img> Eff Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'ExtraCall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_2.png"></img>Ex Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'cal', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_3.png"></img> Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'InvalidCall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_4.png"></img> Inv Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'noo', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_5.png" title="Register Titik Outlet"></img>RTO', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'eff_time', title: 'Eff Time', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'eff_order', title: 'Eff Order', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'amount', title: 'Amount', width: 150, sortable: 'true', halign: 'center', align: 'right', formatter: formatAmount},
                    {field: 'longlatnull', title: 'Blank Posisi', width: 100, sortable: 'true', halign: 'center', align: 'center'},
                    
                ]]
        });
		
        tbl1.datagrid({
			url: '<?php echo site_url('/') . $controller . '/load_data_summary'; ?>',
            title: "List Data Summary Sales",
            toolbar: toolbarCostum1,
            onLoadSuccess: loadAction,
			height:400,
			frozenColumns:[[
				
			]],	
            columns: [[
                   
                    //{field: 'options', title: 'ACTION', width: 170, halign: 'center', align: 'center', formatter: formatButton},
					{field: 'nama_salesman', title: 'Nama Medrep', width: 220, sortable: 'true', halign: 'center', align: 'center'},
					{field: 'jadwal', title: 'Scedule', width: 70, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'effectivecall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_1.png"></img> Eff Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'ExtraCall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_2.png"></img>Ex Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'cal', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_3.png"></img> Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'InvalidCall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_4.png"></img> Inv Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'noo', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_5.png" title="Register Titik Outlet"></img> RTO', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'eff_time', title: 'Eff Time', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'eff_order', title: 'Eff Order', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'amount', title: 'Amount', width: 150, sortable: 'true', halign: 'center', align: 'right', formatter: formatAmount},
                    {field: 'longlatnull', title: 'Blank Posisi', width: 100, sortable: 'true', halign: 'center', align: 'center'},
                    
                ]]
        });
    });
	
	function formatAmount(val, row, index) {
		var result;
		var valData = row.amount;
		
		result = CurrencyFormatted(valData);
		return result;
	}
	
	function CurrencyFormatted(amount) {
		var delimiter = ","; // replace comma if desired
		var a = amount.split('.',2)
		var d = a[1];
		var i = parseInt(a[0]);
		if(isNaN(i)) { return ''; }
		var minus = '';
		if(i < 0) { minus = '-'; }
		i = Math.abs(i);
		var n = new String(i);
		var a = [];
		while(n.length > 3)
		{
			var nn = n.substr(n.length-3);
			a.unshift(nn);
			n = n.substr(0,n.length-3);
		}
		if(n.length > 0) { a.unshift(n); }
		n = a.join(delimiter);
		if(d.length < 1) { amount = n; }
		else { amount = n + '.' + d; }
		amount = minus + amount;
		return amount;
	}
	
	function search_data() {
		var get = document.getElementsByName('get_date')[0].value;
		//alert(get);		
		tbl.datagrid('load',{
			get_date: get,			
		});
	}

	function search_data_summary() {
		var get1 = document.getElementsByName('get_date1')[0].value;
		var get2 = document.getElementsByName('get_date2')[0].value;
		//alert(get);
		tbl1.datagrid('load',{
			get_date1: get1,			
			get_date2: get2,			
		});
	}
	
	
	
	function loadAction() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			/* tbl.datagrid('enableFilter',
				 [ {
					field: 'created_date',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				},{
					field: 'modified_date',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				}] 
			);   */
			// remove filter option
			$("input[name='options']").remove();
			$("input[name='jadwal']").remove();
			$("input[name='effectivecall']").remove();
			$("input[name='extracall']").remove();
			$("input[name='cal']").remove();
			$("input[name='invalidcall']").remove();
			$("input[name='noo']").remove();
			$("input[name='amount']").remove();
			setup = 1;
		}
		// fix
		//resizeContentDOMFit();
		// filter
		tbl.datagrid('resize', 'fixRowHeight');
	}
	
	 // format date
    function myFormatter(date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        var d = date.getDate();
        return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
    }

    // required
    function myParser(s) {
        if (!s)
            return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0], 10);
        var m = parseInt(ss[1], 10);
        var d = parseInt(ss[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
            return new Date(y, m - 1, d);
        } else {
            return new Date();
        }
    }
	
	function open_detail(sid) {
		
		var get = document.getElementsByName('get_date')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/open_detail'; ?>",
			data : "sid="+sid+"&get_date="+get,
			success:function(res){
				response = res;
				$('div .modal-header .modal-title').text('Detail Productifity Sales');			
				$('#modal_detail').find('.modal-body').html(response);
				$("#modal_detail").modal('show');
					
			},
			error:function(){
				alert("Load failed");
			}
		});
		
	}

	//load edit view
	function get_map(sid) {
		var get = document.getElementsByName('get_date')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/get_gmap'; ?>",
			data : "sid="+sid+"&get_date="+get,
			success:function(msg){
				$("#map_canvas").html(msg);
				/* var obj = JSON.parse(msg);
				var totalLocations = obj.length;
				initialize();
				
				
				for (var i = 0; i < totalLocations; i++) {

                    var markerOptions = {  
						map: map,  
						position: new google.maps.LatLng(-6.26149,106.807)       
					};  
				marker = new google.maps.Marker(markerOptions);  
          
				google.maps.event.addListener(marker, "click", function()  
				{  
					iw.setContent("This is an infowindow");  
					iw.open(map, this);  
				});					
					

                } */
					
			},
			error:function(){
				alert("Load failed");
			}
		});
	}
	
	function get_maptracking(sid) {
		var get = document.getElementsByName('get_date')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/get_gmaptracking'; ?>",
			data : "sid="+sid+"&get_date="+get,
			success:function(msg){
				$("#map_canvas").html(msg);
				/* var obj = JSON.parse(msg);
				var totalLocations = obj.length;
				initialize();
				
				
				for (var i = 0; i < totalLocations; i++) {

                    var markerOptions = {  
						map: map,  
						position: new google.maps.LatLng(-6.26149,106.807)       
					};  
				marker = new google.maps.Marker(markerOptions);  
          
				google.maps.event.addListener(marker, "click", function()  
				{  
					iw.setContent("This is an infowindow");  
					iw.open(map, this);  
				});					
					

                } */
					
			},
			error:function(){
				alert("Load failed");
			}
		});
	}
	
	// inline editor on row
    function formatButton(val, row, index) {
      
		var bmap = '<?php echo @$bmap; ?>';
		var bmaptracking = '<?php echo @$bmaptracking; ?>';
		var bmap_detail = '<?php echo @$bmap_detail; ?>';
        
        //return bmap_detail
        return bmap + '&nbsp&nbsp' + bmaptracking + '&nbsp&nbsp' + bmap_detail;
		
    }
	
	function preview_image(cusid,sales) {
		 $.ajax({
			url: '<?php echo site_url('/') . $controller . "/get_fancy_image"; ?>',
			type: 'POST',
			async: false,
			dataType: 'json',
			data: {cusid: cusid, sales: sales},
			success: function (result) {
				// var p = result.images;
				var tempFile = [];
				$.each(result.images, function (index, value) {
					var tempFileElemet = {href: '<?php echo base_url() . DIR_IMAGE; ?>' + value.image, title: value.image};
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
	
</script>