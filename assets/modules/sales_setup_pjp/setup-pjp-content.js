(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Setup Pjp");
    // ui components
    let uiTbl = $("#tbl-setup-pjp");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.setup.pjp.update");
            common.direct("sales_setup_pjp/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Setup Pjp",
            toolbar: toolbar(),
            url: common.baseURL("sales_setup_pjp/load"),
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
				{field:'salesmanid', title:'MEDREPID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_salesman', title:'NAMA MEDREP', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'position', title:'POSITION', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'ram_rsm', title:'RAM RSM', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'aas_aam_tss_tsm', title:'AAS AAM TSS TSM', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'customerid', title:'CUSTOMERID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_customer', title:'NAMA CUSTOMER', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'alamat', title:'ALAMAT', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'group_account', title:'GROUP ACCOUNT', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'outlet_type', title:'OUTLET TYPE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'dc', title:'DC', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'channel_outlet', title:'CHANNEL OUTLET', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'tgl_proses', title:'TGL PROSES', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'keterangan', title:'KETERANGAN', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'minggu', title:'MINGGU', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'hari', title:'HARI', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'status_send', title:'STATUS SEND', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.setup.pjp.update", val);
        common.direct("sales_setup_pjp/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("sales_setup_pjp/delete", val, function (data, status) {
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