(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Report SOS");
    // ui components
    let uiTbl = $("#tbl-report-sos");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.report.sos.update");
            common.direct("rep_sos/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Report SOS",
            toolbar: toolbar(),
            url: common.baseURL("rep_sos/load"),
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
				//{field:'idpromo', title:'IDPROMO', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'promo', title:'Promo Name', halign: 'left', align: 'left', sortable:"true", width:250},
				{field:'account', title:'Account', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'start_periode', title:'Start Periode', halign: 'center', align: 'center', sortable:"true", width:80},
				{field:'end_periode', title:'End Periode', halign: 'center', align: 'center', sortable:"true", width:80},
				{field:'description', title:'Description', halign: 'left', align: 'left', sortable:"true", width:250},
				//{field:'created_by', title:'Created By', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_date', title:'Created Date', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_by', title:'Modified By', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_date', title:'Modified Date', halign: 'center', align: 'left', sortable:"true", width:200},
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
        const btnCreate = commonGrid.btnBuilderDash('btn-create', 'success', '../assets/images/ic_edit.png');
        return '<div class="action-grid-toolbar">' + btnCreate + '</div>';
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
        const btnUpdate = commonGrid.btnBuilderDash('btn-update', 'success', '../assets/images/ic_edit.png');
        const btnDelete = commonGrid.btnBuilderDash('btn-delete', 'danger', '../assets/images/ic_trash.png');
        return '<div class="action-grid">' + btnUpdate + ' ' + btnDelete + '</div>';
    }


})();