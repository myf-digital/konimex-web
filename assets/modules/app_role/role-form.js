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
  let optionProducts = [];
  let searchTimeout = null;
  let spesialisasiTargets = {};
  let productTargets = {};
  let productQtyTargets = {};

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

    // Targets Initialization
    let now = new Date();
    let currentMonth =
      now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0");
    $("#periode").val(currentMonth);
    $("#periode_spesialisasi").val(currentMonth);
    $("#periode_product").val(currentMonth);

    $("#productid").select2({
      placeholder: "Pilih Produk",
      allowClear: true,
      ajax: {
        url: common.baseURL("app_role/products"),
        dataType: "json",
        type: "POST",
        delay: 500,
        data: function (params) {
          return {
            q: params.term || "",
          };
        },
        transport: function (params, success, failure) {
          let q = params.data.q || "";

          if (q === "") {
            if (optionProducts && optionProducts.length > 0) {
              success(optionProducts);
              return;
            }
          }

          if (q !== "" && q.length < 2) {
            success([]);
            return;
          }

          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(function () {
            fetchProducts(q, success, failure);
          }, 500);
        },
        processResults: function (data) {
          return {
            results: $.map(data, function (item) {
              return {
                id: item.productid,
                text: "(" + item.productid + ") - " + item.nama_invoice,
              };
            }),
          };
        },
        cache: true,
      },
    });

    $("#spesialisasiid").on("change", function () {
      updateSpesialisasiTable();
      setTimeout(function () {
        updateSelect2TagsCount("#spesialisasiid", "Spesialisasi");
      }, 50);
    });

    $("#productid").on("change", function () {
      updateProductTable();
      setTimeout(function () {
        updateSelect2TagsCount("#productid", "Produk");
      }, 50);
    });

    $(document).on("click", ".btn-remove-spesialisasi", function () {
      let id = $(this).data("id").toString();
      let currentSelected = $("#spesialisasiid").val() || [];
      let newSelected = currentSelected.filter((val) => val.toString() !== id);
      $("#spesialisasiid").val(newSelected).trigger("change");
    });

    $(document).on("click", ".btn-remove-product", function () {
      let id = $(this).data("id").toString();
      let currentSelected = $("#productid").val() || [];
      let newSelected = currentSelected.filter((val) => val.toString() !== id);
      $("#productid").val(newSelected).trigger("change");
    });

    fetchProducts("", function (res) {
      optionProducts = res;
    });

    initSpesialisasiOptions();

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
              $("#periode").val(formattedPeriode);
              $("#periode_spesialisasi").val(formattedPeriode);
              $("#periode_product").val(formattedPeriode);
            }
            $("#target_hk").val(detail.target_hk || "");
            $("#target_dub").val(detail.target_dub || "");
            $("#target_call_dub").val(detail.target_call_dub || "");
            $("#target_call_visit").val(detail.target_call_visit || "");
          }
          // Load specific role targets
          loadSpesialisasiTargets();
          loadProductTargets();
        },
      ).fail(function () {
        common.loadingClose();
      });
    }

    uiBtnCancel.click(function () {
      common.direct("app_role");
    });
  }

  function initSpesialisasiOptions() {
    common.loading();
    $.post(
      common.baseURL("app_role/spesialisasi"),
      {},
      function (res) {
        $("#spesialisasiid").empty();
        res.forEach(function (item) {
          let option = new Option(item.name, item.id);
          $("#spesialisasiid").append(option);
        });
        $("#spesialisasiid").select2({
          placeholder: "Pilih Spesialisasi",
          allowClear: true,
        });
        common.loadingClose();
        loadSpesialisasiTargets();
      },
    ).fail(function () {
      common.loadingClose();
    });
  }

  function loadSpesialisasiTargets() {
    let role_id = param !== undefined ? param.role_id : null;
    let period = $("#periode_spesialisasi").val();
    if (!role_id || !period) {
      $("#spesialisasiid").val(null).trigger("change");
      return;
    }

    common.loading();
    $.post(
      common.baseURL("app_role/get_spesialisasi_targets"),
      { role_id: role_id, periode: period },
      function (res) {
        spesialisasiTargets = {};
        let selectedIds = [];
        res.forEach(function (item) {
          spesialisasiTargets[item.spesialisasi_id] = item.target;
          selectedIds.push(item.spesialisasi_id);
        });
        $("#spesialisasiid").val(selectedIds).trigger("change");
        common.loadingClose();
      },
    ).fail(function () {
      common.loadingClose();
    });
  }

  function loadProductTargets() {
    let role_id = param !== undefined ? param.role_id : null;
    let period = $("#periode_product").val();
    if (!role_id || !period) {
      $("#productid").val(null).trigger("change");
      return;
    }

    common.loading();
    $.post(
      common.baseURL("app_role/get_product_targets"),
      { role_id: role_id, periode: period },
      function (res) {
        productTargets = {};
        productQtyTargets = {};
        $("#productid").empty();
        let selectedIds = [];
        res.forEach(function (item) {
          productTargets[item.product_id] = item.target;
          productQtyTargets[item.product_id] = item.target_qty || 0;
          selectedIds.push(item.product_id);

          let option = new Option(
            "(" + item.product_id + ") - " + item.nama_invoice,
            item.product_id,
            true,
            true,
          );
          $("#productid").append(option);
        });
        $("#productid").val(selectedIds).trigger("change");
        common.loadingClose();
      },
    ).fail(function () {
      common.loadingClose();
    });
  }

  function fetchProducts(keyword, success, failure) {
    $.post(common.baseURL("app_role/products"), { q: keyword })
      .done(function (res) {
        if (success) success(res);
      })
      .fail(function (xhr, status, error) {
        if (failure) failure(xhr, status, error);
      });
  }

  function updateSpesialisasiTable() {
    let selectedIds = $("#spesialisasiid").val() || [];
    let currentInputs = {};
    $("#tbl-spesialisasi-target tbody tr").each(function () {
      let id = $(this).data("id");
      let val = $(this).find(".target-input").val();
      currentInputs[id] = val;
    });

    let tbody = $("#tbl-spesialisasi-target tbody");
    tbody.empty();

    if (selectedIds.length === 0) {
      $("#tbl-spesialisasi-target").hide();
      return;
    }

    selectedIds.forEach(function (id) {
      let option = $("#spesialisasiid option[value='" + id + "']");
      let name = option.text() || id;
      let targetVal =
        currentInputs[id] !== undefined
          ? currentInputs[id]
          : spesialisasiTargets[id] || 0;
      tbody.append(`
        <tr data-id="${id}">
          <td>${name}</td>
          <td>
            <input type="text" name="target_spesialisasi[${id}]" class="form-control target-input text-right" value="${targetVal}" style="width: 100%;">
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-xs btn-danger btn-remove-spesialisasi" data-id="${id}"><i class="fa fa-trash"></i></button>
          </td>
        </tr>  
      `);
    });

    $("#tbl-spesialisasi-target").show();

    $(".target-input").on("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  }

  function updateProductTable() {
    let selectedIds = $("#productid").val() || [];
    let currentInputsDetail = {};
    let currentInputsQty = {};
    $("#tbl-product-target tbody tr").each(function () {
      let id = $(this).data("id");
      let valDetail = $(this).find(".target-input-detail").val();
      let valQty = $(this).find(".target-input-qty").val();
      currentInputsDetail[id] = valDetail;
      currentInputsQty[id] = valQty;
    });

    let tbody = $("#tbl-product-target tbody");
    tbody.empty();

    if (selectedIds.length === 0) {
      $("#tbl-product-target").hide();
      return;
    }

    selectedIds.forEach(function (id) {
      let option = $("#productid option[value='" + id + "']");
      let name = option.text() || id;
      let targetDetailVal =
        currentInputsDetail[id] !== undefined
          ? currentInputsDetail[id]
          : productTargets[id] || 0;
      let targetQtyVal =
        currentInputsQty[id] !== undefined
          ? currentInputsQty[id]
          : productQtyTargets[id] || 0;
      tbody.append(`
        <tr data-id="${id}">
          <td>${name}</td>
          <td>
            <input type="text" name="target_product[${id}]" class="form-control target-input-detail text-right" value="${targetDetailVal}" style="width: 100%;">
          </td>
          <td>
            <input type="text" name="target_product_qty[${id}]" class="form-control target-input-qty text-right" value="${targetQtyVal}" style="width: 100%;">
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-xs btn-danger btn-remove-product" data-id="${id}"><i class="fa fa-trash"></i></button>
          </td>
        </tr>  
      `);
    });

    $("#tbl-product-target").show();

    $(".target-input-detail, .target-input-qty").on("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  }

  function updateSelect2TagsCount(selectId, labelName) {
    let select = $(selectId);
    let selectedCount = select.val() ? select.val().length : 0;
    let renderedList = select.parent().find(".select2-selection__rendered");

    renderedList.find(".select2-selection__choice-counter").remove();

    if (selectedCount > 0) {
      let extraCount = selectedCount;
      let counterLi = $(
        '<li class="select2-selection__choice select2-selection__choice-counter badge-count-select2">' +
          extraCount +
          " " +
          labelName +
          " Terpilih</li>",
      );

      let searchLi = renderedList.find(".select2-search");
      if (searchLi.length > 0) {
        counterLi.insertBefore(searchLi);
      } else {
        renderedList.append(counterLi);
      }
    }
  }
})();
