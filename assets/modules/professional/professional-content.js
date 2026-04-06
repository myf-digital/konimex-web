(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("List Professional");
    // ui components
    let uiTbl = $("#tbl-professional");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
    }

    function initializeGrid() {
        let option = {
            title: "List Professional",
            // toolbar: toolbar(),
            url: common.baseURL("professional/load"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            columns: [[
				{field:'cab', title:'KODE CABANG', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'cabang', title:'CABANG', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'customerid', title:'KODE PRODUK', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'nama_customer', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'id_professional', title:'KODE PROFESSIONAL', halign: 'center', align: 'center', sortable:"true", width:175},
				{field:'nama_professional', title:'NAMA PROFESSIONAL', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'productid', title:'KODE PRODUK', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'category_product', title:'KATEGORI PRODUK', halign: 'center', align: 'center', sortable:"true", width:175},
				{field:'nama_invoice', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'sat_kecil', title:'SATUAN KECIL', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'qty', title:'QTY', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'total', title:'TOTAL TARGET', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
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
        // return '<div class="action-grid-toolbar">' + btnSearch + '</div>';
    }
})();