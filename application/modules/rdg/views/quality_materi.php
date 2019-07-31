<!DOCTYPE html>
<html>
<head>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="<?php echo base_url('assets/rdg/css/style2.css'); ?>" media="all" rel="stylesheet">
    <link href="<?php echo base_url('assets/rdg/lib/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/rdg/lib/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <title></title>
</head>
<body>
<div id="loading">
    <div class="cube-wrapper">
        <div class="cube-folding">
            <span class="leaf1"></span>
            <span class="leaf2"></span>
            <span class="leaf3"></span>
            <span class="leaf4"></span>
        </div>
        <span class="loading" data-name="Loading">Loading</span>
    </div>
</div>
<div class="main-container">
    <div class="fixed-container bg-white">
        <div class="containe-fluid">
            <a href="#">
                <img src="<?php echo base_url('assets/rdg/img/logo.png'); ?>" style="width: 280px; height: 45px;">
            </a>
        </div>
    </div>
    <div class="content-wrapper bg-primary">
        <!-- ...more content here -->
        <div class="overflow-container">
            <!-- ...and more content here -->
            <div class="overflow-content bg-primary">
                <div class="container text-center z-index-1">
                    <h2 id="title-satker" class="mt-5 mb-5">RDG A NAMA SATKER</h2>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="myChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button id="btn-back" class="btn-start"><i class="fa fa-arrow-left"></i>  Back</button>
                </div>
            </div>

            <span class="back-to-top bg-primary text-white text-center shadow-sm">
                <i class="fa fa-chevron-up"></i>
            </span>

        </div>
    </div>
</div>
<footer>
    <div class="container-fluid">
        <div class="w-100">
            <div class="col-12 d-flex justify-content-center">
                <div class="copyright small text-uppercase">&copy; All rights reserved 2019</div>
            </div>
        </div>
    </div>
</footer>

<!-- javascript -->
<script src="<?php echo base_url('assets/rdg/lib/jquery/jquery.min.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/malsup/jquery.form.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/cookies/js.cookie.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/adminlte/js/app.cise.js'); ?>"></script>
<script src="<?php echo base_url('assets/rdg/js/main.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="<?php echo base_url('assets/rdg/js/main.quality_materi.app.js'); ?>"></script>

</body>
</html>