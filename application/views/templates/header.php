<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<title>Konimex</title>
<meta charset="utf-8">
<meta name="region" content="ID">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="Ikut Menyehatkan Bangsa">
<meta name="keywords" content="Konimex, Ikut Menyehatkan Bangsa">
<meta property="og:url" content="<?php echo base_url(); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="Konimex">
<meta property="og:description" content="Ikut Menyehatkan Bangsa">
<meta property="og:image" content="<?php echo base_url('assets/images/favicon.ico'); ?>">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" />

<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootstrap/css/bootstrap.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/skins/skin-blue.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/app.cise.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/adminlte.font.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/adminlte.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/font-awesome/css/font-awesome.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/ionicons/css/ionicons.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/jeasyui/themes/material-teal/easyui.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/morris/morris.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesomeiconpicker/css/fontawesome-iconpicker.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-confirm-master/css/jquery-confirm.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-filer-master/css/jquery.filer.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-filer-master/css/jquery.filer-dragdropbox-theme.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-timepicker/bootstrap-clockpicker.min.css'); ?>"/>
<!-- key from yudi -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxXaXhYT8PzX2BEDwnqy0aeWXOZ5yYzLo&libraries=places,marker"></script>

<!-- Add fancyBox CSS files --> 
<link rel="stylesheet" href="<?php echo base_url('assets/fancybox/jquery.fancybox.css?v=2.1.5'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url('assets/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo base_url('assets/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7'); ?>" type="text/css"/>

<link rel="stylesheet" href="<?php echo base_url('assets/multiselect/lib/google-code-prettify/prettify.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/multiselect/css/style.css'); ?>" />

<style>
    .pointer {
        cursor: pointer;
    }
</style>

<script src="<?php echo base_url('assets/frameworks/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/jquery-ui-1.11.4/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/multiselect/dist/js/multiselect.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/adminlte/js/adminlte.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jeasyui/jquery.easyui.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jeasyui/jquery.easyui.mobile.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jeasyui/jquery.easyui.commons.datagrid.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jeasyui/easyloader.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jeasyui/extentions/datagrid-filter.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-confirm-master/js/jquery-confirm.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-timepicker/bootstrap-clockpicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/moment/moment.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/validation/jquery.form.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/validation/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/validation/additional-methods.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/malsup/jquery.form.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/chartjs/Chart.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/raphael/raphael.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/morris/morris.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/fontawesomeiconpicker/js/fontawesome-iconpicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/cookies/js.cookie.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/select2/js/select2.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-filer-master/js/jquery.filer.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/flot/jquery.flot.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/flot/jquery.flot.resize.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/flot/jquery.flot.pie.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/flot/jquery.flot.categories.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/adminlte/js/app.cise.js'); ?>"></script>

<!-- Add fancyBox main JS -->
<script src="<?php echo base_url('assets/fancybox/jquery.mousewheel-3.0.6.pack.js'); ?>"></script>
<script src="<?php echo base_url('assets/fancybox/jquery.fancybox.js?v=2.1.5'); ?>"></script>

<script>
    const session = new Common().getCookie("session");
</script>
