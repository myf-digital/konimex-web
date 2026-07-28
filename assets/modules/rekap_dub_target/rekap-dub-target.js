$(function () {
  const common = new Common();
  const paramsession = common.getCookie("session");
  const $startDate = $("#start_date");
  const $endDate = $("#end_date");
  const $btnLoad = $("#btn_load");
  const $btnExport = $("#btn_export");
  const $tblSummary = $("#tbl-summary tbody");
  const $modalDetail = $("#modal-detail");
  const $modalTitleDetail = $("#modal-title-detail");
  const $tblDetail = $("#tbl-detail tbody");
  const $salesmanId = $("#salesman_ids");

  function val(v) {
    return v == null || v === undefined || v === "null" || v === "" ? "-" : v;
  }

  function formatFloat(v) {
    let num = parseFloat(v);
    return isNaN(num) ? 0 : parseFloat(num.toFixed(2));
  }

  function makeProgressBar(actual, target, colorActual, colorTarget) {
    actual = parseFloat(actual) || 0;
    target = parseFloat(target) || 0;
    let pct = target > 0 ? actual / target : 0;
    let barPct = Math.min(100, pct).toFixed(2);
    let displayPct = formatFloat(pct);
    return `
      <div class="progress progress-sm" style="margin-bottom: 0; position: relative; background-color: ${colorTarget}; height: 20px; border-radius: 4px;">
        <div class="progress-bar" role="progressbar" style="width: ${barPct}%; height: 100%; background-color: ${colorActual}; transition: none;"></div>
        <div style="position: absolute; width: 100%; text-align: center; font-weight: bold; color: #fff; line-height: 20px; text-shadow: 1px 1px 2px #000;">
          ${formatFloat(actual)} / ${formatFloat(target)} (${displayPct})
        </div>
      </div>
    `;
  }

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

  function loadSalesman() {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_salesman"),
      {
        idjabatan: paramsession.idjabatan,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      },
      function (res) {
        $salesmanId.empty();
        $salesmanId.select2({
          placeholder: "TPE (Semua)",
          allowClear: true,
          multiple: true,
          data: $.map(res.result, function (o) {
            o.id = o.salesmanid;
            o.text =
              o.salesmanid + " - " + o.nama_salesman + " - " + o.tipe_sales;
            return o;
          }),
        });

        $salesmanId.val(null).trigger("change");
        common.loadingClose();
      },
    );
  }

  function loadSummary() {
    let start = $startDate.val();
    let end = $endDate.val();
    let salesman_ids = $("#salesman_ids").val() || [];

    $tblSummary.html(
      '<tr><td colspan="11" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat data...</td></tr>',
    );

    $.ajax({
      url: BASE_URL + "rekap_dub_target/load",
      type: "POST",
      data: {
        start_date: start,
        end_date: end,
        salesman_ids: salesman_ids,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data && res.data.length > 0) {
          let html = "";
          res.data.forEach(function (r, i) {
            let target_dub = parseFloat(r.target_dub) || 0;
            let act_planned = parseFloat(r.actual_call_planned) || 0;
            let target_visit = parseFloat(r.target_call_visit) || 0;
            let act_visit = parseFloat(r.actual_call_visit) || 0;

            html += `
              <tr>
                  <td>${i + 1}</td>
                  <td>${r.salesmanid}</td>
                  <td>${r.nama_salesman}</td>
                  <td>${r.tipe_sales}</td>
                  <td>${formatFloat(act_planned)}</td>
                  <td>${formatFloat(target_dub)}</td>
                  <td>${makeProgressBar(act_planned, target_dub, "#3d85c6", "#ff4e00")}</td>
                  <td>${formatFloat(act_visit)}</td>
                  <td>${formatFloat(target_visit)}</td>
                  <td>${makeProgressBar(act_visit, target_visit, "#3dc65dff", "#fcae06ff")}</td>
                  <td class="text-center">
                    <button class="btn btn-xs btn-primary btn-detail" data-id="${r.salesmanid}" data-name="${r.nama_salesman}">
                      <i class="fa fa-eye"></i> Detail
                    </button>
                  </td>
              </tr>
            `;
          });
          $tblSummary.html(html);
        } else {
          $tblSummary.html(
            '<tr><td colspan="11" class="text-center">Tidak ada data rekap ditemukan.</td></tr>',
          );
        }
      },
      error: function () {
        $tblSummary.html(
          '<tr><td colspan="11" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data dari server.</td></tr>',
        );
      },
    });
  }

  function loadDetail(salesmanid, name) {
    let start = $startDate.val();
    let end = $endDate.val();

    $modalTitleDetail.text(
      "Detail Kunjungan - " + name + " (" + salesmanid + ")",
    );
    $tblDetail.html(
      '<tr><td colspan="11" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat rincian kunjungan...</td></tr>',
    );
    $modalDetail.modal("show");

    $.ajax({
      url: BASE_URL + "rekap_dub_target/load_detail",
      type: "POST",
      data: {
        salesmanid: salesmanid,
        start_date: start,
        end_date: end,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data && res.data.length > 0) {
          let html = "";
          res.data.forEach(function (r, i) {
            let is_planned =
              parseInt(r.is_planned) === 1 ? "Planned" : "Unplanned";
            html += `
              <tr>
                <td>${i + 1}</td>
                <td>${val(r.customerid)}</td>
                <td>${val(r.nama_customer)}</td>
                <td>${val(r.typeid)}</td>
                <td>${val(r.nama_dokter)}</td>
                <td>${val(r.check_in)}</td>
                <td>${val(r.check_out)}</td>
                <td>${val(r.duration)}</td>
                <td>${is_planned}</td>
                <td>${val(r.array_product)}</td>
                <td>${val(r.keterangan)}</td>
              </tr>
            `;
          });
          $tblDetail.html(html);
        } else {
          $tblDetail.html(
            '<tr><td colspan="11" class="text-center">Tidak ada rincian kunjungan pada rentang tanggal ini.</td></tr>',
          );
        }
      },
      error: function () {
        $tblDetail.html(
          '<tr><td colspan="11" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat rincian kunjungan.</td></tr>',
        );
      },
    });
  }

  $btnLoad.on("click", loadSummary);

  $btnExport.on("click", function () {
    let start = $startDate.val();
    let end = $endDate.val();
    let salesman_ids = $("#salesman_ids").val() || [];
    window.location.href =
      BASE_URL +
      "rekap_dub_target/export?start_date=" +
      start +
      "&end_date=" +
      end +
      "&usersession=" +
      paramsession.username +
      "&restrict_level=" +
      paramsession.restrict_level +
      "&salesman_ids=" +
      salesman_ids.join(",");
  });

  $(document).on("click", ".btn-detail", function () {
    let id = $(this).data("id");
    let name = $(this).data("name");
    loadDetail(id, name);
  });

  loadSalesman();
  loadSummary();
});
