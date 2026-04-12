(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Setup Rrk");
    // ui components
    let uiTbl = $("#tbl-setup-rrk");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.setup.rrk.update");
            common.direct("conf_setup_rrk/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Setup Rrk",
            toolbar: toolbar(),
            url: common.baseURL("conf_setup_rrk/load"),
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
				{field:'siteid', title:'SITEID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'salesmanid', title:'MEDREP', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_area', title:'AREA', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'repeat_minggu', title:'FREQUENCY', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'groupminggu', title:'WEEKS', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'groupday', title:'DAYS', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'user_create', title:'USER CREATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'date_create', title:'DATE CREATE', halign: 'center', align: 'left', sortable:"true", width:200},
            ]],
            onBeforeLoad: function (param) {
                
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
                optionButton(data);
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
        uiTbl.datagrid('enableFilter', [{
            //field:'salesman',
            //type:'textbox',
            //options:{precision:1},
            op:['equal','notequal','less','greater']
        }]);
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
        common.setCookie("module.setup.rrk.update", val);
        common.direct("conf_setup_rrk/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("conf_setup_rrk/delete", val, function (data, status) {
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