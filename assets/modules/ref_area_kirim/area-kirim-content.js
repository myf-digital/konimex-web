(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Area Kirim");
    // ui components
    let uiTbl = $("#tbl-area-kirim");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.area.kirim.update");
            common.direct("ref_area_kirim/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Area Kirim",
            toolbar: toolbar(),
            url: common.baseURL("ref_area_kirim/load"),
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
				{field:'areakirimid', title:'AREAKIRIMID', halign: 'left', align: 'left', sortable:"true", width:200},
				{field:'nama_areakirim', title:'AREA KIRIM', halign: 'left', align: 'left', sortable:"true", width:200},
				//{field:'branchid', title:'BRANCHID', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'companyid', title:'COMPANYID', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'headofficeid', title:'HEADOFFICEID', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'flag_kirim', title:'FLAG KIRIM', halign: 'center', align: 'left', sortable:"true", width:200},
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
        common.setCookie("module.area.kirim.update", val);
        common.direct("ref_area_kirim/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_area_kirim/delete", val, function (data, status) {
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