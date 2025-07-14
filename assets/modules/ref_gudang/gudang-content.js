(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Gudang");
    // ui components
    let uiTbl = $("#tbl-gudang");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.gudang.update");
            common.direct("ref_gudang/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Gudang",
            toolbar: toolbar(),
            url: common.baseURL("ref_gudang/load"),
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
				{field:'siteid', title:'SITEID', halign: 'left', align: 'left', sortable:"true", width:200},
				//{field:'gudangid', title:'GUDANGID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'gudang_name', title:'GUDANG NAME', halign: 'left', align: 'left', sortable:"true", width:200},
				{field:'aktifstatus', title:'STATUS', halign: 'left', align: 'left', sortable:"true", width:200},
				//{field:'user_create', title:'USER CREATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'date_create', title:'DATE CREATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'user_update', title:'USER UPDATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'date_update', title:'DATE UPDATE', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.gudang.update", val);
        common.direct("ref_gudang/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_gudang/delete", val, function (data, status) {
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