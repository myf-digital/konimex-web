(function () {
  const common = new Common();
  common.setTitle("Report Order");

  // DOM Elements
  let uiStartPeriode = $("#start_periode");
  let uiEndPeriode = $("#end_periode");
  let uiSelectRegional = $("#regional-id");
  let uiSelectArea = $("#area-id");
  // let uiSelectSubArea = $("#subarea-id");
  let uiSelectSalesman = $("#salesmanid-id");
  let uiBtnPreview = $("#btn-preview-form");

  let paramsession = common.getCookie("session");
  let localSalesmen = [];

  initializeParam();

  function initializeParam() {
    common.loading();

    // Datepickers
    $(".datepicker")
      .datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
      })
      .datepicker("setDate", new Date());

    // Datepicker date range constraint
    uiStartPeriode.on("changeDate", function (selected) {
      let startDate = new Date(selected.date.valueOf());
      let endDate = new Date(selected.date.valueOf());
      endDate.setDate(endDate.getDate() + 90);
      uiEndPeriode.datepicker("setStartDate", startDate);
      uiEndPeriode.datepicker("setEndDate", endDate);
      if (uiStartPeriode.val() > uiEndPeriode.val()) {
        uiEndPeriode.val(uiStartPeriode.val());
      }
    });

    // Load initial dropdowns
    $.post(common.baseURL("rep_order/load_regional"), {}, function (res) {
      uiSelectRegional.select2({
        placeholder: "Select Regional",
        allowClear: true,
        data: $.map(res.rows, function (o) {
          o.id = o.regionalid;
          o.text = o.nama_regional;
          return o;
        }),
      });

      uiSelectArea.select2({
        placeholder: "Select Area",
        allowClear: true,
      });

      // uiSelectSubArea.select2({
      //   placeholder: "Select Sub Area",
      //   allowClear: true,
      // });

      uiSelectSalesman.select2({
        placeholder: "Select Salesman",
        allowClear: true,
        ajax: {
          transport: function (params, success, failure) {
            let term = params.data.q || "";
            if (term.length < 2) {
              // Return the cached localSalesmen list for empty or 1-char inputs
              let filtered = localSalesmen;
              if (term.length === 1) {
                filtered = $.grep(localSalesmen, function (o) {
                  return (
                    o.nama_salesman.toLowerCase().indexOf(term.toLowerCase()) >
                      -1 ||
                    o.salesmanid.toLowerCase().indexOf(term.toLowerCase()) > -1
                  );
                });
              }
              success({
                rows: filtered,
                total: filtered.length,
              });
              return;
            }

            // Perform actual AJAX request only when search query has 2+ characters
            var $request = $.ajax(params);
            $request.then(success);
            $request.fail(failure);
            return $request;
          },
          url: common.baseURL("rep_order/load_salesman"),
          dataType: "json",
          type: "POST",
          delay: 250,
          data: function (params) {
            return getSalesmanParams(params.term, params.page);
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: $.map(data.rows, function (o) {
                return {
                  id: o.salesmanid,
                  text: o.salesmanid + " - " + o.nama_salesman,
                };
              }),
              pagination: {
                more: params.page * 30 < data.total,
              },
            };
          },
          cache: true,
        },
      });

      uiSelectRegional.val(null).trigger("change");
      common.loadingClose();
    });

    // Event Handlers for Chaining Selects
    uiSelectRegional.on("select2:select", function (e) {
      let regional = e.params.data;
      loadArea(regional);
    });

    uiSelectRegional.on("change", function () {
      if (!uiSelectRegional.val()) {
        uiSelectArea.empty().trigger("change");
        // uiSelectSubArea.empty().trigger("change");
      }
      loadInitialSalesmen();
    });

    uiSelectArea.on("select2:select", function (e) {
      let area = e.params.data;
      // loadSubArea(area);
    });

    uiSelectArea.on("change", function () {
      if (!uiSelectArea.val()) {
        // uiSelectSubArea.empty().trigger("change");
      }
      loadInitialSalesmen();
    });

    // uiSelectSubArea.on("change", function () {
    //   loadInitialSalesmen();
    // });

    // Action button
    uiBtnPreview.click(function () {
      if (uiStartPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else if (uiEndPeriode.val() === "") {
        $.alert({
          title: "Error ",
          content: "Periode harus di isi...!",
          containerFluid: true,
        });
      } else if (
        uiSelectSalesman.val() === "" ||
        uiSelectSalesman.val() === null
      ) {
        $.alert({
          title: "Error ",
          content: "Salesman harus di isi...!",
          containerFluid: true,
        });
      } else {
        open_preview();
      }
    });
  }

  function getSalesmanParams(term, page) {
    return {
      q: term || "",
      page: page || 1,
      rows: 30,
      regionalid: uiSelectRegional.val(),
      areaid: uiSelectArea.val(),
      // subareaid: uiSelectSubArea.val(),
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
    };
  }

  function loadInitialSalesmen() {
    $.post(
      common.baseURL("rep_order/load_salesman"),
      getSalesmanParams("", 1),
      function (res) {
        localSalesmen = res.rows || [];
        uiSelectSalesman.empty();
        $.each(localSalesmen, function (i, o) {
          let newOption = new Option(
            o.salesmanid + " - " + o.nama_salesman,
            o.salesmanid,
            false,
            false,
          );
          uiSelectSalesman.append(newOption);
        });
        uiSelectSalesman.val(null).trigger("change");
      },
    );
  }

  function loadArea(data) {
    common.loading();
    $.post(
      common.baseURL("rep_order/load_area"),
      { regionalid: data.regionalid },
      function (res) {
        uiSelectArea.empty();
        uiSelectArea.select2({
          placeholder: "Select Area",
          allowClear: true,
          data: $.map(res.rows, function (o) {
            o.id = o.areaid;
            o.text = o.nama_area;
            return o;
          }),
        });
        uiSelectArea.val(null).trigger("change");
        // uiSelectSubArea.empty().trigger("change");
        common.loadingClose();
      },
    );
  }

  function loadSubArea(data) {
    common.loading();
    $.post(
      common.baseURL("rep_order/load_city"),
      { areaid: data.areaid },
      function (res) {
        uiSelectSubArea.empty();
        uiSelectSubArea.select2({
          placeholder: "Select Sub Area",
          allowClear: true,
          data: $.map(res.rows, function (o) {
            o.id = o.subareaid;
            o.text = o.nama_area;
            return o;
          }),
        });
        uiSelectSubArea.val(null).trigger("change");
        common.loadingClose();
      },
    );
  }

  function open_preview() {
    let start = uiStartPeriode.val();
    let end = uiEndPeriode.val();
    let salesmanid = uiSelectSalesman.val();

    common.loading();
    $.ajax({
      type: "POST",
      dataType: "html",
      url: common.baseURL("rep_order/get_order_all"),
      data: {
        salesmanid: salesmanid,
        startdate1: start,
        startdate2: end,
      },
      success: function (res) {
        $("#tbl-content").html(res);
        common.loadingClose();
      },
      error: function () {
        common.loadingClose();
        alert("Load failed");
      },
    });
  }
})();
