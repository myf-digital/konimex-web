(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Setup Planned");
  // ui components
  let uiTbl = $("#tbl-setup-pjp");
  let paramsession = common.getCookie("session");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.setup.pjp.update");
      common.direct("conf_setup_pjp/form_addpjp");
    });

    $("#btn-download").click(function () {
      common.removeCookie("module.setup.pjp.update");
      common.direct("conf_setup_pjp/form_download");
    });

    $("#btn-switch").click(function () {
      common.removeCookie("module.setup.pjp.update");
      common.direct("conf_setup_pjp/form_switch");
    });

    $("#btn-upload").click(function () {
      common.removeCookie("module.setup.pjp.update");
      common.direct("conf_setup_pjp/form_upload");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Setup Planned",
      toolbar: toolbar(),
      url: common.baseURL("conf_setup_pjp/load"),
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
            width: 75,
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
            title: "USER TPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "gffname",
            title: "NAMA TPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "customerid",
            title: "TPE ID OUTLET",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "latest_customer_name",
            title: "LATEST CUSTOMER NAME",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "nama_customer",
            title: "TPE OUTLET NAME",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "alamat",
            title: "Alamat",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "city",
            title: "Area",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "mcc",
            title: "DC",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_class",
            title: "Tier",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "group_nama_minggu",
            title: "WEEKS",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 100,
          },
          {
            field: "group_nama_hari",
            title: "DAYS",
            halign: "center",
            align: "center",
            sortable: "true",
            width: 100,
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
    const btnUpload = commonGrid.btnBuilderText(
      "btn-upload",
      "info",
      "fa fa-upload",
      " Upload Planned",
    );
    return (
      '<div class="action-grid-toolbar">' +
      btnCreate +
      btnDownload +
      btnUpload +
      " Week Aktif : " +
      paramsession.week_aktif +
      "</div>"
    );
  }

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
    return '<div class="action-grid">' + btnUpdate + " " + btnDelete + "</div>";
  }

  function updateRow(val) {
    common.setCookie("module.setup.pjp.update", val);
    common.direct("conf_setup_pjp/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("conf_setup_pjp/delete", val, function (data, status) {
        if (200 === data.code) {
          $.alert("Delete success!");
          uiTbl.datagrid("reload");
        } else {
          $.alert(status);
        }
      });
    });
  }
})();
