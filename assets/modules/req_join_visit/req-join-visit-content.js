(function () {
  const common = new Common();
  const commonGrid = new CommonGrid();

  common.setTitle("Join Visit");

  const uiTbl = $("#tbl-req-join-visit");
  const uiStartPeriod = $("#start_periode");
  const uiEndPeriod = $("#end_periode");
  const uiSelectSalesman = $("#salesmanid-id");
  const uiSelectStatus = $("#status-id");
  const uiBtnPreview = $("#btn-preview-form");
  const uiBtnDownload = $("#btn-download-form");

  const paramsession = common.getCookie("session");

  initialize();

  function initialize() {
    setupDatePickers();
    setupSelect2();
    initializeGrid();
    setupEvents();
  }

  function setupDatePickers() {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
    });

    uiStartPeriod.datepicker("setDate", firstDayOfMonth);
    uiEndPeriod.datepicker("setStartDate", firstDayOfMonth);
    uiEndPeriod.datepicker("setDate", today);

    uiStartPeriod.on("changeDate", function (selected) {
      const startDate = new Date(selected.date.valueOf());
      uiEndPeriod.datepicker("setStartDate", startDate);
      if (uiStartPeriod.val() > uiEndPeriod.val() && uiEndPeriod.val() !== "") {
        uiEndPeriod.datepicker("setDate", startDate);
      }
    });
  }

  function setupSelect2() {
    uiSelectStatus.select2({
      placeholder: "Semua Status",
      allowClear: true,
    });

    $.post(
      common.baseURL("req_join_visit/load_salesman"),
      {
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
      },
      function (res) {
        const options = [
          { id: "all", text: "Semua TPE" },
          ...((res.rows || []).map(function (o) {
            return {
              id: o.salesmanid,
              text: o.salesmanid + " - " + o.nama_salesman,
            };
          })),
        ];

        uiSelectSalesman.select2({
          placeholder: "Pilih TPE",
          allowClear: true,
          data: options,
        });

        uiSelectSalesman.val("all").trigger("change");
      },
      "json"
    );
  }

  function initializeGrid() {
    const option = {
      title: "List Join Visit",
      toolbar: "",
      url: common.baseURL("req_join_visit/load"),
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      height: 550,
      striped: true,
      singleSelect: true,
      columns: [
        [
          { field: "periode", title: "PERIODE", width: 100, halign: "center", align: "center", sortable: "true" },
          { field: "pengaju", title: "PENGAJU", width: 190, halign: "center", align: "left", sortable: "true" },
          { field: "peserta_tpe", title: "PESERTA TPE", width: 260, halign: "center", align: "left", sortable: "true", formatter: formatterUlList },
          { field: "outlet", title: "OUTLET", width: 220, halign: "center", align: "left", sortable: "true", formatter: formatterWrapTooltip },
          { field: "professional", title: "PROFESSIONAL", width: 240, halign: "center", align: "left", sortable: "true", formatter: formatterUlList },
          { field: "keterangan", title: "KETERANGAN", width: 200, halign: "center", align: "left", sortable: "true", formatter: formatterWrapTooltip },
          { field: "status_desc", title: "STATUS", width: 110, halign: "center", align: "center", sortable: "true", formatter: formatterStatus },
          { field: "realisasi_desc", title: "REALISASI", width: 150, halign: "center", align: "center", sortable: "true", formatter: formatterRealisasi },
          { field: "action", title: "AKSI", width: 100, halign: "center", align: "center", formatter: formatterAction },
        ],
      ],
      onBeforeLoad: function (param) {
        if (paramsession) {
          param.usersession = paramsession.username || "";
          param.restrict_level = paramsession.restrict_level || "";
        }
        param.start_date = uiStartPeriod.val();
        param.end_date = uiEndPeriod.val();
        param.salesmanid = uiSelectSalesman.val();
        param.filter_status = uiSelectStatus.val();
      },
      onLoadSuccess: function (data) {
        $(this).datagrid("fixRowHeight");
        $(this).datagrid("resize");
        bindDetailButtons();
      },
    };

    const dgOpts = commonGrid.optionValue(option);
    dgOpts.nowrap = false;
    dgOpts.autoRowHeight = true;

    uiTbl.datagrid(dgOpts);
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["action"]);
  }

  function setupEvents() {
    uiBtnPreview.click(function () {
      uiTbl.datagrid("load", {
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
        start_date: uiStartPeriod.val(),
        end_date: uiEndPeriod.val(),
        salesmanid: uiSelectSalesman.val(),
        filter_status: uiSelectStatus.val(),
      });
    });

    uiBtnDownload.click(function () {
      let baseUrl = common.baseURL("req_join_visit/download_excel");
      baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

      const params = $.param({
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
        start_date: uiStartPeriod.val(),
        end_date: uiEndPeriod.val(),
        salesmanid: uiSelectSalesman.val(),
        filter_status: uiSelectStatus.val(),
      });
      window.location.href = baseUrl + "?" + params;
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

  function formatterWrapTooltip(val) {
    if (val && String(val).trim() !== "" && val !== "-") {
      const safe = escapeHtml(val);
      return '<span title="' + safe + '">' + safe + "</span>";
    }
    return '<span class="text-muted" style="font-style: italic; font-size: 11px;">-</span>';
  }

  function formatterUlList(val) {
    if (!val || String(val).trim() === "" || val === "-") {
      return '<span class="text-muted" style="font-style: italic; font-size: 11px;">-</span>';
    }
    const items = String(val)
      .split("||")
      .map(function (item) {
        return item.trim();
      })
      .filter(function (item) {
        return item !== "" && item !== "-";
      });

    if (items.length === 0) {
      return '<span class="text-muted" style="font-style: italic; font-size: 11px;">-</span>';
    }

    const liHtml = items
      .map(function (item) {
        return "<li>" + escapeHtml(item) + "</li>";
      })
      .join("");

    return '<ul class="grid-ul-list">' + liHtml + "</ul>";
  }

  function formatterStatus(val, row) {
    const status = String(row.status);
    if (status === "1") {
      return '<span class="label label-warning" style="font-size: 11px; padding: 3px 8px;"><i class="fa fa-clock-o"></i> Pending</span>';
    } else if (status === "3") {
      return '<span class="label label-success" style="font-size: 11px; padding: 3px 8px;"><i class="fa fa-check"></i> Disetujui</span>';
    } else {
      return '<span class="label label-danger" style="font-size: 11px; padding: 3px 8px;"><i class="fa fa-times"></i> Reject</span>';
    }
  }

  function formatterRealisasi(val, row) {
    const real = parseInt(row.total_realisasi) || 0;
    const tgt = parseInt(row.total_target) || 0;
    if (real > 0) {
      return (
        '<span class="label label-info" style="font-size: 11px; padding: 3px 8px;"><i class="fa fa-check-circle"></i> Sudah Detailing (' +
        real +
        "/" +
        tgt +
        ")</span>"
      );
    }
    return '<span class="label label-default" style="font-size: 10px; padding: 3px 6px;"><i class="fa fa-clock-o"></i> Belum Detailing</span>';
  }

  function formatterAction(val, row) {
    return (
      '<button type="button" class="btn btn-xs btn-primary btn-view-detail" data-reqno="' +
      row.req_no +
      '" style="border-radius: 3px; font-weight: 600;"><i class="fa fa-eye"></i> Detail</button>'
    );
  }

  function bindDetailButtons() {
    $(".btn-view-detail")
      .off("click")
      .on("click", function () {
        const reqNo = $(this).data("reqno");
        openDetailModal(reqNo);
      });
  }

  function openDetailModal(reqNo) {
    common.loading();

    $.ajax({
      url: common.baseURL("req_join_visit/get_detail"),
      type: "POST",
      data: { req_no: reqNo },
      dataType: "json",
      success: function (res) {
        common.loadingClose();
        if (res.status === true && res.data) {
          renderDetailData(res.data);
          $("#modal_detail_visit").modal("show");
        } else {
          Swal.fire("Gagal!", res.message || "Gagal memuat detail join visit.", "error");
        }
      },
      error: function (xhr, status, error) {
        common.loadingClose();
        Swal.fire("Error!", "Terjadi kesalahan saat memuat data: " + error, "error");
      },
    });
  }

  function formatDt(v) {
    if (!v) return "-";
    return String(v).replace("T", " ").substring(0, 16);
  }

  function renderDetailData(detail) {
    $("#detail-req-no").text(detail.req_no ? "#" + detail.req_no : "");
    $("#detail-periode").text(detail.periode || "-");
    $("#detail-pengaju-name").text(detail.nama_pengaju || "-");
    $("#detail-pengaju-id").text("(" + (detail.created_by || "-") + ")");

    const participants = detail.participants || [];
    if (participants.length > 0) {
      const liItems = participants
        .map(function (p) {
          return "<li>" + escapeHtml(p) + "</li>";
        })
        .join("");
      $("#detail-participants").html('<ul class="grid-ul-list">' + liItems + "</ul>");
    } else {
      $("#detail-participants").text("-");
    }

    const status = String(detail.status);
    if (status === "1") {
      $("#detail-status").html('<span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>');
    } else if (status === "3") {
      $("#detail-status").html('<span class="label label-success"><i class="fa fa-check"></i> Disetujui</span>');
    } else {
      $("#detail-status").html('<span class="label label-danger"><i class="fa fa-times"></i> Reject</span>');
    }

    $("#detail-keterangan").text(detail.keterangan || "-");

    if (![1, 3].includes(parseInt(detail.status)) && detail.reason && detail.reason.trim() !== "") {
      $("#detail-reason").text(detail.reason);
      $("#row-detail-reason").show();
    } else {
      $("#row-detail-reason").hide();
    }

    if (parseInt(detail.status) === 3 && detail.modified_by && String(detail.modified_by).trim() !== "") {
      const approverLabel =
        detail.nama_approver && detail.nama_approver !== detail.modified_by
          ? detail.nama_approver + " (" + detail.modified_by + ")"
          : detail.modified_by;
      $("#detail-approved-by").text(approverLabel);
      $("#detail-approved-date").text(detail.modified_date || "-");
      $("#row-detail-approval").show();
    } else {
      $("#row-detail-approval").hide();
    }

    const items = detail.items || [];
    $("#detail-item-count").text(items.length);
    const itemsTbody = $("#detail-items-tbody");
    itemsTbody.empty();

    if (items.length > 0) {
      items.forEach(function (it, idx) {
        const partHtml =
          it.participants && it.participants.length > 0
            ? '<ul class="grid-ul-list">' +
              it.participants
                .map(function (p) {
                  return "<li>" + escapeHtml(p) + "</li>";
                })
                .join("") +
              "</ul>"
            : "-";

        let waktuHtml = "-";
        if (it.start_detailing || it.end_detailing) {
          waktuHtml =
            "<div><strong>Mulai:</strong> " +
            escapeHtml(formatDt(it.start_detailing)) +
            "</div><div><strong>Selesai:</strong> " +
            escapeHtml(formatDt(it.end_detailing)) +
            "</div>";
        } else if (it.check_in || it.check_out) {
          waktuHtml =
            "<div><strong>In:</strong> " +
            escapeHtml(formatDt(it.check_in)) +
            "</div><div><strong>Out:</strong> " +
            escapeHtml(formatDt(it.check_out)) +
            "</div>";
        }

        const statusBadge = it.is_realized
          ? '<span class="label label-success"><i class="fa fa-check"></i> Sudah Detailing</span>'
          : '<span class="label label-default"><i class="fa fa-clock-o"></i> Belum</span>';

        itemsTbody.append(`
          <tr>
            <td style="text-align: center;">${idx + 1}</td>
            <td>
              <strong>${escapeHtml(it.nama_customer || "-")}</strong><br>
              <small class="text-muted">${escapeHtml(it.customerid || "-")}</small>
            </td>
            <td>
              <strong style="color: #0073b7;">${escapeHtml(it.nama_professional || "-")}</strong><br>
              <small class="text-muted">ID: ${escapeHtml(it.user_id || "-")} &bull; ${escapeHtml(it.tipe_pic || "-")}</small>
            </td>
            <td>${partHtml}</td>
            <td style="white-space: nowrap;">${waktuHtml}</td>
            <td>${escapeHtml(it.products || "-")}</td>
            <td style="white-space: pre-wrap;">${escapeHtml(it.visit_keterangan || "-")}</td>
            <td style="text-align: center;">${statusBadge}</td>
          </tr>
        `);
      });
    } else {
      itemsTbody.append('<tr><td colspan="8" class="text-center text-muted">Tidak ada rincian target kunjungan</td></tr>');
    }

    const images = detail.images || [];
    const galleryContainer = $("#gallery-container");
    const galleryEmpty = $("#gallery-empty");

    galleryContainer.empty();
    $("#doc-count").text(images.length);

    if (images.length > 0) {
      galleryEmpty.hide();
      galleryContainer.show();

      images.forEach(function (imgObj, idx) {
        const fullUrl = imgObj.url;
        const filename = fullUrl.split("/").pop() || "Foto " + (idx + 1);
        const label = imgObj.label || "Foto #" + (idx + 1);

        const card = $(`
          <div class="img-gallery-card">
            <img src="${fullUrl}" alt="${escapeHtml(label)}" onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';">
            <div class="card-footer-info">
              <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;" title="${escapeHtml(label + ' (' + filename + ')')}">
                <i class="fa fa-image"></i> ${escapeHtml(label)}
              </span>
              <button type="button" class="btn btn-xs btn-default btn-zoom-image" title="Perbesar Foto">
                <i class="fa fa-search-plus"></i> Lihat
              </button>
            </div>
          </div>
        `);

        card.find("img, .btn-zoom-image").on("click", function () {
          $("#preview-image-title").text(label + " (" + filename + ")");
          $("#preview-img-element").attr("src", fullUrl);
          $("#preview-img-download-link").attr("href", fullUrl);
          $("#modal_preview_image").modal("show");
        });

        galleryContainer.append(card);
      });
    } else {
      galleryContainer.hide();
      galleryEmpty.show();
    }
  }
})();

