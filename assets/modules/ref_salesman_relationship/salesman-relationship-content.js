(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Salesman Relationship");
    // ui components
    let uiTbl = $("#tbl-salesman-relationship");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.salesman.relationship.update");
            common.direct("ref_salesman_relationship/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Salesman Relationship",
            toolbar: toolbar(),
            url: common.baseURL("ref_salesman_relationship/load"),
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
				{field:'salesmanid', title:'Salesman Id', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'nama_salesman', title:'Nama Medrep', halign: 'left', align: 'left', sortable:"true", width:250},
				{field:'relationship_desc', title:'Relationship', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'nama_relationship', title:'Nama Relationship', halign: 'left', align: 'left', sortable:"true", width:120},
				{field:'jenis_kelamin', title:'Jenis Kelamin', halign: 'left', align: 'left', sortable:"true", width:120},
				{field:'tanggal_lahir', title:'Tanggal_lahir', halign: 'left', align: 'left', sortable:"true", width:120},
				{field:'keterangan', title:'Keterangan', halign: 'left', align: 'left', sortable:"true", width:100},
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
        common.setCookie("module.salesman.relationship.update", val);
        common.direct("ref_salesman_relationship/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_salesman_relationship/delete", val, function (data, status) {
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