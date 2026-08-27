(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  common.setTitle('Dashboard Chart');
  const paramsession = common.getCookie("session");

  // ui components
  let uiChartProductivity = $('#chartProductivity');
  let uiChartPerformance = $('#chartPerformance');
  let uiChartMedrepCoverage = $('#chartMedrepCoverage');
  let uiChartPresensi = $('#chartPresensi');
  let labelProductivity = $('#labelProductivity');
  let labelPerformance = $('#labelPerformance');
  let labelMedrepCoverage = $('#labelMedrepCoverage');
  let labelPresensi = $('#labelPresensi');
  let chartProductivity, chartChartPerformance, chartMedrepCoverage, chartPresensi;

  let uiDateRange = $("#date_range");
  let uiSelectPeriode = $("#periode_id");
  let uiSelectSalesman = $("#salesmanid");
  let uiStartPeriode = $("#start_periode");
  let uiEndPeriode = $("#end_periode");
  let uiBtnLoadData = $("#btn_load_data");

  initializeParam();

  function loadChart() {
    const dateRange = getDateRange();
    $.ajax({
      type: 'POST',
      dataType: 'json',
      data: {
        ...dateRange,
        usersession: paramsession.username,
        salesmanid: uiSelectSalesman.val(),
        restrict_level: paramsession.restrict_level,
      },
      beforeSend: function () {
        common.loading();
      },
      url: common.baseURL('dashboard_chart/load'),
      success: function (data) {
        loadChartProductivity(data.productivity);
        loadChartPerformance(data.performance);
        loadChartMedrepCoverage(data.parma_coverage);
        loadChartPresensi(data.presensi);
        common.loadingClose();
      },
      error: function () {
        alert('Load failed');
        common.loadingClose();
      }
    });
  }

  function initializeParam() {
    common.loading();

    $(".datepicker").datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true,
    });

    uiSelectPeriode.select2({
      placeholder: 'Select Periode',
      allowClear: true
    });

    uiSelectSalesman.select2({
      placeholder: 'Select TPE',
      allowClear: true
    });

    uiBtnLoadData.click(function () {
      const periode = uiSelectPeriode.val();
      if (periode == '') {
        $.alert({
          title: 'Error ',
          content: 'Periode harus dipilih!',
          containerFluid: true
        });
      } else {
        loadChart();
      }
    });

    uiSelectPeriode.on('change', function (selected) {
      const val = selected.target.value;
      if (val == 'custom') uiDateRange.removeClass('hidden');
      else {
        uiDateRange.addClass('hidden');
        uiStartPeriode.val(null);
        uiEndPeriode.val(null);
      }
    });

    uiStartPeriode.on('changeDate', function (selected) {
      let endDate = new Date(selected.date.valueOf());
      endDate.setDate(endDate.getDate() + 90);
      uiEndPeriode.datepicker('setEndDate', endDate);
      if (uiStartPeriode.val() > uiEndPeriode.val()) {
        uiEndPeriode.val(uiStartPeriode.val());
      }
    });

    loadSalesman({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level
    });

    loadChart();
  }

  function formatDate(date) {
    const pad = (n) => String(n).padStart(2, "0");
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
  }

  function getDateRange() {
    const periode = uiSelectPeriode.val();
    const now = new Date();

    let start = new Date();
    let end = new Date();

    if (periode === "today") {
      labelProductivity.html('Hari Ini');
      labelPerformance.html('Hari Ini');
      labelPresensi.html('Hari Ini');
      start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      end = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    else if (periode === "week") {
      labelProductivity.html('Minggu Ini');
      labelPerformance.html('Minggu Ini');
      labelPresensi.html('Minggu Ini');
      start = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 6);
      end = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    else if (periode === "month") {
      labelProductivity.html('Bulan Ini');
      labelPerformance.html('Bulan Ini');
      labelPresensi.html('Bulan Ini');
      start = new Date(now.getFullYear(), now.getMonth(), 1);
      end = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    else if (periode === "year") {
      labelProductivity.html('Tahun Ini');
      labelPerformance.html('Tahun Ini');
      labelPresensi.html('Tahun Ini');
      start = new Date(now.getFullYear(), 0, 1);
      end = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    else if (periode === "custom") {
      const s = uiStartPeriode.val();
      const e = uiEndPeriode.val();
      const p = `${s} s/d ${e}`;
      labelProductivity.html(p);
      labelPerformance.html(p);
      labelPresensi.html(p);
      return { start: s, end: e };
    }

    return { start: formatDate(start), end: formatDate(end) };
  }

  function loadChartProductivity(data) {
    if (chartProductivity) chartProductivity.destroy();

    chartProductivity = new Chart(uiChartProductivity, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Productivity',
          data: data.data,
          borderColor: '#2670d8',
          backgroundColor: 'rgba(54,162,235,0.6)',
          borderWidth: 2,
          extraInfo: data.extraInfo,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            min: 0,
            max: 100,
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function (context) {
                let index = context.dataIndex;
                let info = context.dataset.extraInfo[index];
                let name = `👤 ${info.nama_salesman} (${info.city})`;
                return [
                  `${name}`,
                  `${'_'.repeat(name.length)}`,
                  `%FJP Compliance : ${context.parsed.y}%`,
                  `Kehadiran : Hadir(${info.hadir}) | Cuti(${info.cuti}), Sakit(${info.sakit})`,
                  `Keterangan : ${info.rrk_keterangan}`,
                  `Detailing : ${info.rrk_detailing}`,
                ];
              }
            }
          }
        }
      }
    });
  }

  function loadChartPerformance(data) {
    if (chartChartPerformance) chartChartPerformance.destroy();

    chartChartPerformance = new Chart(uiChartPerformance, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'Planned',
            borderColor: '#008d4c',
            backgroundColor: '#008d4c',
            data: data.data_schedule,
            extraInfo: data.extraInfo,
          },
          {
            label: 'Actual Planned',
            borderColor: '#00de93',
            backgroundColor: '#00de93',
            data: data.data_call,
            extraInfo: data.extraInfo,
          },
          {
            label: 'Unplanned',
            borderColor: '#f82347',
            backgroundColor: '#f82347',
            data: data.data_extra,
            extraInfo: data.extraInfo,
          },
          {
            label: 'Order',
            borderColor: '#2a60f7',
            backgroundColor: '#2a60f7',
            data: data.data_order,
            extraInfo: data.extraInfo,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, min: 0 }
        },
        plugins: {
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.85)',
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            padding: 12,
            displayColors: false,
            callbacks: {
              label: function () { return ''; },

              afterBody: function (context) {
                let ctx = context[0];
                let idx = ctx.dataIndex;
                let ds = ctx.dataset;
                let extra = ds.extraInfo[idx];

                let schedule = ctx.chart.data.datasets[0].data[idx];
                let call = ctx.chart.data.datasets[1].data[idx];
                let extraC = ctx.chart.data.datasets[2].data[idx];
                let order = ctx.chart.data.datasets[3].data[idx];
                let name = `👤 ${extra.nama_salesman} (${extra.city})`;

                return [
                  `${name}`,
                  `${'_'.repeat(name.length)}`,
                  `📌 Planned : ${schedule}`,
                  `📞 Actual Planned : ${call}`,
                  `➕ Unplanned : ${extraC}`,
                  `🛒 Order : ${order} ${extra.total_order == '0' ? '' : `(${extra.total_order})`}`,
                ];
              }
            }
          }
        }
      },
    });
  }

  function loadChartMedrepCoverage(data) {
    if (chartMedrepCoverage) chartMedrepCoverage.destroy();

    const legendFooterPlugin = {
      id: "legendFooterPlugin",
      afterDatasetsDraw(chart, args, opts) {
        if (!opts || !opts.showFooter) return;

        const { ctx, legend } = chart;
        if (!legend) return;

        const items = legend.legendItems;
        if (!items.length) return;

        const lastBox = legend.legendHitBoxes[items.length - 1];
        if (!lastBox) return;

        ctx.save();
        ctx.font = "bold 13px sans-serif";
        ctx.fillStyle = "#444";

        const x = legend.left + 20;
        const y = lastBox.top + lastBox.height + 30;

        ctx.fillText(opts.footerText, x, y);
        ctx.restore();
      }
    }

    Chart.register(legendFooterPlugin);

    chartMedrepCoverage = new Chart(uiChartMedrepCoverage, {
      type: 'pie',
      data: {
        labels: data.labels,
        datasets: [{
          data: data.counts,
          backgroundColor: [
            '#3498db', '#2ecc71', '#e67e22', '#9b59b6',
            '#e74c3c', '#16a085', '#f1c40f', '#34495e'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: { left: 10, right: 10 }
        },
        plugins: {
          legend: {
            position: 'right',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              padding: 15
            }
          },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                let idx = ctx.dataIndex;
                let list = data.details[idx];

                let result = [];
                list.forEach(s => {
                  result.push(`${s.salesmanid} - ${s.nama} (${s.city})`);
                });

                return result;
              },

              afterBody: function (ctx) {
                let idx = ctx[0].dataIndex;
                let total = data.counts[idx];

                return [`Total : ${total} medrep`];
              }
            }
          },
          legendFooterPlugin: {
            showFooter: true,
            footerText: `Total: ${data.total_parma}`
          },
        }
      },
    });
  }

  function loadChartPresensi(data) {
    let built = buildMultiSeriesDataset(data);
    if (chartPresensi) chartPresensi.destroy();

    chartPresensi = new Chart(uiChartPresensi, {
      type: 'line',
      data: {
        labels: built.labels,
        datasets: built.datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: "Durasi (jam)"
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function () { return ""; },

              afterBody: function (ctx) {
                let ds = ctx[0].dataset;
                let idx = ctx[0].dataIndex;
                let info = ds.extraInfo[idx];

                if (!info) return ["Tidak ada data presensi"];

                let tanggal = new Date(info.tanggal).toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "long",
                  year: "numeric"
                });

                return [
                  `${info.nama_salesman} (${info.city})`,
                  `${tanggal}`,
                  `Check-in  : ${info.checkin}`,
                  `Check-out : ${info.checkout}`,
                  `Durasi    : ${info.durasi}`,
                ];
              }
            }
          }
        }
      }
    });
  }

  function randomColor() {
    return `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`;
  }

  function convertDurasiToHour(str) {
    let jam = 0, menit = 0, detik = 0;

    if (str.includes("jam")) {
      jam = parseInt(str.split("jam")[0]);
    }
    if (str.includes("menit")) {
      menit = parseInt(str.split("jam")[1].split("menit")[0]);
    }
    if (str.includes("detik")) {
      detik = parseInt(str.split("menit")[1]);
    }

    return jam + (menit / 60) + (detik / 3600);
  }

  function buildMultiSeriesDataset(response) {
    let labels = response.labels;
    let raw = response.data;
    let datasets = [];
    let salesmanList = new Set();

    labels.forEach(date => {
      if (raw[date]) {
        Object.keys(raw[date]).forEach(sid => {
          salesmanList.add(sid);
        });
      }
    });

    salesmanList.forEach(sid => {
      let dataPoints = [];
      let extraInfo = [];

      labels.forEach(date => {
        if (raw[date][sid]) {
          let d = raw[date][sid];

          let val = d.durasi_jam.includes("jam")
            ? convertDurasiToHour(d.durasi_jam)
            : 0;

          dataPoints.push(val);

          extraInfo.push({
            tanggal: date,
            nama_salesman: d.nama_salesman,
            city: d.city,
            checkin: d.checkin,
            checkout: d.checkout,
            durasi: d.durasi_jam
          });
        } else {
          dataPoints.push(0);
          extraInfo.push(null);
        }
      });

      let color = randomColor();
      datasets.push({
        label: sid,
        data: dataPoints,
        borderColor: color,
        backgroundColor: color,
        borderWidth: 2,
        tension: 0.3,
        pointRadius: 4,
        extraInfo: extraInfo
      });
    });

    return { labels, datasets };
  }

  function loadSalesman(data) {
    common.loading();
    $.post(common.baseURL("api_v1/call_salesman"), {
      idjabatan: data.idjabatan,
      usersession: data.usersession,
      restrict_level: data.restrict_level
    }, function (res) {
      uiSelectSalesman.empty();
      uiSelectSalesman.select2({
        placeholder: "Select TPE",
        allowClear: true,
        data: $.map(res.result, function (o) {
          o.id = o.salesmanid;
          o.text = o.salesmanid + " - " + o.nama_salesman + " - " + o.tipe_sales;
          return o;
        }),
      });

      uiSelectSalesman.val(null).trigger('change');
      common.loadingClose();
    });
  }
})();
