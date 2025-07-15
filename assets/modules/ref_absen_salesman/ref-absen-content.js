(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Absen Salesman");
    // ui components
    let uiTbl = $("#tbl");

    initializeGrid();
    initialize();
    setupFormUI();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.absen.update");
            common.direct("ref_absen_salesman/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Absen Salesman",
            toolbar: toolbar(),
            url: common.baseURL("ref_absen_salesman/load"),
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
              {field:'periode', title:'Periode', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'salesmanid', title:'Salesman ID', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'nama_salesman', title:'Nama Salesman', halign: 'left', align: 'left', sortable:"true", width:250},
              {field:'status', title:'Status', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'adjust_pjp', title:'Adjust PJP', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'checkin', title:'Check In', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'checkout', title:'Check Out', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'keterangan', title:'Keterangan', halign: 'left', align: 'left', sortable:"true", width:250}
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
        common.removeFilter(['detail']);    

    }

    function setupFormUI() {          
        let uiTanggalPicker1 = $("#get_date1");
        uiTanggalPicker1.datepicker({
            format: 'yyyy-mm-dd',
            //startDate: '-3d'
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });

        let uiTanggalPicker2 = $("#get_date2");
        uiTanggalPicker2.datepicker({
            format: 'yyyy-mm-dd',
            //startDate: '-3d'
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
    }

    function toolbar() {
        const btnCreate = commonGrid.btnBuilderDash('btn-create', 'success', '../assets/images/ic_edit.png');
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        return '<div class="action-grid-toolbar">' + btnCreate +
               '&nbsp;&nbsp;&nbsp; Periode : &nbsp;' +
               '<input type="text" id="get_date1" value="" name="get_date1" readonly>' +
               '&nbsp; - &nbsp; <input type="text" id="get_date2" value="" name="get_date2" readonly>' +
                btnSearch + '</div>';
    }

    function optionButton(data) {
        let btnContent = $(".action-grid");
        let index = 0;
        for (const btns of btnContent) {
            const param = data.rows[index];
            const btnEdit = $(btns).find("a.btn-success");
            const btnDelete = $(btns).find("a.btn-danger");
            const btnPreview = $(btns).find("a.btn-warning");
            btnEdit.click(function () {
                updateRow(param);
            });
            btnDelete.click(function () {
                deleteRow(param);
            });
            btnPreview.click(function () {
                previewImage(param);
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
        const btnPreview = commonGrid.btnBuilder('btn-preview', 'warning', 'fa fa-image');
        return '<div class="action-grid">' + btnUpdate + ' ' + btnPreview + ' '+ btnDelete +'</div>';
    }

    function updateRow(val) {
        common.setCookie("module.absen.update", val);
        common.direct("ref_absen_salesman/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_absen_salesman/delete", val, function (data, status) {
                if (200 === data.code) {
                    $.alert("Delete success!");
                    uiTbl.datagrid("reload");
                } else {
                    $.alert(status);
                }
            })
        });
    }

    function previewImage(data) {

        if (data.image == null) {
            alert('Image not found');

            return;
        }

        $.fancybox.open([{href: data.image, title: data.salesmanid}], {
           helpers: {
               thumbs: {
                   width: 200,
                   height: 100
               }
           }
       });
    }

})();