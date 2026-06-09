(function () {
  const common = new Common();
  common.setTitle("Form Spesialisasi");
  // declare dom
  let uiForm = $("#form-spesialisasi");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiInput = $("#spesialisasi");

  let param = common.getCookie("module.spesialisasi.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined;

  initialize();

  function initialize() {
    let url = common.baseURL("spesialisasi/update");
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
      directUrl: "spesialisasi",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "id", value: param.id });
        }
        form.push({ name: "usersession", value: paramsession.username });
        form.push({
          name: "spesialisasi",
          value: document.getElementById("spesialisasi").value,
        });
        return true;
      },
      rules: {
        spesialisasi: {
          required: true,
        },
      },
      message: {
        spesialisasi: {
          required: "Spesialisasi wajib diisi.",
        },
      },
    });

    if (isUpdate) {
      uiInput.val(param.name || "");
    }

    uiBtnCancel.click(function () {
      common.direct("spesialisasi");
    });
  }
})();
