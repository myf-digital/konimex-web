(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Menu");
    // ui components
    let uiTbl = $("#tbl-menu");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.menu.update");
            common.direct("app_menu/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Menu",
            toolbar: toolbar(),
            url: common.baseURL("app_menu/load"),
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
				{field:'menu_name', title:'MENU NAME', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'menu_icon', title:'MENU ICON', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'module_name', title:'MODULE NAME', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'type_menu', title:'TYPE MENU', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'seq_number', title:'SEQ NUMBER', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'menu_parent', title:'PARENT MENU', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'status_label', title:'STATUS', halign: 'center', align: 'center', sortable:"true", width:100},
            ]],
            onBeforeLoad: function (param) {
                param = common.replaceGridFilterPrefix(param, "a");
                param = common.replaceGridFilter(param,["menu_parent"],["b.menu_name"]);
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
        common.setCookie("module.menu.update", val);
        common.direct("app_menu/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("app_menu/delete", val, function (data, status) {
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