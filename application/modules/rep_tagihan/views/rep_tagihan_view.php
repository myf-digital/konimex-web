<!-- widget grid -->
<section id="widget-grid" class="">

	<!-- row -->
	<div class="row">
		
		<!-- NEW WIDGET START -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget jarviswidget-color-blueDark" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false" data-widget-deletebutton="false">
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
					<span class="widget-icon"> <i class="fa fa-table"></i> </span>
					<h2>Reporting Tagihan</h2>				
					
				</header>

				<!-- widget div-->
				<div>
					
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">	
					</div>
					<!-- end widget edit box -->
					
					<!-- widget content -->
					<div class="widget-body">
						
						<!-- this is what the user will see -->
						<form class="smart-form" >
							<fieldset>
								<!--<div class="row">
									<section class="col col-4">
										<label class="select">
											< ?php echo $salesman; ?>
										</label>
									</section>	
								</div>-->
								
								<div class="row">
									<section class="col col-2">
										<label class="input">
											<i class="icon-append fa fa-calendar"></i>
											<input type="text" name="startdate" id="startdate" value="" placeholder="Start Periode" autocomplete="off" >
										</label>
									</section>
									<!--<section class="col col-2">
										<label class="input">
											<i class="icon-append fa fa-calendar"></i>
											<input type="text" name="finishdate" id="finishdate" value="" placeholder="Start Periode" autocomplete="off" >
										</label>
									</section>-->
								</div>
								
								<div class="row">
									<section class="col col-4">
										<?php echo $search; ?>
										<?php echo $excell; ?>
									</section>
								</div>
							
							</fieldset>	
						</form>
						
						<div class="row">
							<div id="table-content" class="col-lg-12">
								<table id="tbl">
								</table>
							</div>
						</div>
						<div class="row">
							<div id="div_tagihan" class="col-lg-12">
							
							</div>
						</div>

						<div class="row">
							<div class="extra-footer col-lg-12" style="height:25px;"></div>
							<div class="col-lg-12 page-footer-table"></div>
							<!-- only space 
							-->
						</div>

					</div>
					<!-- end widget content -->
					
				</div>
				<!-- end widget div -->
				
			</div>
			<!-- end widget -->

		</article>
		<!-- WIDGET END -->
		
	</div>

	<!-- end row -->
</section>
<!-- end widget grid -->

<div class="modal fade" id="modal_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

