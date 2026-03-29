(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Stock Product");
    // ui components
    let uiTbl = $("#tbl-stock-product");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-upload").click(function () {
            common.removeCookie("module.stock-product.upload");
            common.direct("stock_product/form_upload");
        });
        $("#btn-history").click(function () {
            common.removeCookie("module.stock-product.history_upload");
            common.direct("stock_product/history_upload");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Stock Product",
            toolbar: toolbar(),
            url: common.baseURL("stock_product/load"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            columns: [[
				{field:'kode_product_principal', title:'KODE PRODUK', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'nama_product', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_cabang', title:'KODE CABANG', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'cabang', title:'CABANG', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'expired_date', title:'EXPIRED DATE', halign: 'center', align: 'center', sortable:"true", width:100, formatter: formatterDate},
				{field:'batch_num', title:'BATCH NUMBER', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'qty_rusak', title:'QTY RUSAK', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'qty_baik', title:'QTY BAIK', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'qty_total', title:'QTY TOTAL', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'qty_terjual', title:'QTY TERJUAL', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'stock_sisa', title:'STOCK SISA', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
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

    function formatterDate(val, row, index) {
        moment.locale('id');
		return val ? moment(val, "YYYYMM").format("MMM YYYY") : '';
	}

    function toolbar() {
        // const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnUpload = commonGrid.btnBuilderText('btn-upload', 'success', 'fa fa-upload', ' Upload Stock Product');
        const btnHistory = commonGrid.btnBuilderText('btn-history', 'info', 'fa fa-history', ' History Upload');
        return '<div class="action-grid-toolbar">' + btnUpload + btnHistory + '</div>';
    }
})();