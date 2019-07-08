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
        <div class="container-fluid vh-100 bg-primary">
            <div class="h-100 w-100 d-flex align-items-center">
                <div class="container text-center z-index-1">
                    <h2 class="first-text">RDG</h2>
                    <p>Penilaian Kualitas Materi Rapat Dewan Gubernur dan High Level Event</p>
                    <p id="event-date" class="date"></p>

                    <form id="login-form" class="home-input">
                        <input type="hidden" id="nip" name="nip">
                        <div class="form-group">
                            <label for="sel1">Pilih Event:</label>
                            <select class="form-control" id="event">
                            </select>
                        </div>
                        <button type="submit" class="fifth-text btn-start">Mulai Review <i class="fa fa-arrow-right"></i></button>
                    </form>
                </div>
                <div id="respon">
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

<script>
    setInterval(function () {
        let date = new Date();
        let options = {
            weekday: "long", year: "numeric", month: "short",
            day: "numeric", hour: "2-digit", minute: "2-digit"
        };
        $('#event-date').text(date.toLocaleDateString("in-ID", options));
    }, 1000);
</script>

<!-- javascript -->
<script src="<?php echo base_url('assets/rdg/lib/jquery/jquery.min.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/malsup/jquery.form.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/cookies/js.cookie.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/adminlte/js/app.cise.js'); ?>"></script>
<script src="<?php echo base_url('assets/rdg/js/main.js'); ?>"></script>
<script src="<?php echo base_url('assets/rdg/js/main.event.js'); ?>"></script>

</body>
</html>
