(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Visibility Program");
    // ui components
    let uiTbl = $("#tbl-visibility-program");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.visibility.program.active.update");
            common.direct("visibility_program_active/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Visibility Program Active",
            toolbar: toolbar(),
            url: common.baseURL("visibility_program_active/load"),
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
				{field:'program_name', title:'Program Name', halign: 'left', align: 'left', sortable:"true", width:50},
				//{field:'description', title:'Description', halign: 'left', align: 'left', sortable:"true", width:20},
				{field:'qty_submit_photo', title:'Qty Photo', halign: 'left', align: 'left', sortable:"true", width:20},
				{field:'start_period', title:'Start Period', halign: 'center', align: 'center', sortable:"true", width:20},
				{field:'end_period', title:'End Period', halign: 'center', align: 'center', sortable:"true", width:20},
				{field:'account_name', title:'Account', halign: 'center', align: 'center', sortable:"true", width:50},
				{field:'nama_regional', title:'Regional', halign: 'center', align: 'center', sortable:"true", width:20},
				{field:'nama_area', title:'Area', halign: 'center', align: 'center', sortable:"true", width:20},
				// {field:'city', title:'City', halign: 'center', align: 'center', sortable:"true", width:50},
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

    function updateRow(val) {
        common.setCookie("module.visibility.program.active.update", val);
        common.direct("visibility_program_active/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("visibility_program_active/delete", val, function (data, status) {
                if (200 === data.code) {
                    $.alert("Delete success!");
                    uiTbl.datagrid("reload");
                } else {
                    $.alert(status);
                }
            })
        });
    }

})();