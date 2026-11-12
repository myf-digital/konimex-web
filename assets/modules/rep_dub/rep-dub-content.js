(function () {
  const common = new Common();
  common.setTitle("Report DUB");

  const paramsession = common.getCookie("session");
  const $periode = $("#periode");
  const $week = $("#week");
  const $salesman = $("#salesmanid");
  const $btnPreview = $("#btn-preview-form");
  const $btnDownload = $("#btn-download-form");
  const $activeBadge = $("#active-date-badge");

  initialize();

  function initialize() {
    setupFilters();
    setupEvents();
  }

  function setupFilters() {
    $periode
      .datepicker({
        format: "yyyy-mm",
        startView: "months",
        minViewMode: "months",
        autoclose: true,
      })
      .datepicker("setDate", new Date());

    $week.select2({
      minimumResultsForSearch: Infinity,
    });

    $.post(
      common.baseURL("rep_dub/load_salesman"),
      {
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
      },
      function (res) {
        const rows = (res && res.rows) || [];
        const options = rows.map(function (o) {
          return {
            id: o.salesmanid,
            text:
              o.salesmanid +
              " - " +
              o.nama_salesman +
              (o.tipe_sales ? " (" + o.tipe_sales + ")" : ""),
          };
        });

        $salesman.select2({
          placeholder: "Pilih Salesman / TPE",
          allowClear: true,
          data: options,
        });

        $salesman.val(null).trigger("change");
      },
      "json"
    );
  }

  function setupEvents() {
    $btnPreview.on("click", function () {
      if (!$periode.val()) {
        alert("Bulan & Tahun harus diisi!");
        return;
      }
      if (!$salesman.val()) {
        alert("Salesman / TPE wajib dipilih!");
        return;
      }
      loadReportData();
    });

    $btnDownload.on("click", function () {
      if (!$periode.val()) {
        alert("Bulan & Tahun harus diisi!");
        return;
      }
      if (!$salesman.val()) {
        alert("Salesman / TPE wajib dipilih!");
        return;
      }
      let baseUrl = common.baseURL("rep_dub/download_excel");
      baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

      const query = $.param({
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
        periode: $periode.val(),
        week: $week.val() || "all",
        salesmanid: $salesman.val(),
      });
      window.location.href = baseUrl + "?" + query;
    });
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function makeProgressHtml(ratio) {
    const num = parseFloat(ratio) || 0;
    const pct = Math.min(100, Math.max(0, Math.round(num * 100)));
    const barColor = pct >= 100 ? "#16a34a" : pct >= 50 ? "#f59e0b" : "#ef4444";

    return (
      '<div class="dub-progress">' +
      '<div class="dub-progress-fill" style="width: ' +
      pct +
      "%; background-color: " +
      barColor +
      ';"></div>' +
      '<div class="dub-progress-text">' +
      pct +
      "%</div>" +
      "</div>"
    );
  }

  function destroyDataTable(selector) {
    if (
      typeof $.fn.DataTable !== "undefined" &&
      $.fn.DataTable.isDataTable &&
      $.fn.DataTable.isDataTable(selector)
    ) {
      $(selector).DataTable().destroy();
    } else if (
      typeof $.fn.dataTable !== "undefined" &&
      $.fn.dataTable.isDataTable &&
      $.fn.dataTable.isDataTable(selector)
    ) {
      $(selector).dataTable().fnDestroy();
    }
  }

  function initDataTable(selector, pageLength) {
    if (typeof $.fn.DataTable === "undefined" && typeof $.fn.dataTable === "undefined") {
      return;
    }

    const opts = {
      bDestroy: true,
      pageLength: pageLength || 25,
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Semua"],
      ],
      order: [],
      language: {
        search: "Cari: ",
        lengthMenu: "Tampilkan _MENU_ baris",
        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
        infoEmpty: "Menampilkan 0 s/d 0 dari 0 data",
        infoFiltered: "(difilter dari _MAX_ total data)",
        zeroRecords: "Tidak ada data yang cocok",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "Berikutnya",
          previous: "Sebelumnya",
        },
      },
      autoWidth: false,
    };

    if (typeof $(selector).DataTable === "function") {
      $(selector).DataTable(opts);
    } else {
      $(selector).dataTable(opts);
    }
  }

  function loadReportData() {
    destroyDataTable("#tbl-dub-summary");
    destroyDataTable("#tbl-dub-detail");

    $("#tbl-dub-summary tbody").html(
      '<tr><td colspan="10" class="text-center text-muted"><i class="fa fa-refresh fa-spin"></i> Memuat data...</td></tr>'
    );
    $("#tbl-dub-detail tbody").html(
      '<tr><td colspan="9" class="text-center text-muted"><i class="fa fa-refresh fa-spin"></i> Memuat data...</td></tr>'
    );

    $.ajax({
      url: common.baseURL("rep_dub/load"),
      type: "POST",
      dataType: "json",
      data: {
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
        periode: $periode.val(),
        week: $week.val() || "all",
        salesmanid: $salesman.val() || "",
      },
      success: function (res) {
        if (!res || !res.status || !res.data) {
          renderEmpty((res && res.message) || "Gagal memuat data Report DUB.");
          return;
        }
        renderReport(res.data);
      },
      error: function () {
        renderEmpty("Terjadi kesalahan saat memuat data Report DUB.");
      },
    });
  }

  function renderEmpty(msg) {
    $("#summary-count").text("0");
    $("#detail-count").text("0");
    $("#tbl-dub-summary tbody").html(
      '<tr><td colspan="10" class="text-center text-muted">' + escapeHtml(msg) + "</td></tr>"
    );
    $("#tbl-dub-detail tbody").html(
      '<tr><td colspan="9" class="text-center text-muted">' + escapeHtml(msg) + "</td></tr>"
    );
  }

  function renderReport(report) {
    if (report.start_date && report.end_date) {
      const weekTxt =
        report.week && report.week !== "all"
          ? "Minggu Ke-" + report.week
          : "1 Bulan Penuh";
      $activeBadge
        .html(
          '<i class="fa fa-calendar-check-o"></i> ' +
            escapeHtml(weekTxt) +
            ": " +
            escapeHtml(report.start_date) +
            " s/d " +
            escapeHtml(report.end_date)
        )
        .show();
    }

    const summary = report.summary || [];
    const rows = report.rows || [];

    $("#summary-count").text(summary.length);
    $("#detail-count").text(rows.length);

    // Render Summary Table
    if (summary.length > 0) {
      const sumHtml = summary
        .map(function (s, idx) {
          return (
            "<tr>" +
            '<td style="text-align: center;">' +
            (idx + 1) +
            "</td>" +
            "<td><strong>" +
            escapeHtml(s.nama_salesman || "-") +
            '</strong><br><small class="text-muted">' +
            escapeHtml(s.salesmanid || "-") +
            "</small></td>" +
            '<td style="text-align: center;"><span class="label label-default">' +
            escapeHtml(s.tipe_sales || "-") +
            "</span></td>" +
            "<td>" +
            escapeHtml(s.wilayah || "-") +
            "</td>" +
            '<td style="text-align: center; font-weight: 600;">' +
            (s.target_dub || 0) +
            "</td>" +
            '<td style="text-align: center; font-weight: 600;">' +
            (s.registered_dub || 0) +
            "</td>" +
            '<td style="text-align: center;">' +
            (s.target_per_doc || 0) +
            "</td>" +
            '<td style="text-align: center; font-weight: 700; color: #0073b7;">' +
            (s.total_actual || 0) +
            "</td>" +
            "<td>" +
            makeProgressHtml(s.percentage_dub) +
            "</td>" +
            "</tr>"
          );
        })
        .join("");

      $("#tbl-dub-summary tbody").html(sumHtml);
      initDataTable("#tbl-dub-summary", 10);
    } else {
      $("#tbl-dub-summary tbody").html(
        '<tr><td colspan="10" class="text-center text-muted">Tidak ada data DUB pada periode ini.</td></tr>'
      );
    }

    // Render Detail Data DUB Table
    if (rows.length > 0) {
      const dtlHtml = rows
        .map(function (r, idx) {
          const specInfo = r.spesialisasi || r.tipe_pic || "-";
          return (
            "<tr>" +
            '<td style="text-align: center;">' +
            (idx + 1) +
            "</td>" +
            "<td><strong>" +
            escapeHtml(r.nama_salesman || "-") +
            '</strong><br><small class="text-muted">' +
            escapeHtml(r.salesmanid || "-") +
            " &bull; " +
            escapeHtml(r.tipe_sales || "-") +
            "</small></td>" +
            "<td><strong>" +
            escapeHtml(r.nama_customer || "-") +
            '</strong><br><small class="text-muted">' +
            escapeHtml(r.customerid || "-") +
            "</small></td>" +
            '<td style="text-align: center;">' +
            escapeHtml(r.user_id || "-") +
            "</td>" +
            '<td><strong style="color: #0073b7;">' +
            escapeHtml(r.nama_professional || "-") +
            "</strong></td>" +
            "<td>" +
            escapeHtml(specInfo) +
            "</td>" +
            '<td style="text-align: center; font-weight: 600;">' +
            (r.target_call !== undefined ? r.target_call : 0) +
            "</td>" +
            '<td style="text-align: center; font-weight: 700;">' +
            (r.actual_call || 0) +
            "</td>" +
            "<td>" +
            makeProgressHtml(r.percentage_call) +
            "</td>" +
            "</tr>"
          );
        })
        .join("");

      $("#tbl-dub-detail tbody").html(dtlHtml);
      initDataTable("#tbl-dub-detail", 25);
    } else {
      $("#tbl-dub-detail tbody").html(
        '<tr><td colspan="9" class="text-center text-muted">Belum ada daftar DUB pada filter yang dipilih.</td></tr>'
      );
    }
  }
})();

