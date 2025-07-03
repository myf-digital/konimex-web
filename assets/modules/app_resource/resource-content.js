(function () {
    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("User Access");
    // ui components
    let uiTbl = $("#tbl-resource");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.resource.update");
            common.direct("app_resource/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "User Access",
            toolbar: toolbar(),
            url: common.baseURL("app_resource/load"),
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
				// {field:'resource_id', title:'RESOURCE ID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nip', title:'NIP', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'name', title:'NAME', halign: 'left', align: 'left', sortable:"true", width:150},
				{field:'email', title:'EMAIL', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'telepon', title:'TELEPON', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'role_name', title:'ROLE', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'username', title:'USERNAME', halign: 'left', align: 'left', sortable:"true", width:150},
				//{field:'password', title:'PASSWORD', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'type', title:'TYPE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'jabatan', title:'Jabatan', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'nama_regional', title:'Regional', halign: 'left', align: 'left', sortable:"true", width:200},
				{field:'nama_area', title:'Area', halign: 'left', align: 'left', sortable:"true", width:200},
				// {field:'city', title:'City', halign: 'left', align: 'left', sortable:"true", width:200},
				//{field:'status', title:'STATUS', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_by', title:'CREATED BY', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_date', title:'CREATED DATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_by', title:'MODIFIED BY', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_date', title:'MODIFIED DATE', halign: 'center', align: 'left', sortable:"true", width:200},
            ]],
            onBeforeLoad: function (param) {
                param = common.replaceGridFilterPrefix(param, "a");
                param = common.replaceGridFilter(param, ["a.role_name"],["b.role_name"]);
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
        common.setCookie("module.resource.update", val);
        common.direct("app_resource/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("app_resource/delete", val, function (data, status) {
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