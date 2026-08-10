(function () {
  const common = new Common();
  common.setTitle("Report Promo");
  // declare dom
  let uiForm = $("#fm-report-promo");
  let uiBtnPreview = $("#btn-preview-form");
  let uiBtnDownload = $("#btn-download-form");
  let uiBtnDownloadText = $("#btn-download-form-text");
  let uiSelectTypePromo = $("#tipepromo-id");
  let uiSelectPromo = $("#idpromo-id");
  let uiSelectAccount = $("#account-id");
  let uiStartPeriode = $("#start_periode");
  let uiSelectTipepromo = $("#tipepromo-id");
  let uiEndPeriode = $("#end_periode");
  let uiSelectRegional = $("#regional-id");
  let uiSelectArea = $("#area-id");
  let uiSelectCity = $("#city-id");
  //let uiTblReport = $("#tbl-content");

  // define from *-content.js
  //let param = common.getCookie("module.report.promo.update");
  let paramsession = common.getCookie("session");

  initializeParam();

  function initializeParam() {
    common.loading();
    let resolver = new HttpResolver();
    let filter = new Filter();

    $.when(
      $.post(common.baseURL("rep_promo/load_account"), filter.build()),
      $.post(common.baseURL("rep_promo/load_regional"), filter.build()),
    )
      .done(function (data, textStatus, jqXHR) {})
      .then(function (r1, r2) {
        common.loadingClose();
        setupForm(r1, r2);
      })
      .fail(resolver.fail);

    uiBtnPreview.click(function () {
      //alert(uiSelectSalesman.val());
      if (uiSelectTipepromo.val() === null) {
        //uiAlertNotif.show();
        alert("Type Promo harus di isi...!");
      } else if (uiSelectAccount.val() === null) {
        //uiAlertNotif.show();
        alert("Channel harus di isi...!");
      } else if (uiSelectPromo.val() === null) {
        //uiAlertNotif.show();
        alert("Promo harus di isi...!");
      } else if (uiStartPeriode.val() === "") {
        //uiAlertNotif.show();
        alert("Periode harus di isi...!");
      } else {
        open_preview();
      }
    });

    uiBtnDownload.click(function () {
      //alert(uiSelectSalesman.val());
      if (uiSelectTipepromo.val() === null) {
        //uiAlertNotif.show();
        alert("Type Promo harus di isi...!");
      } else if (uiSelectAccount.val() === null) {
        //uiAlertNotif.show();
        alert("Channel harus di isi...!");
      } else if (uiSelectPromo.val() === null) {
        //uiAlertNotif.show();
        alert("Promo harus di isi...!");
      } else if (uiStartPeriode.val() === "") {
        //uiAlertNotif.show();
        alert("Periode harus di isi...!");
      } else {
        save_xls();
      }
    });

    uiBtnDownloadText.click(function () {
      //alert(uiSelectSalesman.val());
      if (uiSelectTipepromo.val() === null) {
        //uiAlertNotif.show();
        alert("Type Promo harus di isi...!");
      } else if (uiSelectAccount.val() === null) {
        //uiAlertNotif.show();
        alert("Channel harus di isi...!");
      } else if (uiSelectPromo.val() === null) {
        //uiAlertNotif.show();
        alert("Promo harus di isi...!");
      } else if (uiStartPeriode.val() === "") {
        //uiAlertNotif.show();
        alert("Periode harus di isi...!");
      } else {
        save_xls_text_only();
      }
    });

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
    });

    uiSelectAccount.on("select2:select", function (e) {
      var start = uiStartPeriode.val();
      var end = uiEndPeriode.val();
      accountSelected = e.params.data;
      //alert(uiSelectTipepromo.val());
      paramdata = {
        classid: uiSelectAccount.val(),
        tipepromo: uiSelectTipepromo.val(),
        start: start,
        end: end,
      };
      loadPromo(paramdata);
    });

    uiSelectRegional.on("select2:select", function (e) {
      regionalSelected = e.params.data;
      //alert(uiSelectTipepromo.val());
      paramdata = { regionalid: uiSelectRegional.val() };
      loadArea(paramdata);
      loadCity(paramdata);
    });

    uiSelectArea.on("select2:select", function (e) {
      areaSelected = e.params.data;
      paramdata = { areaid: uiSelectArea.val() };
      loadCity(paramdata);
    });

    uiStartPeriode.on("changeDate", function (selected) {
      var startDate = new Date(selected.date.valueOf());
      var endDate = new Date(selected.date.valueOf());
      endDate.setDate(endDate.getDate() + 90);
      uiEndPeriode.datepicker("setStartDate", startDate);
      uiEndPeriode.datepicker("setEndDate", endDate);
      if (uiStartPeriode.val() > uiEndPeriode.val()) {
        uiEndPeriode.val(uiStartPeriode.val());
      }
    });
    loadParamKey();
  }

  function setupForm(r1, r2) {
    let rows1 = r1[0].rows;
    let rows2 = r2[0].rows;

    uiSelectAccount.select2({
      placeholder: "Select Channel",
      allowClear: true,
      data: $.map(rows1, function (o) {
        o.id = o.classid; // replace name with the property used for the text
        o.text = o.nama_class; // replace name with the property used for the text
        return o;
      }),
    });

    uiSelectAccount.val(null).trigger("change");

    uiSelectRegional.select2({
      placeholder: "Select Regional",
      allowClear: true,
      data: $.map(rows2, function (o) {
        o.id = o.regionalid; // replace name with the property used for the text
        o.text = o.nama_regional; // replace name with the property used for the text
        return o;
      }),
    });

    uiSelectRegional.val(null).trigger("change");

    uiSelectArea.select2({
      placeholder: "Select Area",
      allowClear: true,
    });

    uiSelectCity.select2({
      placeholder: "Select Sub Area",
      allowClear: true,
    });
  }

  function open_preview() {
    var idpromo = uiSelectPromo.val();
    var start = uiStartPeriode.val();
    var end = uiEndPeriode.val();
    var regional = uiSelectRegional.val();
    var area = uiSelectArea.val();
    var subarea = uiSelectCity.val();
    var tipepromo = uiSelectTypePromo.val();
    var idjabatan = paramsession.idjabatan;
    var usersession = paramsession.username;
    var restrict_level = paramsession.restrict_level;

    if (
      tipepromo == "Discount" ||
      tipepromo == "Joint Promo" ||
      tipepromo == "Display"
    ) {
      $.ajax({
        type: "POST",
        dataType: "html",
        beforeSend: function () {
          //$("#map-content").html('Populating data, please wait..');
        },
        url: common.baseURL("rep_promo/open_detail"),
        data:
          "tipepromo=" +
          tipepromo +
          "&idpromo=" +
          idpromo +
          "&start=" +
          start +
          "&end=" +
          end +
          "&regional=" +
          regional +
          "&area=" +
          area +
          "&subarea=" +
          subarea +
          "&idjabatan=" +
          idjabatan +
          "&usersession=" +
          usersession +
          "&restrict_level=" +
          restrict_level,
        success: function (res) {
          response = res;
          //$('div .modal-header .modal-title').text('Detail Productifity Sales');
          $("#tbl-content").html(response);
          //$("#modal_detail").modal('show');
        },
        error: function () {
          alert("Load failed");
        },
      });
    } else {
      $.ajax({
        type: "POST",
        dataType: "html",
        beforeSend: function () {
          //$("#map-content").html('Populating data, please wait..');
        },
        url: common.baseURL("rep_promo/open_detail_gimmick"),
        data:
          "tipepromo=" +
          tipepromo +
          "&idpromo=" +
          idpromo +
          "&start=" +
          start +
          "&end=" +
          end +
          "&regional=" +
          regional +
          "&area=" +
          area +
          "&subarea=" +
          subarea +
          "&idjabatan=" +
          idjabatan +
          "&usersession=" +
          usersession +
          "&restrict_level=" +
          restrict_level,
        success: function (res) {
          response = res;
          //$('div .modal-header .modal-title').text('Detail Productifity Sales');
          $("#tbl-content").html(response);
          //$("#modal_detail").modal('show');
        },
        error: function () {
          alert("Load failed");
        },
      });
    }
  }

  function loadPromo(data) {
    common.loading();
    //alert(data.idaccount);
    $.post(
      common.baseURL("rep_promo/load_promo"),
      {
        classid: data.classid,
        tipepromo: data.tipepromo,
        start: data.start,
        end: data.end,
      },
      function (res) {
        uiSelectPromo.empty();
        uiSelectPromo.select2({
          placeholder: "Select Promo",
          allowClear: true,
          data: $.map(res.rows, function (o) {
            o.id = o.idpromo; // replace name with the property used for the text
            o.text = o.promo + " " + o.periode;
            return o;
          }),
        });
        common.loadingClose();
      },
    );
  }

  function loadArea(data) {
    common.loading();
    //alert(data.idaccount);
    $.post(
      common.baseURL("rep_promo/load_area"),
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
    $.post(
      common.baseURL("rep_promo/load_city"),
      postData,
      function (res) {
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
      },
    );
  }

  function save_xls() {
    var idpromo = uiSelectPromo.val();
    var start = uiStartPeriode.val();
    var end = uiEndPeriode.val();
    var regional = uiSelectRegional.val();
    var area = uiSelectArea.val();
    var subarea = uiSelectCity.val();
    if (!subarea) {
      subarea = "null";
    }
    var tipepromo = uiSelectTypePromo.val();
    var idjabatan = paramsession.idjabatan;
    var usersession = paramsession.username;
    var restrict_level = paramsession.restrict_level;
    //idpromo = idpromo.replace(",", "|");
    //var url = encodeURI();
    if (
      uiSelectTypePromo.val() == "Discount" ||
      uiSelectTypePromo.val() == "Joint Promo" ||
      uiSelectTypePromo.val() == "Display"
    ) {
      common.direct(
        "rep_promo/savetoxlsx/" +
          idpromo +
          "/" +
          start +
          "/" +
          end +
          "/" +
          idjabatan +
          "/" +
          usersession +
          "/" +
          restrict_level +
          "/" +
          tipepromo +
          "/" +
          regional +
          "/" +
          area +
          "/" +
          subarea,
      );
    } else {
      common.direct(
        "rep_promo/savetoxlsx_gimmick/" +
          idpromo +
          "/" +
          start +
          "/" +
          end +
          "/" +
          idjabatan +
          "/" +
          usersession +
          "/" +
          restrict_level +
          "/" +
          regional +
          "/" +
          area +
          "/" +
          subarea,
      );
    }
    /*
        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_promo/savetoxls"),
            data : "idpromo="+idpromo+"&start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);
            },
            error:function(){
                alert("Load failed");
            }
        });
        */
  }

  function save_xls_text_only() {
    var idpromo = uiSelectPromo.val();
    var start = uiStartPeriode.val();
    var end = uiEndPeriode.val();
    var area = uiSelectArea.val();
    var regional = uiSelectRegional.val();
    var subarea = uiSelectCity.val();
    if (!subarea) {
      subarea = "null";
    }
    var tipepromo = uiSelectTypePromo.val();
    var idjabatan = paramsession.idjabatan;
    var usersession = paramsession.username;
    var restrict_level = paramsession.restrict_level;
    if (
      uiSelectTypePromo.val() == "Discount" ||
      uiSelectTypePromo.val() == "Joint Promo" ||
      uiSelectTypePromo.val() == "Display"
    ) {
      common.direct(
        "rep_promo/savetoxlsx_text_only/" +
          idpromo +
          "/" +
          start +
          "/" +
          end +
          "/" +
          idjabatan +
          "/" +
          usersession +
          "/" +
          restrict_level +
          "/" +
          tipepromo +
          "/" +
          regional +
          "/" +
          area +
          "/" +
          subarea,
      );
    } else {
      common.direct(
        "rep_promo/savetoxlsx_gimmick_text_only/" +
          idpromo +
          "/" +
          start +
          "/" +
          end +
          "/" +
          idjabatan +
          "/" +
          usersession +
          "/" +
          restrict_level +
          "/" +
          regional +
          "/" +
          area +
          "/" +
          subarea,
      );
    }
  }

  function loadParamKey() {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_param_key"),
      { parkey: "key_tipe_promo" },
      function (res) {
        uiSelectTipepromo.empty();
        uiSelectTipepromo.select2({
          placeholder: "Select Type Promo",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.value; // replace name with the property used for the text
            o.text = o.desc;
            return o;
          }),
        });

        uiSelectTipepromo.val(null).trigger("change");
        common.loadingClose();
      },
    );
  }
})();
