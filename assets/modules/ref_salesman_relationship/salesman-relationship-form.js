(function () {
  const common = new Common();
  common.setTitle("TPE Relationship");
  // declare dom
  let uiForm = $("#fm-salesman-relationship");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSalesmanId = $("#salesmanid");
  let uiRelationship = $("#relationship");
  let uiJenisKelamin = $("#jenis_kelamin");

  let param = common.getCookie("module.salesman.relationship.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined; // flag create update

  initialize();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("ref_salesman_relationship/create")
        : common.baseURL("ref_salesman_relationship/update");
    uiForm.initForm({
      url: url,
      param: param,
      directUrl: "ref_salesman_relationship",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "siteid", value: param.siteid });
        }
        return true; // MANDATORY!
      },
      rules: {
        salesmanid: {
          required: true,
        },
        relationship: {
          required: true,
        },
        nama_relationship: {
          required: true,
        },
      },
      messages: {
        salesmanid: {
          required: "TPE wajib dipilih",
        },
        relationship: {
          required: "Relationship wajib dipilih",
        },
        nama_relationship: {
          required: "Nama Relationship wajib diisi",
        },
      },
    });

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
      changeMonth: true,
      changeYear: true,
      orientation: "bottom auto",
    });

    uiBtnCancel.click(function () {
      common.direct("ref_salesman_relationship");
    });

    uiSalesmanId.select2({
      placeholder: "Pilih Medrep",
      allowClear: true,
    });

    uiRelationship.select2({
      placeholder: "Pilih Relationship",
      allowClear: true,
    });

    uiJenisKelamin.select2({
      placeholder: "Pilih Jenis Kelamin",
      allowClear: true,
      data: $.map(["Laki-Laki", "Perempuan"], function (o) {
        o.id = o;
        o.text = o;
        return o;
      }),
    });

    if (isUpdate) {
      document.getElementById("salesmanid").readOnly = true;
      document.getElementById("relationship").readOnly = true;
      uiJenisKelamin.val(param.jenis_kelamin).trigger("change");
    } else {
      uiJenisKelamin.val(null).trigger("change");
    }

    loadSite();
    loadParamGlobal();
    loadSalesman(paramsession);

    uiSalesmanId.on("select2:select", function (e) {
      $("#nama_salesman").val(e.params.data.text);
    });
  }

  function loadSite() {
    common.loading();
    $.post(common.baseURL("api_v1/call_siteid"), {}, function (res) {
      if (res.code == 200 && res.result) $("#siteid").val(res.result[0].siteid);
      common.loadingClose();
    });
  }

  function loadSalesman(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_salesman"),
      {
        usersession: data.usersession,
        restrict_level: data.restrict_level,
      },
      function (res) {
        uiSalesmanId.empty();
        uiSalesmanId.select2({
          placeholder: "Pilih Medrep",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.salesmanid;
            o.text = o.nama_salesman;
            return o;
          }),
        });

        if (isUpdate) {
          uiSalesmanId.val(param.salesmanid).trigger("change");
        } else {
          uiSalesmanId.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }

  function loadParamGlobal() {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_param_key"),
      {
        parkey: "key_relationship",
      },
      function (res) {
        uiRelationship.empty();
        uiRelationship.select2({
          placeholder: "Pilih Relationship",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.value;
            o.text = o.desc;
            return o;
          }),
        });

        if (isUpdate) {
          uiRelationship.val(param.relationship).trigger("change");
        } else {
          uiRelationship.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }
})();
