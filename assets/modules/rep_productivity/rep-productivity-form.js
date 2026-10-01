(function () {
  const common = new Common();
  common.setTitle("Report Productivity");
  // declare dom
  let uiForm = $("#fm-report-productivity");
  let uiBtnPreview = $("#btn-preview-form");
  let uiBtnDownload = $("#btn-download-form");
  let uiBtnDownloadAllData = $("#btn-download-all-data-form");

  let uiStartPeriode = $("#start_periode");
  let uiEndPeriode = $("#end_periode");
  let uiSelectRegional = $("#regional-id");
  let uiSelectArea = $("#area-id");
  let uiSelectSubArea = $("#subarea-id");
  let uiSelectMedrep = $("#salesmanid-id");

  let paramsession = common.getCookie("session");

  initializeParam();

  function initializeParam() {
    common.loading();
    let resolver = new HttpResolver();
    let sessionData = {
      usersession: paramsession ? paramsession.username : "",
      restrict_level: paramsession ? paramsession.restrict_level : "",
      idjabatan: paramsession ? paramsession.idjabatan : "",
    };

    $.when(
      $.post(common.baseURL("rep_productivity/load_regional"), sessionData),
      $.post(common.baseURL("rep_productivity/load_parma"), sessionData)
    )
      .done(function (data, textStatus, jqXHR) {})
      .then(function (r1, r2) {
        common.loadingClose();
        setupForm(r1[0], r2[0]);
      })
      .fail(resolver.fail);

    uiBtnPreview.click(function () {
      if (uiStartPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else if (uiEndPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else {
        open_preview();
      }
    });

    uiBtnDownload.click(function () {
      if (uiStartPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else if (uiEndPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else {
        save_xls();
      }
    });

    uiBtnDownloadAllData.click(function () {
      if (uiStartPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else if (uiEndPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else {
        save_xls_all_data();
      }
    });

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
    }).datepicker("setDate", new Date());

    uiSelectRegional.on("select2:select", function (e) {
      let regional = e.params.data;
      loadArea(regional);
    });

    uiSelectRegional.on("select2:unselect", function () {
      uiSelectArea.empty().select2({ placeholder: "Select Area", allowClear: true });
      uiSelectSubArea.empty().select2({ placeholder: "Select Sub Area", allowClear: true });
      loadMedrepList();
    });

    uiSelectArea.on("select2:select", function (e) {
      let area = e.params.data;
      loadSubArea(area);
    });

    uiSelectArea.on("select2:unselect", function () {
      uiSelectSubArea.empty().select2({ placeholder: "Select Sub Area", allowClear: true });
      loadMedrepList();
    });

    uiSelectSubArea.on("select2:select", function () {
      loadMedrepList();
    });

    uiSelectSubArea.on("select2:unselect", function () {
      loadMedrepList();
    });

    uiStartPeriode.on("changeDate", function (selected) {
      let startDate = new Date(selected.date.valueOf());
      let endDate = new Date(selected.date.valueOf());
      endDate.setDate(endDate.getDate() + 90);
      uiEndPeriode.datepicker("setStartDate", startDate);
      uiEndPeriode.datepicker("setEndDate", endDate);
      if (uiStartPeriode.val() > uiEndPeriode.val()) {
        uiEndPeriode.val(uiStartPeriode.val());
      }
    });
  }

  function setupForm(r1, r2) {
    let rows1 = r1 && r1.rows ? r1.rows : [];

    uiSelectRegional.select2({
      placeholder: "Select Regional",
      allowClear: true,
      data: $.map(rows1, function (o) {
        o.id = o.regionalid;
        o.text = o.nama_regional;
        return o;
      }),
    });

    uiSelectArea.select2({
      placeholder: "Select Area",
      allowClear: true,
    });

    uiSelectSubArea.select2({
      placeholder: "Select Sub Area",
      allowClear: true,
    });

    populateMedrepOptions(r2);

    uiSelectRegional.val(null).trigger("change");
  }

  function open_preview() {
    let start = uiStartPeriode.val();
    let end = uiEndPeriode.val();
    let regionalid = uiSelectRegional.val();
    let areaid = uiSelectArea.val();
    let subareaid = uiSelectSubArea.val();
    let salesmanid = uiSelectMedrep.val() || "all";

    let idjabatan = paramsession.idjabatan;
    let usersession = paramsession.username;
    let restrict_level = paramsession.restrict_level;

    $.ajax({
      type: "POST",
      dataType: "html",
      beforeSend: function () {},
      url: common.baseURL("rep_productivity/open_detail"),
      data:
        "start_period=" +
        start +
        "&end_period=" +
        end +
        "&regionalid=" +
        regionalid +
        "&areaid=" +
        areaid +
        "&idjabatan=" +
        idjabatan +
        "&usersession=" +
        usersession +
        "&restrict_level=" +
        restrict_level +
        "&subareaid=" +
        subareaid +
        "&salesmanid=" +
        salesmanid,
      success: function (res) {
        response = res;
        $("#tbl-content").html(response);
      },
      error: function () {
        alert("Load failed");
      },
    });
  }

  function populateMedrepOptions(r) {
    uiSelectMedrep.empty();
    let options = [{ id: "all", text: "Semua TPE" }];
    if (r && r.rows && r.rows.length > 0) {
      $.each(r.rows, function (i, o) {
        options.push({
          id: o.salesmanid,
          text: o.salesmanid + " - " + o.nama_salesman,
        });
      });
    }
    uiSelectMedrep.select2({
      placeholder: "Select TPE",
      allowClear: true,
      data: options,
    });
    uiSelectMedrep.val("all").trigger("change");
  }

  function loadMedrepList() {
    let postData = {
      regionalid: uiSelectRegional.val() || "",
      areaid: uiSelectArea.val() || "",
      subareaid: uiSelectSubArea.val() || "",
      usersession: paramsession ? paramsession.username : "",
      restrict_level: paramsession ? paramsession.restrict_level : "",
      idjabatan: paramsession ? paramsession.idjabatan : "",
    };
    $.post(common.baseURL("rep_productivity/load_parma"), postData, function (res) {
      populateMedrepOptions(res);
    });
  }

  function loadArea(data) {
    common.loading();
    let postData = {
      regionalid: data ? data.regionalid : "",
      usersession: paramsession ? paramsession.username : "",
      restrict_level: paramsession ? paramsession.restrict_level : "",
      idjabatan: paramsession ? paramsession.idjabatan : "",
    };
    $.post(
      common.baseURL("rep_productivity/load_area"),
      postData,
      function (res) {
        uiSelectArea.empty();
        uiSelectArea.select2({
          placeholder: "Select Area",
          allowClear: true,
          data: $.map(res && res.rows ? res.rows : [], function (o) {
            o.id = o.areaid;
            o.text = o.nama_area;
            return o;
          }),
        });
        uiSelectArea.val(null).trigger("change");

        uiSelectSubArea.empty().select2({ placeholder: "Select Sub Area", allowClear: true });
        loadMedrepList();
        common.loadingClose();
      },
    );
  }

  function loadSubArea(data) {
    common.loading();
    let postData = {
      areaid: data ? data.areaid : "",
      regionalid: uiSelectRegional.val() || "",
      usersession: paramsession ? paramsession.username : "",
      restrict_level: paramsession ? paramsession.restrict_level : "",
      idjabatan: paramsession ? paramsession.idjabatan : "",
    };
    $.post(
      common.baseURL("rep_productivity/load_subarea"),
      postData,
      function (res) {
        uiSelectSubArea.empty();
        uiSelectSubArea.select2({
          placeholder: "Select Sub Area",
          allowClear: true,
          data: $.map(res && res.rows ? res.rows : [], function (o) {
            o.id = o.subareaid;
            o.text = o.nama_subarea;
            return o;
          }),
        });
        uiSelectSubArea.val(null).trigger("change");

        loadMedrepList();
        common.loadingClose();
      },
    );
  }

  function save_xls() {
    let start = uiStartPeriode.val() || "";
    let end = uiEndPeriode.val() || "";
    let regionalid = uiSelectRegional.val() || "";
    let areaid = uiSelectArea.val() || "";
    let subareaid = uiSelectSubArea.val() || "";
    let salesmanid = uiSelectMedrep.val() || "all";

    let idjabatan = paramsession && paramsession.idjabatan ? paramsession.idjabatan : "";
    let usersession = paramsession && paramsession.username ? paramsession.username : "";
    let restrict_level = paramsession && paramsession.restrict_level ? paramsession.restrict_level : "";

    let baseUrl = common.baseURL("rep_productivity/savetoxlsx");
    baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

    let downloadUrl =
      baseUrl +
      "?start_period=" + encodeURIComponent(start) +
      "&end_period=" + encodeURIComponent(end) +
      "&regionalid=" + encodeURIComponent(regionalid) +
      "&areaid=" + encodeURIComponent(areaid) +
      "&subareaid=" + encodeURIComponent(subareaid) +
      "&usersession=" + encodeURIComponent(usersession) +
      "&restrict_level=" + encodeURIComponent(restrict_level) +
      "&idjabatan=" + encodeURIComponent(idjabatan) +
      "&salesmanid=" + encodeURIComponent(salesmanid);

    window.location.href = downloadUrl;
  }

  function save_xls_all_data() {
    let start = uiStartPeriode.val() || "";
    let end = uiEndPeriode.val() || "";
    let regionalid = uiSelectRegional.val() || "";
    let areaid = uiSelectArea.val() || "";
    let subareaid = uiSelectSubArea.val() || "";
    let salesmanid = uiSelectMedrep.val() || "all";

    let idjabatan = paramsession && paramsession.idjabatan ? paramsession.idjabatan : "";
    let usersession = paramsession && paramsession.username ? paramsession.username : "";
    let restrict_level = paramsession && paramsession.restrict_level ? paramsession.restrict_level : "";

    let baseUrl = common.baseURL("rep_productivity/savexls_visit_and_order");
    baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

    let downloadUrl =
      baseUrl +
      "?start_period=" + encodeURIComponent(start) +
      "&end_period=" + encodeURIComponent(end) +
      "&regionalid=" + encodeURIComponent(regionalid) +
      "&areaid=" + encodeURIComponent(areaid) +
      "&subareaid=" + encodeURIComponent(subareaid) +
      "&usersession=" + encodeURIComponent(usersession) +
      "&restrict_level=" + encodeURIComponent(restrict_level) +
      "&idjabatan=" + encodeURIComponent(idjabatan) +
      "&salesmanid=" + encodeURIComponent(salesmanid);

    window.location.href = downloadUrl;
  }
})();
