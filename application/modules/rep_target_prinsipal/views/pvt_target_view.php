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
					<h2>Target Prinsipal</h2>				
					
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
						<!-- this is what the user will see -->
						<form class="smart-form" >
							<fieldset>
								<div class="row">
									<section class="col col-4">
										<label class="select">
											<?php echo $salesman; ?>
										</label>
									</section>	
								</div>
								
								<div class="row">
									<section class="col col-2">
										<label class="input">
											<i class="icon-append fa fa-calendar"></i>
											<input type="text" name="startdate" id="startdate" value="" placeholder="Start Periode" autocomplete="off" >
										</label>
									</section>
									<section class="col col-2">
										<label class="input">
											<i class="icon-append fa fa-calendar"></i>
											<input type="text" name="finishdate" id="finishdate" value="" placeholder="Start Periode" autocomplete="off" >
										</label>
									</section>
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
									<!--<div id="tb" style="padding:3px;height:auto;">
										<div style="margin-bottom:0px; max-width:100px;">												
											< ?php echo @$create; ?>
										</div>
									</div>-->
								</table>
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
		tbl.datagrid(gridConfigGeneralNoRowNumFixHeight);
		tbl.datagrid({
			pageNumber: '<?php echo $pnumber?>',
			pageSize: '<?php echo $psize?>',
		});
        tbl.datagrid({
            url: '<?php echo base_url().'index.php/'.$controller . '/load_data'; ?>',
            title: "List Target Prinsipal",
            //toolbar: toolbarCostum,
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
                   
                    {field: 'periode', title: 'Periode', width: 120, sortable: 'true', halign: 'center', align: 'left'},
                    {field: 'salesmanid', title: 'Salesmanid', width: 200, sortable: 'true',halign: 'center', align: 'center'},
                    {field: 'nama_salesman', title: 'Nama Salesman', width: 200, sortable: 'true', halign: 'center', align: 'left'},
                    {field: 'prinsipalid', title: 'Prinsipal id', width: 170, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'nama_prinsipal', title: 'Nama Prinsipal', width: 220, sortable: 'true', halign: 'center', align: 'left'},                    
                    {field: 'target', title: 'Target', width: 220, sortable: 'true', halign: 'center', align: 'left'},
                    {field: 'sales', title: 'Sales', width: 220, sortable: 'true', halign: 'center', align: 'left'},
                    {field: 'percent', title: 'Pesentase Target', width: 220, sortable: 'true', halign: 'center', align: 'left'},
					
                ]]
        });
    });
	
	/* function formatButton(val, row, index) {
		
		var btn_update = '<?php echo $update; ?>';
		var btn_delete = '<?php echo $delete; ?>';
		var scpace = '&nbsp;&nbsp;&nbsp;';
		var previewBtn = '<?php echo @$preview; ?>';
		return previewBtn + scpace + btn_update + scpace + btn_delete;
	
	} */
	
	
								
	
	function loadAction() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			tbl.datagrid('enableFilter',
				[ {
					field: 'date',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				}]
			);  
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
		var sales = document.getElementsByName('salesmanid')[0].value;
		var start = document.getElementsByName('startdate')[0].value;
		var finish = document.getElementsByName('finishdate')[0].value;
		//alert(get);		
		tbl.datagrid('load',{
			sales: sales,			
			start: start,			
			finish: finish,			
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
	
	function export_to_excel() {
		
		var sales = document.getElementsByName('salesmanid')[0].value;
		var start = document.getElementsByName('startdate')[0].value;
		var finish = document.getElementsByName('finishdate')[0].value;
		
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/export_excel/'; ?>"+sales+"/"+start+"/"+finish;
		return false;
		
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
	
</script>
