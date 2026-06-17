(function () {
  const common = new Common();
  common.setTitle("Form User");
  // declare dom
  let uiForm = $("#form-set-outlet");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiInputUser = $("#professional");
  let uiSelectSpesialisasi = $("#spesialisasi");
  let uiSearchOutlet = $("#customerid");
  let uiSelectType = $("#type");

  let param = common.getCookie("module.professional.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined;
  let searchTimeout = null;

  initialize();

  function initialize() {
    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
      changeMonth: true,
      changeYear: true,
    });

    let url = common.baseURL("professional/update");
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
      directUrl: "professional",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "id_professional", value: param.id });
        }
        form.push({ name: "usersession", value: paramsession.username });

        let customer = document.getElementById("customerid_to");
        for (let i = 0; i < customer.options.length; i++) {
          form.push({ name: "customerid[]", value: customer.options[i].value });
        }
        return true;
      },
      rules: {
        professional: {
          required: true,
        },
        spesialisasi: {
          required: true,
        },
      },
      message: {
        professional: {
          required: "User wajib diisi.",
        },
        spesialisasi: {
          required: "Spesialisasi wajib dipilih.",
        },
      },
    });

    loadType();
    loadOutlet("", true);
    loadSpesialisasi();
    if (isUpdate) {
      uiInputUser.val(param.nama_professional || "");

      if (param.status) {
        let sts = "";
        if (param.status == "1") {
          sts = '<span class="label label-warning">Pending</span>';
        } else if (param.status == "5") {
          sts = `
            <span class="label label-danger">Rejected</span>
            <div style="font-size: 12px; color: #666; margin-top: 5px;"><b>Ket:</b> ${param.reason || ""}</div>
          `;
        }

        $("#status-label").html(sts);
      }
    }

    uiSearchOutlet.multiselect({
      search: {
        left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
        right:
          '<input type="text" name="q" class="form-control" placeholder="Search..." />',
      },
      fireSearch: function (value) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
          loadOutlet(value);
        }, 500);
        return false;
      },
      submitAllLeft: false,
      submitAllRight: false,
    });

    uiBtnCancel.click(function () {
      common.direct("professional");
    });
  }

  function loadSpesialisasi() {
    common.loading();
    $.post(common.baseURL("professional/spesialisasi"), function (res) {
      uiSelectSpesialisasi.empty();
      uiSelectSpesialisasi.select2({
        placeholder: "Select User (Professional)",
        allowClear: true,
        data: $.map(res, function (o) {
          o.id = o.id;
          o.text = o.name;
          return o;
        }),
      });

      if (isUpdate) {
        uiSelectSpesialisasi.val(param.spesialisasi_id).trigger("change");
      } else {
        uiSelectSpesialisasi.val(null).trigger("change");
      }
      common.loadingClose();
    });
  }

  function loadType() {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_param_key"),
      {
        parkey: "professional_type",
      },
      function (result) {
        let res = result.result;
        let types = res && res[0].value ? res[0].value.split("|") : [];
        uiSelectType.empty();
        uiSelectType.select2({
          placeholder: "Select Tipe",
          allowClear: true,
          data: $.map(types, function (o) {
            return { id: o, text: o };
          }),
        });

        if (isUpdate) {
          uiSelectType.val(param.type).trigger("change");
        } else {
          uiSelectType.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }

  function loadOutlet(keyword = "", isInitial = false) {
    $.post(
      common.baseURL("professional/outlet"),
      {
        q: keyword,
      },
      function (res) {
        let select = document.getElementById("customerid");
        let selectTo = document.getElementById("customerid_to");

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

          if (isUpdate && param.customer_list) {
            const customerList = param.customer_list
              ? param.customer_list.split("||")
              : [];
            customerList.forEach((item) => {
              const parts = item.split(" - ");
              const customerId = parts[0];
              selectedOutlet.push(customerId);

              let optSelected = document.createElement("option");
              optSelected.value = customerId;
              optSelected.innerHTML = item;
              optSelected.setAttribute("data-position", customerId);
              selectTo.appendChild(optSelected);
            });
          }
        } else {
          for (let i = 0; i < selectTo.options.length; i++) {
            selectedOutlet.push(selectTo.options[i].value);
          }
        }

        for (let i = 0; i < res.length; i++) {
          if (selectedOutlet.includes(res[i].customerid)) {
            continue;
          }

          let html = "";
          if (res[i].customerid) html += res[i].customerid;
          if (res[i].nama_customer) html += ` - ${res[i].nama_customer}`;
          if (res[i].typeid) html += ` - ${res[i].typeid}`;

          let opt = document.createElement("option");
          opt.value = res[i].customerid;
          opt.innerHTML = html;
          opt.setAttribute("data-position", res[i].customerid);
          select.appendChild(opt);
        }
      },
    );
  }
})();
