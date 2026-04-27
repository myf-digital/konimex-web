(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("List Professional");
    // ui components
    let uiTbl = $("#tbl-professional");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        let uiBtnDownload = $("#btn-download");
        uiBtnDownload.click(function () {
            save_xls();
        });
    }

    function initializeGrid() {
        let option = {
            title: "List Professional",
            toolbar: toolbar(),
            url: common.baseURL("professional/load"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            frozenColumns: [[
                {
                    field: 'options',
                    title: 'Action',
                    width: 100,
                    halign: 'center',
                    align: 'center',
                    formatter: formatterButton
                }
            ]],
            columns: [[
				{field:'id', title:'KODE PROFESSIONAL', halign: 'center', align: 'center', sortable:"true", width:175},
				{field:'nama_professional', title:'NAMA PROFESSIONAL', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'customer_list', title:'TEMPAT PRAKTEK', halign: 'center', align: 'left', sortable:"true", width:200, formatter: formatterCustomerList},
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

    function formatterNumber(val, row, index) {
		return val ? val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '';
	}

    function formatterDate(val, row, index) {
        moment.locale('id');
		return val ? moment(val, "YYYYMM").format("MMM YYYY") : '';
	}

    function toolbar() {
        // const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
        return '<div class="action-grid-toolbar">' +
            //    '&nbsp;&nbsp;&nbsp; Periode : &nbsp;' +
            //    '<input type="text" id="get_date1" value="" name="get_date1" readonly>' +
            //    '&nbsp; - &nbsp; <input type="text" id="get_date2" value="" name="get_date2" readonly>' +
                // btnSearch + 
                btnDownload + '</div>';
    }

    function formatterButton(val, row, index) {
        let btnImage = '';
        if (
            (row.url_foto && row.url_foto != undefined) ||
            (row.url_img_signature && row.url_img_signature != undefined)
        ) btnImage = commonGrid.btnBuilder('btn-viem-image', 'info', 'fa fa-image');
        const btnEdit = commonGrid.btnBuilder('btn-viem', 'success', 'fa fa-edit');
        return '<div class="action-grid">' + btnImage + btnEdit + '</div>';
    }

    function formatterCustomerList(val, row, index) {
        if (val) {
            let html = '<ol>';
            val.split('||').forEach(item => {
                html += `<li>${item}</li>`;
            });
            html += '</ol>';
            return html;
        }
        return '';
    }

    function optionButton(data) {
        let btnContent = $(".action-grid");
        let index = 0;
        for (const btns of btnContent) {
            const param = data.rows[index];
            const btnPreview = $(btns).find("a.btn-success");
			btnPreview.click(function () {
				open_detail(param);
			});
            const btnImage = $(btns).find("a.btn-info");
			btnImage.click(function () {
				open_image(param);
			});
            index++;
        }
    }

    function open_detail(data) {
        common.setCookie("module.setup.professional-outlet", data);
        common.direct("professional/form_set_outlet");
    }

	function open_image(data) {
        if (data && data != undefined) {
            $('#myModalImage').text(`User: ${data.nama_professional}`);	
            $("#show-image").html(`
                <div class="row text-left">
                    <div class="col-md-12">
                        <h5><b>1. Foto</b></h5>
                        <img src="${data.url_foto}" alt="Foto" class="img-fluid">
                    </div>
                    <div class="col-md-12">
                        <h5><b>2. Foto Signature</b></h5>
                        ${data.url_img_signature ? `<img src="${data.url_img_signature}" alt="Foto Signature" class="img-fluid">` : '<p>-</p>'}
                    </div>
                </div>
            `);
            $("#modal_image").modal('show');
        }
    }

    function save_xls(start, end) {
        common.direct("professional/savetoxlsx");
    }
})();