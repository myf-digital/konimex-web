(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("List Spesialisasi");
  // ui components
  let uiTbl = $("#tbl-spesialisasi");

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
      "fa fa-plus",
      "Tambah",
    );
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "info",
      "fa fa-download",
      " Download",
    );
    return (
      '<div class="action-grid-toolbar">' + btnCreate + btnDownload + "</div>"
    );
  }

  function formatterButton(val, row, index) {
    const btnEdit = commonGrid.btnBuilder("btn-viem", "success", "fa fa-edit");
    return '<div class="action-grid">' + btnEdit + "</div>";
  }

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnEdit = $(btns).find("a.btn-success");
      btnEdit.click(function () {
        open_edit(param);
      });
      index++;
    }
  }

  function open_edit(data) {
    common.setCookie("module.spesialisasi.update", data);
    common.direct("spesialisasi/form");
  }

  function save_xls(start, end) {
    common.direct("spesialisasi/savetoxlsx");
  }
})();
