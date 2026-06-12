(function () {
  const common = new Common();
  common.setTitle("Role");
  // declare dom
  let uiForm = $("#fm-role");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectRole = $("#role_name");

  let param = common.getCookie("module.role.update");
  let paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("app_role/create")
        : common.baseURL("app_role/update");

    $.validator.setDefaults({
      errorPlacement: function (error, element) {
        if (element.hasClass("select2-hidden-accessible")) {
          error.insertAfter(element.next(".select2-container"));
        } else if (element.parent(".input-group").length) {
          error.insertAfter(element.parent(".input-group"));
        } else {
          error.insertAfter(element);
        }
      },
    });

    uiForm.initForm({
      url: url,
      param: param,
      directUrl: "app_role",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "role_id", value: param.role_id });
        }
        form.push({ name: "usersession", value: paramsession.username });
        return true; // MANDATORY!
      },
      rules: {
        role_name: {
          required: true,
        },
        role_name_desc: {
          required: true,
        },
      },
      messages: {
        role_name: {
          required: "Role Name wajib dipilih.",
        },
        role_name_desc: {
          required: "Role Name kustom wajib diisi.",
        },
      },
    });

    uiSelectRole.select2({
      placeholder: "Select Role Name",
      allowClear: true,
    });

    uiSelectRole.on("change", function () {
      let val = $(this).val();
      let text = $(this).find("option:selected").text();
      if (val === "other") {
        $("#custom_role_group").show();
        $("#role_name_desc").val("");
      } else {
        $("#custom_role_group").hide();
        $("#role_name_desc").val(text ? text.split("-")[1].trim() : "");
      }
      if (typeof $(this).valid === "function") {
        $(this).valid();
      }
    });

    if (param !== undefined && param.role_name) {
      let roleNameVal = param.role_name;
      let exists =
        $("#role_name option[value='" + roleNameVal + "']").length > 0;
      if (exists) {
        uiSelectRole.val(roleNameVal).trigger("change");
      } else {
        uiSelectRole.val("other").trigger("change");
        $("#role_name_desc").val(roleNameVal);
      }
    }

    uiBtnCancel.click(function () {
      common.direct("app_role");
    });
  }
})();
