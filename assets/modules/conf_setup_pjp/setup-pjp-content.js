(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Setup FJP");
    // ui components
    let uiTbl = $("#tbl-setup-pjp");
    let paramsession = common.getCookie("session");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {

        $("#btn-create").click(function () {
            common.removeCookie("module.setup.pjp.update"); 
            common.direct("conf_setup_pjp/form_addpjp");
        });

        $("#btn-download").click(function () {
            common.removeCookie("module.setup.pjp.update"); 
            common.direct("conf_setup_pjp/form_download");
         });

         $("#btn-switch").click(function () {
            common.removeCookie("module.setup.pjp.update"); 
            common.direct("conf_setup_pjp/form_switch");
         });

         $("#btn-upload").click(function () {
            common.removeCookie("module.setup.pjp.update"); 
            common.direct("conf_setup_pjp/form_upload");
         });

    }

    function initializeGrid() {
        let option = {
            title: "Setup FJP",
            toolbar: toolbar(),
            url: common.baseURL("conf_setup_pjp/load"),
            queryParams: {
                usersession: paramsession.username,
                idjabatan: paramsession.idjabatan,
                restrict_level: paramsession.restrict_level
            },
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
				//{field:'siteid', title:'SITEID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'salesmanid', title:'KODE GFF', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'gffname', title:'NAMA GFF', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'position', title:'POSITION', halign: 'center', align: 'left', sortable:"true", width:100},
				//{field:'ram_rsm', title:'RAM/RSM', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'aas_aam_tss_tsm', title:'AAS/AAM/TSS/TSM', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'customerid', title:'OUTLETID', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'kode_outlet', title:'KODE OUTLET', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'nama_customer', title:'OUTLET NAME', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'alamat', title:'Alamat', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'group_account', title:'GROUP ACCOUNT', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'area', title:'Area', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'mcc', title:'DC', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'nama_class', title:'ACCOUNT', halign: 'center', align: 'left', sortable:"true", width:100},
				//{field:'tgl_proses', title:'TGL PROSES', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'keterangan', title:'REMARK', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'group_nama_minggu', title:'WEEKS', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'group_nama_hari', title:'DAYS', halign: 'center', align: 'center', sortable:"true", width:100},
				//{field:'status_send', title:'STATUS SEND', halign: 'center', align: 'left', sortable:"true", width:200},
            ]],
            onBeforeLoad: function (param) {
                param = common.replaceGridFilterPrefix(param, "a");
                //param = common.replaceGridFilter(param,["nama_class"],["c.nama_class"]);
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
        const btnCreate = commonGrid.btnBuilderText('btn-create', 'success', 'fa fa-pencil', ' Create New FJP');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'primary', 'fa fa-download',' Download FJP');
        //const btnSwitch = commonGrid.btnBuilderText('btn-switch', 'info', 'fa fa-exchange',' Switch PJP');
        const btnUpload = commonGrid.btnBuilderText('btn-upload', 'info', 'fa fa-upload',' Upload FJP');
        return '<div class="action-grid-toolbar">' + btnCreate + btnDownload + btnUpload + ' Week Aktif : ' + paramsession.week_aktif +'</div>'; //+ btnUpload
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
        common.direct("conf_setup_pjp/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("conf_setup_pjp/delete", val, function (data, status) {
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