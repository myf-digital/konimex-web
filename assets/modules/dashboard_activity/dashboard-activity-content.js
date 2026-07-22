$(function () {
  let common = new Common();
  let paramsession = common.getCookie("session");
  let baseUrlApi = typeof URL_API !== "undefined" ? URL_API : "";
  let API_URL = baseUrlApi + "/api_v1/dashboard_activity";
  let chartInstances = {};

  let exportData = { summary: [], area: null, subarea: null };

  const $periode = $("#periode");
  const $tipeSales = $("#tipe_sales");
  const $btnLoad = $("#btn_load");
  const $btnExport = $("#btn_export");
  const $divTipeSales = $("#div_tipe_sales");
  const $chartsRow = $("#charts-row");

  const $rowDetail = $("#row-detail");
  const $titleDetail = $("#title-detail");
  const $colDetail = $("#col-detail");

  const $rowDetailArea = $("#row-detail-area");
  const $titleDetailArea = $("#title-detail-area");
  const $colDetailArea = $("#col-detail-area");

  const $rowDetailSubarea = $("#row-detail-subarea");
  const $titleDetailSubarea = $("#title-detail-subarea");
  const $colDetailSubarea = $("#col-detail-subarea");

  const $rowDetailSalesman = $("#row-detail-salesman");
  const $titleDetailSalesman = $("#title-detail-salesman");
  const $colDetailSalesman = $("#col-detail-salesman");

  const $modalVisitDetail = $("#modal-visit-detail");
  const $modalVisitTitle = $("#modal-visit-title");
  const $modalVisitBody = $("#modal-visit-body");

  const $allDetailRows = $(
    "#row-detail, #row-detail-area, #row-detail-subarea, #row-detail-salesman",
  );

  function val(v) {
    return v === null || v === undefined || v === "null" || v === "" ? "-" : v;
  }

  function formatDateTime(v, length = 16, fallback = "-") {
    return v ? v.replace("T", " ").substring(0, length) : fallback;
  }

  function calculateDuration(start, end) {
    if (!start || !end) return "-";
    let s = new Date(start.replace("T", " "));
    let e = new Date(end.replace("T", " "));
    if (isNaN(s) || isNaN(e)) return "-";
    let diff = Math.floor((e - s) / 1000);
    if (diff < 0) return "-";
    let h = Math.floor(diff / 3600);
    let m = Math.floor((diff % 3600) / 60);
    let sec = diff % 60;
    return (
      String(h).padStart(2, "0") +
      ":" +
      String(m).padStart(2, "0") +
      ":" +
      String(sec).padStart(2, "0")
    );
  }

  function makeProgressBar(actual, target) {
    actual = parseInt(actual) || 0;
    target = parseInt(target) || 0;
    let pct = target > 0 ? Math.round((actual / target) * 100) : 0;
    let barPct = Math.min(100, pct);
    return `
      <div class="dashboard-progress">
        <div class="dashboard-progress-fill" style="width: ${barPct}%;"></div>
        <div class="dashboard-progress-text">
          ${actual} / ${target} (${pct})
        </div>
      </div>
    `;
  }

  $periode
    .datepicker({
      format: "MM yyyy",
      viewMode: "months",
      minViewMode: "months",
      autoclose: true,
      language: "id",
    })
    .datepicker("setDate", new Date());

  $btnExport.on("click", exportExcel);

  function initialize(type = "") {
    if (paramsession) {
      let rLoc = paramsession.restrict_location || [];

      let regionalIds = "";
      let areaIds = "";
      let subareaIds = "";
      let namaRegional = "";
      let namaArea = "";
      let namaSubarea = "";
      if (rLoc && rLoc.length > 0) {
        regionalIds = rLoc.map((v) => v.regionalid).join(",");
        areaIds = rLoc.map((v) => v.areaid).join(",");
        subareaIds = rLoc.map((v) => v.subareaid).join(",");

        namaRegional = rLoc.map((v) => v.nama_regional).join(", ");
        namaArea = rLoc.map((v) => v.nama_area).join(", ");
        namaSubarea = rLoc.map((v) => v.nama_subarea).join(", ");
      }

      let restrictLevel = parseInt(paramsession.restrict_level);

      if (restrictLevel == 4 && subareaIds) {
        loadSubareaDetail(regionalIds, areaIds, subareaIds, namaSubarea);
      } else if (restrictLevel == 3 && areaIds) {
        loadAreaDetail(regionalIds, areaIds, namaArea);
      } else if (restrictLevel == 2 && regionalIds) {
        loadRegionDetail(regionalIds, namaRegional);
      } else {
        loadSummary();
      }

      if (
        paramsession.role_name &&
        ["medrep", "mrc"].includes(paramsession.role_name.toLowerCase())
      ) {
        $divTipeSales.hide();
      } else {
        if (type != "load") {
          loadTipeSales();
        }
      }
    } else {
      if (type != "load") {
        loadTipeSales();
      }
      loadSummary();
    }
  }
  initialize();

  $btnLoad.on("click", function () {
    $allDetailRows.hide();
    exportData = {
      summary: [],
      area: null,
      subarea: null,
      salesman: null,
      visits: null,
    };
    initialize("load");
  });

  function apiCall(extraData) {
    let selectedDate = $periode.datepicker("getDate");
    let periodeVal = selectedDate
      ? moment(selectedDate).format("YYYY-MM")
      : moment().format("YYYY-MM");

    let formData = new FormData();
    formData.append("periode", periodeVal);

    let tipeSalesVal = $tipeSales.val();
    if (tipeSalesVal) {
      formData.append("tipe_sales", tipeSalesVal);
    }

    if (extraData) {
      $.each(extraData, function (k, v) {
        formData.append(k, v);
      });
    }
    return $.ajax({
      url: API_URL,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-Token": paramsession ? paramsession.token || "expired" : "expired",
      },
    });
  }

  function loadSummary() {
    $btnLoad
      .prop("disabled", true)
      .html(`<i class="fa fa-spinner fa-spin"></i> Loading...`);

    apiCall()
      .done(function (res) {
        if (res.code == 200) {
          exportData.summary = res.result;
          renderTop(res.result);
        } else {
          Swal.fire("Gagal Memuat Data!", res.message, "warning");
        }
      })
      .fail(function (xhr) {
        Swal.fire("Gagal Memuat Data!", xhr.responseText, "error");
      })
      .done(function () {
        $btnLoad
          .prop("disabled", false)
          .html(`<i class="fa fa-refresh"></i> Load Data`);
      });
  }

  function loadTipeSales() {
    $.ajax({
      url: common.baseURL("api_v1/call_tipesalesman"),
      method: "POST",
      data: {
        param: "key_role_sales",
      },
      dataType: "json",
      success: function (res) {
        let data = $.map(res.result || [], function (o) {
          return {
            id: o.idtipesales,
            text: o.tipesales,
          };
        });
        $tipeSales
          .empty()
          .append(`<option value=""></option>`)
          .select2({
            placeholder: "Select tipe sales",
            allowClear: true,
            data: data,
          })
          .val(null)
          .trigger("change");
      },
      error: function (xhr) {
        console.log(xhr.responseText);
        $tipeSales
          .empty()
          .append(`<option value=""></option>`)
          .select2({
            placeholder: "Select tipe sales",
            allowClear: true,
          })
          .val(null)
          .trigger("change");
      },
    });
  }

  function loadRegionDetail(regionalid, nama) {
    $titleDetail.text(nama);
    $colDetail.html(
      `<p class="text-muted dashboard-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
    );
    $rowDetail.show();
    $rowDetailArea.hide();
    $rowDetailSubarea.hide();

    scrollTo($rowDetail);

    apiCall({ regionalid }).done(function (res) {
      if (res.code !== 200) return;
      exportData.area = { label: nama, data: res.result };
      exportData.subarea = null;
      renderDetail(res.result, regionalid);
    });
  }

  function loadAreaDetail(regionalid, areaid, nama) {
    $titleDetailArea.text(nama);
    $colDetailArea.html(
      `<p class="text-muted dashboard-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
    );
    $rowDetailArea.show();
    $rowDetailSubarea.hide();

    scrollTo($rowDetailArea);

    apiCall({ regionalid, areaid }).done(function (res) {
      if (res.code !== 200) return;
      exportData.subarea = { label: nama, data: res.result };
      renderAreaDetail(res.result, regionalid, areaid);
    });
  }

  function loadSubareaDetail(regionalid, areaid, subareaid, nama) {
    $titleDetailSubarea.text(nama);
    $colDetailSubarea.html(
      `<p class="text-muted dashboard-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
    );
    $rowDetailSubarea.show();
    $rowDetailSalesman.hide();

    scrollTo($rowDetailSubarea);

    apiCall({ regionalid, areaid, subareaid }).done(function (res) {
      if (res.code !== 200) return;
      exportData.salesman = { label: nama, data: res.result };
      renderSubareaDetail(res.result);
    });
  }

  function loadSalesmanDetail(salesmanid, nama) {
    $titleDetailSalesman.text(nama + " (" + salesmanid + ")");
    $colDetailSalesman.html(
      `<p class="text-muted dashboard-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
    );
    $rowDetailSalesman.show();

    scrollTo($rowDetailSalesman);

    apiCall({ salesmanid }).done(function (res) {
      if (res.code !== 200) return;
      $btnExport.prop("disabled", false);
      exportData.visits = {
        label: nama + " (" + salesmanid + ")",
        data: res.result,
      };
      renderSalesmanVisits(res.result);
    });
  }

  function scrollTo($element) {
    $("html, body").animate({ scrollTop: $element.offset().top - 20 }, 400);
  }

  const datalabelsPlugin = {
    id: "datalabelsPlugin",
    afterDatasetsDraw: function (chart) {
      let ctx = chart.ctx;
      ctx.save();
      ctx.font = "bold 10px sans-serif";
      ctx.fillStyle = "#333";
      ctx.textAlign = "left";
      ctx.textBaseline = "middle";

      chart.data.datasets.forEach(function (dataset, datasetIndex) {
        if (datasetIndex !== 1 && datasetIndex !== 3) return;

        let meta = chart.getDatasetMeta(datasetIndex);
        meta.data.forEach(function (bar, index) {
          let rawDetails = chart.options.rawDetails;
          if (!rawDetails || !rawDetails[index]) return;

          let row = rawDetails[index];
          let target = 0;
          let actual = 0;
          if (datasetIndex === 1) {
            target = parseInt(row.target_planned) || 0;
            actual = parseInt(row.call_planned) || 0;
          } else if (datasetIndex === 3) {
            target = parseInt(row.target_visit) || 0;
            actual = parseInt(row.call_visit) || 0;
          }

          let pct = target > 0 ? Math.round((actual / target) * 100) : 0;
          let text = actual + "/" + target + " (" + pct + ")";

          if (bar.x !== undefined && !isNaN(bar.x)) {
            let xPos = bar.x + 5;
            let yPos = bar.y;
            ctx.fillText(text, xPos, yPos);
          }
        });
      });
      ctx.restore();
    },
  };

  function makeDatasets(rowOrDetails) {
    let details = Array.isArray(rowOrDetails)
      ? rowOrDetails
      : rowOrDetails.detail || [];

    let labels = details.map(function (d) {
      return d.tipe_sales;
    });

    let callPlannedData = details.map(function (d) {
      return parseInt(d.call_planned) || 0;
    });
    let remainingPlannedData = details.map(function (d) {
      let target = parseInt(d.target_planned) || 0;
      let actual = parseInt(d.call_planned) || 0;
      return Math.max(0, target - actual);
    });

    let callVisitData = details.map(function (d) {
      return parseInt(d.call_visit) || 0;
    });
    let remainingVisitData = details.map(function (d) {
      let target = parseInt(d.target_visit) || 0;
      let actual = parseInt(d.call_visit) || 0;
      return Math.max(0, target - actual);
    });

    return {
      labels: labels,
      rawDetails: details,
      datasets: [
        {
          label: "DUB (Actual)",
          data: callPlannedData,
          backgroundColor: "#3d85c6",
          stack: "DUB",
          barPercentage: 0.8,
          categoryPercentage: 0.8,
        },
        {
          label: "DUB (Target)",
          data: remainingPlannedData,
          backgroundColor: "#ff4e00",
          stack: "DUB",
          barPercentage: 0.8,
          categoryPercentage: 0.8,
        },
        {
          label: "Visit (Actual)",
          data: callVisitData,
          backgroundColor: "#3dc65dff",
          stack: "Visit",
          barPercentage: 0.8,
          categoryPercentage: 0.8,
        },
        {
          label: "Visit (Target)",
          data: remainingVisitData,
          backgroundColor: "#fcae06ff",
          stack: "Visit",
          barPercentage: 0.8,
          categoryPercentage: 0.8,
        },
      ],
    };
  }

  function makeTotals(data) {
    let map = {};
    data.forEach(function (region) {
      let details = region.detail || [];
      details.forEach(function (d) {
        let role = d.tipe_sales;
        if (!map[role]) {
          map[role] = {
            tipe_sales: role,
            total_sales: 0,
            call_planned: 0,
            call_visit: 0,
            target_planned: 0,
            target_visit: 0,
          };
        }
        map[role].total_sales += parseInt(d.total_sales) || 0;
        map[role].call_planned += parseInt(d.call_planned) || 0;
        map[role].call_visit += parseInt(d.call_visit) || 0;
        map[role].target_planned += parseInt(d.target_planned) || 0;
        map[role].target_visit += parseInt(d.target_visit) || 0;
      });
    });
    return Object.values(map);
  }

  function renderChart(canvasId, title, chartData) {
    if (chartInstances[canvasId]) chartInstances[canvasId].destroy();
    let ctx = document.getElementById(canvasId);
    if (!ctx) return;
    chartInstances[canvasId] = new Chart(ctx, {
      type: "bar",
      data: {
        labels: chartData.labels,
        datasets: chartData.datasets,
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        rawDetails: chartData.rawDetails,
        plugins: {
          legend: {
            position: "bottom",
          },
          tooltip: {
            mode: "index",
            intersect: false,
            filter: function (tooltipItem) {
              let chart = tooltipItem.chart;
              let active = chart.getActiveElements();
              if (active.length > 0) {
                let activeDatasetIndex = active[0].datasetIndex;
                let activeStack = chart.data.datasets[activeDatasetIndex].stack;
                return (
                  chart.data.datasets[tooltipItem.datasetIndex].stack ===
                  activeStack
                );
              }
              return true;
            },
          },
        },
        scales: {
          x: {
            stacked: true,
            beginAtZero: true,
            grace: "20%",
          },
          y: {
            stacked: true,
          },
        },
      },
      plugins: [datalabelsPlugin],
    });
  }

  function makeCard(canvasId, title, colClass, clickable) {
    let cardAttr = clickable
      ? 'class="dashboard-card clickable-card"'
      : 'class="dashboard-card"';
    let clickHelper = clickable
      ? ` <small><i class="fa fa-hand-pointer-o"></i> Klik untuk detail</small>`
      : "";
    return `
      <div class="${colClass || "col-md-12"} dashboard-col-padding">
        <div ${cardAttr} data-canvas="${canvasId}">
          <h5>
            ${title}${clickHelper}
          </h5>
          <div class="chart-container">
            <canvas id="${canvasId}"></canvas>
          </div>
        </div>
      </div>
    `;
  }

  function renderTop(data) {
    $chartsRow.empty();

    if (!paramsession || (paramsession && !paramsession.regionalid)) {
      $chartsRow.append(
        makeCard("chart_nasional", "Nasional", "col-md-4", false),
      );
      renderChart("chart_nasional", "Nasional", makeDatasets(makeTotals(data)));
    }

    data.forEach(function (row) {
      let canvasId = "chart_summary_" + row.id;
      $chartsRow.append(makeCard(canvasId, row.nama, "col-md-4", true));
      renderChart(canvasId, row.nama, makeDatasets(row));

      $(document)
        .off("click", '[data-canvas="' + canvasId + '"]')
        .on("click", '[data-canvas="' + canvasId + '"]', function () {
          loadRegionDetail(row.id, row.nama);
        });
    });
  }

  function renderDetail(data, regionalid) {
    $colDetail.empty();
    data.forEach(function (row) {
      let canvasId = "chart_detail_" + row.id;
      $colDetail.append(makeCard(canvasId, row.nama, "col-md-4", true));
      renderChart(canvasId, row.nama, makeDatasets(row));
      $(document)
        .off("click", '[data-canvas="' + canvasId + '"]')
        .on("click", '[data-canvas="' + canvasId + '"]', function () {
          loadAreaDetail(regionalid, row.id, row.nama);
        });
    });
  }

  function renderAreaDetail(data, regionalid, areaid) {
    $colDetailArea.empty();
    data.forEach(function (row) {
      let canvasId = "chart_area_" + row.id;
      $colDetailArea.append(makeCard(canvasId, row.nama, "col-md-4", true));
      renderChart(canvasId, row.nama, makeDatasets(row));
      $(document)
        .off("click", '[data-canvas="' + canvasId + '"]')
        .on("click", '[data-canvas="' + canvasId + '"]', function () {
          loadSubareaDetail(regionalid, areaid, row.id, row.nama);
        });
    });
  }

  function exportExcel() {
    let selectedTipeSales = $tipeSales.val();

    let hdr = [
      "Nama",
      "DUB (Actual)",
      "DUB (Target)",
      "Visit (Actual)",
      "Visit (Target)",
    ];
    let hdrSalesman = [
      "TPE ID",
      "Nama TPE",
      "Tipe Sales",
      "Jabatan",
      "Area",
      "DUB (Actual)",
      "DUB (Target)",
      "Visit (Actual)",
      "Visit (Target)",
    ];
    let rows = [];

    function toRows(data) {
      return data.map(function (r) {
        let t = {
          call_planned: 0,
          target_planned: 0,
          call_visit: 0,
          target_visit: 0,
        };
        if (r.detail) {
          r.detail.forEach(function (d) {
            if (
              selectedTipeSales &&
              d.tipe_sales &&
              selectedTipeSales.toLowerCase() != d.tipe_sales.toLowerCase()
            ) {
              return;
            }
            t.call_planned += parseInt(d.call_planned) || 0;
            t.target_planned += parseInt(d.target_planned) || 0;
            t.call_visit += parseInt(d.call_visit) || 0;
            t.target_visit += parseInt(d.target_visit) || 0;
          });
        }
        return [
          r.nama,
          t.call_planned,
          t.target_planned,
          t.call_visit,
          t.target_visit,
        ];
      });
    }

    function addSection(title, header, dataRows) {
      if (rows.length) rows.push([]);
      rows.push([title]);
      rows.push(header);
      dataRows.forEach(function (r) {
        rows.push(r);
      });
    }

    if (exportData.summary.length) {
      let totals = {
        call_planned: 0,
        target_planned: 0,
        call_visit: 0,
        target_visit: 0,
      };
      exportData.summary.forEach(function (region) {
        if (region.detail) {
          region.detail.forEach(function (d) {
            if (
              selectedTipeSales &&
              d.tipe_sales &&
              selectedTipeSales.toLowerCase() != d.tipe_sales.toLowerCase()
            ) {
              return;
            }
            totals.call_planned += parseInt(d.call_planned) || 0;
            totals.target_planned += parseInt(d.target_planned) || 0;
            totals.call_visit += parseInt(d.call_visit) || 0;
            totals.target_visit += parseInt(d.target_visit) || 0;
          });
        }
      });
      let nasional = [
        "Nasional",
        totals.call_planned,
        totals.target_planned,
        totals.call_visit,
        totals.target_visit,
      ];
      addSection("Summary", hdr, [nasional].concat(toRows(exportData.summary)));
    }
    if (exportData.area) {
      addSection(
        "Region: " + (exportData.area.label || "Region"),
        hdr,
        toRows(exportData.area.data),
      );
    }
    if (exportData.subarea) {
      addSection(
        "Area: " + (exportData.subarea.label || "Area"),
        hdr,
        toRows(exportData.subarea.data),
      );
    }
    if (exportData.salesman) {
      let filteredSalesmanData = exportData.salesman.data;
      if (selectedTipeSales) {
        filteredSalesmanData = filteredSalesmanData.filter(function (r) {
          return (
            r.tipe_sales &&
            selectedTipeSales.toLowerCase() == r.tipe_sales.toLowerCase()
          );
        });
      }
      addSection(
        "Sub Area: " + (exportData.salesman.label || "Sub Area"),
        hdrSalesman,
        filteredSalesmanData.map(function (r) {
          let area = r.nama_subarea || "";
          if (r.nama_area) area += ", " + r.nama_area;
          if (r.nama_regional) area += ", " + r.nama_regional;
          return [
            r.salesmanid,
            r.nama_salesman,
            r.tipe_sales,
            r.jabatan,
            area,
            r.call_planned,
            r.target_planned,
            r.call_visit,
            r.target_visit,
          ];
        }),
      );
    }

    let hdrVisit = [
      "Periode",
      "Nama TPE",
      "TPE ID",
      "Customer ID",
      "Nama Customer",
      "Check In",
      "Check Out",
      "Duration",
      "Keterangan",
      "Reason",
      "Tipe PIC",
      "Nama Professional",
      "Detailing Product",
      "Mulai Detailing",
      "Selesai Detailing",
      "Durasi Detailing",
      "Reason Detailing",
      "Keterangan Detailing",
    ];

    let planRows = [hdrVisit];
    let unplanRows = [hdrVisit];

    function visitRowsWithDetail(visits) {
      let result = [];
      (visits || []).forEach(function (r) {
        let parentData = [
          r.periode,
          r.nama_salesman,
          r.salesmanid,
          r.customerid,
          r.nama_customer,
          formatDateTime(r.check_in, 19, ""),
          formatDateTime(r.check_out, 19, ""),
          calculateDuration(r.check_in, r.check_out),
          r.keterangan || "",
          r.reason || "",
        ];

        if (r.detail_user && r.detail_user.length > 0) {
          r.detail_user.forEach(function (d) {
            result.push(
              parentData.concat([
                d.tipe_pic || "",
                d.professional_name || "",
                d.products
                  ? d.products.replace(/\|/g, ", ")
                  : d.array_product || "",
                formatDateTime(d.start_detailing, 19, ""),
                formatDateTime(d.end_detailing, 19, ""),
                calculateDuration(d.start_detailing, d.end_detailing),
                d.reason || "",
                d.keterangan || "",
              ]),
            );
          });
        } else {
          result.push(parentData.concat(["", "", "", "", "", "", "", ""]));
        }
      });
      return result;
    }

    let salesmanId = "";
    let salesmanName = "";
    let periodVal = $periode.val() || moment().format("MM yyyy");

    if (exportData.visits) {
      let vData = exportData.visits.data;
      if (vData && vData.length > 0) {
        let salesmanObj = vData[0];
        let firstVisit =
          (salesmanObj.planned && salesmanObj.planned[0]) ||
          (salesmanObj.unplanned && salesmanObj.unplanned[0]);
        if (firstVisit) {
          salesmanId = firstVisit.salesmanid || "";
          salesmanName = firstVisit.nama_salesman || "";
          periodVal = firstVisit.periode || periodVal;
        }
        if (!salesmanId && exportData.visits.label) {
          let label = exportData.visits.label;
          let match = label.match(/^(.*)\s*\((.*)\)$/);
          if (match) {
            salesmanName = match[1].trim();
            salesmanId = match[2].trim();
          }
        }

        planRows = planRows.concat(
          visitRowsWithDetail(salesmanObj.planned || salesmanObj.plan_outlet),
        );
        unplanRows = unplanRows.concat(
          visitRowsWithDetail(
            salesmanObj.unplanned || salesmanObj.unplan_outlet,
          ),
        );
      }
    }

    let hdrSpesialisasi = [
      "Periode",
      "TPE ID",
      "Nama TPE",
      "Spesialisasi",
      "Target",
      "Realisasi",
      "Kekurangan",
      "Pencapaian (%)",
    ];
    let spesialisasiRows = [hdrSpesialisasi];

    let hdrProduk = [
      "Periode",
      "TPE ID",
      "Nama TPE",
      "Product ID",
      "Nama Invoice",
      "Target",
      "Realisasi",
      "Kekurangan",
      "Pencapaian (%)",
    ];
    let produkRows = [hdrProduk];

    if (exportData.visits) {
      let vData = exportData.visits.data;
      if (vData && vData.length > 0) {
        let salesmanObj = vData[0];

        let specTargets = salesmanObj.spesialis_target || [];
        specTargets.forEach(function (t) {
          let targetVal = parseInt(t.target) || 0;
          let actualVal = parseInt(t.actual) || 0;
          let kekurangan = Math.max(0, targetVal - actualVal);
          let pct =
            targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";
          spesialisasiRows.push([
            periodVal,
            salesmanId,
            salesmanName,
            t.nama_spesialisasi || "",
            targetVal,
            actualVal,
            kekurangan,
            pct + "%",
          ]);
        });

        let prodTargets = salesmanObj.produk_target || [];
        prodTargets.forEach(function (t) {
          let targetVal = parseInt(t.target) || 0;
          let actualVal = parseInt(t.actual) || 0;
          let kekurangan = Math.max(0, targetVal - actualVal);
          let pct =
            targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";
          produkRows.push([
            periodVal,
            salesmanId,
            salesmanName,
            t.product_id || "",
            t.nama_invoice || "",
            targetVal,
            actualVal,
            kekurangan,
            pct + "%",
          ]);
        });
      }
    }

    let hdrSales = [
      "Periode",
      "TPE ID",
      "Nama TPE",
      "Target Nominal",
      "Realisasi",
      "Kekurangan",
      "Pencapaian (%)",
    ];
    let salesRows = [hdrSales];

    if (exportData.visits) {
      let vData = exportData.visits.data;
      if (vData && vData.length > 0) {
        let salesmanObj = vData[0];
        let t = salesmanObj.sales_target;
        if (t) {
          let targetVal = parseInt(t.target) || 0;
          let actualVal = parseInt(t.actual) || 0;
          let kekurangan = Math.max(0, targetVal - actualVal);
          let pct =
            targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";
          salesRows.push([
            periodVal,
            salesmanId,
            salesmanName,
            targetVal,
            actualVal,
            kekurangan,
            pct + "%",
          ]);
        }
      }
    }

    let wb = XLSX.utils.book_new();
    let wsSummary = XLSX.utils.aoa_to_sheet(rows);
    let wsPlan = XLSX.utils.aoa_to_sheet(planRows);
    let wsUnplan = XLSX.utils.aoa_to_sheet(unplanRows);
    let wsSpesialisasi = XLSX.utils.aoa_to_sheet(spesialisasiRows);
    let wsProduk = XLSX.utils.aoa_to_sheet(produkRows);
    let wsSales = XLSX.utils.aoa_to_sheet(salesRows);

    let period = $periode.val() || moment().format("MM yyyy");
    XLSX.utils.book_append_sheet(wb, wsSummary, "Summary");
    XLSX.utils.book_append_sheet(wb, wsPlan, "Planned");
    XLSX.utils.book_append_sheet(wb, wsUnplan, "Unplanned");
    XLSX.utils.book_append_sheet(wb, wsSpesialisasi, "Target Spesialisasi");
    XLSX.utils.book_append_sheet(wb, wsProduk, "Target Produk");
    XLSX.utils.book_append_sheet(wb, wsSales, "Target Sales");

    XLSX.writeFile(wb, "Dashboard_Activity_" + period + ".xlsx");
  }

  function renderSubareaDetail(data) {
    $colDetailSubarea.empty();

    let rows = data
      .map(function (r, i) {
        let area = r.nama_subarea || "";
        if (r.nama_area) area += ", " + r.nama_area;
        if (r.nama_regional) area += ", " + r.nama_regional;
        return `
          <tr class="clickable-row" data-salesmanid="${r.salesmanid}" data-nama="${r.nama_salesman || ""}">
            <td>${i + 1}</td>
            <td>
              ${val(r.nama_salesman)}<br>
              <small class="text-muted">${val(r.salesmanid)}</small>
              <small class="dashboard-hint"><i class="fa fa-hand-pointer-o"></i></small>
            </td>
            <td>${val(r.tipe_sales)}</td>
            <td>${val(r.jabatan)}</td>
            <td>${val(area)}</td>
            <td>${makeProgressBar(r.call_planned, r.target_planned)}</td>
            <td>${makeProgressBar(r.call_visit, r.target_visit)}</td>
          </tr>
        `;
      })
      .join("");

    $colDetailSubarea.append(`
      <div class="dashboard-col-padding">
        <div class="dashboard-card">
          <div class="table-responsive">
            <table id="tbl-salesman" class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama TPE</th>
                  <th>Tipe</th>
                  <th>Jabatan</th>
                  <th>Area</th>
                  <th class="th-planned">DUB</th>
                  <th class="th-visit">Visit</th>
                </tr>
              </thead>
              <tbody>
                ${rows || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>'}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `);

    $colDetailSubarea
      .find("#tbl-salesman tbody tr[data-salesmanid]")
      .on("click", function () {
        let salesmanid = $(this).data("salesmanid");
        let nama = $(this).data("nama");
        loadSalesmanDetail(salesmanid, nama);
      });
  }

  function renderSalesmanVisits(result) {
    $colDetailSalesman.empty();
    if (!result || result.length === 0) {
      $colDetailSalesman.append(
        `<p class="text-muted dashboard-loading">Tidak ada data salesman</p>`,
      );
      return;
    }
    let r = result[0];

    function buildSpesialisTargetHtml(targets) {
      if (!targets || targets.length == 0) {
        return `
          <div class="dashboard-card mt-16">
            <h4 class="card-header-custom">
              Target Spesialisasi
            </h4>
            <p class="text-muted text-center dashboard-loading">Tidak ada data spesialis target</p>
          </div>
        `;
      }

      let rowsHtml = "";

      targets.forEach(function (t) {
        let targetVal = parseInt(t.target) || 0;
        let actualVal = parseInt(t.actual) || 0;
        let kekurangan = Math.max(0, targetVal - actualVal);
        let pct =
          targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";

        let actualPct =
          targetVal > 0 ? Math.min(100, (actualVal / targetVal) * 100) : 0;
        let remainingPct = Math.max(0, 100 - actualPct);

        rowsHtml += `
          <tr>
            <td><strong>${val(t.nama_spesialisasi)}</strong></td>
            <td class="text-center">${targetVal}</td>
            <td class="text-center">${actualVal}</td>
            <td class="text-center">${kekurangan}</td>
            <td>
              <div class="table-progress-bar" title="Realisasi: ${actualVal} / Target: ${targetVal} (${Math.round(pct)}%)">
                ${actualPct > 0 ? `<div class="achievement-chart-bar-actual" style="width: ${actualPct}%;"></div>` : ""}
                ${remainingPct > 0 ? `<div class="achievement-chart-bar-remaining" style="width: ${remainingPct}%;"></div>` : ""}
                <div class="table-progress-bar-text">${Math.round(pct)}%</div>
              </div>
            </td>
          </tr>
        `;
      });

      return `
        <div class="dashboard-card mt-16">
          <h4 class="card-header-custom">
            Target Spesialisasi
          </h4>
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-target">
              <thead>
                <tr>
                  <th>Spesialis</th>
                  <th class="th-w-150">Target</th>
                  <th class="th-w-150">Realisasi</th>
                  <th class="th-w-150">Kekurangan</th>
                  <th class="th-w-graph">Grafik Pencapaian</th>
                </tr>
              </thead>
              <tbody>
                ${rowsHtml}
              </tbody>
            </table>
          </div>
        </div>
      `;
    }

    function buildProdukTargetHtml(targets) {
      if (!targets || targets.length === 0) {
        return `
          <div class="dashboard-card mt-24">
            <h4 class="card-header-custom">
              Target Produk
            </h4>
            <p class="text-muted text-center dashboard-loading">Tidak ada data produk target</p>
          </div>
        `;
      }

      let rowsHtml = "";

      targets.forEach(function (t) {
        let targetVal = parseInt(t.target) || 0;
        let actualVal = parseInt(t.actual) || 0;
        let kekurangan = Math.max(0, targetVal - actualVal);
        let pct =
          targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";

        let actualPct =
          targetVal > 0 ? Math.min(100, (actualVal / targetVal) * 100) : 0;
        let remainingPct = Math.max(0, 100 - actualPct);

        rowsHtml += `
          <tr>
            <td>
              <strong>${val(t.product_id)}</strong><br>
              <small class="text-muted">${val(t.nama_invoice)}</small>
            </td>
            <td class="text-center">${targetVal}</td>
            <td class="text-center">${actualVal}</td>
            <td class="text-center">${kekurangan}</td>
            <td>
              <div class="table-progress-bar" title="Realisasi: ${actualVal} / Target: ${targetVal} (${Math.round(pct)}%)">
                ${actualPct > 0 ? `<div class="achievement-chart-bar-actual" style="width: ${actualPct}%;"></div>` : ""}
                ${remainingPct > 0 ? `<div class="achievement-chart-bar-remaining" style="width: ${remainingPct}%;"></div>` : ""}
                <div class="table-progress-bar-text">${Math.round(pct)}%</div>
              </div>
            </td>
          </tr>
        `;
      });

      return `
        <div class="dashboard-card mt-24">
          <h4 class="card-header-custom">
            Target Produk
          </h4>
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-target">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th class="th-w-150">Target</th>
                  <th class="th-w-150">Realisasi</th>
                  <th class="th-w-150">Kekurangan</th>
                  <th class="th-w-graph">Grafik Pencapaian</th>
                </tr>
              </thead>
              <tbody>
                ${rowsHtml}
              </tbody>
            </table>
          </div>
        </div>
      `;
    }

    function buildSalesTargetHtml(salesTarget) {
      if (!salesTarget) {
        return `
          <div class="dashboard-card mt-16">
            <h4 class="card-header-custom">
              Target Sales
            </h4>
            <p class="text-muted text-center dashboard-loading">Tidak ada data sales target</p>
          </div>
        `;
      }

      let targetVal = parseInt(salesTarget.target) || 0;
      let actualVal = parseInt(salesTarget.actual) || 0;
      let kekurangan = Math.max(0, targetVal - actualVal);
      let pct =
        targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";

      let actualPct =
        targetVal > 0 ? Math.min(100, (actualVal / targetVal) * 100) : 0;
      let remainingPct = Math.max(0, 100 - actualPct);

      function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      let rowHtml = `
        <tr>
          <td><strong>Sales TPE</strong></td>
          <td class="text-right">Rp ${formatNumber(targetVal)}</td>
          <td class="text-right">Rp ${formatNumber(actualVal)}</td>
          <td class="text-right">Rp ${formatNumber(kekurangan)}</td>
          <td>
            <div class="table-progress-bar" title="Realisasi: Rp ${formatNumber(actualVal)} / Target: Rp ${formatNumber(targetVal)} (${Math.round(pct)}%)">
              ${actualPct > 0 ? `<div class="achievement-chart-bar-actual" style="width: ${actualPct}%;"></div>` : ""}
              ${remainingPct > 0 ? `<div class="achievement-chart-bar-remaining" style="width: ${remainingPct}%;"></div>` : ""}
              <div class="table-progress-bar-text">${Math.round(pct)}%</div>
            </div>
          </td>
        </tr>
      `;

      return `
        <div class="dashboard-card mt-16">
          <h4 class="card-header-custom">
            Target Sales
          </h4>
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-target">
              <thead>
                <tr>
                  <th>Target</th>
                  <th class="th-w-150 text-center">Target Nominal</th>
                  <th class="th-w-150 text-center">Realisasi</th>
                  <th class="th-w-150 text-center">Kekurangan</th>
                  <th class="th-w-graph text-center">Grafik Pencapaian</th>
                </tr>
              </thead>
              <tbody>
                ${rowHtml}
              </tbody>
            </table>
          </div>
        </div>
      `;
    }

    function buildVisitTable(visits, label, isPlanned) {
      let rowsHtml = "";
      if (!visits || visits.length === 0) {
        rowsHtml = `<tr><td colspan="8" class="text-center">Tidak ada data kunjungan</td></tr>`;
      } else {
        visits.forEach(function (v, idx) {
          let checkIn = formatDateTime(v.check_in, 16, "-");
          let checkOut = formatDateTime(v.check_out, 16, "-");
          let duration = calculateDuration(v.check_in, v.check_out);
          let customerStr = `<strong>${val(v.nama_customer)}</strong><br><small class="text-muted">${val(v.customerid)}</small>`;
          let btnDetail = `<button class="btn btn-xs btn-primary btn-visit-detail" type="button"><i class="fa fa-info-circle"></i> Detail</button>`;

          rowsHtml += `
            <tr>
              <td>${idx + 1}</td>
              <td>${customerStr}</td>
              <td>${checkIn}</td>
              <td>${checkOut}</td>
              <td>${duration}</td>
              <td>${val(v.keterangan)}</td>
              <td>${val(v.reason)}</td>
              <td class="text-center">${btnDetail}</td>
            </tr>`;
        });
      }

      let titleClass = isPlanned
        ? "visit-title-planned"
        : "visit-title-unplanned";
      let theadClass = isPlanned
        ? "visit-thead-planned"
        : "visit-thead-unplanned";

      let tableId = "tbl-" + label.toLowerCase().replace(/\s+/g, "-");
      let cardHtml = `
        <div class="dashboard-card mt-24">
          <h4 class="visit-title ${titleClass}">
            ${label} (${visits ? visits.length : 0})
          </h4>
          <div class="table-responsive">
            <table id="${tableId}" class="table table-bordered table-striped table-hover">
              <thead>
                <tr class="${theadClass}">
                  <th class="th-w-50">#</th>
                  <th>Customer</th>
                  <th class="th-w-150">Check In</th>
                  <th class="th-w-150">Check Out</th>
                  <th class="th-w-150">Duration</th>
                  <th>Keterangan</th>
                  <th>Reason</th>
                  <th class="th-w-150-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                ${rowsHtml}
              </tbody>
            </table>
          </div>
        </div>
      `;
      return cardHtml;
    }

    let plannedHtml = buildVisitTable(r.planned, "Planned Visit", true);
    let unplannedHtml = buildVisitTable(r.unplanned, "Unplanned Visit", false);

    let $plannedContainer = $(plannedHtml);
    let $unplannedContainer = $(unplannedHtml);

    if (r.planned && r.planned.length > 0) {
      $plannedContainer.find(".btn-visit-detail").each(function (idx) {
        $(this).data("visit", r.planned[idx]);
      });
    }
    if (r.unplanned && r.unplanned.length > 0) {
      $unplannedContainer.find(".btn-visit-detail").each(function (idx) {
        $(this).data("visit", r.unplanned[idx]);
      });
    }

    $colDetailSalesman.append(buildSalesTargetHtml(r.sales_target));
    $colDetailSalesman.append(buildSpesialisTargetHtml(r.spesialis_target));
    $colDetailSalesman.append(buildProdukTargetHtml(r.produk_target));
    $colDetailSalesman.append($plannedContainer);
    $colDetailSalesman.append($unplannedContainer);

    $colDetailSalesman.find(".btn-visit-detail").on("click", function (e) {
      e.preventDefault();
      let visitObj = $(this).data("visit");
      if (visitObj) {
        showVisitModal(visitObj);
      }
    });
  }

  function showVisitModal(r) {
    let infoRows = [
      [
        "Customer",
        `${val(r.nama_customer)} <small class="text-muted">(${val(r.customerid)})</small>`,
      ],
      ["Periode", val(r.periode)],
      [
        "TPE",
        `${val(r.nama_salesman)} <small class="text-muted">(${val(r.salesmanid)})</small>`,
      ],
      ["Check In", formatDateTime(r.check_in, 16, "-")],
      ["Check Out", formatDateTime(r.check_out, 16, "-")],
      ["Duration", calculateDuration(r.check_in, r.check_out)],
      ["Keterangan", val(r.keterangan)],
      ["Reason", val(r.reason)],
    ]
      .map(function (row) {
        return `
          <tr>
            <td class="modal-info-label">${row[0]}</td>
            <td>${row[1]}</td>
          </tr>
        `;
      })
      .join("");

    let detailTable = "";
    if (r.detail_user && r.detail_user.length > 0) {
      let detailRows = r.detail_user
        .map(function (d, i) {
          let productsHtml = "-";
          let rawProducts = d.products || d.array_product;
          if (rawProducts) {
            let separator = rawProducts.indexOf("|") !== -1 ? "|" : ",";
            let productList = rawProducts
              .split(separator)
              .map(function (item) {
                return `<li>${val(item)}</li>`;
              })
              .join("");
            productsHtml = `
              <ul class="modal-product-list">
                ${productList}
              </ul>
            `;
          }

          return `
            <tr>
              <td>${i + 1}</td>
              <td>${val(d.tipe_pic)}</td>
              <td>${val(d.professional_name)}</td>
              <td>${productsHtml}</td>
              <td>${formatDateTime(d.start_detailing, 16, "-")}</td>
              <td>${formatDateTime(d.end_detailing, 16, "-")}</td>
              <td>${val(d.reason)}</td>
              <td>${val(d.keterangan)}</td>
            </tr>
          `;
        })
        .join("");
      detailTable = `
        <h5 class="modal-section-title">Detailing</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-condensed">
            <thead class="modal-thead-blue">
              <tr>
                <th>#</th>
                <th>Tipe</th>
                <th>Professional</th>
                <th>Detailing Product</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Reason</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              ${detailRows}
            </tbody>
          </table>
        </div>
      `;
    }

    $modalVisitTitle.text(`${val(r.nama_customer)} — ${val(r.periode)}`);
    $modalVisitBody.html(`
      <table class="table table-bordered">
        <tbody>
          ${infoRows}
        </tbody>
      </table>
      ${detailTable}
    `);
    $modalVisitDetail.modal("show");
  }
});
