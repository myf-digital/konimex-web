(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Outlet");
    // ui components
    let uiTbl = $("#tbl-request_new_outlet");
    let paramsession = common.getCookie("session");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.request_new_outlet.update");
            common.direct("/ref_request_new_outlet/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Outlet",
            toolbar: toolbar(),
            url: common.baseURL("ref_request_new_outlet/load"),
            queryParams: {
                usersession: paramsession.username,
                idjabatan: paramsession.idjabatan,
                restrict_level: paramsession.restrict_level,
                restrict_bu: paramsession.restrict_bu
                },
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            frozenColumns: [[
                {
                    field: 'options',
                    title: 'ACTION',
                    width: 150,
                    halign: 'center',
                    align: 'center',
                    formatter: formatterButton
                }
            ]],
            columns: [[
				//{field:'siteid', title:'SITEID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'kode_outlet', title:'Kode Outlet', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'customerid', title:'CUSTOMERID', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_customer', title:'Nama Outlet', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'alamat', title:'Alamat', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'top_cust', title:'TOP CUST', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'tipe_bayar', title:'TIPE BAYAR', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_regional', title:'Regional', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'nama_area', title:'Area', halign: 'center', align: 'left', sortable:"true", width:200},
				// {field:'nama_subarea', title:'Sub Area', halign: 'center', align: 'left', sortable:"true", width:200},
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
				{field:'salesmanid', title:'USER GFF', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'gff_name', title:'GFF Name', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'position', title:'Position', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'mcc', title:'DC', halign: 'center', align: 'left', sortable:"true", width:200},            ]],
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
        //const btnCreate = commonGrid.btnBuilderText('btn-create', 'success', 'fa fa-pencil', 'Approve');
        //return '<div class="action-grid-toolbar">' + btnCreate + '</div>';
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
        const btnUpdate = commonGrid.btnBuilderText('btn-update', 'success', 'fa fa-pencil','Review');
        const btnDelete = commonGrid.btnBuilderText('btn-delete', 'danger', 'fa fa-times','Reject');
        return '<div class="action-grid">' + btnUpdate + ' ' + btnDelete + '</div>';
    }

    function updateRow(val) {
        common.setCookie("module.request_new_outlet.update", val);
        common.direct("ref_request_new_outlet/form");
    }

    function deleteRow(val) {
        common.dialogReject(function () {
            $.post("ref_request_new_outlet/delete", val, function (data, status) {
                if (200 === data.code) {
                    $.alert("Rejected...!");
                    uiTbl.datagrid("reload");
                } else {
                    $.alert(status);
                }
            })
        });
    }

})();