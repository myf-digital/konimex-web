(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("List User");
  // ui components
  let uiTbl = $("#tbl-professional");
  let paramsession = common.getCookie("session");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    let uiBtnDownload = $("#btn-download");
    uiBtnDownload.click(function () {
      save_xls();
    });
    $("#btn-create").click(function () {
      common.removeCookie("module.professional.update");
      common.direct("professional/form");
    });
    $("#btn-upload").click(function () {
      common.direct("ref_customer/form_upload?ref=professional");
    });
  }

  function initializeGrid() {
    let option = {
      title: "List User",
      toolbar: toolbar(),
      url: common.baseURL("professional/load"),
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "options",
            title: "Action",
            width: 150,
            halign: "center",
            align: "center",
            formatter: formatterButton,
          },
        ],
      ],
      columns: [
        [
          {
            field: "id",
            title: "ID USER",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 150,
          },
          {
            field: "nama_professional",
            title: "NAMA USER",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 250,
          },
          {
            field: "type",
            title: "TIPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "spesialisasi",
            title: "SPESIALISASI",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 250,
          },
          {
            field: "tanggal_lahir",
            title: "TANGGAL LAHIR",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 200,
            formatter: formatterDate,
          },
          {
            field: "tanggal_aniv_pernikahan",
            title: "TANGGAL ANIV PERNIKAHAN",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 200,
            formatter: formatterDate,
          },
          {
            field: "status",
            title: "STATUS",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 100,
            formatter: formatterStatus,
          },
          {
            field: "customer_list",
            title: "TEMPAT PRAKTEK",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 450,
            formatter: formatterCustomerList,
          },
        ],
      ],
      onBeforeLoad: function (param) {},
      onLoadSuccess: function (data) {
        $(this).datagrid("resize");
        $(this).datagrid("autoSizeColumn", "customer_list");
      },
    };
    let gridOptions = commonGrid.optionValue(option);
    gridOptions.fitColumns = false;
    uiTbl.datagrid(gridOptions);
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["options"]);

    const panel = uiTbl.datagrid("getPanel");

    panel.off("click", ".action-grid a.btn-warning, .action-grid #btn-viem").on("click", ".action-grid a.btn-warning, .action-grid #btn-viem", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        open_edit(rows[index]);
      }
    });

    panel.off("click", ".action-grid #btn-delete").on("click", ".action-grid #btn-delete", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        deleteRow(rows[index]);
      }
    });

    panel.off("click", ".action-grid a.btn-success, .action-grid #btn-approval").on("click", ".action-grid a.btn-success, .action-grid #btn-approval", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        handleApproveReject(rows[index].id, 3);
      }
    });

    panel.off("click", ".action-grid #btn-reject").on("click", ".action-grid #btn-reject", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        handleApproveReject(rows[index].id, 5);
      }
    });

    panel.off("click", ".action-grid a.btn-info, .action-grid #btn-viem-image").on("click", ".action-grid a.btn-info, .action-grid #btn-viem-image", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        open_image(rows[index]);
      }
    });
  }

  function toolbar() {
    const btnCreate = commonGrid.btnBuilderText(
      "btn-create",
      "success",
      "fa fa-plus",
      " Tambah",
    );
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "info",
      "fa fa-download",
      " Download",
    );
    const btnUpload = commonGrid.btnBuilderText(
      "btn-upload",
      "warning",
      "fa fa-upload",
      " Upload",
    );
    return (
      '<div class="action-grid-toolbar">' +
      btnCreate +
      btnDownload +
      btnUpload +
      "</div>"
    );
  }

  function formatterButton(val, row, index) {
    let btnImage = "";
    let btnApproval = "";

    if (row.status == 1) {
      btnApproval += commonGrid.btnBuilder(
        "btn-approval",
        "success",
        "fa fa-check",
        "Approve",
      );
      btnApproval += commonGrid.btnBuilder(
        "btn-reject",
        "danger",
        "fa fa-times",
        "Reject",
      );
    } else if (
      (row.url_foto && row.url_foto != undefined) ||
      (row.url_img_signature && row.url_img_signature != undefined)
    ) {
      btnImage = commonGrid.btnBuilder("btn-viem-image", "info", "fa fa-image");
    }

    const btnEdit = commonGrid.btnBuilder("btn-viem", "warning", "fa fa-edit");
    const btnDelete = commonGrid.btnBuilder(
      "btn-delete",
      "danger",
      "fa fa-trash",
    );
    return (
      '<div class="action-grid" style="display: flex; gap: 4px; align-items: center; justify-content: center;">' +
      btnEdit +
      btnDelete +
      btnApproval +
      btnImage +
      "</div>"
    );
  }

  function formatterStatus(value, row, index) {
    if (value === "1") {
      return '<span class="label label-warning">Pending</span>';
    } else if (value === "3") {
      return `<span class="label label-success" title="${row.reason || ""}">Approved</span>`;
    } else if (value === "5") {
      return `<span class="label label-danger" title="${row.reason || ""}">Rejected</span>`;
    } else {
      return "-";
    }
  }

  function formatterCustomerList(val, row, index) {
    if (val) {
      let listCustomer = val.split("||");
      let chunks = [];
      for (let i = 0; i < listCustomer.length; i += 5) {
        chunks.push(listCustomer.slice(i, i + 5));
      }

      let result =
        '<div style="display: flex; gap: 15px; align-items: start;">';
      chunks.forEach((chunk, chunkIdx) => {
        let startNum = chunkIdx * 5 + 1;
        result +=
          '<ol start="' +
          startNum +
          '" style="margin: 0; padding-left: 15px; width: 320px; min-width: 320px; max-width: 320px; white-space: normal; word-break: break-word;">';
        for (const customer of chunk) {
          result += "<li>" + customer + "</li>";
        }
        result += "</ol>";
      });
      result += "</div>";
      return result;
    }
    return "";
  }

  function formatterDate(val, row, index) {
    return val ? moment(val).format("DD MMM YYYY") : "-";
  }



  function open_edit(data) {
    common.setCookie("module.professional.update", data);
    common.direct("professional/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("professional/delete", { id: val.id }, function (data, status) {
        if (200 === data.code) {
          $.alert("Delete success!");
          uiTbl.datagrid("reload");
        } else {
          $.alert("Delete failed!");
        }
      });
    });
  }

  function open_image(data) {
    if (data && data != undefined) {
      $("#myModalImage").text(`User: ${data.nama_professional}`);
      $("#show-image").html(`
        <div class="row text-left">
          <div class="col-md-12">
            <h5><b>1. Foto</b></h5>
            <img src="${data.url_foto}" alt="Foto" class="img-fluid">
          </div>
          <div class="col-md-12">
            <h5><b>2. Foto Signature</b></h5>
            ${data.url_img_signature ? `<img src="${data.url_img_signature}" alt="Foto Signature" class="img-fluid">` : "<p>-</p>"}
          </div>
        </div>
      `);
      $("#modal_image").modal("show");
    }
  }

  function save_xls(start, end) {
    common.direct("professional/savetoxlsx");
  }

  function handleApproveReject(id, targetStatus) {
    let statusText = targetStatus == 3 ? "Approve" : "Reject";
    let inputPlaceholder =
      targetStatus == 3
        ? "Alasan persetujuan (opsional)"
        : "Alasan penolakan (wajib)";

    Swal.fire({
      title: statusText + " Request User",
      input: "textarea",
      inputLabel: "Masukkan keterangan/alasan:",
      inputPlaceholder: inputPlaceholder,
      showCancelButton: true,
      confirmButtonText: "Kirim",
      cancelButtonText: "Batal",
      confirmButtonColor: targetStatus == 3 ? "#28a745" : "#d33",
      inputValidator: (value) => {
        if (targetStatus == 5 && !value) {
          return "Alasan penolakan wajib diisi!";
        }
      },
    }).then((result) => {
      if (result.isConfirmed) {
        common.loading();
        $.post(
          common.baseURL("professional/update_status"),
          {
            id: id,
            status: targetStatus,
            reason: result.value || "",
            usersession: paramsession.username,
          },
          function (res) {
            common.loadingClose();
            if (res.code === 200) {
              Swal.fire({
                title: "Success",
                text: "Status berhasil diperbarui",
                icon: "success",
              }).then(() => {
                $("#modalDetail").modal("hide");
                uiTbl.datagrid("reload");
              });
            } else {
              Swal.fire({
                title: "Gagal",
                text: res.message || "Gagal memperbarui status",
                icon: "error",
              });
            }
          },
        ).fail(function (xhr, status, error) {
          common.loadingClose();
          Swal.fire({
            title: "Error",
            text: "Terjadi kesalahan sistem: " + error,
            icon: "error",
          });
        });
      }
    });
  }
})();
