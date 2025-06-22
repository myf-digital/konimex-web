<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget" id="wid-id-6" data-widget-editbutton="false" data-widget-custombutton="false" data-widget-deletebutton="false">
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
		<h2>Change Password</h2>				
		
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
			
			<form action="<?php echo @$action; ?>" method="post" id="crud-form" class="smart-form" autocomplete="off" >
				<header>
					<?php echo $header; ?>
				</header>

				<fieldset>
					
					
				
					<div class="row">
						<section class="col col-4">
							<label class="label">Old Password</label>
							<label class="input"> 
								<input id="old_pass" type="password" name="old_pass"  value="" autocomplete="off"  />
							</label>
						</section>
					</div>	
					<div class="row">
						<section class="col col-4">
							<label class="label">New Password</label>
							<label class="input"> 
								<input id="new_pass" type="password" name="new_pass"  value="" autocomplete="off"  />
								
							</label>
						</section>	
					</div>	
					<div class="row">						
						<section class="col col-4">
							<label class="label">Confirm New Password</label>
							<label class="input"> 
								<input id="c_new_pass" type="password" name="c_new_pass"  value="" autocomplete="off"  />
							</label>
						</section>	
					</div>	
								
				</fieldset>
				
				<footer>
					<button id="btn_submit" type="submit" name="submit" class="btn btn-primary" value="<?php echo $submit; ?>">
						Save
					</button>					
				</footer>

				<div class="message">
					<i class="fa fa-check fa-lg"></i>
					<p>
						Your comment was successfully added!
					</p>
				</div>
			</form>
			
		</div>
		<!-- end widget content -->
		
	</div>
	<!-- end widget div -->
				
</div>
<!-- end widget -->

<script type="text/javascript">
	
		
	$(function() {	

		//var newPass = $("#new_pass").val();
		$(".chosen-select").chosen();	
			
		alert_cancel = 'Are You Sure to cancel this form ?';
		$('#cancel_create').on('click', function () {
			if (confirm(alert_cancel)) {
				window.location.href = "<?php echo site_url('/').'home#'.$controller; ?>";
			}
		});
		
		// Validation
		$("#crud-form").validate({
			// Rules for form validation
			rules : {
				old_pass : {
					required : true,
					remote	 : {
						url		: "<?php echo base_url();?>index.php/change_pass/cek_old_pass",
						type	: "POST",
						data: {
							old_pass: function() {
								return $("#old_pass").val();
							}
						}
					}	
				},
				new_pass : {
					required : true
				},
				c_new_pass	 : {
					required: true,
					equalTo	:'#new_pass'
				}
			},
			ignore: [],
			submitHandler : function(form) {
				
				$(form).ajaxSubmit({
					beforeSubmit: function() {
						$("#btn_submit").prop("disabled",true);
						$("#btn_submit").append('  <i class="fa fa-gear fa-1x fa-spin">');
					},
					dataType: 'json',
					success : function(msg) {
						window.location.hash = base_url + 'index.php/<?php echo $controller; ?>/successPage';
						
					}
				});
				
			},

			// Messages for form validation
			messages : {
				old_pass : {
					required : 'Isi Password Lama',
					remote   : 'Password Lama Salah'     
						
				},
				new_pass : {
					required : 'Isi Password Baru'
						
				},
				c_new_pass	 : {
					required: 'Isi Confirm Password Baru',
					equalTo: 'Change New Password Harus Sama Dengan New Password'
				}
			},

			// Do not change code below
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			}
		});		
		
	});
</script>