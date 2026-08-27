<style>
  .alert {
    padding: 20px;
    background-color: #f44336;
    color: white;
    opacity: 1;
    transition: opacity 0.6s;
    margin-bottom: 15px;
  }

  .alert.success {background-color: #4CAF50;}
  .alert.info {background-color: #2196F3;}
  .alert.warning {background-color: #ff9800;}

  .closebtn {
    margin-left: 15px;
    color: white;
    font-weight: bold;
    float: right;
    font-size: 22px;
    line-height: 20px;
    cursor: pointer;
    transition: 0.3s;
  }

  .closebtn:hover {
    color: black;
  }
</style>

<div id="alertnotif" class="alert warning" style="display:none;">
  <span class="closebtn">&times;</span>  
  <strong></strong> TPE harus di isi...
</div>

<script>
  // Get all elements with class="closebtn"
  var close = document.getElementsByClassName("closebtn");
  var i;

  // Loop through all close buttons
  for (i = 0; i < close.length; i++) {
    // When someone clicks on a close button
    close[i].onclick = function(){
      let uiAlertNotif = document.getElementById('alertnotif');
      setTimeout(function(){ uiAlertNotif.style.display = "none"; }, 600);
    }
  }
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
      Download Request PJP Weekly <small>Control panel</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Download Request PJP Weekly</a></li>
    <li class="active">Form Download PJP</li>
  </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Download Request PJP Weekly</h3>
                </div>

                <form id="fm-download-req-pjp-weekly" role="form" method="post">
                  <div class="box-body">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                      <label for="salesmanid">TPE</label>
                      <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="TPE"></select>
                    </div>
                  </div>
                  <div class="box-footer">
                    <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning fa fa-backward">  Back</a>
                    <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>
                  </div>
              </form>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/req_pjp_weekly/req-pjp-weekly-form-download.js' ?>"></script>
