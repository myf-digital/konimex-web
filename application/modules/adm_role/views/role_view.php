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
					<h2>Role Management</h2>				
					
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
						<form class="form-inline" role="form">
							<fieldset>
								<?php echo $create;?>
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

<script>
	
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

	var tbl, setup; // setup is flag
    $(document).ready(function () {
		
		setup = 0;
		tbl = $('#tbl').datagrid({width: '100%'});
        // toolbar action
		var toolbarCostum = $('#tb');	
		// handle when direct update or create
		tbl.datagrid(gridConfigGeneralNoRowNumFixHeight);
		tbl.datagrid({
			pageNumber: '<?php echo $pnumber?>',
			pageSize: '<?php echo $psize?>',
		});
        tbl.datagrid({
            url: '<?php echo site_url('/') . $controller . '/load_data'; ?>',
            title: "List Role Management",
            toolbar: toolbarCostum,
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
				{field: 'options', title: 'ACTION', width: 300, halign: 'center', align: 'center', formatter: formatButton},
			]],	
            columns: [[
                    {field: 'role_name', title: 'Role Name', width: 200, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'role_status', title: 'Role Status', width: 100, sortable: 'true', halign: 'center', align: 'center', formatter:formatStatus},
					{field: 'created_by', title: 'Created By', width: 100, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'created_date', title: 'Created Date', width: 150, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'modified_by', title: 'Modified By', width: 100, sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'modified_date', title: 'Modified Date', width: 150, sortable: 'true', halign: 'center', align: 'center'},
                    
                ]]
        });
    });
	
	function loadAction() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			tbl.datagrid('enableFilter',
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
			);  
			// remove filter option
			$("input[name='options']").remove();
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

	//load edit view	
	function edit_view(role_id) {
		window.location.href = "<?php echo '#'.$controller.'/form/'; ?>"+role_id;
	}
	
	function assigment_view(role_id) {
		window.location.href = "<?php echo '#'.$controller.'/role_assigment/'; ?>"+role_id;		
	}
	
	function create_view() {
		window.location.href = "<?php echo '#'.$controller.'/form/'; ?>";
	}
	
	function delete_data(role_id) {
		
		var alertmsg = 'Delete Data Ini ?';
		if (confirm(alertmsg)) {
			
			
			/* $.post("adm_tahun_ajaran/change_tahun_ajaran", {row_id: row_id}, function () {
                  //window.location.href = '#adm_tahun_ajaran/';
                    //window.location.reload();
                }, 'json'); */
				
			$.ajax({
			url:"<?php echo site_url('/').$controller; ?>/delete",
			type: "POST",
			dataType:"json",
			data:"role_id="+role_id,
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
	}
	// inline editor on row
    function formatButton(val, row, index) {
      
		var updateBtn = '<?php echo @$update; ?>';				
        var deleteBtn = '<?php echo @$delete; ?>';
        var assigment = '<?php echo @$assigment; ?>';
        var scpace = '&nbsp;';
        // return previewBtn
        return updateBtn + deleteBtn + assigment;
		
    }
	
	function formatStatus(val, row, index) {
		var stat = row.role_status;
		if(stat == "Y") {
			return "VALID";
		} else {
			return "INVALID";
		}
	
	}
	
</script>
					