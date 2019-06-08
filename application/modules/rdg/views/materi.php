<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
    <link href="<?php echo base_url('assets/rdg/css/style.css'); ?>" media="all" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <!-- Bootstrap CSS File -->
    <!-- <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->

    <!-- Libraries CSS Files -->
    <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<header>
    <nav id='flexmenu'>
        <div class="logo">
            <a href=""><img src="<?php echo base_url('assets/rdg/img/logo.png'); ?>" style="width: 220px; height: auto;"></a>
        </div>
    </nav>
</header>


<section id="main" class="container-fluid">
    <div class="note">
        <h2>LIST REVIEW</h2>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
        <p class="date">14 Juni 2019</p>
        <ul>
            <li>
                <span>Nama RDG / High Level Event (Nama Satker)</span>
                <a href="" class="btn-start">Evaluasi</a>
            </li>
            <li>
                <span>Nama RDG / High Level Event (Nama Satker)</span>
                <a href="" class="btn-start">Evaluasi</a>
            </li>
            <li>
                <span>Nama RDG / High Level Event (Nama Satker)</span>
                <a href="" class="btn-start">Evaluasi</a>
            </li>
            <li>
                <span>Nama RDG / High Level Event (Nama Satker)</span>
                <a href="" class="btn-start">Evaluasi</a>
            </li>

        </ul>

    </div>
</section>

<!-- javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<!-- <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<script src="<?php echo base_url('assets/rdg/js/main.js'); ?>"></script>
<script>
    $(document).ready(function () {
        $('.toggle').click(function () {
            $('#nav').toggleClass('open');
            $('.container').toggleClass('menu-open');
        });
    });
</script>

</body>
</html>