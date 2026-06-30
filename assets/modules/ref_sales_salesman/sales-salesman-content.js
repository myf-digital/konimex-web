(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Karyawan");
  // ui components
  let uiTbl = $("#tbl-sales-salesman");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.sales.salesman.update");
      common.direct("ref_sales_salesman/form");
    });
    $("#btn-org").click(function () {
      common.direct("ref_sales_salesman/salesman_org");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Karyawan",
      toolbar: toolbar(),
      url: common.baseURL("ref_sales_salesman/load"),
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "options",
            title: "ACTION",
            width: 130,
            halign: "center",
            align: "center",
            formatter: formatterButton,
          },
        ],
      ],
      columns: [
        [
          {
            field: "salesmanid",
            title: "USER MEDREP",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_salesman",
            title: "NAMA MEDREP",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 250,
          },
          {
            field: "supervisor",
            title: "LEADER",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "tipe_sales",
            title: "MEDREP TYPE",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_regional",
            title: "Regional",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 120,
            formatter: common.formatListArea,
          },
          {
            field: "nama_area",
            title: "Area",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 120,
            formatter: common.formatListArea,
          },
          {
            field: "nama_subarea",
            title: "Sub Area",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 120,
            formatter: common.formatListArea,
          },
          {
            field: "aktifstatus",
            title: "STATUS",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 100,
          },
        ],
      ],
      onBeforeLoad: function (param) {},
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
    const btnCreate = commonGrid.btnBuilderDash(
      "btn-create",
      "success",
      "../assets/images/ic_edit.png",
    );
    const btnOrg = commonGrid.btnBuilder("btn-org", "danger", "fa fa-users");
    return '<div class="action-grid-toolbar">' + btnCreate + btnOrg + "</div>";
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
        detailMappingRow(param);
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
    const btnDetail = commonGrid.btnBuilderDash(
      "btn-detail",
      "info",
      "../assets/images/ic_detail.png",
    );
    const btnDelete = commonGrid.btnBuilderDash(
      "btn-delete",
      "danger",
      "../assets/images/ic_trash.png",
    );
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

  function updateRow(val) {
    common.setCookie("module.sales.salesman.update", val);
    common.direct("ref_sales_salesman/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("ref_sales_salesman/delete", val, function (data, status) {
        if (200 === data.code) {
          $.alert("Delete success!");
          uiTbl.datagrid("reload");
        } else {
          $.alert(status);
        }
      });
    });
  }

  function detailMappingRow(val) {
    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/detail_mapping"),
      { salesmanid: val.salesmanid },
      function (res) {
        common.loadingClose();
        if (res.status) {
          $("#detail-salesman-name").text(res.salesman.nama_salesman || "-");
          $("#detail-salesman-id").text(res.salesman.salesmanid || "-");
          $("#detail-salesman-tipe").text(res.salesman.tipe_sales || "-");
          if (res.salesman && res.salesman.supervisorid) {
            $("#detail-salesman-supervisor").text(
              res.salesman.supervisorid + " - " + res.salesman.supervisor,
            );
          }

          let roleActiveBody = $("#role-active-body");
          roleActiveBody.empty();
          if (res.role.active.length === 0) {
            roleActiveBody.append(
              `<tr><td colspan="5" style="text-align:center;color:#999;padding:12px;">Belum ada mapping target.</td></tr>`,
            );
          } else {
            res.role.active.forEach(function (h) {
              roleActiveBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">${h.target_hk || 0}</td>
                        <td class="td-detail">${h.target_dub || 0}</td>
                        <td class="td-detail">${h.target_call_dub || 0}</td>
                        <td class="td-detail">${h.target_call_visit || 0}</td>
                    </tr>
                `);
            });
          }

          let roleHistoryBody = $("#role-history-body");
          roleHistoryBody.empty();
          if (res.role.history.length === 0) {
            roleHistoryBody.append(
              `<tr><td colspan="5" style="text-align:center;color:#999;padding:12px;">Belum ada history mapping target.</td></tr>`,
            );
          } else {
            res.role.history.forEach(function (h) {
              roleHistoryBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">${h.target_hk || 0}</td>
                        <td class="td-detail">${h.target_dub || 0}</td>
                        <td class="td-detail">${h.target_call_dub || 0}</td>
                        <td class="td-detail">${h.target_call_visit || 0}</td>
                    </tr>
                `);
            });
          }

          let spesialisActiveBody = $("#spesialis-active-body");
          spesialisActiveBody.empty();
          if (res.spesialis.active.length === 0) {
            spesialisActiveBody.append(
              `<tr><td colspan="3" style="text-align:center;color:#999;padding:12px;">Belum ada mapping target.</td></tr>`,
            );
          } else {
            res.spesialis.active.forEach(function (h) {
              spesialisActiveBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">${h.nama_spesialisasi || "-"}</td>
                        <td class="td-detail">${h.target || 0}</td>
                    </tr>
                `);
            });
          }

          let spesialisHistoryBody = $("#spesialis-history-body");
          spesialisHistoryBody.empty();
          if (res.spesialis.history.length === 0) {
            spesialisHistoryBody.append(
              `<tr><td colspan="3" style="text-align:center;color:#999;padding:12px;">Belum ada history mapping target.</td></tr>`,
            );
          } else {
            res.spesialis.history.forEach(function (h) {
              spesialisHistoryBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">${h.nama_spesialisasi || "-"}</td>
                        <td class="td-detail">${h.target || 0}</td>
                    </tr>
                `);
            });
          }

          let produkActiveBody = $("#produk-active-body");
          produkActiveBody.empty();
          if (res.produk.active.length === 0) {
            produkActiveBody.append(
              `<tr><td colspan="3" style="text-align:center;color:#999;padding:12px;">Belum ada mapping target.</td></tr>`,
            );
          } else {
            res.produk.active.forEach(function (h) {
              produkActiveBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">${h.product_id || ""} - ${h.nama_invoice || ""}</td>
                        <td class="td-detail">${h.target || 0}</td>
                        <td class="td-detail">${h.target_qty || 0}</td>
                    </tr>
                `);
            });
          }

          let produkHistoryBody = $("#produk-history-body");
          produkHistoryBody.empty();
          if (res.produk.history.length === 0) {
            produkHistoryBody.append(
              `<tr><td colspan="3" style="text-align:center;color:#999;padding:12px;">Belum ada history mapping target.</td></tr>`,
            );
          } else {
            res.produk.history.forEach(function (h) {
              produkHistoryBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">${h.product_id || ""} - ${h.nama_invoice || ""}</td>
                        <td class="td-detail">${h.target || 0}</td>
                        <td class="td-detail">${h.target_qty || 0}</td>
                    </tr>
                `);
            });
          }

          let salesActiveBody = $("#sales-active-body");
          salesActiveBody.empty();
          if (
            !res.sales ||
            !res.sales.active ||
            res.sales.active.length === 0
          ) {
            salesActiveBody.append(
              `<tr><td colspan="2" style="text-align:center;color:#999;padding:12px;">Belum ada mapping target.</td></tr>`,
            );
          } else {
            res.sales.active.forEach(function (h) {
              salesActiveBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">Rp ${formatRupiah(h.total_target || 0)}</td>
                    </tr>
                `);
            });
          }

          let salesHistoryBody = $("#sales-history-body");
          salesHistoryBody.empty();
          if (
            !res.sales ||
            !res.sales.history ||
            res.sales.history.length === 0
          ) {
            salesHistoryBody.append(
              `<tr><td colspan="2" style="text-align:center;color:#999;padding:12px;">Belum ada history mapping target.</td></tr>`,
            );
          } else {
            res.sales.history.forEach(function (h) {
              salesHistoryBody.append(`
                    <tr>
                        <td class="td-detail">${h.tahun && h.bulan ? moment(`${h.tahun}-${h.bulan}-01`, "YYYY-M-DD").locale("id").format("MMMM YYYY") : "-"}</td>
                        <td class="td-detail">Rp ${formatRupiah(h.total_target || 0)}</td>
                    </tr>
                `);
            });
          }
          $("#mappingTabs a:first").tab("show");

          $("#modalMappingDetail").modal("show");
        } else {
          Swal.fire({
            title: "Warning",
            text: res.message || "Gagal memuat detail mapping",
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

  function formatRupiah(value) {
    if (!value) return "0";
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
})();
