(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Product");
    // ui components
    let uiTbl = $("#tbl-product");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.product.update");
            common.direct("ref_product/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Product",
            toolbar: toolbar(),
            url: common.baseURL("ref_product/load"),
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
				{field:'productid', title:'KODE PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'barcode', title:'BARCODE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_invoice', title:'NAMA PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'group_product', title:'GROUP PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'category_product', title:'KATEGORI PRODUK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_brand', title:'BRAND', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'h_grosir', title:'HJP', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'h_ritel', title:'HNA', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'status_desc', title:'STATUS', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_by', title:'CREATED BY', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_date', title:'CREATED DATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_by', title:'MODIFIED BY', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_date', title:'MODIFIED DATE', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.product.update", val);
        common.direct("ref_product/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_product/delete", val, function (data, status) {
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