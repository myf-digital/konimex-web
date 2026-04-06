(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("History Upload");
    // ui components
    let uiTbl = $("#tbl-history-upload");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-back").click(function () {
            common.direct("professional/target");
        });
    }

    function initializeGrid() {
        let option = {
            title: "History Upload",
            toolbar: toolbar(),
            url: common.baseURL("professional/load_history"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            frozenColumns: [[
                {
                    field: 'options',
                    title: 'ACTION',
                    width: 100,
                    halign: 'center',
                    align: 'center',
                    formatter: formatterButton
                }
            ]],
            columns: [[
				{field:'id', title:'UPLOAD ID', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'created_at', title:'PERIODE', halign: 'center', align: 'center', sortable:"true", width:100, formatter: formatterDate},
				{field:'file_name', title:'FILE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'total_row', title:'TOTAL DATA', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatterNumber},
            ]],
            onBeforeLoad: function (param) {
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
                optionButton(data);
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
        uiTbl.datagrid('enableFilter');
        common.removeFilter(['options']);
    }
    
    function toolbar() {
        const btnBack = commonGrid.btnBuilderText('btn-back', 'warning', 'fa fa-backward', ' Kembali ke Target Professional');
        return '<div class="action-grid-toolbar">' + btnBack + '</div>';
    }

    function formatterNumber(val, row, index) {
		return val ? val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '';
	}

    function formatterDate(val, row, index) {
        moment.locale('id');
		return val ? moment(val).format("DD MMM YYYY") : '';
	}
    
    function formatterButton(val, row, index) {
        const btnDetail = commonGrid.btnBuilderText('btn-update', 'info', 'fa fa-eye', ' Detail');
        return '<div class="action-grid">' + btnDetail + '</div>';
    }

    function optionButton(data) {
        let btnContent = $(".action-grid");
        let index = 0;
        for (const btns of btnContent) {
            const param = data.rows[index];
            const btnDetail = $(btns).find("a.btn-info");
            btnDetail.click(function () {
                common.direct("professional/history_upload_detail?id=" + param.id);
            });
            index++;
        }
    }
})();