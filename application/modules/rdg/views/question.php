<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
    <link href="<?php echo base_url('assets/rdg/css/style.css'); ?>" media="all" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <!-- Bootstrap CSS File -->
    <link href="<?php echo base_url('assets/rdg/lib/bootstrap/css/bootstrap.min.css'); ?>" media="all" rel="stylesheet">
    <!-- Libraries CSS Files -->
    <link href="<?php echo base_url('assets/rdg/lib/font-awesome/css/font-awesome.min.css'); ?>" media="all" rel="stylesheet">
</head>
<body>
<header>
    <nav id='flexmenu'>
        <div class="logo">
            <a href=""><img src="<?php echo base_url('assets/rdg/img/logo.png'); ?>" style="width: 220px; height: auto;"></a>
        </div>
    </nav>
</header>

<content>
    <div id="main">
        <div class="note">
            <h2>LIST REVIEW</h2>
            <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p> -->
            <!-- <p class="date">14 Juni 2019</p> -->
            <form>
                <div class="ask-list">
                    <div class="list-group">
                        <div class="nomor">
                            <span>1.</span>
                        </div>
                        <div class="pertanyaan">
                            <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                        </div>
                    </div>
                    <div class="pilihan">
                        <label class="checkbox">One
                            <input type="radio" checked="checked" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Two
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Three
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Four
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="ask-list">
                    <div class="list-group">
                        <div class="nomor">
                            <span>1.</span>
                        </div>
                        <div class="pertanyaan">
                            <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                        </div>
                    </div>
                    <div class="pilihan">
                        <label class="checkbox">One
                            <input type="radio" checked="checked" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Two
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Three
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Four
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="ask-list">
                    <div class="list-group">
                        <div class="nomor">
                            <span>1.</span>
                        </div>
                        <div class="pertanyaan">
                            <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                        </div>
                    </div>
                    <div class="pilihan">
                        <label class="checkbox">One
                            <input type="radio" checked="checked" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Two
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Three
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Four
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="ask-list">
                    <div class="list-group">
                        <div class="nomor">
                            <span>1.</span>
                        </div>
                        <div class="pertanyaan">
                            <span>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
                        </div>
                    </div>
                    <div class="pilihan">
                        <label class="checkbox">One
                            <input type="radio" checked="checked" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Two
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Three
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                        <label class="checkbox">Four
                            <input type="radio" name="radio">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>

            </form>
        </div>
    </div>
</content>

<!-- footer -->
<footer class="footer">
    <div class="row bottom">
        <div class="col-sm-12">
            <p class="text-center copyright">All Rights Reserved by JME Since 2005.©</p>
        </div>
    </div>
</footer>


<!-- javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="<?php echo base_url('assets/rdg/lib/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
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