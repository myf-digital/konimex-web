(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Sales Salesman");
    // ui components
    let uiTbl = $("#tbl-sales-salesman");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.sales.salesman.update");
            common.direct("ref_sales_salesman/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Sales Salesman",
            toolbar: toolbar(),
            url: common.baseURL("ref_sales_salesman/load"),
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
				//{field:'siteid', title:'SITEID', halign: 'left', align: 'left', sortable:"true", width:200},
				{field:'salesmanid', title:'USER GFF', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'nama_salesman', title:'NAMA GFF', halign: 'left', align: 'left', sortable:"true", width:250},
				{field:'tipe_sales', title:'GFF TYPE', halign: 'left', align: 'left', sortable:"true", width:100},
				{field:'nama_regional', title:'Regional', halign: 'left', align: 'left', sortable:"true", width:120},
				{field:'nama_area', title:'Area', halign: 'left', align: 'left', sortable:"true", width:120},
				{field:'city', title:'City', halign: 'left', align: 'left', sortable:"true", width:120},
				{field:'aktifstatus', title:'STATUS', halign: 'left', align: 'left', sortable:"true", width:100},
				//{field:'password', title:'PASSWORD', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'nama_category', title:'CATEGORY', halign: 'left', align: 'left', sortable:"true", width:200},
				//{field:'nilai_sales', title:'NILAI SALES', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'last_sync', title:'LAST SYNC', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'status', title:'STATUS', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.sales.salesman.update", val);
        common.direct("ref_sales_salesman/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_sales_salesman/delete", val, function (data, status) {
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