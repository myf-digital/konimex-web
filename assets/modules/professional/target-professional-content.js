(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Target Professional");
    // ui components
    let uiTbl = $("#tbl-target-professional");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-upload").click(function () {
            common.removeCookie("module.professional.upload");
            common.direct("professional/form_upload");
        });
        $("#btn-history").click(function () {
            common.removeCookie("module.professional.history_upload");
            common.direct("professional/history_upload");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Target Professional",
            toolbar: toolbar(),
            url: common.baseURL("professional/load_target"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            columns: [[
				{field:'cab', title:'KODE CABANG', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'cabang', title:'CABANG', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'customerid', title:'KODE PRODUK', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'nama_customer', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'id_professional', title:'KODE PROFESSIONAL', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'nama_professional', title:'NAMA PROFESSIONAL', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'productid', title:'KODE PRODUK', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'nama_invoice', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'sat_kecil', title:'SATUAN KECIL', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'qty', title:'QTY', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'total', title:'TOTAL TARGET', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
				{field:'total_actual', title:'TOTAL ACTUAL', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
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
        const btnUpload = commonGrid.btnBuilderText('btn-upload', 'success', 'fa fa-upload', ' Upload Target Professional');
        const btnHistory = commonGrid.btnBuilderText('btn-history', 'info', 'fa fa-history', ' History Upload');
        return '<div class="action-grid-toolbar">' + btnUpload + btnHistory + '</div>';
    }
})();