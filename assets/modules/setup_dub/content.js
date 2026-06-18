(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Setup DUB");
  // ui components
  let uiTbl = $("#tbl-setup-dub");
  let paramsession = common.getCookie("session");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.setup_dub.update");
      common.direct("setup_dub/form");
    });

    $("#btn-download").click(function () {
      common.removeCookie("module.setup_dub.update");
      common.direct("setup_dub/form_download");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Setup DUB",
      toolbar: toolbar(),
      url: common.baseURL("setup_dub/load"),
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
            title: "ID MEDREP",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_salesman",
            title: "NAMA MEDREP",
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
      " Create DUB",
    );
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "primary",
      "fa fa-download",
      " Download DUB",
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
    common.setCookie("module.setup_dub.update", val);
    common.direct("setup_dub/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("setup_dub/delete", val, function (data, status) {
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
      common.baseURL("setup_dub/get_detail"),
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

          let body = $("#detail-list-body");
          body.empty();

          let details = res.result || [];
          if (details.length === 0) {
            body.append(
              '<tr><td colspan="2" style="text-align:center;color:#999;padding:12px;">Tidak ada detail professional/DUB.</td></tr>',
            );
          } else {
            let grouped = {};
            details.forEach(function (d) {
              let key = d.customerid;
              if (!grouped[key]) {
                grouped[key] = {
                  outletName: d.outlet || "Outlet tidak diketahui",
                  users: [],
                };
              }
              grouped[key].users.push(
                `<b>${d.user_id}</b> - ${d.user_name || "Professional tidak diketahui"}`,
              );
            });

            Object.keys(grouped).forEach(function (key) {
              let group = grouped[key];
              let outletCell = `<b>${key}</b> - ${group.outletName}`;
              let usersCell =
                `<ul style="margin: 0; padding-left: 20px;">` +
                group.users.map((u) => `<li>${u}</li>`).join("") +
                `</ul>`;
              body.append(`
                <tr>
                  <td class="td-detail w-50 font-weight-bold">${outletCell}</td>
                  <td class="td-detail w-50">${usersCell}</td>
                </tr>
              `);
            });

            body.append(`
              <tr style="background-color: #f9f9f9; font-weight: bold; border-top: 2px solid #ddd;">
                <td class="td-detail w-50 td-footer">Total Outlet: ${Object.keys(grouped).length}</td>
                <td class="td-detail w-50 td-footer">Total DUB: ${details.length}</td>
              </tr>
            `);
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
            text: res.message || "Gagal memuat detail DUB",
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
      title: statusText + " Request DUB",
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
          common.baseURL("setup_dub/update_status"),
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
