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
  const $tblDetailVisit = $("#tbl-detail-visit tbody");
  const $tblDetailSales = $("#tbl-detail-sales tbody");
  const $productId = $("#product_ids");

  function val(v) {
    return v === null || v === undefined || v === "null" || v === "" ? "-" : v;
  }

  function formatNumber(v) {
    return Number(v).toLocaleString("id-ID");
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
          ${formatNumber(formatFloat(actual))} / ${formatNumber(formatFloat(target))} (${displayPct}%)
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

  function loadProduct() {
    common.loading();
    $.post(common.baseURL("api_v1/call_product"), function (res) {
      $productId.empty();
      $productId.select2({
        placeholder: "Produk (Semua)",
        allowClear: true,
        multiple: true,
        data: $.map(res.result, function (o) {
          o.id = o.productid;
          o.text = o.productid + " - " + o.nama_invoice;
          return o;
        }),
      });

      $productId.val(null).trigger("change");
      common.loadingClose();
    });
  }

  function loadSummary() {
    let start = $startDate.val();
    let end = $endDate.val();
    let product_ids = $("#product_ids").val() || [];

    $tblSummary.html(
      '<tr><td colspan="10" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat data...</td></tr>',
    );

    $.ajax({
      url: BASE_URL + "rekap_produk_target/load",
      type: "POST",
      data: {
        start_date: start,
        end_date: end,
        product_ids: product_ids,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data && res.data.length > 0) {
          let html = "";
          res.data.forEach(function (r, i) {
            let target_visit = parseFloat(r.target) || 0;
            let actual_visit = parseFloat(r.actual_visit) || 0;
            let target_qty = parseFloat(r.target_qty) || 0;
            let actual_qty = parseFloat(r.actual_qty) || 0;

            html += `
              <tr>
                <td>${i + 1}</td>
                <td>${r.product_id}</td>
                <td>${r.nama_invoice}</td>
                <td>${formatNumber(formatFloat(actual_visit))}</td>
                <td>${formatNumber(formatFloat(target_visit))}</td>
                <td>${makeProgressBar(actual_visit, target_visit, "#3d85c6", "#ff4e00")}</td>
                <td>${formatNumber(formatFloat(actual_qty))}</td>
                <td>${formatNumber(formatFloat(target_qty))}</td>
                <td>${makeProgressBar(actual_qty, target_qty, "#3dc65dff", "#fcae06ff")}</td>
                <td class="text-center">
                  <button class="btn btn-xs btn-primary btn-detail" data-id="${r.product_id}" data-name="${r.nama_invoice}">
                    <i class="fa fa-eye"></i> Detail
                  </button>
                </td>
              </tr>
            `;
          });
          $tblSummary.html(html);
        } else {
          $tblSummary.html(
            '<tr><td colspan="10" class="text-center">Tidak ada data rekap produk ditemukan.</td></tr>',
          );
        }
      },
      error: function () {
        $tblSummary.html(
          '<tr><td colspan="10" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data dari server.</td></tr>',
        );
      },
    });
  }

  function loadDetail(product_id, name) {
    let start = $startDate.val();
    let end = $endDate.val();

    $modalTitleDetail.text(
      "Detail Rincian Produk - " + name + " (" + product_id + ")",
    );
    $tblDetailVisit.html(
      '<tr><td colspan="10" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat rincian kunjungan...</td></tr>',
    );
    $tblDetailSales.html(
      '<tr><td colspan="8" class="text-center"><i class="fa fa-refresh fa-spin"></i> Memuat rincian penjualan...</td></tr>',
    );

    $('.nav-tabs a[href="#tab_visit"]').tab("show");
    $modalDetail.modal("show");

    $.ajax({
      url: BASE_URL + "rekap_produk_target/load_detail",
      type: "POST",
      data: {
        product_id: product_id,
        start_date: start,
        end_date: end,
        usersession: paramsession.username,
        restrict_level: paramsession.restrict_level,
      },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          if (res.visits && res.visits.length > 0) {
            let html = "";
            res.visits.forEach(function (r, i) {
              html += `
                <tr>
                  <td>${i + 1}</td>
                  <td>${r.salesmanid}</td>
                  <td>${r.nama_salesman}</td>
                  <td>${val(r.customerid)}</td>
                  <td>${val(r.nama_customer)}</td>
                  <td>${val(r.check_in)}</td>
                  <td>${val(r.check_out)}</td>
                  <td>${val(r.duration)}</td>
                  <td>${val(r.keterangan)}</td>
                </tr>
              `;
            });
            $tblDetailVisit.html(html);
          } else {
            $tblDetailVisit.html(
              '<tr><td colspan="10" class="text-center">Tidak ada rincian kunjungan detailing produk.</td></tr>',
            );
          }

          if (res.sales && res.sales.length > 0) {
            let html = "";
            res.sales.forEach(function (r, i) {
              html += `
                <tr>
                  <td>${i + 1}</td>
                  <td>${val(r.tanggal)}</td>
                  <td>${r.salesmanid}</td>
                  <td>${r.nama_salesman}</td>
                  <td>${val(r.no_po)}</td>
                  <td>${formatNumber(r.qty_kecil)}</td>
                  <td>${formatNumber(r.h_jual)}</td>
                  <td>${formatNumber(r.total_price)}</td>
                </tr>
              `;
            });
            $tblDetailSales.html(html);
          } else {
            $tblDetailSales.html(
              '<tr><td colspan="8" class="text-center">Tidak ada rincian transaksi penjualan produk.</td></tr>',
            );
          }
        } else {
          $tblDetailVisit.html(
            '<tr><td colspan="10" class="text-center text-red">Gagal memproses rincian produk.</td></tr>',
          );
          $tblDetailSales.html(
            '<tr><td colspan="8" class="text-center text-red">Gagal memproses rincian produk.</td></tr>',
          );
        }
      },
      error: function () {
        $tblDetailVisit.html(
          '<tr><td colspan="10" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat rincian.</td></tr>',
        );
        $tblDetailSales.html(
          '<tr><td colspan="8" class="text-center text-red"><i class="fa fa-exclamation-triangle"></i> Gagal memuat rincian.</td></tr>',
        );
      },
    });
  }

  $btnLoad.on("click", loadSummary);

  $btnExport.on("click", function () {
    let start = $startDate.val();
    let end = $endDate.val();
    let product_ids = $("#product_ids").val() || [];
    window.location.href =
      BASE_URL +
      "rekap_produk_target/export?start_date=" +
      start +
      "&end_date=" +
      end +
      "&usersession=" +
      paramsession.username +
      "&restrict_level=" +
      paramsession.restrict_level +
      "&product_ids=" +
      product_ids.join(",");
  });

  $(document).on("click", ".btn-detail", function () {
    let id = $(this).data("id");
    let name = $(this).data("name");
    loadDetail(id, name);
  });

  loadProduct();
  loadSummary();
});
