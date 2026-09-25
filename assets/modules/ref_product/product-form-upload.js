function validasiFile() {
  var inputFile = document.getElementById("fileupload");
  var uiAlertNotif = document.getElementById("alertnotif");
  var uiAlertMsg = document.getElementById("alert-message");
  var pathFile = inputFile.value;
  var ekstensiOk = /(\.xls|\.xlsx)$/i;

  if (!ekstensiOk.exec(pathFile)) {
    uiAlertMsg.innerText = "File yang diupload harus file Excel (.xls atau .xlsx)";
    uiAlertNotif.style.display = "block";
    inputFile.value = "";
    return false;
  } else {
    var size = (inputFile.files[0].size / 1024 / 1024).toFixed(2);
    if (size > 10) {
      uiAlertMsg.innerText = "Ukuran file terlalu besar! Maksimal 10MB";
      uiAlertNotif.style.display = "block";
      inputFile.value = "";
      return false;
    } else {
      uiAlertNotif.style.display = "none";
    }
  }
}

(function () {
  var common = new Common();
  common.setTitle("Upload Product");

  var uiBtnCancel = $("#btn-cancel-form");
  var uiBtnDownloadTemp = $("#btn-download-temp");
  var uiBtnUpload = $("#btn-upload-form");
  var uiForm = $("#fm-upload-product");
  var paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    uiBtnCancel.click(function () {
      common.direct("ref_product");
    });

    uiBtnDownloadTemp.click(function () {
      window.location.href = common.baseURL("ref_product/download_template");
    });

    uiForm.on("submit", function (e) {
      e.preventDefault();

      var fileInput = $("#fileupload");
      if (!fileInput.val()) {
        Swal.fire("Peringatan!", "Silakan pilih file Excel terlebih dahulu!", "warning");
        return;
      }

      var formData = new FormData(this);
      if (paramsession && paramsession.username) {
        formData.append("usersession", paramsession.username);
      }

      uiBtnUpload
        .attr("disabled", true)
        .removeClass("fa-upload")
        .addClass("fa-spinner");

      $.ajax({
        url: common.baseURL("ref_product/upload"),
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          let res =
            typeof response === "string" ? JSON.parse(response) : response;

          if (res.status === false) {
            Swal.fire("Gagal!", res.message, "error");
            return;
          }

          Swal.fire("Berhasil!", res.message, "success").then(() => {
            common.direct("ref_product");
          });
        },
        error: function (xhr, status, error) {
          console.error("Upload failed:", error);
          Swal.fire(
            "Gagal!",
            "Terjadi kesalahan saat mengupload file.",
            "error",
          );
        },
        complete: function () {
          uiBtnUpload
            .attr("disabled", false)
            .removeClass("fa-spinner")
            .addClass("fa-upload");
        },
      });
    });
  }
})();
