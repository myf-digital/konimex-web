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
			<h2>Basic CRUD</h2>				
			
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
							<section class="col col-6">
								<label class="label">Basic Input text</label>
								<label class="input"> 
									<input maxlength="20" type="text" name="input_text" value="<?php echo @$input_text; ?>" placeholder="Input Text" autocomplete="off" >									
									<input type="hidden" name="id" value="<?php echo @$id; ?>" autocomplete="off" >									
								</label>
							</section>

							<?php echo $select_box; ?>			
						</div>
						
						<div class="row">
							<section class="col col-3">
								<label class="label">Input Date</label>
								<label class="input">
									<i class="icon-prepend fa fa-calendar"></i>
									<input type="text" name="startdate" id="startdate" value="<?php echo @$startdate; ?>" placeholder="Expected start date" autocomplete="off" >
								</label>
							</section>
						</div>	
						
						<div class="row">
							<section class="col col-6">
								<label class="label">E-mail</label>
								<label class="input">
									<input type="email" placeholder="E-mail" name="email" value="<?php echo @$email; ?>" autocomplete="off" >
								</label>
							</section>
						</div>					
											
						<div class ="row">
							
							<!--<section class="col col-6">
								<label class="label">Upload File</label>
								<input id="fileupload" type="file" name="myfile[]"  multiple />							
							</section>-->
								
							<section class="col col-6">
								<label class="label">Text Area</label>
								<label class="textarea"> 										
									<textarea rows="5" name="text_area" placeholder="Input Text Area"><?php echo @$text_area; ?> </textarea> 
								</label>
							</section>							
						</div>	

					</fieldset>

					<header>
						Hierarki Dropdown
					</header>
					<fieldset>
					
						<div class="row">
							<section class="col col-3">
								<label class="label">Area</label>
								<label class="select">									
									<select name="area" class="chosen-select" onchange="get_region(this.value),get_branch(0)">
										<option value="" selected="" disabled=""> -Choose- </option>
										<?php echo @$area; ?>	
									</select>
								</label>
							</section>
						</div>
						
						<div class="row">
							
								<section class="col col-3">
									<label class="label">Region</label>
									<div id="block_region">
										<label class="select">									
											<select name="region" class="chosen-select" onchange="get_branch(this.value)">
												<option value="" selected="" disabled=""> -Choose- </option>
												<?php echo @$region; ?>		
											</select>
										</label>
									</div>
								</section>
							
						</div>
						
						<div class="row">
							
								<section class="col col-3">
									<label class="label">Branch</label>
									<div id="block_branch">
										<label class="select">									
											<select name="branch" class="chosen-select">
												<option value="" selected="" disabled=""> -Choose- </option>
												<?php echo @$branch; ?>		
											</select>
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
				input_text : {
					required : true/* ,
					remote	 : {
						url		: "<?php echo base_url();?>index.php/adm_role/cek_role",
						type	: "POST",
						data: {
							role_name: function() {
								return $("#role_name").val();
							},
							role_id : $("#role_id").val()
						}
					} */
					//remote : '<?php echo base_url();?>index.php/login/cek_user'		
				},
				select_box : {
					required : true
				},
				startdate : {
					required : true
				},
				finishdate : {
					required : true
				},
				email : {
					required : true,
					email : true
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
				input_text : {
					required : 'Input Text Tidak Boleh Kosong'
						
				},
				select_box : {
					required : 'Pilih Salah Satu'
				},
				startdate : {
					required : 'Pilih Start Date'
				},
				finishdate : {
					required : 'Pilih Finish Date'
				},
				email : {
					required : 'Email Harus Di Isi',
					email : 'Format Emaill Salah'
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
		// START AND FINISH DATE
		$('#startdate').datepicker({
			dateFormat : 'dd-mm-yy',
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',
			changeMonth: true,
			changeYear: true
		});
		
					
		
	});	
	
	function get_region(area) {
		
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				$("#block_region").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/get_region'; ?>",
			data : "area="+area,
			success:function(msg){
				$("#block_region").html(msg);			
			},
			error:function(){
				alert("Load failed");
			}
		});	
	}
	
	function get_branch(region) {
		
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				$("#block_branch").html('Populating data, please wait..');
			},
			url:"<?php echo site_url('/') . $controller . '/get_branch'; ?>",
			data : "region="+region,
			success:function(msg){
				$("#block_branch").html(msg);			
			},
			error:function(){
				alert("Load failed");
			}
		});	
	}
	
</script>
