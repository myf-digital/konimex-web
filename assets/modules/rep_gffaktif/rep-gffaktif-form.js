(function () {
  const common = new Common();
  common.setTitle("Report MEDREP Aktif");
  // declare dom
  let uiForm = $("#fm-report-promo");
  let uiBtnPreview = $("#btn-preview-form");
  let uiBtnDownload = $("#btn-download-form");
  let uiStartPeriode = $("#start_periode");
  let uiEndPeriode = $("#end_periode");
  let uiSelectPosition = $("#tipe_sales-id");
  let uiSelectRegional = $("#regional-id");
  let uiSelectArea = $("#area-id");
  // let uiSelectCity = $("#city-id");

  let paramsession = common.getCookie("session");

  initializeParam();

  function initializeParam() {
    //common.loading();
    let resolver = new HttpResolver();
    let filter = new Filter();

    $.when($.post(common.baseURL("rep_gffaktif/load_regional"), filter.build()))
      .done(function (data, textStatus, jqXHR) {})
      .then(function (r1) {
        common.loadingClose();
        setupForm(r1);
      })
      .fail(resolver.fail);

    uiBtnPreview.click(function () {
      if (uiStartPeriode.val() === "") {
        alert("Periode harus di isi...!");
      } else {
        open_preview();
      }
    });

    uiBtnDownload.click(function () {
      if (uiStartPeriode.val() === "") {
        alert("Periode harus di isi...!");
      } else {
        save_xlsx();
      }
    });

    $(".datepicker")
      .datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
      })
      .datepicker("setDate", new Date());

    uiSelectRegional.on("select2:select", function (e) {
      regional = e.params.data;

      loadArea(regional);
      // loadCity(regional);
    });

    uiSelectArea.on("select2:select", function (e) {
      area = e.params.data;

      // loadCity(area);
    });

    uiStartPeriode.on("changeDate", function (selected) {
      var startDate = new Date(selected.date.valueOf());
      uiEndPeriode.datepicker("setStartDate", startDate);
      if (uiStartPeriode.val() > uiEndPeriode.val()) {
        uiEndPeriode.val(uiStartPeriode.val());
      }
    });

    load_tipegff();
    open_preview();
  }

  function setupForm(r1) {
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

    // uiSelectCity.select2({
    //   placeholder: "Select Sub Area",
    //   allowClear: true,
    // });

    uiSelectRegional.val(null).trigger("change");
  }

  function loadArea(data) {
    common.loading();
    $.post(
      common.baseURL("rep_gffaktif/load_area"),
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

  function loadCity(data) {
    common.loading();
    let postData = {};
    if (data.areaid) {
      postData.areaid = data.areaid;
    } else {
      postData.regionalid = data.regionalid;
    }
    $.post(common.baseURL("rep_gffaktif/load_city"), postData, function (res) {
      uiSelectCity.empty();
      uiSelectCity.select2({
        placeholder: "Select Sub Area",
        allowClear: true,
        data: $.map(res.rows, function (o) {
          o.id = o.subareaid; // replace name with the property used for the text
          o.text = o.nama_area;
          return o;
        }),
      });
      uiSelectCity.val(null).trigger("change");
      common.loadingClose();
    });
  }

  function open_preview() {
    var start = uiStartPeriode.val();
    var end = uiEndPeriode.val();
    var position = uiSelectPosition.val();
    var idjabatan = paramsession.idjabatan;
    var usersession = paramsession.username;
    var restrictlevel = paramsession.restrict_level;

    var regionalid = uiSelectRegional.val();
    var areaid = uiSelectArea.val();
    // var subareaid = uiSelectCity.val();
    $.ajax({
      type: "GET",
      dataType: "html",
      beforeSend: function () {},
      url: common.baseURL("api_v1/call_absensi_daily"),
      data: "",
      success: function (res) {
        response = res;
      },
      error: function () {
        alert("Load failed");
      },
    });

    $.ajax({
      type: "POST",
      dataType: "html",
      url: common.baseURL("rep_gffaktif/load_data_att"),
      data:
        "start=" +
        start +
        "&end=" +
        end +
        "&position=" +
        position +
        "&idjabatan=" +
        idjabatan +
        "&usersession=" +
        usersession +
        "&restrict_level=" +
        restrictlevel +
        "&regionalid=" +
        regionalid +
        "&areaid=" +
        areaid,
      // "&subareaid=" +
      // subareaid,
      success: function (res) {
        $("#tbl-content").html(res);
      },
      error: function () {
        alert("Load failed");
      },
    });
  }

  function load_tipegff() {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_tipesalesman"),
      {
        param: "key_role_sales",
      },
      function (res) {
        uiSelectPosition
          .empty()
          .append('<option value=""></option>')
          .select2({
            placeholder: "Select Position",
            allowClear: true,
            data: $.map(res.result, function (o) {
              o.id = o.idtipesales; // replace name with the property used for the text
              o.text = o.tipesales;
              return o;
            }),
          })
          .val(null)
          .trigger("change");

        common.loadingClose();
      },
    );
  }

  function save_xlsx() {
    var start = uiStartPeriode.val();
    var end = uiEndPeriode.val();
    var position = uiSelectPosition.val();
    var idjabatan = paramsession.idjabatan;
    var usersession = paramsession.username;
    var restrictlevel = paramsession.restrict_level;

    var regionalid = uiSelectRegional.val();
    var areaid = uiSelectArea.val();
    // var subareaid = uiSelectCity.val();
    // if (!subareaid) {
    //   subareaid = "null";
    // }
    common.direct(
      "rep_gffaktif/savetoxlsx/" +
        start +
        "/" +
        end +
        "/" +
        idjabatan +
        "/" +
        usersession +
        "/" +
        restrictlevel +
        "/" +
        position +
        "/" +
        regionalid +
        "/" +
        areaid,
      // "/" +
      // subareaid,
    );
  }
})();
