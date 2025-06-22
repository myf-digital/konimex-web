(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Setupsite Db");
    // ui components
    let uiTbl = $("#tbl-setupsite-db");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.setupsite.db.update");
            common.direct("conf_setupsite_db/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Setupsite Db",
            toolbar: toolbar(),
            url: common.baseURL("conf_setupsite_db/load"),
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
				{field:'Id', title:'ID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'siteid', title:'SITEID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'hostname', title:'HOSTNAME', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'dbname', title:'DBNAME', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'user', title:'USER', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'pwd', title:'PWD', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'jeniskoneksi', title:'JENISKONEKSI', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.setupsite.db.update", val);
        common.direct("conf_setupsite_db/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("conf_setupsite_db/delete", val, function (data, status) {
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