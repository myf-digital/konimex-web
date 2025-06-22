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
			<h2>User Management</h2>				
			
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
								<label class="label">User Name</label>
								<label class="input"> 
									<input id="userid" type="hidden" name ="userid" value="<?php echo @$userid; ?>" />
									<input id="username" type="text" name="username"  value="<?php echo @$username; ?>" autocomplete="off" />
								</label>
							</section>
							<section class="col col-2">
								<label class="label">User Status</label>
								<?php echo @$status; ?>
							</section>			
						</div>	
						<div class="row">
							<section class="col col-3">
								<label class="label">Role Name</label>
								<?php echo @$role; ?>
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
				role_name : {
					required : true,
					remote	 : {
						url		: "<?php echo base_url();?>index.php/adm_role/cek_user",
						type	: "POST",
						data: {
							role_name: function() {
								return $("#username").val();
							},
							role_id : $("#userid").val()
						}
					}
					//remote : '<?php echo base_url();?>index.php/login/cek_user'		
				},
				user_status : {	
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
				username : {
					required : 'Username harus di isi',
					remote : 'Username sudah di gunakan'	
				},
				user_status :{
					required: 'Pilih User Status'
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
</script>
