(function () {
  const common = new Common();
  common.setTitle("Role");
  // declare dom
  let uiForm = $("#fm-role");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectRole = $("#role_name");

  let param = common.getCookie("module.role.update");
  let paramsession = common.getCookie("session");
  let keyRoleSales = "";

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

    $.post(
      common.baseURL("api_v1/call_param_key"),
      { parkey: "key_role_sales" },
      function (res) {
        if (res.result && res.result.length > 0) {
          const valRoles = res.result.find(
            (r) => r.key_param == "key_role_sales",
          );
          keyRoleSales = valRoles.value;
        }
        checkMappingTargetVisibility();
      },
    );

    function checkMappingTargetVisibility() {
      let selectedVal = uiSelectRole.val();
      let roleName = "";
      if (selectedVal == "other") {
        roleName = $("#role_name_desc").val().trim();
      } else {
        roleName = selectedVal;
      }

      let match = false;
      if (roleName) {
        let normalized = roleName.toUpperCase();
        match = keyRoleSales.includes(normalized);
      }

      if (match) {
        $("#mapping_target_group").show();
      } else {
        $("#mapping_target_group").hide();
      }
    }

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
      checkMappingTargetVisibility();
      if (typeof $(this).valid === "function") {
        $(this).valid();
      }
    });

    $("#role_name_desc").on("input", function () {
      checkMappingTargetVisibility();
    });

    $("#periode").datepicker({
      format: "yyyy-mm",
      viewMode: "months",
      minViewMode: "months",
      autoclose: true,
      startDate: moment().format("YYYY-MM"),
    });

    $("#target_hk, #target_dub").on("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });

    $("#target_call_dub, #target_call_visit").on("input", function () {
      let val = this.value;
      val = val.replace(/[^0-9.]/g, "");
      let parts = val.split(".");
      if (parts.length > 2) {
        val = parts[0] + "." + parts.slice(1).join("");
      }
      this.value = val;
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

      common.loading();
      $.post(
        common.baseURL("app_role/detail"),
        { role_id: param.role_id },
        function (res) {
          common.loadingClose();
          if (res.status && res.result) {
            let detail = res.result;
            if (detail.tahun && detail.bulan) {
              let formattedPeriode =
                detail.tahun + "-" + String(detail.bulan).padStart(2, "0");
              $("#periode").datepicker("setDate", formattedPeriode);
            }
            $("#target_hk").val(detail.target_hk || "");
            $("#target_dub").val(detail.target_dub || "");
            $("#target_call_dub").val(detail.target_call_dub || "");
            $("#target_call_visit").val(detail.target_call_visit || "");
          }
        },
      ).fail(function () {
        common.loadingClose();
      });
    }

    uiBtnCancel.click(function () {
      common.direct("app_role");
    });
  }
})();
