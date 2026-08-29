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
  <span id="alertnotif-text">Peringatan... File yang diupload harus file excel (.xls atau .xlsx) dan maksimal ukuran file adalah 10MB</span>
</div>
<script>
var close = document.getElementsByClassName("closebtn");
var i;
for (i = 0; i < close.length; i++) {
  close[i].onclick = function(){
    let uiAlertNotif = document.getElementById('alertnotif');
    setTimeout(function(){ uiAlertNotif.style.display = "none"; }, 600);
  }
}
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Upload DUB <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup DUB</a></li>
        <li class="active">Form Upload DUB</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Upload DUB</h3>
                </div>

                <form id="fm-upload-setup-dub" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-8 col-md-6">
                                <label for="salesmanid-id">Pilih MEDREP (Untuk Download Template)</label>
                                <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="Pilih MEDREP"></select>
                                <small class="text-muted">Pilih MEDREP untuk mengunduh template yang sudah terisi data outlet dan user binaan.</small>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <button id="btn-download-template" type="button" class="btn btn-info fa fa-download"> Download Template</button>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-12 col-md-6">
                                <label for="fileupload">File Upload DUB (Excel)</label>
                                <input type="file" name="fileupload" id="fileupload" class="form-control" accept=".xls,.xlsx" onchange="return validasiFile()">
                                <small class="text-muted">*Format file harus .xls atau .xlsx dengan ukuran maksimal 10 MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning fa fa-backward"> Back</a>
                        <button id="btn-upload-form" type="submit" class="btn btn-success fa fa-upload"> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo base_url() . 'assets/modules/setup_dub/form-upload.js' ?>"></script>
