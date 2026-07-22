$(function () {
  let common = new Common();
  let paramsession = common.getCookie("session");
  let baseUrlApi = typeof URL_API !== "undefined" ? URL_API : "";
  let API_URL = baseUrlApi + "/api_v1/dashboard_spesialisasi_target";
  let chartInstances = {};
  let listSalesmanVisit = [];

  let activeFilters = {
    regionalid: "",
    areaid: "",
    subareaid: "",
  };

  let exportData = {
    summary: [],
    area: null,
    subarea: null,
    subarea_detail: null,
    visits: null,
  };

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
          ${actual} / ${target} (${pct}%)
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
        activeFilters.regionalid = regionalIds;
        activeFilters.areaid = areaIds;
        activeFilters.subareaid = subareaIds;
        loadSubareaDetail(regionalIds, areaIds, subareaIds, namaSubarea);
      } else if (restrictLevel == 3 && areaIds) {
        activeFilters.regionalid = regionalIds;
        activeFilters.areaid = areaIds;
        loadAreaDetail(regionalIds, areaIds, namaArea);
      } else if (restrictLevel == 2 && regionalIds) {
        activeFilters.regionalid = regionalIds;
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
    activeFilters = { regionalid: "", areaid: "", subareaid: "" };
    exportData = {
      summary: [],
      area: null,
      subarea: null,
      subarea_detail: null,
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

    if (activeFilters.regionalid)
      formData.append("regionalid", activeFilters.regionalid);
    if (activeFilters.areaid) formData.append("areaid", activeFilters.areaid);
    if (activeFilters.subareaid)
      formData.append("subareaid", activeFilters.subareaid);

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
    activeFilters.regionalid = regionalid;
    activeFilters.areaid = "";
    activeFilters.subareaid = "";

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
      exportData.subarea_detail = null;
      exportData.visits = null;
      renderDetail(res.result, regionalid);
    });
  }

  function loadAreaDetail(regionalid, areaid, nama) {
    activeFilters.regionalid = regionalid;
    activeFilters.areaid = areaid;
    activeFilters.subareaid = "";

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
      exportData.subarea_detail = null;
      exportData.visits = null;
      renderAreaDetail(res.result, regionalid, areaid);
    });
  }

  function loadSubareaDetail(regionalid, areaid, subareaid, nama) {
    activeFilters.regionalid = regionalid;
    activeFilters.areaid = areaid;
    activeFilters.subareaid = subareaid;

    $titleDetailSubarea.text(nama);
    $colDetailSubarea.html(
      `<p class="text-muted dashboard-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
    );
    $rowDetailSubarea.show();
    $rowDetailSalesman.hide();

    scrollTo($rowDetailSubarea);

    apiCall({ regionalid, areaid, subareaid }).done(function (res) {
      if (res.code !== 200) return;
      exportData.subarea_detail = { label: nama, data: res.result };
      exportData.visits = null;
      renderSubareaDetail(res.result);
    });
  }

  function loadSpecializationDetail(spesialisasiId, nama) {
    $titleDetailSalesman.text("List TPE - Spesialisasi: " + nama);
    $colDetailSalesman.html(
      `<p class="text-muted dashboard-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</p>`,
    );
    $rowDetailSalesman.show();

    scrollTo($rowDetailSalesman);

    apiCall({ spesialisasi_id: spesialisasiId }).done(function (res) {
      if (res.code !== 200) return;
      $btnExport.prop("disabled", false);
      exportData.visits = {
        label: "Spesialisasi " + nama,
        data: res.result,
      };
      renderSpecializationSalesmen(res.result);
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
        if (datasetIndex !== 1) return;

        let meta = chart.getDatasetMeta(datasetIndex);
        meta.data.forEach(function (bar, index) {
          let rawDetails = chart.options.rawDetails;
          if (!rawDetails || !rawDetails[index]) return;

          let row = rawDetails[index];
          let target = parseInt(row.target) || 0;
          let actual = parseInt(row.actual) || 0;

          let pct = target > 0 ? Math.round((actual / target) * 100) : 0;
          let text = actual + "/" + target + " (" + pct + "%)";

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

    let actualData = details.map(function (d) {
      return parseInt(d.actual) || 0;
    });
    let remainingData = details.map(function (d) {
      let target = parseInt(d.target) || 0;
      let actual = parseInt(d.actual) || 0;
      return Math.max(0, target - actual);
    });

    return {
      labels: labels,
      rawDetails: details,
      datasets: [
        {
          label: "Aktual",
          data: actualData,
          backgroundColor: "#3d85c6",
          stack: "TargetSp",
          barPercentage: 0.8,
          categoryPercentage: 0.8,
        },
        {
          label: "Target",
          data: remainingData,
          backgroundColor: "#ff4e00",
          stack: "TargetSp",
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
            actual: 0,
            target: 0,
          };
        }
        map[role].total_sales += parseInt(d.total_sales) || 0;
        map[role].actual += parseInt(d.actual) || 0;
        map[role].target += parseInt(d.target) || 0;
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

  function renderSubareaDetail(data) {
    $colDetailSubarea.empty();

    let rows = data
      .map(function (r, i) {
        let targetVal = parseInt(r.target) || 0;
        let actualVal = parseInt(r.actual) || 0;
        return `
          <tr class="clickable-row" data-spesialisasi-id="${r.spesialisasi_id}" data-nama="${r.nama_spesialisasi || ""}">
            <td>${i + 1}</td>
            <td>
              <strong>${val(r.nama_spesialisasi)}</strong>
              <small class="dashboard-hint"><i class="fa fa-hand-pointer-o"></i></small>
            </td>
            <td>${targetVal}</td>
            <td>${actualVal}</td>
            <td>${makeProgressBar(actualVal, targetVal)}</td>
          </tr>
        `;
      })
      .join("");

    $colDetailSubarea.append(`
      <div class="dashboard-col-padding">
        <div class="dashboard-card">
          <div class="table-responsive">
            <table id="tbl-spesialisasi" class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Spesialisasi</th>
                  <th>Target</th>
                  <th>Realisasi</th>
                  <th>Pencapaian</th>
                </tr>
              </thead>
              <tbody>
                ${rows || '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>'}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `);

    $colDetailSubarea
      .find("#tbl-spesialisasi tbody tr[data-spesialisasi-id]")
      .on("click", function () {
        let spesialisasiId = $(this).data("spesialisasi-id");
        let nama = $(this).data("nama");
        loadSpecializationDetail(spesialisasiId, nama);
      });
  }

  function renderSpecializationSalesmen(result) {
    listSalesmanVisit = [];
    $colDetailSalesman.empty();
    if (!result || result.length === 0) {
      $colDetailSalesman.append(
        `<p class="text-muted dashboard-loading">Tidak ada data salesman</p>`,
      );
      return;
    }

    listSalesmanVisit = result;
    let rowsHtml = result
      .map(function (sm, i) {
        let targetVal = parseInt(sm.target) || 0;
        let actualVal = parseInt(sm.actual) || 0;
        let btnDetail = `
          <button 
            class="btn btn-xs btn-primary btn-salesman-sp-detail" 
            type="button"
            data-salesmanid="${sm.salesmanid}"
          >
            <i class="fa fa-info-circle"></i> Detail Visit
          </button>
        `;

        return `
          <tr>
            <td>${i + 1}</td>
            <td><strong>${val(sm.nama_salesman)}</strong><br><small class="text-muted">${val(sm.salesmanid)}</small></td>
            <td>${val(sm.tipe_sales)}</td>
            <td>${targetVal}</td>
            <td>${actualVal}</td>
            <td>${makeProgressBar(actualVal, targetVal)}</td>
            <td class="text-center">${btnDetail}</td>
          </tr>
        `;
      })
      .join("");

    let tableHtml = `
      <div class="dashboard-card">
        <div class="table-responsive">
          <table id="tbl-salesman-sp" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="th-w-50">#</th>
                <th>Nama TPE</th>
                <th>Tipe</th>
                <th>Target</th>
                <th>Realisasi</th>
                <th>Pencapaian</th>
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

    let $tableContainer = $(tableHtml);
    $colDetailSalesman.append($tableContainer);

    $colDetailSalesman
      .find(".btn-salesman-sp-detail")
      .on("click", function (e) {
        e.preventDefault();
        let salesmanid = $(this).attr("data-salesmanid");
        let salesman = listSalesmanVisit.find(
          (sm) => sm.salesmanid === salesmanid,
        );
        if (salesman) {
          showSpecializationVisitsModal(salesman);
        }
      });
  }

  function showSpecializationVisitsModal(sm) {
    let rowsHtml = "";
    let visits = sm.visits || [];
    if (visits.length === 0) {
      rowsHtml = `<tr><td colspan="8" class="text-center">Tidak ada kunjungan untuk spesialisasi ini</td></tr>`;
    } else {
      visits.forEach(function (v, idx) {
        let checkIn = formatDateTime(v.check_in, 16, "-");
        let checkOut = formatDateTime(v.check_out, 16, "-");
        let duration = calculateDuration(v.check_in, v.check_out);
        rowsHtml += `
          <tr>
            <td>${idx + 1}</td>
            <td>
              <strong>${val(v.nama_professional)}</strong><br>
              <small class="text-muted">${val(v.tipe_pic)}</small>
            </td>
            <td>${val(v.spesialisasi)}</td>
            <td>${val(v.nama_customer)}</td>
            <td>${checkIn}</td>
            <td>${checkOut}</td>
            <td>${duration}</td>
            <td>${val(v.keterangan)}</td>
          </tr>
        `;
      });
    }

    $modalVisitTitle.html(
      `Kunjungan TPE: ${sm.nama_salesman} (${sm.salesmanid})`,
    );
    $modalVisitBody.html(`
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="modal-thead-blue">
            <tr>
              <th>#</th>
              <th>Nama Professional</th>
              <th>Spesialisasi</th>
              <th>Outlet</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th>Duration</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            ${rowsHtml}
          </tbody>
        </table>
      </div>
    `);
    $modalVisitDetail.modal("show");
  }

  function exportExcel() {
    let selectedTipeSales = $tipeSales.val();
    let periodVal = $periode.val() || moment().format("MM yyyy");

    let hdr = ["Nama", "Realisasi", "Target", "Pencapaian (%)"];
    let rows = [];

    function toRows(data) {
      return data.map(function (r) {
        let t = { actual: 0, target: 0 };
        if (r.detail) {
          r.detail.forEach(function (d) {
            if (
              selectedTipeSales &&
              d.tipe_sales &&
              selectedTipeSales.toLowerCase() != d.tipe_sales.toLowerCase()
            )
              return;
            t.actual += parseInt(d.actual) || 0;
            t.target += parseInt(d.target) || 0;
          });
        }
        let pct =
          t.target > 0 ? ((t.actual / t.target) * 100).toFixed(2) : "0.00";
        return [r.nama, t.actual, t.target, pct + "%"];
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

    if (exportData.summary && exportData.summary.length) {
      let totals = { actual: 0, target: 0 };
      exportData.summary.forEach(function (region) {
        if (region.detail) {
          region.detail.forEach(function (d) {
            if (
              selectedTipeSales &&
              d.tipe_sales &&
              selectedTipeSales.toLowerCase() != d.tipe_sales.toLowerCase()
            )
              return;
            totals.actual += parseInt(d.actual) || 0;
            totals.target += parseInt(d.target) || 0;
          });
        }
      });
      let pct =
        totals.target > 0
          ? ((totals.actual / totals.target) * 100).toFixed(2)
          : "0.00";
      let nasional = ["Nasional", totals.actual, totals.target, pct + "%"];
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
    if (exportData.subarea_detail) {
      addSection(
        "Sub Area: " + (exportData.subarea_detail.label || "Sub Area"),
        ["Spesialisasi", "Realisasi", "Target", "Pencapaian (%)"],
        exportData.subarea_detail.data.map(function (r) {
          let targetVal = parseInt(r.target) || 0;
          let actualVal = parseInt(r.actual) || 0;
          let pct =
            targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";
          return [r.nama_spesialisasi || "", actualVal, targetVal, pct + "%"];
        }),
      );
    }

    if (exportData.visits) {
      let filteredSalesmanData = exportData.visits.data || [];
      if (selectedTipeSales) {
        filteredSalesmanData = filteredSalesmanData.filter(function (r) {
          return (
            r.tipe_sales &&
            selectedTipeSales.toLowerCase() == r.tipe_sales.toLowerCase()
          );
        });
      }

      let hdrSalesman = [
        "TPE ID",
        "Nama TPE",
        "Tipe Sales",
        "Target",
        "Realisasi",
        "Pencapaian (%)",
      ];

      addSection(
        "List TPE - " + (exportData.visits.label || "Spesialisasi"),
        hdrSalesman,
        filteredSalesmanData.map(function (r) {
          let targetVal = parseInt(r.target) || 0;
          let actualVal = parseInt(r.actual) || 0;
          let pct =
            targetVal > 0 ? ((actualVal / targetVal) * 100).toFixed(2) : "0.00";
          return [
            r.salesmanid,
            r.nama_salesman,
            r.tipe_sales,
            targetVal,
            actualVal,
            pct + "%",
          ];
        }),
      );
    }

    let detailVisitRows = [];
    if (exportData.visits) {
      let filteredSalesmanData = exportData.visits.data || [];
      if (selectedTipeSales) {
        filteredSalesmanData = filteredSalesmanData.filter(function (r) {
          return (
            r.tipe_sales &&
            selectedTipeSales.toLowerCase() == r.tipe_sales.toLowerCase()
          );
        });
      }

      let hdrVisit = [
        "Nama TPE",
        "TPE ID",
        "Nama Professional",
        "Tipe PIC",
        "Spesialisasi",
        "Outlet",
        "Check In",
        "Check Out",
        "Duration",
        "Keterangan",
      ];
      detailVisitRows.push(hdrVisit);

      filteredSalesmanData.forEach(function (sm) {
        let visits = sm.visits || [];
        visits.forEach(function (v) {
          detailVisitRows.push([
            sm.nama_salesman || "",
            sm.salesmanid || "",
            v.nama_professional || "",
            v.tipe_pic || "",
            v.spesialisasi || "",
            v.nama_customer || "",
            formatDateTime(v.check_in, 19, ""),
            formatDateTime(v.check_out, 19, ""),
            calculateDuration(v.check_in, v.check_out),
            v.keterangan || "",
          ]);
        });
      });
    }

    let wb = XLSX.utils.book_new();
    let wsSummary = XLSX.utils.aoa_to_sheet(rows);
    XLSX.utils.book_append_sheet(wb, wsSummary, "Summary");

    if (exportData.visits && detailVisitRows.length > 1) {
      let wsDetail = XLSX.utils.aoa_to_sheet(detailVisitRows);
      XLSX.utils.book_append_sheet(wb, wsDetail, "Detail Visit");
    }

    XLSX.writeFile(wb, "Dashboard_Spesialisasi_" + periodVal + ".xlsx");
  }
});
