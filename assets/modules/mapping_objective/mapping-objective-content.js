(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Mapping Objective");
    // ui components
    let uiTbl = $("#tbl-mapping-objective");
    let paramsession = common.getCookie("session");

    initializeGrid();
    initialize();
    setupFormUI();

    /*
    * initialize content
    */
    function initializeGrid() {
        let option = {
            title: "Mapping Objective",
            toolbar: toolbar(),
            url: common.baseURL("mapping_objective/load"),
            queryParams: {
                usersession: paramsession.username,
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
				{field:'start_periode', title:'Periode Start', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'end_periode', title:'Periode End', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'productid', title:'ID Produk', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'nama_invoice', title:'Nama Produk', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'barcode', title:'Barcode', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'nama_brand', title:'Brand', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'category_product', title:'Variant', halign: 'center', align: 'left', sortable:"true", width:100},
				{field:'objective', title:'Objective', halign: 'center', align: 'left', sortable:"false", width:200, formatter: formatObjective},
				{field:'accounts', title:'Account', halign: 'center', align: 'left', sortable:"false", width:200, formatter: formatAccounts},
				{field:'keterangan', title:'Keterangan', halign: 'center', align: 'left', sortable:"false", width:100},
				{field:'h_grosir', title:'HJP', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatNumber},
				{field:'h_ritel', title:'HNA', halign: 'center', align: 'right', sortable:"true", width:100, formatter: formatNumber},
				{field:'status_objective', title:'Status', halign: 'center', align: 'center', sortable:"false", width:100},
            ]],
            onBeforeLoad: function (param) {
                param = common.replaceGridFilterPrefix(param, "a");
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
                common.removeCookie("module.mapping.objective.update");
                optionButton(data);
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
        uiTbl.datagrid('enableFilter');
        common.removeFilter(['options']);        
    }

    function formatAccounts(val, row, index) {
        return val && val != undefined ? val.split('||').map((v, i) => `${i+1}. ${v}`).join('<br />') : val;
    }

    function formatObjective(val, row, index) {
        return val && val == 'Order Reguler Min Order' ? `${val} <br /> (${row.min_order})` : val;
    }

    function formatNumber(val, row, index) {
        return Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.mapping.objective.update");
            common.direct("mapping_objective/form");
        });
    }

    function setupFormUI() {
        let uiTanggalPicker1 = $("#get_date1");
        uiTanggalPicker1.datepicker({
            format: 'yyyy-mm-dd',
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });

        let uiTanggalPicker2 = $("#get_date2");
        uiTanggalPicker2.datepicker({
            format: 'yyyy-mm-dd',
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });

        uiTanggalPicker1.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiTanggalPicker2.datepicker('setStartDate', startDate);
            if(uiTanggalPicker1.val() > uiTanggalPicker2.val()){
                uiTanggalPicker2.val(uiTanggalPicker1.val());
            }
        });

        let uiBtnSearch = $("#btn-search");
        uiBtnSearch.click(function () {
            uiTbl.datagrid('load',{
              get_date1: uiTanggalPicker1.val(),
              get_date2: uiTanggalPicker2.val()
            });
        });

        let uiBtnDownload = $("#btn-download");
        uiBtnDownload.click(function () {
            let start = uiTanggalPicker1.val();
            let end = uiTanggalPicker2.val();
            if (start===null){
                alert ('Periode Start di isi...!');
            } else if (end===null){
                alert ('Periode End di isi...!');
            } else {
                save_xls(start, end);
            }
        });
    }

    function toolbar() {
        const btnCreate = commonGrid.btnBuilderDash('btn-create', 'success', '../assets/images/ic_edit.png');
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'primary', 'fa fa-download',' Download');
        return '<div class="action-grid-toolbar">' +
                btnCreate +
               '&nbsp;&nbsp;&nbsp; Periode : &nbsp;' +
               '<input type="text" id="get_date1" value="" name="get_date1" readonly>' +
               '&nbsp; - &nbsp; <input type="text" id="get_date2" value="" name="get_date2" readonly>' +
                btnSearch + btnDownload + '</div>';
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
        common.setCookie("module.mapping.objective.update", val);
        common.direct("mapping_objective/form_update");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("mapping_objective/delete", val, function (data, status) {
                if (200 === data.code) {
                    $.alert("Delete success!");
                    uiTbl.datagrid("reload");
                } else {
                    $.alert(status);
                }
            })
        });
    }

    function save_xls(start, end) {
        common.direct("mapping_objective/savetoxlsx/"+start+"/"+end);
    }	
})();