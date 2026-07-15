(function () {
  const common = new Common();
  common.setTitle("Outlet");
  // declare dom
  let uiForm = $("#fm-request_new_outlet");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectType = $("#typeid-id");
  let uiSelectRegional = $("#regionalid-id");
  let uiSelectArea = $("#areaid-id");
  // let uiSelectSubarea = $("#subareaid-id");
  let uiSelectSalesman = $("#salesmanid-id");

  // define from *-content.js
  let param = common.getCookie("module.request_new_outlet.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined; // flag create update

  initializeParam();
  initialize();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("ref_request_new_outlet/create")
        : common.baseURL("ref_request_new_outlet/update");

    $.validator.setDefaults({
      errorPlacement: function (error, element) {
        if (element.hasClass("select2-hidden-accessible")) {
          error.insertAfter(element.next(".select2-container"));
        } else {
          error.insertAfter(element);
        }
      },
    });
    uiForm.initForm({
      url: url,
      param: param,
      directUrl: "ref_request_new_outlet",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "siteid", value: param.siteid });
        }
        form.push({ name: "usersession", value: paramsession.username });
        return true; // MANDATORY!
      },
      rules: {
        kode_outlet: {
          required: true,
        },
        nama_customer: {
          required: true,
        },
        salesmanid: {
          required: true,
        },
        regionalid: {
          required: true,
        },
        areaid: {
          required: true,
        },
        typeid: {
          required: true,
        },
      },
      message: {
        kode_outlet: {
          required: "Kode outlet wajib diisi.",
        },
        nama_customer: {
          required: "Nama outlet wajib diisi.",
        },
        salesmanid: {
          required: "MEDREP wajib dipilih.",
        },
        regionalid: {
          required: "Regional wajib dipilih.",
        },
        areaid: {
          required: "Area wajib dipilih.",
        },
        typeid: {
          required: "Channel wajib dipilih.",
        },
      },
    });
    uiBtnCancel.click(function () {
      common.direct("ref_request_new_outlet");
    });

    loadRegional({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
      restrict_bu: paramsession.restrict_bu,
    });
    loadSalesman({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
      restrict_bu: paramsession.restrict_bu,
    });

    uiSelectRegional.on("select2:select", function (e) {
      regionalSelected = e.params.data;
      let srval = regionalSelected;
      srval = {
        regionalid: srval.regionalid,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      };
      loadArea(srval);
    });

    uiSelectArea.on("select2:select", function (e) {
      areaSelected = e.params.data;
      regionalSelected = uiSelectRegional.val();
      // loadSubArea({
      //   regionalid: regionalSelected,
      //   areaid: areaSelected.areaid,
      //   usersession: paramsession.username,
      //   restrict_level: paramsession.restrict_level,
      // });
    });

    uiSelectRegional.select2({
      placeholder: "Select Regional",
      allowClear: true,
    });

    uiSelectArea.select2({
      placeholder: "Select Area",
      allowClear: true,
    });

    // uiSelectSubarea.select2({
    //   placeholder: "Select SubArea",
    //   allowClear: true,
    // });

    uiSelectSalesman.select2({
      placeholder: "Select MEDREP",
      allowClear: true,
    });

    if (isUpdate) {
      loadSalesman({
        usersession: paramsession.username,
        idjabatan: paramsession.idjabatan,
        restrict_level: paramsession.restrict_level,
      });
      loadRegional({
        usersession: paramsession.username,
        idjabatan: paramsession.idjabatan,
        restrict_level: paramsession.restrict_level,
      });
      loadArea({
        regionalid: param.regionalid,
        usersession: paramsession.username,
        idjabatan: paramsession.idjabatan,
        restrict_level: paramsession.restrict_level,
      });
      // loadSubArea({
      //   regionalid: param.regionalid,
      //   areaid: param.areaid,
      //   usersession: paramsession.username,
      //   restrict_level: paramsession.restrict_level,
      // });
    }
  }

  function initializeParam() {
    common.loading();
    let resolver = new HttpResolver();
    let filter = new Filter();

    $.when($.post(common.baseURL("ref_customer_type/load"), filter.build()))
      .done(function (data, textStatus, jqXHR) {})
      .then(function (rows) {
        common.loadingClose();
        setupForm(rows);
      })
      .fail(resolver.fail);
  }

  function setupForm(rows) {
    let rows1 = rows.rows;

    uiSelectType.select2({
      placeholder: "Select Channel",
      allowClear: true,
      data: $.map(rows1, function (o) {
        o.id = o.typeid; // replace name with the property used for the text
        o.text = o.nama_type; // replace name with the property used for the text
        return o;
      }),
    });

    if (isUpdate) {
      uiSelectType.val(param.typeid).trigger("change");
    } else {
      uiSelectType.val(null).trigger("change");
    }
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
          data: $.map(res.result, function (o) {
            o.id = o.regionalid; // replace name with the property used for the text
            o.text = o.nama_regional;
            return o;
          }),
        });

        if (isUpdate) {
          uiSelectRegional.val(param.regionalid).trigger("change");
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
          data: $.map(res.result, function (o) {
            o.id = o.areaid; // replace name with the property used for the text
            o.text = o.nama_area;
            return o;
          }),
        });

        if (isUpdate) {
          uiSelectArea.val(param.areaid).trigger("change");
        } else {
          uiSelectArea.val(null).trigger("change");
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
          data: $.map(res.result, function (o) {
            o.id = o.subareaid; // replace name with the property used for the text
            o.text = o.nama_area;
            return o;
          }),
        });

        if (isUpdate) {
          uiSelectSubarea.val(param.subareaid).trigger("change");
        } else {
          uiSelectSubarea.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
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
          placeholder: "Select Medrep",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.salesmanid; // replace name with the property used for the text
            o.text = o.salesmanid + " - " + o.nama_salesman;
            return o;
          }),
        });

        if (isUpdate) {
          uiSelectSalesman.val(param.salesmanid).trigger("change");
        } else {
          uiSelectSalesman.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }
})();
