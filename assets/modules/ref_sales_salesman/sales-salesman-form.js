(function () {
  const common = new Common();
  common.setTitle("Karyawan");
  // declare dom
  let uiForm = $("#fm-sales-salesman");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectSiteid = $("#siteid-id");
  let uiSalesmanid = $("#salesmanid-id");
  let uiPassword = $("#password-id");
  let uiSelectAktif = $("#aktif-id");
  let uiSelectTipesales = $("#tipe_sales-id");
  let uiSelectRegional = $("#regionalid-id");
  let uiSelectArea = $("#areaid-id");
  let uiSelectSubarea = $("#subareaid-id");
  let uiSupervisor = $("#supervisorid");
  let uiJoinDate = $("#join_date");
  let uiResignDate = $("#resign_date");

  // define from *-content.js
  let param = common.getCookie("module.sales.salesman.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined; // flag create update
  let isInitializing = false;

  initialize();
  initializeParam();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("ref_sales_salesman/create")
        : common.baseURL("ref_sales_salesman/update");
    let urlcek =
      param === undefined
        ? common.baseURL("ref_sales_salesman/cek_user_gff")
        : common.baseURL("ref_sales_salesman/cek_user_gff_update");
    uiForm.initForm({
      url: url,
      param: param,
      directUrl: "ref_sales_salesman",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "siteid", value: param.siteid });
        }
        form.push({ name: "usersession", value: paramsession.username });
        for (let i = 0; i < form.length; i++) {
          if (form[i].name === "total_target") {
            form[i].value = form[i].value.replace(/\./g, "");
          }
        }
        return true; // MANDATORY!
      },
      rules: {
        siteid: {
          required: true,
        },
        salesmanid: {
          required: true,
          remote: {
            url: urlcek,
            type: "POST",
          },
        },
        password: {
          required: isUpdate ? false : true,
        },
        nama_salesman: {
          required: true,
        },
        "regionalid[]": {
          required: true,
        },
        join_date: {
          required: true,
        },
      },
      messages: {
        salesmanid: {
          required: "Isi User MEDREP",
          remote: "User MEDREP Sudah terdaftar",
        },
      },
    });
    uiBtnCancel.click(function () {
      common.direct("ref_sales_salesman");
    });

    $("#total_target").on("keyup input", function () {
      $(this).val(formatRupiah($(this).val()));
    });

    uiSelectAktif.select2({
      placeholder: "Select Status",
      allowClear: true,
    });

    uiSelectTipesales.select2({
      placeholder: "Select Sales Type",
      allowClear: true,
    });

    uiSelectRegional.select2({
      placeholder: "Select Regional",
      allowClear: true,
    });

    uiSelectArea.select2({
      placeholder: "Select Area",
      allowClear: true,
    });

    uiSelectSubarea.select2({
      placeholder: "Select SubArea",
      allowClear: true,
    });

    uiSupervisor.select2({
      placeholder: "Select Leader",
      allowClear: true,
    });

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
    });

    uiJoinDate.datepicker();
    uiResignDate.datepicker();

    let now = new Date();
    let currentMonth =
      now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0");
    $("#periode_sales").val(currentMonth);

    $("#periode_sales")
      .datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
      })
      .on("changeDate", function () {
        loadSalesTargets();
      });

    uiSalesmanid.on("change blur", function () {
      loadSalesTargets();
    });

    loadRegional({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
      restrict_bu: paramsession.restrict_bu,
    });

    uiSelectRegional.on("change", function () {
      if (isInitializing) return;
      let regionalids = $(this).val();
      if (regionalids && regionalids.length > 0) {
        loadArea({
          regionalid: regionalids,
          usersession: paramsession.username,
          restrict_level: paramsession.restrict_level,
        });
      } else {
        uiSelectArea.empty().trigger("change");
        uiSelectSubarea.empty().trigger("change");
      }
    });

    uiSelectArea.on("change", function () {
      if (isInitializing) return;
      let regionalids = uiSelectRegional.val();
      let areaids = $(this).val();
      if (
        regionalids &&
        regionalids.length > 0 &&
        areaids &&
        areaids.length > 0
      ) {
        loadSubArea({
          regionalid: regionalids,
          areaid: areaids,
          usersession: paramsession.username,
          restrict_level: paramsession.restrict_level,
        });
      } else {
        uiSelectSubarea.empty().trigger("change");
      }
    });

    uiSelectTipesales.on("select2:select", function (e) {
      tipeSalesSelected = e.params.data;
      let salesmanId = uiSalesmanid.val();

      loadSupervisor({
        tipe_sales: tipeSalesSelected.id ?? null,
        salesmanid: salesmanId,
      });
    });

    if (isUpdate) {
      isInitializing = true;
      document.getElementById("salesmanid-id").readOnly = true;
      uiSelectSiteid.val(param.siteid).trigger("change");
      uiPassword.val("");

      loadSupervisor({
        tipe_sales: param.tipe_sales,
        salesmanid: param.salesmanid,
      });
      loadRegional(
        {
          usersession: paramsession.username,
          idjabatan: paramsession.idjabatan,
          restrict_level: paramsession.restrict_level,
        },
        function () {
          loadArea(
            {
              regionalid: param.regionalid,
              usersession: paramsession.username,
              idjabatan: paramsession.idjabatan,
              restrict_level: paramsession.restrict_level,
            },
            function () {
              loadSubArea(
                {
                  regionalid: param.regionalid,
                  areaid: param.areaid,
                  usersession: paramsession.username,
                  restrict_level: paramsession.restrict_level,
                },
                function () {
                  isInitializing = false;
                },
              );
            },
          );
        },
      );

      loadSalesTargets();
    }
  }

  function loadSalesTargets() {
    let salesmanid = isUpdate ? param.salesmanid : uiSalesmanid.val();
    let period = $("#periode_sales").val();
    if (!salesmanid || !period) {
      $("#total_target").val("");
      return;
    }

    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/get_sales_targets"),
      { salesmanid: salesmanid, periode: period },
      function (res) {
        if (res && res.length > 0) {
          $("#total_target").val(formatRupiah(res[0].total_target));
        } else {
          $("#total_target").val("");
        }
        common.loadingClose();
      },
    ).fail(function () {
      common.loadingClose();
    });
  }

  function loadRegional(data, callback) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_regional_restrict"),
      { usersession: data.usersession, restrict_level: data.restrict_level },
      function (res) {
        uiSelectRegional.empty();
        uiSelectRegional.select2({
          placeholder: "Select Regional",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.regionalid; // replace name with the property used for the text
            o.text = o.nama_regional;
            return o;
          }),
        });

        if (isUpdate) {
          let vals = param.regionalid
            ? String(param.regionalid).split(",")
            : [];
          uiSelectRegional.val(vals).trigger("change");
        } else {
          uiSelectRegional.val(null).trigger("change");
        }
        common.loadingClose();
        if (callback) callback();
      },
    );
  }

  function loadArea(data, callback) {
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
          data: $.map(res.result, function (o) {
            o.id = o.areaid; // replace name with the property used for the text
            o.text = o.nama_area;
            return o;
          }),
        });

        if (isUpdate) {
          let vals = param.areaid ? String(param.areaid).split(",") : [];
          uiSelectArea.val(vals).trigger("change");
        } else {
          uiSelectArea.val(null).trigger("change");
        }
        common.loadingClose();
        if (callback) callback();
      },
    );
  }

  function loadSubArea(data, callback) {
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
          data: $.map(res.result, function (o) {
            o.id = o.subareaid; // replace name with the property used for the text
            o.text = o.nama_area;
            return o;
          }),
        });

        if (isUpdate) {
          let vals = param.subareaid ? String(param.subareaid).split(",") : [];
          uiSelectSubarea.val(vals).trigger("change");
        } else {
          uiSelectSubarea.val(null).trigger("change");
        }
        common.loadingClose();
        if (callback) callback();
      },
    );
  }

  function loadSupervisor(data) {
    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/supervisor"),
      {
        salesmanid: data.salesmanid,
        tipe_sales: data.tipe_sales,
      },
      function (res) {
        uiSupervisor.empty();
        uiSupervisor.select2({
          placeholder: "Select Leader",
          allowClear: true,
          data: $.map(res, function (o) {
            o.id = o.salesmanid; // replace name with the property used for the text
            o.text = o.nama_salesman;
            return o;
          }),
        });

        if (isUpdate) {
          uiSupervisor.val(param.supervisorid).trigger("change");
        } else {
          uiSupervisor.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }

  function initializeParam() {
    common.loading();
    let resolver = new HttpResolver();
    let filter = new Filter();

    $.when(
      $.post(common.baseURL("conf_setup_site/load"), filter.build()),
      $.post(common.baseURL("api_v1/call_statusaktif"), filter.build()),
      $.post(common.baseURL("api_v1/call_tipesalesman"), {
        param: "key_role_sales",
      }),
    )
      .done(function (data, textStatus, jqXHR) {})
      .then(function (r1, r2, r3) {
        common.loadingClose();
        setupForm(r1[0], r2[0], r3[0]);
      })
      .fail(resolver.fail);
  }

  function setupForm(r1, r2, r3) {
    let rows = r1.rows;
    let rows2 = r2.result;
    let rows3 = r3.result;

    uiSelectAktif
      .empty()
      .append('<option value=""></option>')
      .select2({
        placeholder: "Select Status",
        allowClear: true,
        data: $.map(rows2, function (o) {
          o.id = o.idaktif; // replace name with the property used for the text
          o.text = o.status; // replace name with the property used for the text
          return o;
        }),
      });

    uiSelectTipesales
      .empty()
      .append('<option value=""></option>')
      .select2({
        placeholder: "Select Sales Type",
        allowClear: true,
        data: $.map(rows3, function (o) {
          o.id = o.idtipesales; // replace name with the property used for the text
          o.text = o.tipesales; // replace name with the property used for the text
          return o;
        }),
      });

    if (isUpdate) {
      uiSelectSiteid.val(param.siteid).trigger("change");
      uiSelectAktif.val(param.aktif).trigger("change");
      uiSelectTipesales.val(param.tipe_sales).trigger("change");
    } else {
      uiSelectSiteid.val(null).trigger("change");
      uiSelectAktif.val(null).trigger("change");
      uiSelectTipesales.val(null).trigger("change");
    }
  }

  function formatRupiah(value) {
    if (!value) return "";
    let numberString = value.toString().replace(/[^0-9]/g, "");
    let split = numberString.split("");
    let sisa = split.length % 3;
    let rupiah = split.slice(0, sisa).join("");
    let ribuan = split.slice(sisa).join("").match(/\d{3}/gi);

    if (ribuan) {
      let separator = sisa ? "." : "";
      rupiah += separator + ribuan.join(".");
    }
    return rupiah;
  }
})();
