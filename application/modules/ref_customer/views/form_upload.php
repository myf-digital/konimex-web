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
  <strong>Peringatan...</strong> <span id="alert-message">File yang di upload harus file excel(xls atau xlsx) dan maksimal size file yang di upload adalah 10MB</span>
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
        Upload Outlet <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Outlet</a></li>
        <li class="active">Form Upload</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Upload Outlet</h3>
                </div>
                <form id="fm-upload-customer" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row" id="row-filter-location" style="display: flex; flex-wrap: wrap;">
                            <div class="form-group col-md-4">
                                <label for="regionalid">Regional</label>
                                <select id="regionalid-id" name="regionalid[]" class="form-control" placeholder="Regional" multiple="multiple"></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="areaid">Area</label>
                                <select id="areaid-id" name="areaid[]" class="form-control" placeholder="Area" multiple="multiple"></select>
                            </div>
                            <!-- <div class="form-group col-md-4">
                                <label for="subareaid">Sub Area</label>
                                <select id="subareaid-id" name="subareaid[]" class="form-control" placeholder="Sub Area" multiple="multiple"></select>
                            </div> -->
                        </div>
                        <div class="row" style="margin-top: 15px;">
                            <div class="form-group col-xs-12 col-sm-12 col-md-6">
                                <label for="fileupload">File Upload Outlet</label>
                                <input type="file" name="fileupload" id="fileupload" class="form-control" onchange="return validasiFile()">
                                <small>*Max Size File Upload 10 MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning fa fa-backward"> Back</a>
                        <button id="btn-download-temp" type="button" class="btn btn-info fa fa-download"> Download Template</button>
                        <button id="btn-upload-form" type="submit" class="btn btn-success fa fa-upload"> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/ref_customer/customer-form-upload.js' ?>"></script>
