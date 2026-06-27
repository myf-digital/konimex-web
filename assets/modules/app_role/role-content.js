(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Role");
  // ui components
  let uiTbl = $("#tbl-role");
  let keyRoleSales = "";

  common.loading();
  $.post(
    common.baseURL("api_v1/call_param_key"),
    { parkey: "key_role_sales" },
    function (res) {
      if (res.result && res.result.length > 0) {
        const valRoles = res.result.find(
          (r) => r.key_param == "key_role_sales",
        );
        keyRoleSales = valRoles.value;
      }
      initializeGrid();
      initialize();
      common.loadingClose();
    }
  ).fail(function () {
    initializeGrid();
    initialize();
    common.loadingClose();
  });

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.role.update");
      common.direct("app_role/form");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Role",
      toolbar: toolbar(),
      url: common.baseURL("app_role/load"),
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
            field: "role_name",
            title: "ROLE NAME",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "description",
            title: "DESCRIPTION",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "-",
            title: "LAST MODIFIED",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 200,
            formatter: formaterLastModified,
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
  }

  function toolbar() {
    const btnCreate = commonGrid.btnBuilderDash(
      "btn-create",
      "success",
      "../assets/images/ic_edit.png",
    );
    return '<div class="action-grid-toolbar">' + btnCreate + "</div>";
  }

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnEdit = $(btns).find("a.btn-success");
      const btnDetail = $(btns).find("a.btn-info");
      const btnDelete = $(btns).find("a.btn-danger");
      btnEdit.click(function () {
        updateRow(param);
      });
      btnDetail.click(function () {
        detailRow(param);
      });
      btnDelete.click(function () {
        deleteRow(param);
      });
      index++;
    }
  }

  /*
   * action button generator
   */
  function formatterButton(val, row, index) {
    const btnUpdate = commonGrid.btnBuilderDash(
      "btn-update",
      "success",
      "../assets/images/ic_edit.png",
    );
    const btnDelete = commonGrid.btnBuilderDash(
      "btn-delete",
      "danger",
      "../assets/images/ic_trash.png",
    );

    let btnDetail = "";
    if (row.role_name && keyRoleSales) {
      let normalized = row.role_name.toUpperCase();
      if (keyRoleSales.includes(normalized)) {
        btnDetail = commonGrid.btnBuilderDash(
          "btn-detail",
          "info",
          "../assets/images/ic_detail.png",
        ) + " ";
      }
    }

    return (
      '<div class="action-grid">' +
      btnUpdate +
      " " +
      btnDetail +
      btnDelete +
      "</div>"
    );
  }

  function formaterLastModified(val, row, index) {
    if (row.modified_date) {
      return (
        "at: " +
        moment(row.modified_date).locale("id").format("DD MMMM YYYY") +
        " <br/> by: " +
        (row.modified_by || "-")
      );
    } else if (row.created_date) {
      return (
        "at: " +
        moment(row.created_date).locale("id").format("DD MMMM YYYY") +
        " <br/> by: " +
        (row.created_by || "-")
      );
    }
    return "";
  }

  function updateRow(val) {
    common.setCookie("module.role.update", val);
    common.direct("app_role/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("app_role/delete", val, function (data, status) {
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
      common.baseURL("app_role/detail"),
      { role_id: val.role_id },
      function (res) {
        common.loadingClose();
        if (res.status) {
          $("#detail-role-name").text(val.role_name || "-");
          $("#detail-description").text(val.description || "-");

          let body = $("#detail-list-body");
          body.empty();

          let detail = res.result || null;
          if (!detail || (detail && !detail.target_dub)) {
            body.append(
              `<tr>
                <td colspan="5" style="text-align:center;color:#999;padding:12px;">
                  Belum ada mapping target.
                </td>
              </tr>`,
            );
          } else {
            body.append(`
              <tr>
                <td class="td-detail">${
                  detail.tahun && detail.bulan
                    ? moment(`${detail.tahun}-${detail.bulan}-01`, "YYYY-M-DD")
                        .locale("id")
                        .format("MMMM YYYY")
                    : "-"
                }</td>
                <td class="td-detail">${detail.target_hk}</td>
                <td class="td-detail">${detail.target_dub}</td>
                <td class="td-detail">${detail.target_call_dub}</td>
                <td class="td-detail">${detail.target_call_visit}</td>
              </tr>
            `);
          }

          let bodyHistory = $("#history-list-body");
          bodyHistory.empty();

          let history = res.result && res.result.history;
          if (!history || (history && history.length == 0)) {
            bodyHistory.append(
              `<tr>
                <td colspan="5" style="text-align:center;color:#999;padding:12px;">
                  Belum ada history mapping target.
                </td>
              </tr>`,
            );
          } else {
            history.forEach((h) => {
              bodyHistory.append(`
                <tr>
                  <td class="td-detail">${
                    h.tahun && h.bulan
                      ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD")
                          .locale("id")
                          .format("MMMM YYYY")
                      : "-"
                  }</td>
                  <td class="td-detail">${h.target_hk}</td>
                  <td class="td-detail">${h.target_dub}</td>
                  <td class="td-detail">${h.target_call_dub}</td>
                  <td class="td-detail">${h.target_call_visit}</td>
                </tr>
              `);
            });
          }

          let footer = $("#modalDetail .modal-footer");
          footer.empty();
          footer.append(
            '<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>',
          );

          $("#modalDetail").modal("show");
        } else {
          Swal.fire({
            title: "Warning",
            text: res.message || "Gagal memuat detail",
            icon: "warning",
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
})();
