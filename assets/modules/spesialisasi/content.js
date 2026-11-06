(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("List Spesialisasi");
  // ui components
  let uiTbl = $("#tbl-spesialisasi");
  let selectedSpecs = {};

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
      common.removeCookie("module.spesialisasi.update");
      common.direct("spesialisasi/form");
    });
    $(document)
      .off("click", "#btn-delete-selected")
      .on("click", "#btn-delete-selected", function (e) {
        e.preventDefault();
        deleteSelectedRows();
      });
  }

  function updateBulkDeleteButton() {
    const count = Object.keys(selectedSpecs).length;
    const $btn = $("#btn-delete-selected");
    if (count > 0) {
      $btn
        .html(`<i class="fa fa-trash"></i> Hapus Terpilih (${count})`)
        .show();
    } else {
      $btn.hide();
    }
  }

  function syncCheckAllState() {
    const panel = uiTbl.datagrid("getPanel");
    const $rowChks = panel.find(".chk-row-spec");
    const $chkAll = panel.find("#chk-all-spec");
    if (
      $rowChks.length > 0 &&
      $rowChks.filter(":checked").length === $rowChks.length
    ) {
      $chkAll.prop("checked", true);
    } else {
      $chkAll.prop("checked", false);
    }
  }

  function initializeGrid() {
    let option = {
      title: "List Spesialisasi",
      toolbar: toolbar(),
      url: common.baseURL("spesialisasi/load"),
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "chk_spec",
            title:
              '<input type="checkbox" id="chk-all-spec" class="grid-checkbox" title="Pilih Semua">',
            width: 35,
            halign: "center",
            align: "center",
            formatter: formatterCheckbox,
          },
          {
            field: "options",
            title: "Action",
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
            field: "id",
            title: "ID SPESIALISASI",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 50,
          },
          {
            field: "name",
            title: "NAMA SPESIALISASI",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 250,
          },
        ],
      ],
      onBeforeLoad: function (param) {},
      onLoadSuccess: function (data) {
        $(this).datagrid("resize");
        syncCheckAllState();
        updateBulkDeleteButton();
      },
    };
    let gridOptions = commonGrid.optionValue(option);
    gridOptions.selectOnCheck = false;
    gridOptions.checkOnSelect = false;
    uiTbl.datagrid(gridOptions);
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["chk_spec", "options"]);

    const panel = uiTbl.datagrid("getPanel");
    panel.find("#chk-all-spec").unbind(".datagrid");

    panel
      .off("change", "#chk-all-spec")
      .on("change", "#chk-all-spec", function (e) {
        e.stopPropagation();
        const isChecked = $(this).is(":checked");
        const rows = uiTbl.datagrid("getRows");
        panel.find(".chk-row-spec").prop("checked", isChecked);
        rows.forEach(function (row) {
          if (!row || !row.id) return;
          if (isChecked) {
            selectedSpecs[row.id] = row;
          } else {
            delete selectedSpecs[row.id];
          }
        });
        updateBulkDeleteButton();
      });

    panel
      .off("click", ".chk-row-spec")
      .on("click", ".chk-row-spec", function (e) {
        e.stopPropagation();
      });

    panel
      .off("change", ".chk-row-spec")
      .on("change", ".chk-row-spec", function (e) {
        e.stopPropagation();
        const tr = $(this).closest("tr.datagrid-row");
        const index = parseInt(tr.attr("datagrid-row-index"));
        const rows = uiTbl.datagrid("getRows");
        const row = !isNaN(index) && rows[index] ? rows[index] : null;
        const id = row ? row.id : $(this).data("id");

        if (id) {
          if ($(this).is(":checked")) {
            selectedSpecs[id] = row || {
              id: id,
              name: $(this).data("name"),
            };
          } else {
            delete selectedSpecs[id];
          }
        }
        syncCheckAllState();
        updateBulkDeleteButton();
      });

    panel
      .off("click", ".action-grid a.btn-success, .action-grid #btn-viem")
      .on("click", ".action-grid a.btn-success, .action-grid #btn-viem", function (e) {
        e.preventDefault();
        const tr = $(this).closest("tr.datagrid-row");
        const index = parseInt(tr.attr("datagrid-row-index"));
        const rows = uiTbl.datagrid("getRows");
        if (!isNaN(index) && rows[index]) {
          open_edit(rows[index]);
        }
      });

    panel
      .off("click", ".action-grid #btn-delete")
      .on("click", ".action-grid #btn-delete", function (e) {
        e.preventDefault();
        const tr = $(this).closest("tr.datagrid-row");
        const index = parseInt(tr.attr("datagrid-row-index"));
        const rows = uiTbl.datagrid("getRows");
        if (!isNaN(index) && rows[index]) {
          deleteRow(rows[index]);
        }
      });
  }

  function toolbar() {
    const btnCreate = commonGrid.btnBuilderText(
      "btn-create",
      "success",
      "fa fa-plus",
      "Tambah",
    );
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "info",
      "fa fa-download",
      " Download",
    );
    const btnDeleteSelected =
      '<a id="btn-delete-selected" href="javascript:void(0)" class="btn btn-danger btn-xs btn-bulk-delete"><i class="fa fa-trash"></i> Hapus Terpilih</a>';
    return (
      '<div class="action-grid-toolbar">' +
      btnCreate +
      btnDownload +
      btnDeleteSelected +
      "</div>"
    );
  }

  function formatterCheckbox(val, row, index) {
    if (!row || !row.id) return "";
    const isChecked = selectedSpecs[row.id] ? "checked" : "";
    const specLabel = (row.name ? `${row.id} - ${row.name}` : row.id)
      .toString()
      .replace(/"/g, "&quot;");
    return `<input type="checkbox" class="chk-row-spec grid-checkbox" data-id="${row.id}" data-name="${specLabel}" ${isChecked}>`;
  }

  function formatterButton(val, row, index) {
    const btnEdit = commonGrid.btnBuilder("btn-viem", "success", "fa fa-edit");
    const btnDelete = commonGrid.btnBuilder(
      "btn-delete",
      "danger",
      "fa fa-trash",
    );
    return (
      '<div class="action-grid action-grid-flex">' +
      btnEdit +
      btnDelete +
      "</div>"
    );
  }

  function open_edit(data) {
    common.setCookie("module.spesialisasi.update", data);
    common.direct("spesialisasi/form");
  }

  function deleteRow(val) {
    const specLabel = val.name ? `${val.id} - ${val.name}` : val.id;
    $.confirm({
      title: "Confirmation!",
      content: `Apakah Anda yakin ingin menghapus Spesialisasi <b>${specLabel}</b>?`,
      buttons: {
        confirm: {
          btnClass: "btn-red",
          action: function () {
            $.post("spesialisasi/delete", { id: val.id }, function (data) {
              if (data && 200 === data.code) {
                delete selectedSpecs[val.id];
                updateBulkDeleteButton();
                $.alert("Delete success!");
                uiTbl.datagrid("reload");
              } else {
                $.alert("Delete failed!");
              }
            });
          },
        },
        cancel: function () {},
      },
    });
  }

  function deleteSelectedRows() {
    const ids = Object.keys(selectedSpecs);
    if (ids.length === 0) return;

    const listItems = ids
      .map(function (id) {
        const s = selectedSpecs[id];
        const label = s.name ? `${s.id} - ${s.name}` : id;
        return `<li><b>${label}</b></li>`;
      })
      .join("");

    $.confirm({
      title: "Konfirmasi Hapus Terpilih!",
      content: `
        <div>Apakah Anda yakin ingin menghapus <b>${ids.length}</b> Spesialisasi berikut?</div>
        <div class="bulk-delete-box">
          <ol class="bulk-delete-list">${listItems}</ol>
        </div>
      `,
      buttons: {
        confirm: {
          text: "Hapus",
          btnClass: "btn-red",
          action: function () {
            common.loading();
            $.post(
              "spesialisasi/delete",
              { ids: ids },
              function (data) {
                common.loadingClose();
                if (data && 200 === data.code) {
                  selectedSpecs = {};
                  updateBulkDeleteButton();
                  $.alert("Delete success!");
                  uiTbl.datagrid("reload");
                } else {
                  $.alert((data && data.message) || "Delete failed!");
                }
              },
            ).fail(function () {
              common.loadingClose();
              $.alert("Delete failed!");
            });
          },
        },
        cancel: {
          text: "Batal",
          action: function () {},
        },
      },
    });
  }

  function save_xls(start, end) {
    common.direct("spesialisasi/savetoxlsx");
  }
})();
