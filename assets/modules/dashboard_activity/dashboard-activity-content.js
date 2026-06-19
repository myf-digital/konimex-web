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
      <div style="width: 100%; min-width: 140px; background-color: #ff4e00; border-radius: 4px; overflow: hidden; height: 18px; position: relative;">
        <div style="width: ${barPct}%; background-color: #0b5394; height: 100%; transition: width 0.3s;"></div>
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; text-align: center; font-size: 11px; color: #fff; line-height: 18px; font-weight: bold; text-shadow: 0px 0px 2px rgba(0,0,0,0.5);">
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

  function initialize() {
    if (paramsession) {
      if (paramsession.subareaid) {
        loadSubareaDetail(
          paramsession.regionalid,
          paramsession.areaid,
          paramsession.subareaid,
          paramsession.nama_subarea,
        );
      } else if (paramsession.areaid) {
        loadAreaDetail(
          paramsession.regionalid,
          paramsession.areaid,
          paramsession.nama_area,
        );
      } else if (paramsession.regionalid) {
        loadRegionDetail(paramsession.regionalid, paramsession.nama_regional);
      } else {
        loadSummary();
      }

      if (["medrep", "mrc"].includes(paramsession.role_name.toLowerCase())) {
        $divTipeSales.hide();
      } else {
        loadTipeSales();
      }
    } else {
      loadTipeSales();
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
    initialize();
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
      `<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
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
      `<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
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
      `<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
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
      `<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
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
          label: "Planned DUB (Actual)",
          data: callPlannedData,
          backgroundColor: "#3d85c6",
          stack: "Planned DUB",
          barPercentage: 0.8,
          categoryPercentage: 0.8,
        },
        {
          label: "Planned DUB (Target)",
          data: remainingPlannedData,
          backgroundColor: "#ff4e00",
          stack: "Planned DUB",
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
    let style = "padding-left:10px;padding-right:10px;";
    let cardStyle = clickable
      ? "cursor:pointer;transition:box-shadow .2s;"
      : "";
    let cardAttr = clickable
      ? 'class="dashboard-card clickable-card"'
      : 'class="dashboard-card"';
    let clickHelper = clickable
      ? ` <small style="font-size:11px;color:#aaa;font-weight:normal;"><i class="fa fa-hand-pointer-o"></i> Klik untuk detail</small>`
      : "";
    return `
      <div class="${colClass || "col-md-12"}" style="${style}">
        <div ${cardAttr} style="${cardStyle}" data-canvas="${canvasId}">
          <h5 style="margin:0 0 12px;font-weight:600;">
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
    let hdr = [
      "Nama",
      "Planned DUB (Actual)",
      "Planned DUB (Target)",
      "Visit (Actual)",
      "Visit (Target)",
    ];
    let hdrSalesman = [
      "Salesman ID",
      "Nama Salesman",
      "Tipe Sales",
      "Jabatan",
      "Planned DUB (Actual)",
      "Planned DUB (Target)",
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
      addSection(
        "Subarea: " + (exportData.salesman.label || "Subarea"),
        hdrSalesman,
        exportData.salesman.data.map(function (r) {
          return [
            r.salesmanid,
            r.nama_salesman,
            r.tipe_sales,
            r.jabatan,
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
      "Nama Salesman",
      "Salesman ID",
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

    if (exportData.visits) {
      let vData = exportData.visits.data;
      if (vData && vData.length > 0) {
        let salesmanObj = vData[0];
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

    let wb = XLSX.utils.book_new();
    let wsSummary = XLSX.utils.aoa_to_sheet(rows);
    let wsPlan = XLSX.utils.aoa_to_sheet(planRows);
    let wsUnplan = XLSX.utils.aoa_to_sheet(unplanRows);

    let period = $periode.val() || moment().format("MM yyyy");
    XLSX.utils.book_append_sheet(wb, wsSummary, "Summary");
    XLSX.utils.book_append_sheet(wb, wsPlan, "Planned");
    XLSX.utils.book_append_sheet(wb, wsUnplan, "Unplanned");

    XLSX.writeFile(wb, "PJP_Daily_" + period + ".xlsx");
  }

  function renderSubareaDetail(data) {
    $colDetailSubarea.empty();

    let rows = data
      .map(function (r, i) {
        return `
          <tr style="cursor:pointer;" data-salesmanid="${r.salesmanid}" data-nama="${r.nama_salesman || ""}">
            <td>${i + 1}</td>
            <td>
              ${val(r.nama_salesman)}<br>
              <small class="text-muted">${val(r.salesmanid)}</small>
              <small style="color:#aaa;font-size:10px;"><i class="fa fa-hand-pointer-o"></i></small>
            </td>
            <td>${val(r.tipe_sales)}</td>
            <td>${val(r.jabatan)}</td>
            <td>${makeProgressBar(r.call_planned, r.target_planned)}</td>
            <td>${makeProgressBar(r.call_visit, r.target_visit)}</td>
          </tr>
        `;
      })
      .join("");

    $colDetailSubarea.append(`
      <div style="padding-left:10px;padding-right:10px;">
        <div class="dashboard-card">
          <div class="table-responsive">
            <table id="tbl-salesman" class="table table-bordered table-striped table-hover" style="margin-bottom:0;">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama Salesman</th>
                  <th>Tipe</th>
                  <th>Jabatan</th>
                  <th style="width: 180px; text-align: center;">Planned</th>
                  <th style="width: 180px; text-align: center;">Visit</th>
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
        `<p class="text-muted" style="padding:10px;">Tidak ada data salesman</p>`,
      );
      return;
    }
    let r = result[0];

    function buildVisitTable(visits, label, headerColor) {
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

      let tableId = "tbl-" + label.toLowerCase().replace(/\s+/g, "-");
      let cardHtml = `
        <div class="dashboard-card" style="margin-top: 24px;">
          <h4 style="margin: 0 0 16px; font-weight: bold; color: ${headerColor}; border-bottom: 2px solid #eee; padding-bottom: 8px;">
            ${label} (${visits ? visits.length : 0})
          </h4>
          <div class="table-responsive">
            <table id="${tableId}" class="table table-bordered table-striped table-hover" style="margin-bottom:0;">
              <thead>
                <tr style="background-color: ${headerColor}; color: white;">
                  <th style="width: 50px;">#</th>
                  <th>Customer</th>
                  <th style="width: 150px;">Check In</th>
                  <th style="width: 150px;">Check Out</th>
                  <th style="width: 100px;">Duration</th>
                  <th>Keterangan</th>
                  <th>Reason</th>
                  <th style="width: 100px; text-align: center;">Aksi</th>
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

    let plannedHtml = buildVisitTable(r.planned, "Planned Visit", "#0b5394");
    let unplannedHtml = buildVisitTable(
      r.unplanned,
      "Unplanned Visit",
      "#3d85c6",
    );

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
        "Salesman",
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
            <td style="width:35%;font-weight:600;background:#f9f9f9;">${row[0]}</td>
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
              <ul style="margin:0; padding-left:16px;">
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
        <h5 style="font-weight:600;margin:16px 0 8px;border-left:4px solid #0073b7;padding-left:8px;">Detailing</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-condensed" style="margin-bottom:0;">
            <thead style="background:#0073b7;color:#fff;">
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
      <table class="table table-bordered" style="margin-bottom:0;">
        <tbody>
          ${infoRows}
        </tbody>
      </table>
      ${detailTable}
    `);
    $modalVisitDetail.modal("show");
  }
});
