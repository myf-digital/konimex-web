(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Outlet");
  // ui components
  let uiTbl = $("#tbl-request_new_outlet");
  let paramsession = common.getCookie("session");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.request_new_outlet.update");
      common.direct("/ref_request_new_outlet/form");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Outlet",
      toolbar: toolbar(),
      url: common.baseURL("ref_request_new_outlet/load"),
      queryParams: {
        usersession: paramsession.username,
        idjabatan: paramsession.idjabatan,
        restrict_level: paramsession.restrict_level,
        restrict_bu: paramsession.restrict_bu,
      },
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "options",
            title: "ACTION",
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
            field: "kode_outlet",
            title: "Kode Outlet",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "nama_customer",
            title: "Nama Outlet",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "nama_regional",
            title: "Regional",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_area",
            title: "Area",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 120,
          },
          // {
          //   field: "nama_subarea",
          //   title: "Sub Area",
          //   halign: "center",
          //   align: "left",
          //   sortable: "true",
          //   width: 120,
          // },
          {
            field: "typeid",
            title: "Channel",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "alamat",
            title: "Alamat",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 350,
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

  function toolbar() {}

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnEdit = $(btns).find("a.btn-success");
      const btnDelete = $(btns).find("a.btn-danger");
      btnEdit.click(function () {
        updateRow(param);
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
    const btnUpdate = commonGrid.btnBuilderText(
      "btn-update",
      "success",
      "fa fa-pencil",
      "Review",
    );
    const btnDelete = commonGrid.btnBuilderText(
      "btn-delete",
      "danger",
      "fa fa-times",
      "Reject",
    );
    return '<div class="action-grid">' + btnUpdate + " " + btnDelete + "</div>";
  }

  function updateRow(val) {
    common.setCookie("module.request_new_outlet.update", val);
    common.direct("ref_request_new_outlet/form");
  }

  function deleteRow(val) {
    common.dialogReject(function () {
      $.post("ref_request_new_outlet/delete", val, function (data, status) {
        if (200 === data.code) {
          $.alert("Rejected...!");
          uiTbl.datagrid("reload");
        } else {
          $.alert(status);
        }
      });
    });
  }
})();
