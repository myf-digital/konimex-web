<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title>Login Page</title>
		<link href="<?php echo base_url(); ?>assets/template/img/logo.png" rel="logo icon" />
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Use the correct meta names below for your web application
			 Ref: http://davidbcalhoun.com/2010/viewport-metatag 
			 
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">-->
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/template/css/bootstrap.min.css">	
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/template/css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/template/css/smartadmin-production.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/template/css/smartadmin-skins.css">	
		
		<!-- SmartAdmin RTL Support is under construction
			<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.css"> -->
		
		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/template/css/demo.css">

		<!-- FAVICONS -->
		<!-- <link rel="shortcut icon" href="< ?php echo base_url(); ?>assets/template/ico/favicon.ico" type="image/x-icon"> -->
		<!-- <link rel="icon" href="< ?php echo base_url(); ?>assets/template/ico/favicon.ico" type="image/x-icon"> -->

		<!-- GOOGLE FONT -->
		<!--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">-->

	</head>
	<body id="login" class="animated fadeInDown">

		<div id="main" role="main">
			<!-- MAIN CONTENT -->
			<div id="content" class="container" style="padding-top:50px;">

				<div class="row">
					<!--<div class="col-xs-12 col-sm-12 col-md-7 col-lg-8">
						
						<img src="< ?php echo ASSETS_URL.TEMPLATE?>/img/dsi_login.png" style="width:100%;" alt="Welcome to Direct Sales Application">

					</div>-->
					<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
						<div class="well no-padding">
							
							<form action="<?php echo $action; ?>" method="POST" id="login-form" class="smart-form client-form">
								<header>
									<img src="<?php echo base_url(); ?>assets/template/img/search.png" style="width:50px; height:50px;" alt="Logo">
									<span style="color:blue;">Monitoring</span><span style="color:white;">Sales</span>									
								</header>
								<span class="txt-color-red">
								
								<?php echo $error; ?>
								</span>
								<fieldset>
									
									<section>
										<label class="label">Username</label>
										<label class="input"> <i class="icon-append fa fa-user"></i>
											<input type="text" name="username" autocomplete="off" />
											<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter username</b></label>
									</section>

									<section>
										<label class="label">Password</label>
										<label class="input"> <i class="icon-append fa fa-lock"></i>
											<input type="password" name="password" autocomplete="off" />
											<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
										<!--<div class="note">
											<a href="<?php echo base_url()?>login/forgetpassword">Forgot password?</a>
										</div>-->
									</section>
									

									<!--<section>
										<label class="checkbox">
											<input type="checkbox" name="remember" checked="">
											<i></i>Stay signed in</label>
									</section>-->
								</fieldset>
								<footer>
									<button type="submit" class="btn btn-primary">
										<i class="fa fa-sign-in" aria-hidden="true"></i>
										Sign in
									</button>
								</footer>
							</form>

						</div>
						
					</div>
				</div>
			</div>

		</div>
			
		<!--================================================== -->	

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/pace/pace.min.js"></script>

	    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="<?php echo base_url(); ?>assets/template/js/libs/jquery-2.0.2.min.js"></script>
		
		<script src="<?php echo base_url(); ?>assets/template/js/libs/jquery-ui-1.10.3.min.js"></script>
		
		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events 		
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->		
		<script src="<?php echo base_url(); ?>assets/template/js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="<?php echo base_url(); ?>assets/template/js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="<?php echo base_url(); ?>assets/template/js/smartwidgets/jarvis.widget.min.js"></script>
		
		<!-- EASY PIE CHARTS -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
		
		<!-- SPARKLINES -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/sparkline/jquery.sparkline.min.js"></script>
		
		<!-- JQUERY VALIDATE -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/jquery-validate/jquery.validate.min.js"></script>
		
		<!-- JQUERY MASKED INPUT -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/masked-input/jquery.maskedinput.min.js"></script>
		
		<!-- JQUERY SELECT2 INPUT -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>
		
		<!-- browser msie issue fix -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>
		
		<!-- FastClick: For mobile devices -->
		<script src="<?php echo base_url(); ?>assets/template/js/plugin/fastclick/fastclick.js"></script>
		
		<!--[if IE 7]>
			
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
			
		<![endif]-->

		<!-- MAIN APP JS FILE -->
		<script src="<?php echo base_url(); ?>assets/template/js/app.js"></script>

		<script type="text/javascript">
			runAllForms();

			$(function() {
				// Validation
				$("#login-form").validate({
					// Rules for form validation
					rules : {
						username : {
							required : true,
							remote	 : {
								url		: "<?php echo base_url();?>index.php/login/cek_user",
								type	: "POST",
								data: {
									username: function() {
										return $("#username").val();
									},
									username : $("#username").val()
								}
							}	
						},
						password : {
							required : true
						}
					},

					// Messages for form validation
					messages : {
						username : {
							required : 'Please Enter Your Username',
							remote   : 'User Not Valid'
						},
						password : {
							required : 'Please enter your password'
						}
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});
			});
		</script>

		<!-- Your GOOGLE ANALYTICS CODE Below -->
		<script type="text/javascript">
		
		 /*  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-43548732-3']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })(); */
		
		</script>

	</body>
</html>