<script type="text/javascript">
	
	/* DO NOT REMOVE : GLOBAL FUNCTIONS!
	 *
	 * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
	 * 
	 * // activate tooltips
	 * $("[rel=tooltip]").tooltip();
	 * 
	 * // activate popovers
	 * $("[rel=popover]").popover();
	 * 
	 * // activate popovers with hover states
	 * $("[rel=popover-hover]").popover({ trigger: "hover" });
	 * 
	 * // activate inline charts
	 * runAllCharts();
	 * 
	 * // setup widgets
	 * setup_widgets_desktop();
	 * 
	 * //setup nav height (dynamic)
	 * nav_page_height();
	 * 
	 * // run form elements
	 * runAllForms();
	 * 
	 ********************************
	 * 
	 * pageSetUp() is needed whenever you load a page. 
	 * It initializes and checks for all basic elements of the page 
	 * and makes rendering easier.
	 * 
	 */	
	 
	pageSetUp();
	
	/*
	 * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
	 * eg alert("my home function");
	 */
	 //JQ EASY
	var tbl, setup; // setup is flag
    $(document).ready(function () {
		
		$('#startdate').datepicker({
            dateFormat : 'yy-mm-dd',
            prevText : '<i class="fa fa-chevron-left"></i>',
            nextText : '<i class="fa fa-chevron-right"></i>',
            changeMonth: true,
			changeYear: true,
			onSelect : function(selectedDate) {
                $('#finishdate').datepicker('option', 'minDate', selectedDate);
            }
        });
		
        $('#finishdate').datepicker({
            dateFormat : 'yy-mm-dd',
            prevText : '<i class="fa fa-chevron-left"></i>',
            nextText : '<i class="fa fa-chevron-right"></i>',
            changeMonth: true,
			changeYear: true,
			onSelect : function(selectedDate) {
                $('#startdate').datepicker('option', 'maxDate', selectedDate);
            }
        });
	
		$(".chosen-select").chosen();	
		setup = 0;
		tbl = $('#tbl').datagrid({width: '100%'});
        // toolbar action
		//var toolbarCostum = $('#tb');	
		// handle when direct update or create
		tbl.datagrid(gridConfigGeneralNoRowNum);
		tbl.datagrid({
			pageNumber: '<?php echo $pnumber?>',
			pageSize: '<?php echo $psize?>',
		});
        tbl.datagrid({
            url: '<?php echo base_url().'index.php/'.$controller . '/load_data'; ?>',
            title: "List Tangguhan",
            //toolbar: toolbarCostum,
            rownumbers: true,
			onLoadSuccess: loadAction, // event on navigator.js
            //onAfterRender: tableAfterLoad,
            /*
             filterStringify: function(data){
             $.map(data, function(item){
             if (item.field == 'changeDate'){
             item.value = $.fn.datebox.defaults.parser(item.value).getTime();
             }
             });
             return JSON.stringify(data);
             },
             */
			frozenColumns:[[
				//{field: 'options', title: 'ACTION', width: 300, halign: 'center', align: 'center', formatter: formatButton},
				 //DATE_FORMAT(basic_crud.periode, '%d-%m-%Y') ,siteid,salesmanid,nama_salesman,,,,,percent
			]],	
            columns: [[
            
                    {field: 'options', title: 'ACTION', width: 300, halign: 'center', align: 'center', formatter: formatButton},
					{field: 'nama_salesman', title: 'Nama TPE', width: 220, sortable: 'true', halign: 'center', align: 'center'},
					{field: 'jadwal', title: 'Scedule', width: 70, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'effectivecall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_1.png"></img> Eff Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'ExtraCall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_2.png"></img>Ex Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'cal', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_3.png"></img> Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'InvalidCall', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_4.png"></img> Inv Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'noo', title: '<img class="color" src="<?php echo base_url(); ?>assets/mapIcon/legend_5.png" title="Register Titik Outlet"></img> RTO', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'eff_time', title: 'Eff Time', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'eff_order', title: 'Eff Order', width: 75, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'amount', title: 'Amount', width: 200, sortable: 'true', halign: 'center', align: 'right', formatter: formatAmount},
                    
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
	
	function formatButton(val, row, index) {
		
		var tagihan = '<?php echo $tagihan; ?>';
		var order = '<?php echo $order; ?>';
		var tagihan_excel = '<?php echo $tagihan_excel; ?>';
		var scpace = '&nbsp;&nbsp;&nbsp;';
		return tagihan + order + tagihan_excel; 
	
	}
	
	
	function export_to_excel() {
		
		var vdate = document.getElementsByName('startdate')[0].value;
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/export_excel/'; ?>"+vdate;
		return false;
		
	}							
	
	function loadAction() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			/*tbl.datagrid('enableFilter',
				[ {
					field: 'tgl_ink',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				}]
			);  */
			// remove filter option
			$("input[name='options']").remove();			
			$("input[name='id']").remove();			
			setup = 1;
		}
		// fix
		//resizeContentDOMFit();
		// filter
		tbl.datagrid('resize', 'fixRowHeight');
	}
	
	function search_data() {
		//var sales = document.getElementsByName('salesmanid')[0].value;
		var start = document.getElementsByName('startdate')[0].value;
		//var finish = document.getElementsByName('finishdate')[0].value;
		//alert(start); die();		
		tbl.datagrid('load',{
			//sales: sales,			
			start: start,			
			//finish: finish,			
		});
		$('#div_tagihan').hide();

	}

	function get_data_tagihan(sid) {
		var startdate = document.getElementsByName('startdate')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/show_tagihan'; ?>",
			data : "sid="+sid+"&startdate="+startdate,
			success:function(res){
				response = res;
				$('#div_tagihan').html(response);
			},
			error:function(){
				alert("Load failed");
			}
		});
		$('#div_tagihan').show();
	}
	
	function export_tagihan(sid,nmsales) {
		var vdate = document.getElementsByName('startdate')[0].value;
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/excel_tagihan/'; ?>"+sid+"/"+nmsales+"/"+vdate;
		return false;
	}
	
	function load_tagihan(sid,nmsales) {
		var vdate = document.getElementsByName('startdate')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/show_tagihan'; ?>",
			data : "sid="+sid+"&startdate="+vdate+"&nama_salesman="+nmsales,
			success:function(res){
				response = res;
				$('#div_tagihan').show();
				$('#div_tagihan').html(response);
			},
			error:function(){
				alert("Load failed");
			}
		});
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
	
	/* function create_view() {
		window.location.href = "<?php echo '#'.$controller.'/form'; ?>";
	}
	
	function update_view(id) {
		window.location.href = "<?php echo '#'.$controller.'/form/'; ?>"+id;
	}
	
	function export_to_excel() {
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/export_excel/'; ?>";
		return false;
	} 
	
	function export_to_pdf() {
		//window.location.href = "<?php echo base_url().'index.php/'.$controller.'/export_pdf/'; ?>";
		window.open("<?php echo base_url().'index.php/'.$controller.'/export_pdf/'; ?>","_blank");
		return false;
	} */
	
	/* function delete_data(id) {
		
		var alertmsg = 'Delete Data Ini ?';
		if (confirm(alertmsg)) {
			
			
				
			$.ajax({
			url:"<?php echo site_url('/').$controller; ?>/delete",
			type: "POST",
			dataType:"json",
			data:"id="+id,
			success : function(msg){			
				container = $('#content');
				var url = location.hash.replace(/^#/, '');
				loadURL(url + location.search, container);
			},
			error :function(msg){
				$("#content").html("<span class='label label-important'> Error </span>");			
				$("#loading_anim").hide();
			}
			}); 
			
		}
	} */

//fancy box
	/* function preview_image(id) {
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
	}	 */
	
	function open_order(sid) {
		
		var startdate = document.getElementsByName('startdate')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/open_order'; ?>",
			data : "sid="+sid+"&startdate="+startdate,
			success:function(res){
				response = res;
				$('div .modal-header .modal-title').text('Target Penjualan');			
				$('#modal_data').find('.modal-body').html(response);
				$("#modal_data").modal('show');
					
			},
			error:function(){
				alert("Load failed");
			}
		});
		
	}
	
	function open_tagihan(sid) {
		
		var startdate = document.getElementsByName('startdate')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/open_tagihan'; ?>",
			data : "sid="+sid+"&startdate="+startdate,
			success:function(res){
				response = res;
				$('div .modal-header .modal-title').text('Productivity TPE');			
				$('#modal_data').find('.modal-body').html(response);
				$("#modal_data").modal('show');
					
			},
			error:function(){
				alert("Load failed");
			}
		});
		
	}
	
</script>
