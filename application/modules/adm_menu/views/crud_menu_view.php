<link href="<?php echo base_url()."assets/" ?>fa-picker/dist/css/fontawesome-iconpicker.min.css" rel="stylesheet">
<script src="<?php echo base_url()."assets/" ?>fa-picker/dist/js/fontawesome-iconpicker.min.js"></script>
<!-- widget grid -->
<section id="widget-grid" class="">

	<!-- Widget ID (each widget will need unique ID)-->
	<div class="jarviswidget jarviswidget-color-blueDark" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false" data-widget-deletebutton="false" >
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
			<span class="widget-icon"> <i class="fa fa-edit"></i> </span>
			<h2>Menu Management</h2>				
			
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
				
				<form action="<?php echo $action; ?>" method="post" id="crud-form" class="smart-form" >
					<header>
						<?php echo $header; ?>
					</header>

					<fieldset>
						<div class="row">
						<section class="col col-2">
							<label class="label">Menu Name</label>
							<label class="input"> 
								<input id="menu_id" type="hidden" name ="menu_id" value="<?php echo @$menu_id; ?>" />
								<input id="menu_name" type="text" name="menu_name"  value="<?php echo @$menu_name; ?>" autocomplete="off" />
							</label>
						</section>
						<section class="col col-2">
							<label class="label">Module Name</label>
							<label class="input">
								<input id="module_name" type="text" name="module_name"  value="<?php echo @$module_name; ?>" autocomplete="off" />		
							</label>
						</section>
						<section class="col col-2">
							<label class="label">Type Menu</label>
							<?php echo @$type_menu; ?>		
						</section>
						<section class="col col-2">
							<label class="label">Sequence Number</label>
							<label class="input"> 
								<input id="seq_number" type="text" name="seq_number"  value="<?php echo @$seq_number; ?>" autocomplete="off" />
							</label>
						</section>	
					</div>
					<div class="row">
						<section class="col col-3">
							<label class="label">Parent Menu</label>
							<div id="block_parent" >
								<label class="select">
									<select name="parent_id" class="chosen-select">
										<option value="">--Select--</option>
										<?php echo @$parent_id; ?>		
									</select>
								</label>
							</div>		
						</section>
						<section class="col col-2">
							<label class="label">Menu Icon</label>
							<div id="block_icon" >
								<label class="input">
									<?php echo @$menu_icon; ?>		
								</label>
							</div>		
						</section>
					</div>				
					</fieldset>

					<footer>
						
						<div class="col-md-12">
							<button class="btn btn-default" type="submit" id="cancel_create"> Cancel </button>
							<button id="btn_submit" class="btn btn-primary" name="submit" type="submit" value= "<?php echo $submit; ?>"><i class="fa fa-save"></i> Save</button>
						</div>
						
					</footer>
				</form>

			</div>
			<!-- end widget content -->
			
		</div>
		<!-- end widget div -->
		
	</div>
	<!-- end widget -->	

</section>
<!-- end widget grid -->

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
	 
	 // Load form valisation dependency 
	loadScript(url+"js/plugin/jquery-form/jquery-form.min.js", runFormValidation);
	
	
	function runFormValidation() {
				
		// Validation
		$("#crud-form").validate({
			// Rules for form validation
			rules : {
				menu_name : {
					required : true							
				},
				module_name : {
					required : true
				},
				type_menu : {
					required : true
				}
			},
			
			submitHandler : function(form) {
				
				$(form).ajaxSubmit({
					beforeSubmit: function() {
						$("#btn_submit").prop("disabled",true);
						$("#btn_submit").append('  <i class="fa fa-gear fa-1x fa-spin">');
					},	
					dataType: 'json',
					success : function(msg) {
						window.location.hash = '<?php echo base_url()."index.php/".$controller; ?>/';
						
					}
				});
				
			},

			// Messages for form validation
			messages : {
				menu_name : {
					required : 'Menu Name Tidak Boleh Kosong'
						
				},
				module_name : {
					required : 'Nama Module Tidak Boleh Kosong'
				},
				type_menu : {
					required : 'Pilih Type Menu'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});
	}
	
	$(function() {
		
		$(".chosen-select").chosen();		
		
		alert_cancel = 'Are You Sure to cancel this form ?';
		$('#cancel_create').on('click', function () {
			if (confirm(alert_cancel)) {
				window.location.href = "<?php echo base_url().'index.php/home#'.$controller; ?>";
			}
		});		
		
	});	
	
	function get_parent(type_menu) {
		
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				$("#block_parent").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/get_parent'; ?>",
			data : "type_menu="+type_menu,
			success:function(msg){
				$("#block_parent").html(msg);			
			},
			error:function(){
				alert("Load failed");
			}
		});	
	}
	
	function get_icon(type_menu) {
		
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				$("#block_icon").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/get_icon'; ?>",
			data : "type_menu="+type_menu,
			success:function(msg){
				$("#block_icon").html(msg);			
			},
			error:function(){
				alert("Load failed");
			}
		});
		
	}
</script>
