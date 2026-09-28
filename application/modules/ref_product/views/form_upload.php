<style>
.alert {
  padding: 20px;
  background-color: #f44336;
  color: white;
  opacity: 1;
  transition: opacity 0.6s;
  margin-bottom: 15px;
  border-radius: 4px;
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
  <strong>Peringatan...</strong> <span id="alert-message">File yang diupload harus file excel (.xls atau .xlsx) dan maksimal ukuran 10MB</span>
</div>

<script>
var close = document.getElementsByClassName("closebtn");
for (var i = 0; i < close.length; i++) {
  close[i].onclick = function(){
    var uiAlertNotif = document.getElementById('alertnotif');
    setTimeout(function(){ uiAlertNotif.style.display = "none"; }, 400);
  }
}
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Upload Product <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Product</a></li>
        <li class="active">Form Upload</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-upload"></i> Form Upload Product</h3>
                </div>
                <form id="fm-upload-product" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <label for="fileupload">File Excel Product</label>
                                <input type="file" name="fileupload" id="fileupload" class="form-control" onchange="return validasiFile()">
                                <small class="text-muted">*Format file: .xls / .xlsx | Ukuran Maksimal: 10 MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Kembali</a>
                        <button id="btn-download-temp" type="button" class="btn btn-info"><i class="fa fa-download"></i> Download Template</button>
                        <button id="btn-upload-form" type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Mulai Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo base_url() . 'assets/modules/ref_product/product-form-upload.js' ?>?v=1.0"></script>
