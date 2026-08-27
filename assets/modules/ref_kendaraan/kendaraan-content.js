(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Kendaraan");
    // ui components
    let uiTbl = $("#tbl-kendaraan");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.kendaraan.update");
            common.direct("ref_kendaraan/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Kendaraan",
            toolbar: toolbar(),
            url: common.baseURL("ref_kendaraan/load"),
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
				{field:'kendaraanid', title:'KENDARAANID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'keterangan', title:'KETERANGAN', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_salesman', title:'TPE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'no_polisi', title:'NO POLISI', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_pemilik', title:'NAMA PEMILIK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'alamat', title:'ALAMAT', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'merk', title:'MERK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'no_rangka', title:'NO RANGKA', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'tahun', title:'TAHUN', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'stnk_akhir', title:'STNK AKHIR', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.kendaraan.update", val);
        common.direct("ref_kendaraan/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_kendaraan/delete", val, function (data, status) {
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