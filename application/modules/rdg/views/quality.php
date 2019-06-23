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
                <img src="<?php echo base_url('assets/rdg/img/logo.png'); ?>" style="width: 280px; height: 45px;">
            </div>
        </nav>

        <div class="container-fluid vh-100 bg-primary">
            <div class="h-100 w-100 d-flex align-items-center">
                <div class="container text-center z-index-1">
                    <h2 class="mt-5 mb-5">RDG A NAMA SATKER</h2>
                    <div class="chart-container">
                        <canvas id="myChart"></canvas>
                    </div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="<?php echo base_url('assets/rdg/js/main.quality.app.js'); ?>"></script>

<!-- javascript -->
<script>

    var yLabels = {
        0 : '0',
        1 : '1',
        2 : '2',
        3 : '3',
        4 : '4',
        5 : '5',
        6 : '6'
    }

    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Kedalaman Materi", "Ketepatan Proyek", "Kelengkapan data/ informasi", "Keterkinian data/ informasi","Rekomendasi implemen table","Bahasa yang mudah diapahami","Kualitas secara keseluruhan"],
            datasets: [{
                data: [3, 3.5, 5, 2.5,4,3.5,5],
                backgroundColor: [
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(255, 204, 86, 0.2)',
                    'rgba(255, 202, 86, 0.2)',

                    'rgba(255, 206, 86, 0.2)'

                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(255, 204, 86, 1)',
                    'rgba(255, 203, 86, 1)',

                    'rgba(75, 192, 192, 1)'

                ],
                borderWidth: 1
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return yLabels[value];
                        }
                    }
                }]
            },
            title: {
                display: true,
                text: 'Shameless Bar Graph to show proficency in skills'
            }
        }
    });

</script>

</body>
</html>