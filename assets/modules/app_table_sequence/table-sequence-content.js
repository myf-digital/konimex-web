(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Table Sequence");
    // ui components
    let uiTbl = $("#tbl-table-sequence");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.table.sequence.update");
            common.direct("app_table_sequence/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Table Sequence",
            toolbar: toolbar(),
            url: common.baseURL("app_table_sequence/load"),
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
				{field:'id', title:'ID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'name', title:'NAME', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'prefix', title:'PREFIX', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'increment', title:'INCREMENT', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'pad', title:'PAD', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'row', title:'ROW', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'used', title:'USED', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'last_insert_id', title:'LAST INSERT ID', halign: 'center', align: 'left', sortable:"true", width:200},
            ]],
            onBeforeLoad: function (param) {
                
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
                optionButton(data);
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
    }

    function toolbar() {
        const btnCreate = commonGrid.btnBuilder('btn-create', 'success', 'fa fa-pencil');
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
        const btnUpdate = commonGrid.btnBuilder('btn-update', 'success', 'fa fa-pencil');
        const btnDelete = commonGrid.btnBuilder('btn-delete', 'danger', 'fa fa-times');
        return '<div class="action-grid">' + btnUpdate + ' ' + btnDelete + '</div>';
    }

    function updateRow(val) {
        common.setCookie("module.table.sequence.update", val);
        common.direct("app_table_sequence/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("app_table_sequence/delete", val, function (data, status) {
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