function validasiFile() {
  var inputFile = document.getElementById("fileupload");
  var uiAlertNotif = document.getElementById("alertnotif");
  var uiAlertText = document.getElementById("alertnotif-text");
  var pathFile = inputFile.value;
  var ekstensiOk = /(\.xls|\.xlsx)$/i;

  if (!ekstensiOk.exec(pathFile)) {
    if (uiAlertText) {
      uiAlertText.innerText =
        "File yang diupload harus file excel (.xls atau .xlsx).";
    }
    uiAlertNotif.style.display = "block";
    inputFile.value = "";
    return false;
  } else {
    var size = (inputFile.files[0].size / 1024 / 1024).toFixed(2);
    if (size > 10) {
      if (uiAlertText) {
        uiAlertText.innerText = "Ukuran file maksimal adalah 10 MB.";
      }
      uiAlertNotif.style.display = "block";
      inputFile.value = "";
      return false;
    } else {
      uiAlertNotif.style.display = "none";
    }
  }
}

(function () {
  const common = new Common();
  common.setTitle("Upload Setup DUB");

  let uiForm = $("#fm-upload-setup-dub");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiBtnUpload = $("#btn-upload-form");
  let uiBtnDownloadTemplate = $("#btn-download-template");
  let uiSelectSalesman = $("#salesmanid-id");
  let uiFile = $("#fileupload");
  let uiAlertNotif = $("#alertnotif");

  let paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    loadSalesman({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
    });

    uiBtnDownloadTemplate.click(function () {
      let selectedSalesman = uiSelectSalesman.val();
      if (!selectedSalesman) {
        Swal.fire({
          title: "Perhatian",
          text: "Silakan pilih MEDREP terlebih dahulu untuk mengunduh template.",
          icon: "warning",
        });
        return;
      }
      window.location.href = common.baseURL(
        "setup_dub/download_template/" + selectedSalesman,
      );
    });

    uiBtnUpload.click(function (e) {
      e.preventDefault();

      uiAlertNotif.hide();
      if (!uiFile.val()) {
        Swal.fire({
          title: "Peringatan",
          text: "Silakan pilih file Excel yang akan diupload.",
          icon: "warning",
        });
        return;
      }

      let formData = new FormData();
      formData.append("fileupload", uiFile[0].files[0]);
      formData.append("usersession", paramsession.username);
      formData.append("rolename", paramsession.role_name);

      Swal.fire({
        title: "Mengupload...",
        text: "Mohon tunggu, file sedang diproses dan divalidasi.",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      uiBtnUpload
        .html(" Uploading...")
        .attr("disabled", true)
        .removeClass("fa-upload")
        .addClass("fa-spinner");

      $.ajax({
        url: common.baseURL("setup_dub/upload"),
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          let res =
            typeof response === "string" ? JSON.parse(response) : response;

          if (res.status === false) {
            Swal.fire({
              title: "Gagal Upload!",
              html: res.message || "Terjadi kesalahan saat memproses data.",
              icon: "error",
            });
            return;
          }

          Swal.fire({
            title: "Berhasil!",
            text: res.message || "Data DUB berhasil diupload.",
            icon: "success",
          }).then(() => {
            common.direct("setup_dub");
          });
        },
        error: function (xhr, status, error) {
          let msg = "Terjadi kesalahan sistem saat mengupload file.";
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          Swal.fire({
            title: "Error!",
            text: msg,
            icon: "error",
          });
        },
        complete: function () {
          uiBtnUpload
            .html(" Upload")
            .attr("disabled", false)
            .removeClass("fa-spinner")
            .addClass("fa-upload");
          resetForm();
        },
      });
    });

    uiBtnCancel.click(function () {
      common.direct("setup_dub");
    });
  }

  function resetForm() {
    uiForm[0].reset();
    uiSelectSalesman.val(null).trigger("change");
    uiFile.val("");
    uiAlertNotif.hide();
  }

  function loadSalesman(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_salesman"),
      {
        idjabatan: data.idjabatan,
        usersession: data.usersession,
        restrict_level: data.restrict_level,
      },
      function (res) {
        uiSelectSalesman.empty();
        uiSelectSalesman.select2({
          placeholder: "Pilih MEDREP",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.salesmanid;
            o.text =
              o.salesmanid + " - " + o.nama_salesman + " - " + o.tipe_sales;
            return o;
          }),
        });

        uiSelectSalesman.val(null).trigger("change");
        common.loadingClose();
      },
    );
  }
})();
