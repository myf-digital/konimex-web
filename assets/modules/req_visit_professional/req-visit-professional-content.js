(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();

  common.setTitle("Request Visit Professional");

  const uiTbl = $("#tbl-req-visit-professional");
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
    $(".datepicker")
      .datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
      })
      .datepicker("setDate", new Date());

    uiStartPeriod.on("changeDate", function (selected) {
      const startDate = new Date(selected.date.valueOf());
      const endDate = new Date(selected.date.valueOf());
      endDate.setDate(endDate.getDate() + 31);
      uiEndPeriod.datepicker("setStartDate", startDate);
      if (uiStartPeriod.val() > uiEndPeriod.val() && uiEndPeriod.val() !== "") {
        uiEndPeriod.val(uiStartPeriod.val());
      }
    });
  }

  function setupSelect2() {
    uiSelectStatus.select2({
      placeholder: "Semua Status",
      allowClear: true,
    });

    $.post(
      common.baseURL("req_visit_professional/load_salesman"),
      {
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
      },
      function (res) {
        let options = [
          { id: "all", text: "Semua MEDREP" },
          ...((res.rows || []).map(function (o) {
            return {
              id: o.salesmanid,
              text: o.salesmanid + " - " + o.nama_salesman,
            };
          })),
        ];

        uiSelectSalesman.select2({
          placeholder: "Pilih MEDREP",
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
      url: common.baseURL("req_visit_professional/load"),
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      height: 550,
      striped: true,
      singleSelect: true,
      columns: [
        [
          { field: "periode", title: "PERIODE", width: 100, halign: "center", align: "center", sortable: "true" },
          { field: "salesman", title: "MEDREP", width: 220, halign: "center", align: "left", sortable: "true" },
          { field: "outlet", title: "OUTLET", width: 220, halign: "center", align: "left", sortable: "true" },
          { field: "professional", title: "PROFESSIONAL", width: 220, halign: "center", align: "left", sortable: "true" },
          { field: "keterangan", title: "KETERANGAN", width: 220, halign: "center", align: "left", sortable: "true" },
          { field: "status_desc", title: "STATUS", width: 110, halign: "center", align: "center", sortable: "true", formatter: formatterStatus },
          { field: "visit_keterangan", title: "KETERANGAN VISIT", width: 220, halign: "center", align: "left", sortable: "true", formatter: formatterVisitKeterangan },
          { field: "action", title: "AKSI", width: 110, halign: "center", align: "center", formatter: formatterAction },
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
        $(this).datagrid("resize");
        bindDetailButtons(data);
      },
    };

    uiTbl.datagrid(commonGrid.optionValue(option));
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
      const params = $.param({
        usersession: paramsession ? paramsession.username || "" : "",
        restrict_level: paramsession ? paramsession.restrict_level || "" : "",
        start_date: uiStartPeriod.val(),
        end_date: uiEndPeriod.val(),
        salesmanid: uiSelectSalesman.val(),
        filter_status: uiSelectStatus.val(),
      });
      window.location.href = common.baseURL("req_visit_professional/download_excel?" + params);
    });
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

  function formatterVisitKeterangan(val, row) {
    if (val && val.trim() !== "") {
      return '<span title="' + val + '">' + val + '</span>';
    }
    return '<span class="text-muted" style="font-style: italic; font-size: 11px;">-</span>';
  }

  function formatterAction(val, row, index) {
    let hasDoc = false;
    if (row.visit_doc && row.visit_doc !== "" && row.visit_doc !== "null" && row.visit_doc !== "[]") {
      try {
        const parsed = typeof row.visit_doc === "string" ? JSON.parse(row.visit_doc) : row.visit_doc;
        if (Array.isArray(parsed) && parsed.length > 0) {
          hasDoc = true;
        }
      } catch (e) {
        hasDoc = true;
      }
    }

    const hasKeterangan = row.visit_keterangan && row.visit_keterangan.trim() !== "";

    if (hasDoc || hasKeterangan) {
      return (
        '<button type="button" class="btn btn-xs btn-primary btn-view-detail" data-reqno="' +
        row.req_no +
        '" style="border-radius: 3px; font-weight: 600;"><i class="fa fa-eye"></i> Detail</button>'
      );
    } else {
      return '<span class="label label-default" style="font-size: 10px; padding: 3px 6px;" title="Belum melakukan kunjungan"><i class="fa fa-clock-o"></i> Belum Visit</span>';
    }
  }

  function bindDetailButtons(data) {
    $(".btn-view-detail").off("click").on("click", function () {
      const reqNo = $(this).data("reqno");
      openDetailModal(reqNo);
    });
  }

  function openDetailModal(reqNo) {
    common.loading();

    $.ajax({
      url: common.baseURL("req_visit_professional/get_detail"),
      type: "POST",
      data: { req_no: reqNo },
      dataType: "json",
      success: function (res) {
        common.loadingClose();
        if (res.status === true && res.data) {
          renderDetailData(res.data);
          $("#modal_detail_visit").modal("show");
        } else {
          Swal.fire("Gagal!", res.message || "Gagal memuat detail kunjungan.", "error");
        }
      },
      error: function (xhr, status, error) {
        common.loadingClose();
        Swal.fire("Error!", "Terjadi kesalahan saat memuat data: " + error, "error");
      },
    });
  }

  function renderDetailData(detail) {
    $("#detail-periode").text(detail.periode || "-");
    $("#detail-salesman-name").text(detail.nama_salesman || "-");
    $("#detail-salesman-id").text("(" + (detail.salesmanid || "-") + ")");
    $("#detail-customer-name").text(detail.nama_customer || "-");
    $("#detail-customer-id").text("(" + (detail.customerid || "-") + ")");
    $("#detail-prof-name").text(detail.nama_professional || "-");
    $("#detail-prof-id").text("(" + (detail.user_id || "-") + ")");

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

    $("#detail-visit-keterangan").text(detail.visit_keterangan || "-");

    if (parseInt(detail.status) == 3 && detail.approved_by && detail.approved_by.trim() !== "") {
      $("#detail-approved-by").text(detail.approved_by);
      $("#detail-approved-date").text(detail.approved_date || "-");
      $("#row-detail-approval").show();
    } else {
      $("#row-detail-approval").hide();
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

        const card = $(`
          <div class="img-gallery-card">
            <img src="${fullUrl}" alt="Foto ${idx + 1}" onerror="this.onerror=null; this.src='https://placehold.co/400x300?text=No+Image';">
            <div class="card-footer-info">
              <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 120px;" title="${filename}">
                <i class="fa fa-image"></i> Foto #${idx + 1}
              </span>
              <button type="button" class="btn btn-xs btn-default btn-zoom-image" title="Perbesar Foto">
                <i class="fa fa-search-plus"></i> Lihat
              </button>
            </div>
          </div>
        `);

        card.find("img, .btn-zoom-image").on("click", function () {
          $("#preview-image-title").text("Foto Dokumentasi Kunjungan #" + (idx + 1) + " (" + filename + ")");
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
