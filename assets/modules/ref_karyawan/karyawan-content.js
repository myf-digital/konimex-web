(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Karyawan");
    // ui components
    let uiTbl = $("#tbl-karyawan");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.karyawan.update");
            common.direct("ref_karyawan/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Karyawan",
            toolbar: toolbar(),
            url: common.baseURL("ref_karyawan/load"),
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
				//{field:'id_karyawan', title:'ID KARYAWAN', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nip', title:'NIP', halign: 'center', align: 'center', sortable:"true", width:200},
				{field:'nama_karyawan', title:'NAMA KARYAWAN', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'satker', title:'SATKER', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'email', title:'EMAIL', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'telepon', title:'TELEPON', halign: 'center', align: 'center', sortable:"true", width:200},
				//{field:'created_by', title:'CREATED BY', halign: 'center', align: 'center', sortable:"true", width:200},
				//{field:'created_date', title:'CREATED DATE', halign: 'center', align: 'center', sortable:"true", width:200},
				//{field:'modified_by', title:'MODIFIED BY', halign: 'center', align: 'center', sortable:"true", width:200},
				//{field:'modified_date', title:'MODIFIED DATE', halign: 'center', align: 'center', sortable:"true", width:200},
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
        const btnCreate = commonGrid.btnBuilder('btn-create', 'primary', 'fa fa-plus');
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
        common.setCookie("module.karyawan.update", val);
        common.direct("ref_karyawan/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_karyawan/delete", val, function (data, status) {
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