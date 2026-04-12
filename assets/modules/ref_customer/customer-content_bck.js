(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Outlet");
    // ui components
    let uiTbl = $("#tbl-customer");
    let paramsession = common.getCookie("session");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.customer.update");
            common.direct("ref_customer/form");
        });
        $("#btn-download").click(function () {
            common.removeCookie("module.setup.pjp.update"); 
            common.direct("ref_customer/form_download");
         });
    }

    function toolbar() {
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'primary', 'fa fa-download', ' Download Outlet');
        const btnCreate = commonGrid.btnBuilderText('btn-create', 'success', 'fa fa-pencil',' Create New');
        return '<div class="action-grid-toolbar">' + btnCreate + '&nbsp;' + btnDownload + '</div>';
        //return '<div class="action-grid-toolbar">' + btnCreate + '</div>';
    }

    function initializeGrid() {
        let option = {
            title: "Outlet",
            toolbar: toolbar(),
            url: common.baseURL("ref_customer/load"),
            queryParams: {
                usersession: paramsession.username,
                idjabatan: paramsession.idjabatan
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
				{field:'customerid', title:'OutletID_DRC', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'kode_outlet', title:'Kode Outlet', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'customerid', title:'CUSTOMERID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_customer', title:'Nama Outlet', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'alamat', title:'Alamat', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'top_cust', title:'TOP CUST', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'tipe_bayar', title:'TIPE BAYAR', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_regional', title:'Regional', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_area', title:'Area', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_subarea', title:'Sub Area', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'propinsiid', title:'Propinsi', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'kotaid', title:'Kota/Kabupaten', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'kelurahanid', title:'Kelurahan', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'kecamatanid', title:'Kecamatan', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'kodepos', title:'KODEPOS', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'telp', title:'TELP', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'email', title:'EMAIL', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'segmentid', title:'BU', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'typeid', title:'Channel', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_account', title:'Sub Channel', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'latitude', title:'LATITUDE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'longitude', title:'LONGITUDE', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'mcc', title:'DC', halign: 'center', align: 'left', sortable:"true", width:200},            ]],
				{field:'salesmanid', title:'User PAR-MA', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'gff_name', title:'PAR-MA Name', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'position', title:'Position', halign: 'center', align: 'left', sortable:"true", width:200},
				]]
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
        common.setCookie("module.customer.update", val);
        common.direct("ref_customer/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_customer/delete", val, function (data, status) {
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