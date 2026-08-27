(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Customer Image");
    // ui components
    let uiTbl = $("#tbl-customer-image");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.customer.image.update");
            common.direct("ref_customer_image/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Customer Image",
            toolbar: toolbar(),
            url: common.baseURL("ref_customer_image/load"),
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
				{field:'siteid', title:'SITEID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'salesmanid', title:'TPEID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'customerid', title:'CUSTOMERID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'image', title:'IMAGE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'datecreate', title:'DATECREATE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'size', title:'SIZE', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.customer.image.update", val);
        common.direct("ref_customer_image/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_customer_image/delete", val, function (data, status) {
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