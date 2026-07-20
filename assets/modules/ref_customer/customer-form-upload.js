function validasiFile() {
  let inputFile = document.getElementById("fileupload");
  let uiAlertNotif = document.getElementById("alertnotif");
  let uiAlertMsg = document.getElementById("alert-message");
  let pathFile = inputFile.value;
  let ekstensiOk = /(\.xls|\.xlsx)$/i;
  if (!ekstensiOk.exec(pathFile)) {
    uiAlertMsg.innerText =
      "File yang di upload harus file excel(xls atau xlsx)";
    uiAlertNotif.style.display = "block";
    inputFile.value = "";
    return false;
  } else {
    const size = (inputFile.files[0].size / 1024 / 1024).toFixed(2);
    if (size > 10) {
      uiAlertMsg.innerText = "Maksimal size file yang di upload adalah 10MB";
      uiAlertNotif.style.display = "block";
      inputFile.value = "";
    } else {
      uiAlertNotif.style.display = "none";
    }
  }
}

(function () {
  const common = new Common();
  common.setTitle("Upload Outlet");

  let uiSelectRegional = $("#regionalid-id");
  let uiSelectArea = $("#areaid-id");
  let uiSelectSubarea = $("#subareaid-id");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiBtnDownloadTemp = $("#btn-download-temp");
  let uiBtnUpload = $("#btn-upload-form");
  let uiAlertNotif = $("#alertnotif");
  let uiFile = $("#fileupload");

  let paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    loadRegional({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
      restrict_bu: paramsession.restrict_bu,
    });

    uiSelectRegional.on("select2:select", function (e) {
      let regVal = uiSelectRegional.val();
      let regStr = Array.isArray(regVal) ? regVal.join(",") : regVal || "";
      let srval = {
        regionalid: regStr,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      };
      loadArea(srval);
    });

    uiSelectRegional.on("select2:unselect", function (e) {
      let regVal = uiSelectRegional.val();
      if (!regVal || regVal.length === 0) {
        uiSelectArea.empty().trigger("change");
        uiSelectSubarea.empty().trigger("change");
      } else {
        let regStr = Array.isArray(regVal) ? regVal.join(",") : regVal || "";
        loadArea({
          regionalid: regStr,
          usersession: paramsession.username,
          restrict_level: paramsession.restrict_level,
        });
      }
    });

    uiSelectArea.on("select2:select", function (e) {
      let regVal = uiSelectRegional.val();
      let regStr = Array.isArray(regVal) ? regVal.join(",") : regVal || "";
      let areaVal = uiSelectArea.val();
      let areaStr = Array.isArray(areaVal) ? areaVal.join(",") : areaVal || "";
      let srval = {
        regionalid: regStr,
        areaid: areaStr,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      };
      loadSubArea(srval);
    });

    uiSelectArea.on("select2:unselect", function (e) {
      let areaVal = uiSelectArea.val();
      if (!areaVal || areaVal.length === 0) {
        uiSelectSubarea.empty().trigger("change");
      } else {
        let regVal = uiSelectRegional.val();
        let regStr = Array.isArray(regVal) ? regVal.join(",") : regVal || "";
        let areaStr = Array.isArray(areaVal)
          ? areaVal.join(",")
          : areaVal || "";
        loadSubArea({
          regionalid: regStr,
          areaid: areaStr,
          usersession: paramsession.username,
          restrict_level: paramsession.restrict_level,
        });
      }
    });

    uiSelectRegional.select2({
      placeholder: "Select Regional",
      allowClear: true,
      multiple: true,
    });

    uiSelectArea.select2({
      placeholder: "Select Area",
      allowClear: true,
      multiple: true,
    });

    uiSelectSubarea.select2({
      placeholder: "Select SubArea",
      allowClear: true,
      multiple: true,
    });

    let urlParams = new URLSearchParams(window.location.search);
    let ref = urlParams.get("ref");
    let redirectUrl = ref.includes("professional")
      ? "professional"
      : "ref_customer";

    uiBtnCancel.click(function () {
      common.direct(redirectUrl);
    });

    uiBtnDownloadTemp.click(function () {
      let regVal = uiSelectRegional.val();
      let regStr = Array.isArray(regVal) ? regVal.join(",") : regVal || "";
      let areaVal = uiSelectArea.val();
      let areaStr = Array.isArray(areaVal) ? areaVal.join(",") : areaVal || "";
      let subareaVal = uiSelectSubarea.val();
      let subareaStr = Array.isArray(subareaVal)
        ? subareaVal.join(",")
        : subareaVal || "";

      let downloadUrl =
        common.baseURL("ref_customer/download_template") +
        "?regionalid=" +
        encodeURIComponent(regStr) +
        "&areaid=" +
        encodeURIComponent(areaStr) +
        "&subareaid=" +
        encodeURIComponent(subareaStr) +
        "&usersession=" +
        encodeURIComponent(paramsession.username) +
        "&restrict_level=" +
        encodeURIComponent(paramsession.restrict_level || "0");
      window.location.href = downloadUrl;
    });

    uiBtnUpload.click(function (e) {
      e.preventDefault();
      let url = common.baseURL("ref_customer/upload");

      uiAlertNotif.hide();
      if (uiFile.val() === "") {
        $("#alert-message").text("Silakan pilih file Excel terlebih dahulu.");
        uiAlertNotif.show();
        return;
      }

      let formData = new FormData();
      formData.append("fileupload", uiFile[0].files[0]);
      formData.append("usersession", paramsession.username);
      formData.append("restrict_level", paramsession.restrict_level || "0");

      uiBtnUpload
        .html(" Uploading...")
        .attr("disabled", true)
        .removeClass("fa-upload")
        .addClass("fa-spinner");

      $.ajax({
        url: url,
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
            common.direct(redirectUrl);
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
            .html(" Upload")
            .attr("disabled", false)
            .removeClass("fa-spinner")
            .addClass("fa-upload");
        },
      });
    });
  }

  function loadRegional(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_regional_restrict"),
      { usersession: data.usersession, restrict_level: data.restrict_level },
      function (res) {
        uiSelectRegional.empty();
        uiSelectRegional.select2({
          placeholder: "Select Regional",
          allowClear: true,
          multiple: true,
          data: $.map(res.result, function (o) {
            return { id: o.regionalid, text: o.nama_regional };
          }),
        });

        let selectedRegs = [];
        if (
          paramsession.restrict_location &&
          Array.isArray(paramsession.restrict_location)
        ) {
          paramsession.restrict_location.forEach(function (loc) {
            if (loc.regionalid) selectedRegs.push(loc.regionalid);
          });
        }

        if (selectedRegs.length > 0) {
          uiSelectRegional.val(selectedRegs).trigger("change");
          let regStr = selectedRegs.join(",");
          loadArea({
            regionalid: regStr,
            usersession: paramsession.username,
            restrict_level: paramsession.restrict_level,
          });
        } else {
          uiSelectRegional.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }

  function loadArea(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_area_restrict"),
      {
        regionalid: data.regionalid,
        usersession: data.usersession,
        restrict_level: data.restrict_level,
      },
      function (res) {
        uiSelectArea.empty();
        uiSelectArea.select2({
          placeholder: "Select Area",
          allowClear: true,
          multiple: true,
          data: $.map(res.result, function (o) {
            return { id: o.areaid, text: o.nama_area };
          }),
        });

        let selectedAreas = [];
        if (
          paramsession.restrict_location &&
          Array.isArray(paramsession.restrict_location)
        ) {
          paramsession.restrict_location.forEach(function (loc) {
            if (loc.areaid) selectedAreas.push(loc.areaid);
          });
        }

        let optionIds = $.map(res.result, function (o) {
          return o.areaid;
        });
        let filteredAreas = selectedAreas.filter(function (id) {
          return optionIds.indexOf(id) !== -1;
        });

        if (filteredAreas.length > 0) {
          uiSelectArea.val(filteredAreas).trigger("change");
          let areaStr = filteredAreas.join(",");
          loadSubArea({
            regionalid: data.regionalid,
            areaid: areaStr,
            usersession: paramsession.username,
            restrict_level: paramsession.restrict_level,
          });
        } else {
          uiSelectArea.val(null).trigger("change");
          uiSelectSubarea.empty().trigger("change");
        }
        common.loadingClose();
      },
    );
  }

  function loadSubArea(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_subarea_restrict"),
      {
        regionalid: data.regionalid,
        areaid: data.areaid,
        usersession: data.usersession,
        restrict_level: data.restrict_level,
      },
      function (res) {
        uiSelectSubarea.empty();
        uiSelectSubarea.select2({
          placeholder: "Select SubArea",
          allowClear: true,
          multiple: true,
          data: $.map(res.result, function (o) {
            return { id: o.subareaid, text: o.nama_area };
          }),
        });

        let selectedSubareas = [];
        if (
          paramsession.restrict_location &&
          Array.isArray(paramsession.restrict_location)
        ) {
          paramsession.restrict_location.forEach(function (loc) {
            if (loc.subareaid) selectedSubareas.push(loc.subareaid);
          });
        }

        let optionIds = $.map(res.result, function (o) {
          return o.subareaid;
        });
        let filteredSubareas = selectedSubareas.filter(function (id) {
          return optionIds.indexOf(id) !== -1;
        });

        if (filteredSubareas.length > 0) {
          uiSelectSubarea.val(filteredSubareas).trigger("change");
        } else {
          uiSelectSubarea.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }
})();
