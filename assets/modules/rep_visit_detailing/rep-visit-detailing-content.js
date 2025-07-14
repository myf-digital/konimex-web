(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Visit Detailing");
    // ui components
    let uiTbl = $("#tbl");
    let uiMaps = $("#maps");

    initializeGrid();
    setupFormUI();
	initializeParamMaps();
    /*
    * initialize content
    */
    function initializeGrid() {
        let option = {
            title: "Visit Detailing",
            toolbar: toolbar(),
            url: common.baseURL("rep_visit_detailing/load"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            frozenColumns: [[
                {
                    field: 'options',
                    title: 'Action',
                    width: 75,
                    halign: 'center',
                    align: 'center',
                    formatter: formatterButton
                }
            ]],
            columns: [[
              {field:'periode', title:'Periode', halign: 'left', align: 'left', sortable:"true", width:100},
              {field:'salesmanid', title:'User Login', halign: 'left', align: 'left', sortable:"true", width:75},
              {field:'nama_salesman', title:'Salesman', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'customerid', title:'Outlet ID', halign: 'left', align: 'left', sortable:"true", width:75},
              {field:'nama_customer', title:'Outlet', halign: 'left', align: 'left', sortable:"true", width:200},
              {field:'professional_name', title:'PIC', halign: 'left', align: 'left', sortable:"true", width:100},
              {field:'brands', title:'Brand', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'start_detailing', title:'Start Detailing', halign: 'left', align: 'left', sortable:"true", width:125},
              {field:'end_detailing', title:'End Detailing', halign: 'left', align: 'left', sortable:"true", width:125},
              {field:'status_label', title:'Status', halign: 'left', align: 'left', sortable:"true", width:125},
              {field:'keterangan', title:'Keterangan', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'reason', title:'Reason', halign: 'left', align: 'left', sortable:"true", width:150},
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
        }).on('change', function(){
            $('.datepicker').hide();
        });

        let uiTanggalPicker2 = $("#get_date2");
        uiTanggalPicker2.datepicker({
            format: 'yyyy-mm-dd',
        }).on('change', function(){
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
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
        return '<div class="action-grid-toolbar">' +
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
            const btnPreview = $(btns).find("a.btn-success");
			btnPreview.click(function () {
				open_detail(param.salesmanid, param.periode, param.nama_salesman);
			});
            index++;
        }
    }


    /*
    * action button generator
    */
    function formatterButton(val, row, index) {
        const btnPreview = commonGrid.btnBuilder('btn-viem-maps', 'success', 'fa fa-map-o');
        return '<div class="action-grid">' + btnPreview + '</div>';
    }

	function open_detail(sid,periode,nama) {
		$.ajax({
			type:"POST",
			dataType: "html",
			url: common.baseURL("rep_visit_detailing/get_gmap"),
			data : "sid="+sid+"&periode="+periode,
			success:function(res){
				response = res;
				$('div .modal-header .modal-title').text('Visit Detailing '+nama);	
				$("#maps").html(response);
				$("#modal_detail").modal('show');
			},
			error:function(){
				alert("Load failed");
			}
		});
		
	}

	function initializeParamMaps() {
		common.loading();
		let resolver = new HttpResolver();
		let filter = new Filter();
		
		$.when(
			$.post(common.baseURL("api_v1/call_siteid"), filter.build()),
		).done(function (data, textStatus, jqXHR) {
		}).then(function (r1) {
			common.loadingClose();
			initializemap(r1);
		}).fail(resolver.fail);
	}

	function initializemap(r1)   
	{   
		let rows = r1.result[0];
		let vloc = {lat: Number(rows.latitude), lng: Number(rows.longitude)};
		var myOptions = {  
			 zoom: 12,  
			 mapTypeId: google.maps.MapTypeId.ROADMAP,
			 zoomControl: true,
			 center: new google.maps.LatLng(vloc),  
				   mapTypeId: google.maps.MapTypeId.ROADMAP  
			   }  
		var map;
		map = new google.maps.Map(document.getElementById("maps"), myOptions);  
	}

    function save_xls(start, end) {
        common.direct("rep_visit_detailing/savetoxlsx/"+start+"/"+end);
    }	
})();