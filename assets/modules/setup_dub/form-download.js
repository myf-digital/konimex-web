(function () {
  const common = new Common();
  common.setTitle("Download Setup DUB");
  // declare dom
  let uiBtnCancel = $("#btn-cancel-form");
  let uiBtnDownload = $("#btn-download-form");
  let uiSelectSalesman = $("#salesmanid-id");

  let paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    loadSalesman({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
    });
    uiSelectSalesman.on("select2:select", function (e) {
      valselected = e.params.data;
    });

    uiBtnDownload.click(function () {
      let vSelected = uiSelectSalesman.val();
      if (!vSelected || (vSelected && vSelected.length == 0)) {
        Swal.fire({
          title: "Perhatian",
          text: "Harap pilih minimal 1 TPE",
          icon: "warning",
        });
        return;
      }
      let baseUrl = common.baseURL("setup_dub/savetoxlsx");
      baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

      let username =
        paramsession && paramsession.username ? paramsession.username : "";
      let idjabatan =
        paramsession && paramsession.idjabatan ? paramsession.idjabatan : "";
      let restrict_level =
        paramsession && paramsession.restrict_level
          ? paramsession.restrict_level
          : "";

      let downloadUrl =
        baseUrl +
        "?salesmanid=" +
        encodeURIComponent(vSelected.join(",")) +
        "&username=" +
        encodeURIComponent(username) +
        "&idjabatan=" +
        encodeURIComponent(idjabatan) +
        "&restrict_level=" +
        encodeURIComponent(restrict_level);

      window.location.href = downloadUrl;
    });

    uiBtnCancel.click(function () {
      common.direct("setup_dub");
    });
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
          placeholder: "All TPE",
          allowClear: true,
          multiple: true,
          data: $.map(res.result, function (o) {
            o.id = o.salesmanid; // replace name with the property used for the text
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
