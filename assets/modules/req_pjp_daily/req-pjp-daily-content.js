(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Request PJP Daily");
  // ui components
  let uiTbl = $("#tbl-req-pjp-daily");
  let paramsession = common.getCookie("session");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.req.pjp.daily.update");
      common.direct("req_pjp_daily/form_addpjp");
    });
    $("#btn-download").click(function () {
      common.removeCookie("module.req.pjp.daily.update");
      common.direct("req_pjp_daily/form_download");
    });
  }

  function initializeGrid() {
    let option = {
      title: "Request PJP Daily",
      toolbar: toolbar(),
      url: common.baseURL("req_pjp_daily/load"),
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
            field: "periode",
            title: "Periode",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "salesmanid",
            title: "TPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "salesman_name",
            title: "Nama TPE",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "status_label",
            title: "Status",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "reason",
            title: "Reason",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "keterangan",
            title: "Keterangan",
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
            width: 100,
          },
        ],
      ],
      onBeforeLoad: function (param) {
        param = common.replaceGridFilterPrefix(param, "a");
      },
      onLoadSuccess: function (data) {
        $(this).datagrid("resize");
        common.removeCookie("module.req.pjp.daily.update");
        optionButton(data);
      },
    };

    uiTbl.datagrid(commonGrid.optionValue(option));
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["options"]);
  }

  function toolbar() {
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "primary",
      "fa fa-download",
      " Download PJP Daily",
    );
    return (
      '<div class="action-grid-toolbar">' +
      " Week Aktif : " +
      paramsession.week_aktif +
      btnDownload +
      "</div>"
    ); //+ btnUpload
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
    common.loading();
    $.post(
      common.baseURL("api_v1/call_pjp_detail"),
      {
        salesmanid: val.salesmanid,
        req_no: val.req_no,
        type: "daily",
      },
      function (res) {
        val.pjp_detail = res.result;
        common.setCookie("module.req.pjp.daily.update", val);
        common.direct("req_pjp_daily/form");
      },
    );
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("req_pjp_daily/delete", val, function (data, status) {
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
