(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Gl Coa");
    // ui components
    let uiTbl = $("#tbl-gl-coa");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.gl.coa.update");
            common.direct("ref_gl_coa/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Gl Coa",
            toolbar: toolbar(),
            url: common.baseURL("ref_gl_coa/load"),
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
				{field:'coa_id', title:'COA ID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'decr_coa', title:'DECR COA', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'status', title:'STATUS', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'create_user', title:'CREATE USER', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'create_date', title:'CREATE DATE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'update_user', title:'UPDATE USER', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'update_date', title:'UPDATE DATE', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.gl.coa.update", val);
        common.direct("ref_gl_coa/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_gl_coa/delete", val, function (data, status) {
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