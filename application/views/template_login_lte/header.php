<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<title><?php echo $title; ?></title>
<meta charset="utf-8">
<meta name="region" content="ID">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="Ikut Menyehatkan Bangsa">
<meta name="keywords" content="Himalaya, Ikut Menyehatkan Bangsa">
<meta property="og:url" content="<?php echo base_url(); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="Himalaya">
<meta property="og:description" content="Ikut Menyehatkan Bangsa">
<meta property="og:image" content="<?php echo base_url('assets/images/favicon.ico'); ?>">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootstrap/css/bootstrap.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/font-awesome/css/font-awesome.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/ionicons/css/ionicons.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/adminlte.font.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/adminlte.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-confirm-master/css/jquery-confirm.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/app.cise.css'); ?>"/>
<!-- jQuery and Plugin -->
<script src="<?php echo base_url('assets/frameworks/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/jquery-ui-1.11.4/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap/js/bootstrap.min.js'); ?>"></script>
<!-- JEasyUI-->
<script src="<?php echo base_url('assets/plugins/jeasyui/plugins/jquery.form.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/validation/jquery.form.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/validation/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/validation/additional-methods.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/malsup/jquery.form.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-confirm-master/js/jquery-confirm.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/cookies/js.cookie.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/adminlte/js/app.cise.js'); ?>"></script>

<style>
	/* validation */
	label.error-login {
		font-weight: normal;
		color: #ff0000;
	}
  .img-left {
    position: relative;
    top: 30%;
  }
  .slogan {
    font-size: 1.75em;
    font-weight: lighter;
    position: relative;
    top: 30%;
    left: -30px;
    margin: 0;
  }
</style>