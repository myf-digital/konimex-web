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
  <strong></strong> Peringatan... File yang di upload harus file excel(xls atau xlsx) dan maximal size file yang di upload adalah 2Mb
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
    // Get the parent of <span class="closebtn"> (<div class="alert">)
    //var div = this.parentElement;

    // Set the opacity of div to 0 (transparent)
    //div.style.opacity = "0";

    // Hide the div after 600ms (the same amount of milliseconds it takes to fade out)
    //setTimeout(function(){ div.style.display = "none"; }, 600);
  }
}
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Upload Pjp <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Upload Pjp</a></li>
        <li class="active">Form Upload Pjp</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Upload Pjp</h3>
                </div>
                <!--<form action="<?php echo base_url();?>conf_setup_pjp/upload/" method="post" enctype="multipart/form-data">-->
                <form id="fm-upload-setup-pjp" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
							<!--<div class="form-group  col-xs-12 col-sm-12 col-md-1">
								<label for="weekaktif">Week Aktif</label>
								<input name="week_aktif" class="form-control" placeholder="Week Aktif" disabled>
							</div>-->
							<div class="form-group col-xs-12 col-sm-12 col-md-6">
								<label for="salesmanid">File Upload PJP</label>
                                <input type="file" class="form-control-file" id="fileupload" name="fileupload" onchange="return validasiFile()" >
                                <small>*Max Size File Upload 2 Mb</small>
							</div>
                    </div>

                    <div class="box-footer">
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning fa fa-backward"> Back</a>
                        <button id="btn-upload-form" type="submit" class="btn btn-primary fa fa-upload"> Upload</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script>
function validasiFile(){
        var inputFile = document.getElementById('fileupload');
        let uiAlertNotif = document.getElementById('alertnotif');
        var pathFile = inputFile.value;
        var ekstensiOk = /(\.xls|\.xlsx)$/i;
        if(!ekstensiOk.exec(pathFile)){
            //alert('Silakan upload file yang memiliki ekstensi .xls dan .xlsx');
            uiAlertNotif.style.display = "block";
            inputFile.value = '';
            return false;
        }else{
            const size = (inputFile.files[0].size / 1024 / 1024).toFixed(2); 
            if (size > 2) { 
                //alert("File Max 2 MB"); 
                uiAlertNotif.style.display = "block";
                inputFile.value = '';
            } else { 
                uiAlertNotif.style.display = "none";
                //alert("Valid File"); 
            } 
        }    
    }


    const common = new Common();
    common.setTitle("Upload Setup Pjp");
    let uiForm = $("#fm-upload-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnUpload = $("#btn-upload-form");
    let uiAlertNotif = $("#alertnotif");
    let uiFile = $("#fileupload");

    let paramsession = common.getCookie("session");

    initialize();

    function initialize() {
        let url = common.baseURL("conf_setup_pjp/upload");
        uiForm.initForm({
            url: url,
            directUrl: "conf_setup_pjp",
            beforeSubmit: function (form, options) {
                if (uiFile.val()===''){
                    uiAlertNotif.show();
                    return true; // MANDATORY!
                }else{
                    uiAlertNotif.hide();
                    return true; // MANDATORY!
                }
            }
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

    }
</script>
