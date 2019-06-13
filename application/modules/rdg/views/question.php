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
<div class="container-fluid">
    <main class="row">
        <nav class="navbar navbar-expand-md fixed-top bg-white shadow-sm">
            <div class="containe-fluid">
                <a href="#">
                    <img src="<?php echo base_url('assets/rdg/img/logo.png'); ?>" style="width: 280px; height: 45px;">
                </a>
            </div>
        </nav>

        <div id="home" class="container-fluid parallax bg-primary">
            <div class="h-100 w-100 d-flex align-items-center">
                <div class="container p-100">
                    <h6 class="first-text mb-3 mt-5 mt-lg-1 text-center">LIST REVIEW</h6>
                    <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p> -->
                    <!-- <p class="date">14 Juni 2019</p> -->
                    <form id="form-question" action="<?php echo base_url('api_v1/synchronize_penilaian') ?>" method="post">
                        <div id="aspek-list"></div>
                        <div class="ask-list">
                            <div class="list-group">
                                <textarea name="sarandesc" class="form-control mt-3" placeholder="Saran untuk Peningkatan Kualitas" rows="3"></textarea>
                            </div>
                        </div>
                        <button class="btn-start ml-0">Save And Close <i class="fa fa-arrow-right"></i></button>
                    </form>
                </div>
                <div class="bg-overlay bg-primary"></div>
            </div>
        </div>

        <span class="back-to-top bg-primary text-white text-center shadow-sm">
          <i class="fa fa-chevron-up"></i>
        </span>

    </main>
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
<script src="<?php echo base_url('assets/rdg/js/main.materi.question.app.js'); ?>"></script>

</body>
</html>