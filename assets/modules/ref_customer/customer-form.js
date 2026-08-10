(function () {
  const common = new Common();
  common.setTitle("Outlet");
  // declare dom
  let uiForm = $("#fm-customer");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectType = $("#typeid-id");
  let uiSelectRegional = $("#regionalid-id");
  let uiSelectArea = $("#areaid-id");
  let uiSelectSubarea = $("#subareaid-id");

  let param = common.getCookie("module.customer.update");
  let paramsession = common.getCookie("session");
  let uiSearchProfessional = $("#professional");

  let isUpdate = param !== undefined; // flag create update
  let searchTimeout = null;

  let modalSpesialisasiLoaded = false;
  let modalTypeLoaded = false;

  initializeParam();
  initialize();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("ref_customer/create")
        : common.baseURL("ref_customer/update");

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
      directUrl: "ref_customer",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "siteid", value: param.siteid });
        }
        form.push({ name: "usersession", value: paramsession.username });

        let professional = document.getElementById("professional_to");
        for (let i = 0; i < professional.options.length; i++) {
          form.push({
            name: "professional[]",
            value: professional.options[i].value,
          });
        }
        return true; // MANDATORY!
      },
      rules: {
        nama_customer: {
          required: true,
        },
        regionalid: {
          required: true,
        },
        typeid: {
          required: true,
        },
      },
      message: {
        nama_customer: {
          required: "Nama outlet wajib diisi.",
        },
        regionalid: {
          required: "Regional wajib dipilih.",
        },
        typeid: {
          required: "Channel wajib dipilih.",
        },
      },
    });
    uiBtnCancel.click(function () {
      common.direct("ref_customer");
    });

    loadProfessional("", true);
    loadRegional({
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
      srval = {
        regionalid: uiSelectRegional.val(),
        areaid: areaSelected.areaid,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      };
      loadSubArea(srval);
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

    uiSearchProfessional.multiselect({
      search: {
        left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
        right:
          '<input type="text" name="q" class="form-control" placeholder="Search..." />',
      },
      fireSearch: function (value) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
          loadProfessional(value);
        }, 500);
        return false;
      },
      submitAllLeft: false,
      submitAllRight: false,
    });

    if (isUpdate) {
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
      loadSubArea({
        regionalid: param.regionalid,
        areaid: param.areaid,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      });
    }

    $("#modal-tanggal_lahir, #modal-tanggal_aniv_pernikahan").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
      changeMonth: true,
      changeYear: true,
    });

    $("#btn-add-professional").click(function () {
      loadModalSpesialisasi();
      loadModalType();
      $("#modal-add-professional").modal("show");
    });

    $("#form-add-professional").submit(function (e) {
      e.preventDefault();

      let namaVal = $("#modal-professional").val();
      let spesialisasiVal = $("#modal-spesialisasi").val();
      let typeVal = $("#modal-type").val();
      let lahirVal = $("#modal-tanggal_lahir").val();
      let anivVal = $("#modal-tanggal_aniv_pernikahan").val();

      if (!namaVal) {
        alert("Nama User wajib diisi.");
        return;
      }
      if (!spesialisasiVal) {
        alert("Spesialisasi wajib dipilih.");
        return;
      }

      common.loading();
      $.post(
        common.baseURL("professional/quick_create"),
        {
          professional: namaVal,
          spesialisasi: spesialisasiVal,
          type: typeVal,
          tanggal_lahir: lahirVal,
          tanggal_aniv_pernikahan: anivVal,
          usersession: paramsession.username,
        },
        function (res) {
          common.loadingClose();
          if (res.code === 200 && res.result) {
            let newProf = res.result;

            let html = "";
            if (newProf.id) html += newProf.id;
            if (newProf.nama_professional)
              html += ` - ${newProf.nama_professional}`;
            if (newProf.spesialisasi) html += ` - ${newProf.spesialisasi}`;
            if (newProf.type) html += ` - ${newProf.type}`;

            let selectTo = document.getElementById("professional_to");
            let optSelected = document.createElement("option");
            optSelected.value = newProf.id;
            optSelected.innerHTML = html;
            optSelected.setAttribute("data-position", newProf.id);
            selectTo.appendChild(optSelected);

            $("#modal-add-professional").modal("hide");
            $("#form-add-professional")[0].reset();
            $("#modal-spesialisasi").val(null).trigger("change");
            $("#modal-type").val(null).trigger("change");
          } else {
            alert(res.message || "Gagal menyimpan User.");
          }
        },
      ).fail(function () {
        common.loadingClose();
        alert("Terjadi kesalahan sistem.");
      });
    });
  }

  function initializeParam() {
    common.loading();
    let resolver = new HttpResolver();
    let filter = new Filter();

    $.when($.post(common.baseURL("ref_customer_type/load"), filter.build()))
      .done(function (data, textStatus, jqXHR) {})
      .then(function (ct, cc) {
        common.loadingClose();
        setupForm(ct);
      })
      .fail(resolver.fail);
  }

  function setupForm(ct) {
    let rowsCT = ct.rows;

    uiSelectType.select2({
      placeholder: "Select Channel",
      allowClear: true,
      data: $.map(rowsCT, function (o) {
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

  function loadProfessional(keyword = "", isInitial = false) {
    $.post(
      common.baseURL("api_v1/call_professional"),
      {
        q: keyword,
      },
      function (res) {
        let result = res.result;
        let select = document.getElementById("professional");
        let selectTo = document.getElementById("professional_to");

        let length = select.options.length;
        for (let i = length - 1; i >= 0; i--) {
          select.options[i] = null;
        }

        let selectedOutlet = [];
        if (isInitial) {
          let selectToLength = selectTo.options.length;
          for (let i = selectToLength - 1; i >= 0; i--) {
            selectTo.options[i] = null;
          }

          if (isUpdate && param.list_professional) {
            const professionalList = param.list_professional
              ? param.list_professional.split("||")
              : [];
            professionalList.forEach((item) => {
              const parts = item.split(" - ");
              const professionalId = parts[0];
              selectedOutlet.push(professionalId);

              let optSelected = document.createElement("option");
              optSelected.value = professionalId;
              optSelected.innerHTML = item;
              optSelected.setAttribute("data-position", professionalId);
              selectTo.appendChild(optSelected);
            });
          }
        } else {
          for (let i = 0; i < selectTo.options.length; i++) {
            selectedOutlet.push(selectTo.options[i].value);
          }
        }

        for (let i = 0; i < result.length; i++) {
          if (selectedOutlet.includes(result[i].id)) {
            continue;
          }

          let html = "";
          if (result[i].id) html += result[i].id;
          if (result[i].nama_professional)
            html += ` - ${result[i].nama_professional}`;
          if (result[i].spesialisasi) html += ` - ${result[i].spesialisasi}`;
          if (result[i].type) html += ` - ${result[i].type}`;

          let opt = document.createElement("option");
          opt.value = result[i].id;
          opt.innerHTML = html;
          opt.setAttribute("data-position", result[i].id);
          select.appendChild(opt);
        }
      },
    );
  }

  function loadModalSpesialisasi() {
    if (modalSpesialisasiLoaded) return;
    $.post(common.baseURL("professional/spesialisasi"), function (res) {
      let uiModalSpesialisasi = $("#modal-spesialisasi");
      uiModalSpesialisasi.empty();
      uiModalSpesialisasi.select2({
        placeholder: "Select Spesialisasi",
        allowClear: true,
        data: $.map(res, function (o) {
          return { id: o.id, text: o.name };
        }),
      });
      uiModalSpesialisasi.val(null).trigger("change");
      modalSpesialisasiLoaded = true;
    });
  }

  function loadModalType() {
    if (modalTypeLoaded) return;
    $.post(
      common.baseURL("api_v1/call_param_key"),
      {
        parkey: "professional_type",
      },
      function (result) {
        let res = result.result;
        let types = res && res[0].value ? res[0].value.split("|") : [];
        let uiModalType = $("#modal-type");
        uiModalType.empty();
        uiModalType.select2({
          placeholder: "Select Tipe",
          allowClear: true,
          data: $.map(types, function (o) {
            return { id: o, text: o };
          }),
        });
        uiModalType.val(null).trigger("change");
        modalTypeLoaded = true;
      },
    );
  }
})();
