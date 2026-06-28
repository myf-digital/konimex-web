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
  let optionProducts = [];
  let searchTimeout = null;

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
        regionalid: {
          required: true,
        },
        areaid: {
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
    $("#periode_spesialisasi").val(currentMonth);
    $("#periode_product").val(currentMonth);
    $("#periode_sales").val(currentMonth);

    $("#periode_spesialisasi")
      .datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
      })
      .on("changeDate", function () {
        loadSpesialisasiTargets();
      });

    $("#periode_product")
      .datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true,
      })
      .on("changeDate", function () {
        loadProductTargets();
      });

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

    $("#productid").select2({
      placeholder: "Pilih Produk",
      allowClear: true,
      ajax: {
        url: common.baseURL("ref_sales_salesman/products"),
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

    // Initialize change event handlers to rebuild target tables
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

    uiSalesmanid.on("change blur", function () {
      loadSpesialisasiTargets();
      loadProductTargets();
      loadSalesTargets();
    });

    fetchProducts("", function (res) {
      optionProducts = res;
    });

    initSpesialisasiOptions();

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
      regionalSelected = uiSelectRegional.val();
      let sraval = areaSelected;
      sraval = {
        regionalid: regionalSelected,
        areaid: sraval.areaid,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      };
      loadSubArea(sraval);
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
      document.getElementById("salesmanid-id").readOnly = true;
      uiSelectSiteid.val(param.siteid).trigger("change");
      let pval = { siteid: param.siteid };

      loadSupervisor({
        tipe_sales: param.tipe_sales,
        salesmanid: param.salesmanid,
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
      loadSubArea({
        regionalid: param.regionalid,
        areaid: param.areaid,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      });

      loadSpesialisasiTargets();
      loadProductTargets();
      loadSalesTargets();
    }
  }

  let spesialisasiTargets = {};
  let productTargets = {};

  function initSpesialisasiOptions() {
    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/spesialisasi"),
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
    let salesmanid = isUpdate ? param.salesmanid : uiSalesmanid.val();
    let period = $("#periode_spesialisasi").val();
    if (!salesmanid || !period) {
      $("#spesialisasiid").val(null).trigger("change");
      return;
    }

    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/get_spesialisasi_targets"),
      { salesmanid: salesmanid, periode: period },
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
    let salesmanid = isUpdate ? param.salesmanid : uiSalesmanid.val();
    let period = $("#periode_product").val();
    if (!salesmanid || !period) {
      $("#productid").val(null).trigger("change");
      return;
    }

    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/get_product_targets"),
      { salesmanid: salesmanid, periode: period },
      function (res) {
        productTargets = {};
        $("#productid").empty();
        let selectedIds = [];
        res.forEach(function (item) {
          productTargets[item.product_id] = item.target;
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

  function fetchProducts(keyword, success, failure) {
    $.post(common.baseURL("ref_sales_salesman/products"), { q: keyword })
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
    let currentInputs = {};
    $("#tbl-product-target tbody tr").each(function () {
      let id = $(this).data("id");
      let val = $(this).find(".target-input").val();
      currentInputs[id] = val;
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
      let targetVal =
        currentInputs[id] !== undefined
          ? currentInputs[id]
          : productTargets[id] || 0;
      tbody.append(`
        <tr data-id="${id}">
          <td>${name}</td>
          <td>
            <input type="text" name="target_product[${id}]" class="form-control target-input text-right" value="${targetVal}" style="width: 100%;">
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-xs btn-danger btn-remove-product" data-id="${id}"><i class="fa fa-trash"></i></button>
          </td>
        </tr>  
      `);
    });

    $("#tbl-product-target").show();

    $(".target-input").on("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
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
