(function () {
  const common = new Common();
  common.setTitle("Struktur Organisasi");

  let uiBtnBack = $("#btn-back");
  let paramsession = common.getCookie("session");
  let dataSalesman = [];

  initialize();

  function initialize() {
    loadSalesman(paramsession);

    uiBtnBack.click(function () {
      common.direct("ref_sales_salesman");
    });
  }

  function loadSalesman(data) {
    common.loading();
    $.post(
      common.baseURL("ref_sales_salesman/data_salesman"),
      {
        usersession: data.usersession,
        restrict_level: data.restrict_level,
      },
      function (res) {
        dataSalesman = res;

        loadSalesmanOrg();

        common.loadingClose();
      },
    );
  }

  function loadSalesmanOrg() {
    const chart = new OrgChart(document.getElementById("tree"), {
      template: "ula",
      menu: null,
      lazyLoading: true,
      enableDragDrop: false,
      enableSearch: true,
      searchDisplayField: "nama_salesman",
      scaleInitial: 1.2,
      mouseScroll: OrgChart.action.zoom,
      nodeBinding: {
        field_0: "nama_salesman",
        field_1: "tipe_sales",
        img_0: "img",
      },
      editForm: {
        buttons: {
          edit: null,
          share: null,
          pdf: null,
        },
        elements: [
          { type: "textbox", label: "Nama", binding: "nama_salesman" },
        ],
      },
      collapse: {
        level: 2,
      },
      nodes: dataSalesman,
      tags: {
        manager: {
          template: "ula",
        },
      },
    });

    setTimeout(() => {
      chart.center("05152");
      let label = document.querySelector(".boc-search .boc-input label");
      if (label) {
        label.innerHTML = "Cari nama...";
      }
    }, 250);
  }
})();
