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
    loadAccount();
    uiSelectClass.on("select2:select", function (e) {
      valselected = e.params.data;
    });

    uiBtnDownload.click(function () {
      let classid = $("#classid-id option:selected").text();
      if (classid) classid = classid.replace(/ /g, "_");
      common.direct(
        "ref_customer/savetoxlsx/" +
          uiSelectClass.val() +
          "/" +
          paramsession.username +
          "/" +
          paramsession.idjabatan +
          "/" +
          paramsession.restrict_level +
          "/" +
          classid,
      );
      //}
    });

    uiBtnCancel.click(function () {
      common.direct("ref_customer");
    });
  }

  function loadAccount() {
    common.loading();
    $.post(common.baseURL("ref_customer_class/load"), function (res) {
      uiSelectClass.empty();
      uiSelectClass.select2({
        placeholder: "All Account",
        allowClear: true,
        data: $.map(res.rows, function (o) {
          o.id = o.classid; // replace name with the property used for the text
          o.text = o.nama_class;
          return o;
        }),
      });

      uiSelectClass.val(null).trigger("change");
      common.loadingClose();
    });
  }
})();
