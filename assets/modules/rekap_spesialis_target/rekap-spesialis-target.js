$(function () {
  const common = new Common();
  const $startDate = $("#start_date");
  const $endDate = $("#end_date");
  const $btnLoad = $("#btn_load");
  const $btnExport = $("#btn_export");
  const $tblSummary = $("#tbl-summary tbody");
  const $modalDetail = $("#modal-detail");
  const $modalTitleDetail = $("#modal-title-detail");
  const $tblDetail = $("#tbl-detail tbody");
  const $spesialisasiId = $("#spesialisasi_ids");

  function val(v) {
    return v === null || v === undefined || v === "null" || v === "" ? "-" : v;
  }

  function formatFloat(v) {
    let num = parseFloat(v);
    return isNaN(num) ? 0 : parseFloat(num.toFixed(2));
  }

  function makeProgressBar(actual, target, colorActual, colorTarget) {
    actual = parseFloat(actual) || 0;
    target = parseFloat(target) || 0;
    let pct = target > 0 ? (actual / target) * 100 : 0;
    let barPct = Math.min(100, pct).toFixed(2);
    let displayPct = formatFloat(pct);
    return `
      <div class="progress progress-sm" style="margin-bottom: 0; position: relative; background-color: ${colorTarget}; height: 20px; border-radius: 4px;">
        <div class="progress-bar" role="progressbar" style="width: ${barPct}%; height: 100%; background-color: ${colorActual}; transition: none;"></div>
        <div style="position: absolute; width: 100%; text-align: center; font-weight: bold; color: #fff; line-height: 20px; text-shadow: 1px 1px 2px #000;">
          ${formatFloat(actual)} / ${formatFloat(target)} (${displayPct}%)
        </div>
      </div>
    `;
  }

  $("#spesialisasi_ids").select2();

  let today = new Date();
  let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

  $startDate
    .datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
    })
    .datepicker("setDate", firstDay);

  $endDate
    .datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
    })
    .datepicker("setDate", today);

  function loadSpesialisasi() {
    common.loading();
    $.post(common.baseURL("api_v1/call_spesialisasi"), function (res) {
      $spesialisasiId.empty();
      $spesialisasiId.select2({
        placeholder: "Spesialisasi (Semua)",
        allowClear: true,
        multiple: true,
        data: $.map(res.result, function (o) {
          o.id = o.id;
          o.text = o.name;
          return o;
        }),
      });

      $spesialisasiId.val(null).trigger("change");
      common.loadingClose();
    });
  }

  function loadSummary() {
    let start = $startDate.val();
    let end = $endDate.val();
    let spesialisasi_ids = $("#spesialisasi_ids").val() || [];

    $tblSummary.html(
      '<tr><td colspan="7" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat data...</td></tr>',
    );

    $.ajax({
      url: BASE_URL + "rekap_spesialis_target/load",
      type: "POST",
      data: {
        start_date: start,
        end_date: end,
        spesialisasi_ids: spesialisasi_ids,
      },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data && res.data.length > 0) {
          let html = "";
          res.data.forEach(function (r, i) {
            let target = parseFloat(r.target) || 0;
            let actual = parseFloat(r.actual) || 0;

            html += `
              <tr>
                  <td>${i + 1}</td>
                  <td>${r.spesialisasi_id}</td>
                  <td>${r.nama_spesialisasi}</td>
                  <td>${formatFloat(actual)}</td>
                  <td>${formatFloat(target)}</td>
                  <td>${makeProgressBar(actual, target, "#3d85c6", "#ff4e00")}</td>
                  <td class="text-center">
                    <button class="btn btn-xs btn-primary btn-detail" data-id="${r.spesialisasi_id}" data-name="${r.nama_spesialisasi}">
                      <i class="fa fa-eye"></i> Detail
                    </button>
                  </td>
              </tr>
            `;
          });
          $tblSummary.html(html);
        } else {
          $tblSummary.html(
            '<tr><td colspan="7" class="text-center">Tidak ada data rekap spesialisasi ditemukan.</td></tr>',
          );
        }
      },
      error: function () {
        $tblSummary.html(
          '<tr><td colspan="7" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data dari server.</td></tr>',
        );
      },
    });
  }

  function loadDetail(spesialisasi_id, name) {
    let start = $startDate.val();
    let end = $endDate.val();

    $modalTitleDetail.text("Detail Kunjungan Spesialisasi - " + name);
    $tblDetail.html(
      '<tr><td colspan="10" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat rincian kunjungan...</td></tr>',
    );
    $modalDetail.modal("show");

    $.ajax({
      url: BASE_URL + "rekap_spesialis_target/load_detail",
      type: "POST",
      data: {
        spesialisasi_id: spesialisasi_id,
        start_date: start,
        end_date: end,
      },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data && res.data.length > 0) {
          let html = "";
          res.data.forEach(function (r, i) {
            html += `
              <tr>
                <td>${i + 1}</td>
                <td>${r.salesmanid}</td>
                <td>${r.nama_salesman}</td>
                <td>${val(r.customerid)}</td>
                <td>${val(r.nama_customer)}</td>
                <td>${val(r.nama_dokter)}</td>
                <td>${val(r.check_in)}</td>
                <td>${val(r.check_out)}</td>
                <td>${val(r.duration)}</td>
                <td>${val(r.keterangan)}</td>
              </tr>
            `;
          });
          $tblDetail.html(html);
        } else {
          $tblDetail.html(
            '<tr><td colspan="10" class="text-center">Tidak ada rincian kunjungan pada rentang tanggal ini.</td></tr>',
          );
        }
      },
      error: function () {
        $tblDetail.html(
          '<tr><td colspan="10" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat rincian kunjungan.</td></tr>',
        );
      },
    });
  }

  $btnLoad.on("click", loadSummary);

  $btnExport.on("click", function () {
    let start = $startDate.val();
    let end = $endDate.val();
    let spesialisasi_ids = $("#spesialisasi_ids").val() || [];
    window.location.href =
      BASE_URL +
      "rekap_spesialis_target/export?start_date=" +
      start +
      "&end_date=" +
      end +
      "&spesialisasi_ids=" +
      spesialisasi_ids.join(",");
  });

  $(document).on("click", ".btn-detail", function () {
    let id = $(this).data("id");
    let name = $(this).data("name");
    loadDetail(id, name);
  });

  loadSpesialisasi();
  loadSummary();
});
