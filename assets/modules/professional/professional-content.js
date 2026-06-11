(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("List User");
  // ui components
  let uiTbl = $("#tbl-professional");

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
            width: 120,
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

        let $grid = $(this);
        setTimeout(function () {
          $grid.datagrid("autoSizeColumn", "customer_list");
          let col = $grid.datagrid("getColumnOption", "customer_list");
          if (col && col.width < 450) {
            $grid.datagrid("resizeColumn", { field: "customer_list", width: 450 });
          }
        }, 50);

        optionButton(data);
      },
    };
    let gridOptions = commonGrid.optionValue(option);
    gridOptions.fitColumns = false;
    uiTbl.datagrid(gridOptions);
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
    let btnImage = "";
    if (
      (row.url_foto && row.url_foto != undefined) ||
      (row.url_img_signature && row.url_img_signature != undefined)
    )
      btnImage = commonGrid.btnBuilder("btn-viem-image", "info", "fa fa-image");
    const btnEdit = commonGrid.btnBuilder("btn-viem", "success", "fa fa-edit");
    return '<div class="action-grid">' + btnImage + btnEdit + "</div>";
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

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnEdit = $(btns).find("a.btn-success");
      btnEdit.click(function () {
        open_edit(param);
      });
      const btnImage = $(btns).find("a.btn-info");
      btnImage.click(function () {
        open_image(param);
      });
      index++;
    }
  }

  function open_edit(data) {
    common.setCookie("module.professional.update", data);
    common.direct("professional/form");
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
})();
