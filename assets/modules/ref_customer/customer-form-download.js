(function () {
  const common = new Common();
  common.setTitle("Download Outlet");
  // declare dom
  let uiBtnCancel = $("#btn-cancel-form");
  let uiBtnDownload = $("#btn-download-form");
  let uiSelectClass = $("#classid-id");
  let uiAlertNotif = $("#alertnotif");

  let paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    loadChannel();
    uiSelectClass.on("select2:select", function (e) {
      valselected = e.params.data;
    });

    uiBtnDownload.click(function () {
      let selectedVal = uiSelectClass.val();
      if (!selectedVal || selectedVal === "null" || selectedVal === "All") {
        selectedVal = "All";
      }

      let selectedText = $("#classid-id option:selected").text();
      if (!selectedText || selectedVal === "All") {
        selectedText = "Semua_Channel";
      } else {
        selectedText = selectedText.replace(/ /g, "_");
      }

      let username =
        paramsession && paramsession.username ? paramsession.username : "admin";
      let idjabatan =
        paramsession && paramsession.idjabatan ? paramsession.idjabatan : "-";
      let restrict_level =
        paramsession && paramsession.restrict_level
          ? paramsession.restrict_level
          : "-";

      let baseUrl = common.baseURL("ref_customer/savetoxlsx");
      // Clean up any double slash before endpoint
      baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

      let downloadUrl =
        baseUrl +
        "?account=" +
        encodeURIComponent(selectedVal) +
        "&username=" +
        encodeURIComponent(username) +
        "&idjabatan=" +
        encodeURIComponent(idjabatan) +
        "&restrict_level=" +
        encodeURIComponent(restrict_level) +
        "&filtername=" +
        encodeURIComponent(selectedText);

      window.location.href = downloadUrl;
    });

    uiBtnCancel.click(function () {
      common.direct("ref_customer");
    });
  }

  function loadChannel() {
    common.loading();
    $.post(common.baseURL("ref_customer_type/load"), function (res) {
      uiSelectClass.empty();
      let options = [{ id: "All", text: "Semua Channel" }];
      if (res && res.rows && res.rows.length > 0) {
        $.each(res.rows, function (i, o) {
          options.push({
            id: o.typeid,
            text: o.nama_type || o.typeid,
          });
        });
      }

      uiSelectClass.select2({
        placeholder: "Semua Channel",
        allowClear: true,
        data: options,
      });

      uiSelectClass.val("All").trigger("change");
      common.loadingClose();
    }).fail(function () {
      common.loadingClose();
    });
  }
})();
