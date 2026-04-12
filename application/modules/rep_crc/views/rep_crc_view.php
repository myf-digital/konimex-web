
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
					<h2>Reporting CRC</h2>				
					
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
								</div> -->
								
								<div class="row">
									<section class="col col-2">
										<label class="input">
											<i class="icon-append fa fa-calendar"></i>
											<input type="text" name="startdate" id="startdate" value="" placeholder="Start Periode" autocomplete="off" >
										</label>
									</section>
									<!-- <section class="col col-2">
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
							<div id="div_tbl" class="col-lg-12">
								<table id="tbl">
								</table>
							</div>
						</div>
						<div class="row">
							<div id="div_tbl1" class="col-lg-12">
								<table id="tbl1">
								</table>
							</div>
						</div>
						<div class="row">
							<div id="div_tbl2" class="col-lg-12">
								<table id="tbl2">
								</table>
							</div>
						</div>

						<div class="row" style="margin:10px; overflow-y: auto; max-height: 100%; max-width: 100%; white-space: nowrap; ">
							<div id="month_crc">
							
							</div>
						</div>

						<!--<div class="row">
							<div id="table-content" class="col-lg-12">
								<table id="tbl3">
								</table>
							</div>
						</div>
						-->
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
	var tbl, tbl1,  tbl2, setup; // setup is flag
    $(document).ready(function () {
		$('#div_tbl1').hide();
		$('#div_tbl2').hide();
		$('#month_crc').hide();		
			
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
            title: "List Reporting CRC",
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
				 //DATE_FORMAT(basic_crud.periode, '%d-%m-%Y') periode,salesmanid,nama_salesman,target,sales,percent,ob,oa,oavsob,ec,ecvsoa,salesvsec 
			]],	
            columns: [[
          	   
                    {field: 'options', title: 'ACTION', width: 300, halign: 'center', align: 'center', formatter: formatButton},
					{field: 'nama_salesman', title: 'Nama Medrep', width: 220, sortable: 'true', halign: 'center', align: 'center'},
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
		tbl1 = $('#tbl1').datagrid({width: '100%'});
        // toolbar action
		//var toolbarCostum = $('#tb');	
		// handle when direct update or create
		tbl1.datagrid(gridConfigGeneralNoRowNum);
        tbl1.datagrid({
            url: '<?php echo base_url().'index.php/'.$controller . '/load_data_outlet'; ?>',
            title: "List Outlet",
            //toolbar: toolbarCostum,
			rownumbers: true,
			pagination: false,
            onLoadSuccess: loadActionOutlet, // event on navigator.js
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
				 //DATE_FORMAT(basic_crud.periode, '%d-%m-%Y') periode,salesmanid,nama_salesman,target,sales,percent,ob,oa,oavsob,ec,ecvsoa,salesvsec 
			]],	
            columns: [[
          	   
                    {field: 'options', title: 'ACTION', width: '15%', halign: 'center', align: 'left', formatter: formatButtonCrc},
					{field: 'customerid', title: 'Customer ID', width: '7%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'nama_customer', title: 'Nama Outlet', width: '10%', sortable: 'true', halign: 'center', align: 'left'},
					{field: 'alamat', title: 'Alamat', width: '20%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'check_in', title: 'Check In', width: '7.5%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'crc_time', title: 'CRC', width: '7.5%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'order_time', title: 'Order', width: '7.5%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'ink_time', title: 'Tagihan', width: '7.5%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'check_out', title: 'Check Out', width: '7.5%', sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'lama_kunjungan', title: 'Lama Kunjungan', width: '10%', sortable: 'true', halign: 'center', align: 'center'},
                    {field: 'alasan', title: 'Alasan', width: '12.5%', sortable: 'true', halign: 'center', align: 'left'},
                    
                ]]
        });

		tbl2 = $('#tbl2').datagrid({width: '100%'});
        // toolbar action
		//var toolbarCostum = $('#tb');	
		// handle when direct update or create
		tbl2.datagrid(gridConfigGeneralNoRowNum);
        tbl2.datagrid({
            url: '<?php echo base_url().'index.php/'.$controller . '/load_data_crc'; ?>',
            title: "List CRC Current Date",
            //toolbar: toolbarCostum,
			rownumbers: true,
			pagination: false,
            onLoadSuccess: loadActionCrc, // event on navigator.js
			frozenColumns:[[
				//{field: 'options', title: 'ACTION', width: 300, halign: 'center', align: 'center', formatter: formatButton},
				 //DATE_FORMAT(basic_crud.periode, '%d-%m-%Y') periode,salesmanid,nama_salesman,target,sales,percent,ob,oa,oavsob,ec,ecvsoa,salesvsec 
			]],	
            columns: [[
          	   
					{field: 'productid', title: 'ProductID', width: '10%', sortable: 'true', halign: 'center', align: 'center'},
					{field: 'product_desc', title: 'Product Desc', width: '30%', sortable: 'true', halign: 'center', align: 'left'},
					{field: 'qty_rata', title: 'Rata', width: '10%', sortable: 'true', halign: 'center', align: 'right'},
					{field: 'qty_akhir', title: 'Stock Akhir', width: '10%', sortable: 'true', halign: 'center', align: 'right'},
					{field: 'qty_saran_order', title: 'Saran Order', width: '10%', sortable: 'true', halign: 'center', align: 'right'},
					{field: 'qty_fix_order', title: 'Fix Order', width: '10%', sortable: 'true', halign: 'center', align: 'right'},
					
                ]]
        });		

		/*tbl3 = $('#tbl3').datagrid({width: '100%'});
        // toolbar action
		//var toolbarCostum = $('#tb');	
		// handle when direct update or create
		tbl3.datagrid(gridConfigGeneralNoRowNum);
        tbl3.datagrid({
            url: '<?php echo base_url().'index.php/'.$controller . '/load_data_crc_month'; ?>',
            title: "List CRC Month To Date",
            //toolbar: toolbarCostum,
			rownumbers: true,
			pagination: false,
            onLoadSuccess: loadActionCrcMonth, // event on navigator.js
            columns: [
							[
								{title:'Tanggal',colspan:2},
								{title:'1',colspan:4},{title:'2',colspan:4},{title:'3',colspan:4},{title:'4',colspan:4},
								{title:'5',colspan:4},{title:'6',colspan:4},{title:'7',colspan:4},{title:'8',colspan:4},
								{title:'9',colspan:4},{title:'10',colspan:4},{title:'11',colspan:4},{title:'12',colspan:4},
								{title:'13',colspan:4},{title:'14',colspan:4},{title:'15',colspan:4},{title:'16',colspan:4},
								{title:'17',colspan:4},{title:'18',colspan:4},{title:'19',colspan:4},{title:'20',colspan:4},
								{title:'21',colspan:4},{title:'22',colspan:4},{title:'23',colspan:4},{title:'24',colspan:4},
								{title:'25',colspan:4},{title:'26',colspan:4},{title:'27',colspan:4},{title:'28',colspan:4},
								{title:'29',colspan:4},{title:'30',colspan:4},{title:'31',colspan:4}
							],[
								{field:'productid',title:'ProductID',width:'10%',sortable:true},
								{field:'product_desc',title:'Product Desc',width:'10%',sortable:true},
								{field:'r1',title:'R',align:'center'},{field:'a1',title:'A',align:'center'},{field:'s1',title:'S',align:'center'},{field:'f1',title:'F',align:'center'},
								{field:'r2',title:'R',align:'center'},{field:'a2',title:'A',align:'center'},{field:'s2',title:'S',align:'center'},{field:'f2',title:'F',align:'center'},
								{field:'r3',title:'R',align:'center'},{field:'a3',title:'A',align:'center'},{field:'s3',title:'S',align:'center'},{field:'f3',title:'F',align:'center'},
								{field:'r4',title:'R',align:'center'},{field:'a4',title:'A',align:'center'},{field:'s4',title:'S',align:'center'},{field:'f4',title:'F',align:'center'},
								{field:'r5',title:'R',align:'center'},{field:'a5',title:'A',align:'center'},{field:'s5',title:'S',align:'center'},{field:'f5',title:'F',align:'center'},
								{field:'r6',title:'R',align:'center'},{field:'a6',title:'A',align:'center'},{field:'s6',title:'S',align:'center'},{field:'f6',title:'F',align:'center'},
								{field:'r7',title:'R',align:'center'},{field:'a7',title:'A',align:'center'},{field:'s7',title:'S',align:'center'},{field:'f7',title:'F',align:'center'},
								{field:'r8',title:'R',align:'center'},{field:'a8',title:'A',align:'center'},{field:'s8',title:'S',align:'center'},{field:'f8',title:'F',align:'center'},
								{field:'r9',title:'R',align:'center'},{field:'a9',title:'A',align:'center'},{field:'s9',title:'S',align:'center'},{field:'f9',title:'F',align:'center'},
								{field:'r10',title:'R',align:'center'},{field:'a10',title:'A',align:'center'},{field:'s10',title:'S',align:'center'},{field:'f10',title:'F',align:'center'},
								{field:'r11',title:'R',align:'center'},{field:'a11',title:'A',align:'center'},{field:'s11',title:'S',align:'center'},{field:'f11',title:'F',align:'center'},
								{field:'r12',title:'R',align:'center'},{field:'a12',title:'A',align:'center'},{field:'s12',title:'S',align:'center'},{field:'f12',title:'F',align:'center'},
								{field:'r13',title:'R',align:'center'},{field:'a13',title:'A',align:'center'},{field:'s13',title:'S',align:'center'},{field:'f13',title:'F',align:'center'},
								{field:'r14',title:'R',align:'center'},{field:'a14',title:'A',align:'center'},{field:'s14',title:'S',align:'center'},{field:'f14',title:'F',align:'center'},
								{field:'r15',title:'R',align:'center'},{field:'a15',title:'A',align:'center'},{field:'s15',title:'S',align:'center'},{field:'f15',title:'F',align:'center'},
								{field:'r16',title:'R',align:'center'},{field:'a16',title:'A',align:'center'},{field:'s16',title:'S',align:'center'},{field:'f16',title:'F',align:'center'},
								{field:'r17',title:'R',align:'center'},{field:'a17',title:'A',align:'center'},{field:'s17',title:'S',align:'center'},{field:'f17',title:'F',align:'center'},
								{field:'r18',title:'R',align:'center'},{field:'a18',title:'A',align:'center'},{field:'s18',title:'S',align:'center'},{field:'f18',title:'F',align:'center'},
								{field:'r19',title:'R',align:'center'},{field:'a19',title:'A',align:'center'},{field:'s19',title:'S',align:'center'},{field:'f19',title:'F',align:'center'},
								{field:'r20',title:'R',align:'center'},{field:'a20',title:'A',align:'center'},{field:'s20',title:'S',align:'center'},{field:'f20',title:'F',align:'center'},
								{field:'r21',title:'R',align:'center'},{field:'a21',title:'A',align:'center'},{field:'s21',title:'S',align:'center'},{field:'f21',title:'F',align:'center'},
								{field:'r22',title:'R',align:'center'},{field:'a22',title:'A',align:'center'},{field:'s22',title:'S',align:'center'},{field:'f22',title:'F',align:'center'},
								{field:'r23',title:'R',align:'center'},{field:'a23',title:'A',align:'center'},{field:'s23',title:'S',align:'center'},{field:'f23',title:'F',align:'center'},
								{field:'r24',title:'R',align:'center'},{field:'a24',title:'A',align:'center'},{field:'s24',title:'S',align:'center'},{field:'f24',title:'F',align:'center'},
								{field:'r25',title:'R',align:'center'},{field:'a25',title:'A',align:'center'},{field:'s25',title:'S',align:'center'},{field:'f25',title:'F',align:'center'},
								{field:'r26',title:'R',align:'center'},{field:'a26',title:'A',align:'center'},{field:'s26',title:'S',align:'center'},{field:'f26',title:'F',align:'center'},
								{field:'r27',title:'R',align:'center'},{field:'a27',title:'A',align:'center'},{field:'s27',title:'S',align:'center'},{field:'f27',title:'F',align:'center'},
								{field:'r28',title:'R',align:'center'},{field:'a28',title:'A',align:'center'},{field:'s28',title:'S',align:'center'},{field:'f28',title:'F',align:'center'},
								{field:'r29',title:'R',align:'center'},{field:'a29',title:'A',align:'center'},{field:'s29',title:'S',align:'center'},{field:'f29',title:'F',align:'center'},
								{field:'r30',title:'R',align:'center'},{field:'a30',title:'A',align:'center'},{field:'s30',title:'S',align:'center'},{field:'f30',title:'F',align:'center'},
								{field:'r31',title:'R',align:'center'},{field:'a31',title:'A',align:'center'},{field:'s31',title:'S',align:'center'},{field:'f31',title:'F',align:'center'}
							]
					] });*/
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
		
		var outlet = '<?php echo $outlet; ?>';
		var order = '<?php echo $order; ?>';
		var outlet_excel = '<?php echo $outlet_excel; ?>';
		var scpace = '&nbsp;&nbsp;&nbsp;';
		return outlet + order + outlet_excel; 
	
	}

	function formatButtonCrc(val, row, index) {
		
		var crc = '<?php echo $crc; ?>';
		var crc_export = '<?php echo $crc_export; ?>';
		var scpace = '&nbsp;&nbsp;&nbsp;';
		return crc + crc_export; 
	
	}
	
	function loadAction() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			/*tbl.datagrid('enableFilter',
				[ {
					field: 'nama_salesman',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				}]
			);*/  
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

	function loadActionOutlet() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			/*tbl.datagrid('enableFilter',
				[ {
					field: 'nama_salesman',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				}]
			);*/  
			// remove filter option
			$("input[name='options']").remove();
			$("input[name='id']").remove();
			setup = 1;
		}
		// fix
		//resizeContentDOMFit();
		// filter
		tbl1.datagrid('resize', 'fixRowHeight');
	}

	function loadActionCrc() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			/*tbl.datagrid('enableFilter',
				[ {
					field: 'nama_salesman',
					type: 'datebox',
					options: {formatter: myFormatter, parser: myParser},
					op: ['equal', 'less', 'greater']
				}]
			);*/  
			// remove filter option
			$("input[name='options']").remove();
			$("input[name='id']").remove();
			setup = 1;
		}
		tbl2.datagrid('resize', 'fixRowHeight');
	}

	function loadActionCrcMonth() {
		// fix asyn setup and multiple setup
		if (setup == 0){
			// remove filter option
			$("input[name='options']").remove();
			$("input[name='id']").remove();
			setup = 1;
		}
		tbl3.datagrid('resize', 'fixRowHeight');
	}
	
	function search_data() {
		//var sales = document.getElementsByName('salesmanid')[0].value;
		var start = document.getElementsByName('startdate')[0].value;
		//var finish = document.getElementsByName('finishdate')[0].value;
		//alert(get);		
		tbl.datagrid('load',{
			//sales: sales,			
			start: start,
			//finish: finish,
		});
		search_data_outlet();
		$('#div_tbl1').hide();
		$('#div_tbl2').hide();
		$('#month_crc').hide();
		//search_data_crc_month();
	}
	
	function export_outlet(sid) {
		
		var start = document.getElementsByName('startdate')[0].value;
		
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/export_outlet/'; ?>"+sid+'/'+start;
		return false;
	}

	function search_data_outlet(salesid) {
		var sales = salesid;
		var start = document.getElementsByName('startdate')[0].value;
		//var finish = document.getElementsByName('finishdate')[0].value;
		//alert(get);		
		tbl1.datagrid('load',{
			sales: sales,			
			start: start,
			//finish: finish,
		});
		$('#div_tbl1').show();
		$('#div_tbl2').hide();
		$('#month_crc').hide();		
	}

	function search_data_crc(salesid,customerid) {
		var sales = salesid;
		var customer = customerid;
		var start = document.getElementsByName('startdate')[0].value;
		//alert(get);		
		tbl2.datagrid('load',{
			sales: sales,
			customer: customer,
			start: start,
			//finish: finish,
		});
		$('#div_tbl2').show();
		$('#month_crc').show();		
		month_crc(sales,customer);
	}

	
	
	function search_data_crc_month(salesid,customerid) {
		var sales = salesid;
		var customer = customerid;
		var start = document.getElementsByName('startdate')[0].value;
		//alert(get);		
		tbl3.datagrid('load',{
			sales: sales,
			customer: customer,
			start: start,
			//finish: finish,
		});
	}
	
	function month_crc(sid,cid) {
		var startdate = document.getElementsByName('startdate')[0].value;
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/month_crc'; ?>",
			data : "sid="+sid+"&startdate="+startdate+"&cid="+cid,
			success:function(res){
				response = res;
				$('#month_crc').html(response);
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
	
	function crc_export(sid,cid) {
		var start = document.getElementsByName('startdate')[0].value;
		
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/crc_export/'; ?>"+sid+"/"+cid+"/"+start;
		return false;
	}

	function export_to_excel() {
		
		var start = document.getElementsByName('startdate')[0].value;
		
		window.location.href = "<?php echo base_url().'index.php/'.$controller.'/export_excel/'; ?>"+start;
		return false;
		
	}		
	
	/* function create_view() {
		window.location.href = "<?php echo '#'.$controller.'/form'; ?>";
	}
	
	function update_view(id) {
		window.location.href = "<?php echo '#'.$controller.'/form/'; ?>"+id;
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
	
</script>
