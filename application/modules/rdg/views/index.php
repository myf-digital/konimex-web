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
 <nav id='flexmenu'>
    <div class="logo">
      <a href=""><img src="<?php echo base_url('assets/rdg/img/logo.png'); ?>" style="width: 220px; height: auto;"></a>
    </div>
  </nav>


  <section id="main">
     <div class="note">
      <h2>WRITE REVIEW</h2>
      <p>Penilaian Kualitas Materi Rapat Dewan Gubernur dan High Level Event</p>
      <p class="date">14 Juni 2019</p>
      <form>
        <input type="text" name="" class="form-control" placeholder="NIP">
      </form>
      <button class="btn-start">Mulai Review</button>

    </div>
  </section>


<!-- javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<!-- <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<script src="<?php echo base_url('assets/rdg/js/main.js'); ?>"></script>
<script>
$(document).ready(function(){
    $('.toggle').click(function(){
        $('#nav').toggleClass('open');
        $('.container').toggleClass('menu-open');
    });
});
</script>

</body>
</html>
