(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Setup Planned");
  // ui components
  let uiTbl = $("#tbl-setup-planned");
  let paramsession = common.getCookie("session");

  if ($.fn.modal && $.fn.modal.Constructor) {
    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
  }

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.setup_planned.update");
      common.direct("setup_planned/form");
    });

    $("#btn-download").click(function () {
      common.removeCookie("module.setup_planned.update");
      common.direct("setup_planned/form_download");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Setup Planned",
      toolbar: toolbar(),
      url: common.baseURL("setup_planned/load"),
      queryParams: {
        usersession: paramsession.username,
        idjabatan: paramsession.idjabatan,
        restrict_level: paramsession.restrict_level,
      },
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "options",
            title: "ACTION",
            width: 100,
            halign: "center",
            align: "center",
            formatter: formatterButton,
          },
        ],
      ],
      columns: [
        [
          {
            field: "req_no",
            title: "REQ NO",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 50,
          },
          {
            field: "periode",
            title: "TGL PENGAJUAN",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 100,
            formatter: formatterDate,
          },
          {
            field: "salesmanid",
            title: "ID TPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_salesman",
            title: "NAMA TPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
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
            field: "reason",
            title: "REASON",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "keterangan",
            title: "KETERANGAN",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
        ],
      ],
      onBeforeLoad: function (param) {
        param = common.replaceGridFilterPrefix(param, "a");
      },
      onLoadSuccess: function (data) {
        $(this).datagrid("resize");
        optionButton(data);
      },
    };

    uiTbl.datagrid(commonGrid.optionValue(option));
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["options"]);
  }

  function toolbar() {
    const btnCreate = commonGrid.btnBuilderText(
      "btn-create",
      "success",
      "fa fa-pencil",
      " Create Planned",
    );
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "primary",
      "fa fa-download",
      " Download Planned",
    );
    return (
      '<div class="action-grid-toolbar">' + btnCreate + btnDownload + "</div>"
    );
  }

  function formatterDate(val, row, index) {
    return val ? moment(val).locale("id").format("DD MMM YYYY") : "";
  }

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnEdit = $(btns).find("a.btn-success");
      const btnDelete = $(btns).find("a.btn-danger");
      const btnDetail = $(btns).find("a.btn-info");
      btnEdit.click(function () {
        updateRow(param);
      });
      btnDelete.click(function () {
        deleteRow(param);
      });
      btnDetail.click(function () {
        detailRow(param);
      });
      index++;
    }
  }

  function formatterStatus(value, row, index) {
    if (value === "1") {
      return '<span class="label label-warning">Pending</span>';
    } else if (value === "3") {
      let html = '<span class="label label-success">Approved</span>';
      return html;
    } else if (value === "5") {
      let html = '<span class="label label-danger">Rejected</span>';
      return html;
    } else {
      return "-";
    }
  }

  function formatterButton(val, row, index) {
    const btnUpdate = commonGrid.btnBuilder(
      "btn-update",
      "success",
      "fa fa-pencil",
    );
    const btnDetail = commonGrid.btnBuilder("btn-detail", "info", "fa fa-eye");
    const btnDelete = commonGrid.btnBuilder(
      "btn-delete",
      "danger",
      "fa fa-trash-o",
    );
    if (row.status == 3) {
      return '<div class="action-grid">' + btnDetail + "</div>";
    } else {
      return (
        '<div class="action-grid">' +
        btnUpdate +
        " " +
        btnDetail +
        " " +
        btnDelete +
        "</div>"
      );
    }
  }

  function updateRow(val) {
    common.setCookie("module.setup_planned.update", val);
    common.direct("setup_planned/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("setup_planned/delete", val, function (data, status) {
        if (200 === data.code) {
          $.alert("Delete success!");
          uiTbl.datagrid("reload");
        } else {
          $.alert(status);
        }
      });
    });
  }

  function detailRow(val) {
    common.loading();
    $.post(
      common.baseURL("setup_planned/get_detail"),
      { req_no: val.req_no },
      function (res) {
        common.loadingClose();
        if (res.code === 200) {
          $("#detail-req-no").text(val.req_no || "-");
          $("#detail-periode").text(
            val.periode
              ? moment(val.periode).locale("id").format("DD MMM YYYY")
              : "-",
          );
          $("#detail-salesman").text(
            (val.salesmanid || "-") +
              (val.nama_salesman ? " - " + val.nama_salesman : ""),
          );
          $("#detail-keterangan").text(val.keterangan || "-");

          let statusHtml = "-";
          if (val.status == "1") {
            statusHtml = '<span class="label label-warning">Pending</span>';
          } else if (val.status == "3") {
            statusHtml = '<span class="label label-success">Approved</span>';
            if (val.reason) {
              statusHtml +=
                '<br/><small style="color: #666; margin-left: 5px;">Reason: ' +
                val.reason +
                "</small>";
            }
          } else if (val.status == "5") {
            statusHtml = '<span class="label label-danger">Rejected</span>';
            if (val.reason) {
              statusHtml +=
                '<br/><small style="color: #666; margin-left: 5px;">Reason: ' +
                val.reason +
                "</small>";
            }
          }
          $("#detail-status").html(statusHtml);

          let container = $("#detail-accordion-container");
          container.empty();

          let details = res.result || [];
          if (details.length === 0) {
            container.append(
              '<div class="no-data-text">Tidak ada detail User Planned.</div>',
            );
            $("#detail-summary-bar").hide();
          } else {
            $("#detail-summary-bar").show();

            let groupedByDate = {};
            let uniqueOutlets = new Set();
            let distinctDates = new Set();

            details.forEach(function (d) {
              if (d.customerid) uniqueOutlets.add(d.customerid);
              if (d.periode) distinctDates.add(d.periode);

              let dateKey = d.periode || "Tanpa Tanggal";
              if (!groupedByDate[dateKey]) {
                groupedByDate[dateKey] = [];
              }
              groupedByDate[dateKey].push(d);
            });

            $("#detail-total-outlet").text(`${uniqueOutlets.size} Outlet`);
            $("#detail-total-periode").text(`${distinctDates.size} Hari`);
            $("#detail-total-user").text(`${details.length} User`);

            let sortedDates = Object.keys(groupedByDate).sort();

            sortedDates.forEach(function (dateVal) {
              let dateRecords = groupedByDate[dateVal];
              let displayDate =
                dateVal !== "Tanpa Tanggal"
                  ? moment(dateVal).locale("id").format("dddd, DD MMM YYYY")
                  : "Tanpa Tanggal";

              let totalRecords = dateRecords.length;

              let groupedByOutlet = {};
              dateRecords.forEach(function (d) {
                let key = d.customerid;
                if (!groupedByOutlet[key]) {
                  groupedByOutlet[key] = {
                    outletName: d.outlet || "Outlet tidak diketahui",
                    records: [],
                  };
                }
                groupedByOutlet[key].records.push({
                  user_id: d.user_id,
                  user_name: d.user_name || "Professional tidak diketahui",
                });
              });

              let accordionItem = $(`
                <div class="panel panel-default planned-accordion-item">
                  <div class="panel-heading">
                    <div class="panel-title-label">
                      <h4 class="panel-title">
                        <i class="fa fa-calendar"></i> ${displayDate}
                      </h4>
                    </div>
                    <div class="panel-title-icon">
                      <span class="label label-success selected-count-badge">${totalRecords} terpilih</span>
                      <i class="fa fa-chevron-down accordion-arrow"></i>
                    </div>
                  </div>
                  <div class="panel-collapse" style="display: none;">
                    <div class="panel-body">
                      <table class="table table-bordered table-striped">
                        <thead>
                          <tr class="bg-f5">
                            <th class="th-detail w-50">Outlet (Customer)</th>
                            <th class="th-detail w-50">Daftar User Binaan (DUB)</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              `);

              let tbody = accordionItem.find("tbody");

              Object.keys(groupedByOutlet).forEach(function (cid) {
                let group = groupedByOutlet[cid];
                let records = group.records;
                let rowspan = records.length;

                records.forEach(function (rec, index) {
                  let userCell = `<b>${rec.user_id}</b> - ${rec.user_name}`;

                  if (index === 0) {
                    let outletCell = `<b>${cid}</b> - ${group.outletName}`;
                    tbody.append(`
                      <tr>
                        <td rowspan="${rowspan}">${outletCell}</td>
                        <td>${userCell}</td>
                      </tr>
                    `);
                  } else {
                    tbody.append(`
                      <tr>
                        <td>${userCell}</td>
                      </tr>
                    `);
                  }
                });
              });

              container.append(accordionItem);
            });

            $(document)
              .off(
                "click",
                "#detail-accordion-container .planned-accordion-item .panel-heading",
              )
              .on(
                "click",
                "#detail-accordion-container .planned-accordion-item .panel-heading",
                function (e) {
                  let item = $(this).closest(".planned-accordion-item");
                  let collapse = item.find(".panel-collapse");
                  let arrow = item.find(".accordion-arrow");

                  collapse.slideToggle(200, function () {
                    if (collapse.is(":visible")) {
                      arrow.css("transform", "rotate(180deg)");
                    } else {
                      arrow.css("transform", "rotate(0deg)");
                    }
                  });
                },
              );
          }

          let footer = $("#modalDetail .modal-footer");
          footer.empty();
          footer.append(
            '<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>',
          );

          if (val.status == "1" && details.length > 0) {
            footer.prepend(`
              <button type="button" class="btn btn-danger pull-left" id="btn-detail-reject">Reject</button>
              <button type="button" class="btn btn-success pull-left" id="btn-detail-approve">Approve</button>
            `);

            $("#btn-detail-approve").click(function () {
              handleApproveReject(val.req_no, 3);
            });

            $("#btn-detail-reject").click(function () {
              handleApproveReject(val.req_no, 5);
            });
          }

          $("#modalDetail").modal("show");
        } else {
          Swal.fire({
            title: "Gagal",
            text: res.message || "Gagal memuat detail Planned",
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

  function handleApproveReject(reqNo, targetStatus) {
    let statusText = targetStatus == 3 ? "Approve" : "Reject";
    let inputPlaceholder =
      targetStatus == 3
        ? "Alasan persetujuan (opsional)"
        : "Alasan penolakan (wajib)";

    Swal.fire({
      title: statusText + " Request Planned",
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
          common.baseURL("setup_planned/update_status"),
          {
            req_no: reqNo,
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
