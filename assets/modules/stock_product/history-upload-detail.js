(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    const paramId = new URLSearchParams(window.location.search).get('id');
    // update title
    common.setTitle("Detail History Upload");
    // ui components
    let uiTbl = $("#tbl-detail-history-upload");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-back").click(function () {
            common.removeCookie("module.detail-history-upload.history_upload");
            common.direct("stock_product/history_upload");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Detail History Upload",
            toolbar: toolbar(),
            url: common.baseURL("stock_product/load_history_detail") + "?id=" + paramId,
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            columns: [[
				{field:'periode', title:'PERIODE', halign: 'center', align: 'center', sortable:"true", width:100, formatter: formatterPeriode},
				{field:'kode_product_principal', title:'KODE PRODUK', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'nama_product', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_cabang', title:'KODE CABANG', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'cabang', title:'CABANG', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'expired_date', title:'EXPIRED DATE', halign: 'center', align: 'center', sortable:"true", width:100, formatter: formatterDate},
				{field:'batch_num', title:'BATCH NUMBER', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'qty_rusak', title:'QTY RUSAK', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'qty_baik', title:'QTY BAIK', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'qty_total', title:'QTY TOTAL', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
            ]],
            onBeforeLoad: function (param) {
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
        uiTbl.datagrid('enableFilter');
        common.removeFilter(['options']);
    }

    function formatterNumber(val, row, index) {
		return val ? val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '';
	}

    function formatterPeriode(val, row, index) {
        moment.locale('id');
		return val ? moment(val).format("DD MMM YYYY") : '';
	}

    function formatterDate(val, row, index) {
        moment.locale('id');
		return val ? moment(val, "YYYYMM").format("MMM YYYY") : '';
	}

    function toolbar() {
        const btnBack = commonGrid.btnBuilderText('btn-back', 'warning', 'fa fa-backward', ' Kembali ke History Upload');
        return '<div class="action-grid-toolbar">' + btnBack + '</div>';
    }
})();