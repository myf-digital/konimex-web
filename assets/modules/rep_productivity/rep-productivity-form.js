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
    let filter = new Filter();

    $.when(
      $.post(common.baseURL("rep_productivity/load_regional"), filter.build()),
      $.post(common.baseURL("rep_productivity/load_parma"), filter.build())
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
      regional = e.params.data;

      loadArea(regional);
    });

    uiSelectArea.on("select2:select", function (e) {
      area = e.params.data;

      loadSubArea(area);
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
    let rows1 = r1.rows;

    uiSelectRegional.select2({
      placeholder: "Select Regional",
      allowClear: true,
      data: $.map(rows1, function (o) {
        o.id = o.regionalid; // replace name with the property used for the text
        o.text = o.nama_regional; // replace name with the property used for the text
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

    if (r2 && r2.rows) {
      let rows2 = r2.rows;
      let options = [
        { id: "all", text: "Semua Medrep" },
        ...(rows2.map((o) => ({
          id: o.salesmanid,
          text: o.salesmanid + " - " + o.nama_salesman,
        }))),
      ];
      uiSelectMedrep.select2({
        placeholder: "Select User Medrep",
        allowClear: true,
        data: options,
      });
      uiSelectMedrep.val("all").trigger("change");
    }

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

  function loadArea(data) {
    common.loading();
    $.post(
      common.baseURL("rep_productivity/load_area"),
      { regionalid: data.regionalid },
      function (res) {
        uiSelectArea.empty();
        uiSelectArea.select2({
          placeholder: "Select Area",
          allowClear: true,
          data: $.map(res.rows, function (o) {
            o.id = o.areaid; // replace name with the property used for the text
            o.text = o.nama_area;
            return o;
          }),
        });
        uiSelectArea.val(null).trigger("change");
        common.loadingClose();
      },
    );
  }

  function loadSubArea(data) {
    common.loading();
    $.post(
      common.baseURL("rep_productivity/load_subarea"),
      { areaid: data.areaid },
      function (res) {
        uiSelectSubArea.empty();
        uiSelectSubArea.select2({
          placeholder: "Select Sub Area",
          allowClear: true,
          data: $.map(res.rows, function (o) {
            o.id = o.subareaid; // replace name with the property used for the text
            o.text = o.nama_subarea;
            return o;
          }),
        });
        uiSelectSubArea.val(null).trigger("change");
        common.loadingClose();
      },
    );
  }

  function save_xls() {
    let start = uiStartPeriode.val();
    let end = uiEndPeriode.val();
    let regionalid = uiSelectRegional.val();
    let areaid = uiSelectArea.val();
    let subareaid = uiSelectSubArea.val();
    let salesmanid = uiSelectMedrep.val() || "all";

    let idjabatan = paramsession.idjabatan;
    let usersession = paramsession.username;
    let restrict_level = paramsession.restrict_level;

    common.direct(
      "rep_productivity/savetoxlsx/" +
        start +
        "/" +
        end +
        "/" +
        regionalid +
        "/" +
        areaid +
        "/" +
        subareaid +
        "/" +
        usersession +
        "/" +
        restrict_level +
        "/" +
        idjabatan +
        "/" +
        salesmanid,
    );
  }

  function save_xls_all_data() {
    let start = uiStartPeriode.val();
    let end = uiEndPeriode.val();
    let regionalid = uiSelectRegional.val();
    let areaid = uiSelectArea.val();
    let subareaid = uiSelectSubArea.val();
    let salesmanid = uiSelectMedrep.val() || "all";

    let idjabatan = paramsession.idjabatan;
    let usersession = paramsession.username;
    let restrict_level = paramsession.restrict_level;

    common.direct(
      "rep_productivity/savexls_visit_and_order/" +
        start +
        "/" +
        end +
        "/" +
        regionalid +
        "/" +
        areaid +
        "/" +
        subareaid +
        "/" +
        usersession +
        "/" +
        restrict_level +
        "/" +
        idjabatan +
        "/" +
        salesmanid,
    );
  }
})();
