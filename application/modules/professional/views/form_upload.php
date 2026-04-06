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
  <strong></strong> Peringatan... File yang di upload harus file excel(xls atau xlsx) dan maximal size file yang di upload adalah 10MB
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
        Upload Target Professional <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Upload Target Professional</a></li>
        <li class="active">Form Upload Target Professional</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Upload Target Professional</h3>
                </div>
                <form id="fm-upload-target-professional" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <label for="salesmanid">File Upload Target Professional</label>
                            <input type="file" name="fileupload" id="fileupload" class="form-control"  onchange="return validasiFile()">
                            <small>*Max Size File Upload 10 MB</small>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function validasiFile() {
        var inputFile = document.getElementById('fileupload');
        let uiAlertNotif = document.getElementById('alertnotif');
        var pathFile = inputFile.value;
        var ekstensiOk = /(\.xls|\.xlsx|\.csv)$/i;
        if (!ekstensiOk.exec(pathFile)) {
            //alert('Silakan upload file yang memiliki ekstensi .xls dan .xlsx');
            uiAlertNotif.style.display = "block";
            inputFile.value = '';
            return false;
        } else {
            const size = (inputFile.files[0].size / 1024 / 1024).toFixed(2); 
            if (size > 10) { 
                //alert("File Max 10 MB"); 
                uiAlertNotif.style.display = "block";
                inputFile.value = '';
            } else { 
                uiAlertNotif.style.display = "none";
                //alert("Valid File"); 
            } 
        }    
    }

    const common = new Common();
    common.setTitle("Upload Target Professional");
    let uiForm = $("#fm-upload-target-professional");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnUpload = $("#btn-upload-form");
    let uiBtnDownloadTemp = $("#btn-download-temp");
    let uiAlertNotif = $("#alertnotif");
    let uiFile = $("#fileupload");

    let paramsession = common.getCookie("session");

    initialize();

    function initialize() {
        uiBtnUpload.click(function (e) {
            e.preventDefault();
            let url = common.baseURL("professional/upload");

            uiAlertNotif.hide();
            if (uiFile.val() === '') {
                uiAlertNotif.show();
                return;
            }

            let formData = new FormData();
            formData.append('fileupload', uiFile[0].files[0]);
            formData.append('usersession', paramsession.username);

            uiBtnUpload.html(' Uploading...').attr('disabled', true).removeClass('fa-upload').addClass('fa-spinner');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    let res = typeof response === 'string' ? JSON.parse(response) : response;

                    if (res.status === false) {
                        Swal.fire(
                            'Gagal!',
                            res.message,
                            'error'
                        );
                        return;
                     }

                    Swal.fire(
                        'Berhasil!',
                        res.message,
                        'success'
                    ).then(() => {
                        window.location.href = common.baseURL("professional/target");
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Upload failed:", error);
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat mengupload file.',
                        'error'
                    );
                },
                complete: function() {
                    uiBtnUpload.html(' Upload').attr('disabled', false).removeClass('fa-spinner').addClass('fa-upload');
                },
            });
        });

        uiBtnCancel.click(function () {
            common.direct("professional/target");
        });

        uiBtnDownloadTemp.click(function () {
            window.location.href = common.baseURL("professional/download_template");
        });
    }
</script>
