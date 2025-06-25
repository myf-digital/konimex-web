(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Visit FC");
    // ui components
    let uiTbl = $("#tbl");
    let uiMaps = $("#maps");

    initializeGrid();
    initialize();
    setupFormUI();
	initializeParamMaps();
    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.visit.update");
            common.direct("rep_visit/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Visit FC",
            toolbar: toolbar(),
            url: common.baseURL("rep_visit/load"),
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
              {field:'periode', title:'Periode', halign: 'left', align: 'left', sortable:"true", width:50},
              {field:'salesmanid', title:'User Login', halign: 'left', align: 'left', sortable:"true", width:120},
              {field:'nama_salesman', title:'Nama FC', halign: 'left', align: 'left', sortable:"true", width:150},
              {field:'status', title:'Status', halign: 'left', align: 'left', sortable:"true", width:50},
              {field:'visit', title:'Visit', halign: 'center', align: 'center', sortable:"true", width:50},
              {field:'checkin', title:'Start Visit', halign: 'left', align: 'left', sortable:"true", width:100},
              {field:'checkout', title:'End Visit', halign: 'left', align: 'left', sortable:"true", width:100},
              {field:'keterangan', title:'Keterangan', halign: 'left', align: 'left', sortable:"true", width:200}
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
        }).on('change', function(){
            $('.datepicker').hide();
        });;

        let uiTanggalPicker2 = $("#get_date2");
        uiTanggalPicker2.datepicker({
            format: 'yyyy-mm-dd',
            //startDate: '-3d'
        }).on('change', function(){
            $('.datepicker').hide();
        });;

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
        return '<div class="action-grid-toolbar">' +
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
            // const btnEdit = $(btns).find("a.btn-success");
            // const btnDelete = $(btns).find("a.btn-danger");
            const btnPreview = $(btns).find("a.btn-success");
            // btnEdit.click(function () {
                // updateRow(param);
            // });
            // btnDelete.click(function () {
                // deleteRow(param);
            // });
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
        //const btnDelete = commonGrid.btnBuilderDash('btn-delete', 'danger', '../assets/images/ic_trash.png');
        //const btnPreview = commonGrid.btnBuilder('btn-preview', 'warning', 'fa fa-image');
        return '<div class="action-grid">' + btnPreview + '</div>';
    }

	function open_detail(sid,periode,nama) {
		$.ajax({
			type:"POST",
			dataType: "html",
			beforeSend : function() {
				//$("#map-content").html('Populating data, please wait..');
			},
			url: common.baseURL("rep_visit/get_gmap"),
			data : "sid="+sid+"&periode="+periode,
			success:function(res){
				//alert ("show maps");
				//initializeParamMaps();
				response = res;
				$('div .modal-header .modal-title').text('Visit FC '+nama);	
				//$('#modal_detail').find('.modal-body').html(response);
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
		console.log(filter);
		
		$.when(
			$.post(common.baseURL("api_v1/call_siteid"), filter.build()),
		).done(function (data, textStatus, jqXHR) {
			console.log("done");
			//console.log(d);
		}).then(function (r1) {
			common.loadingClose();
			//console.log("then");
			initializemap(r1);
		}).fail(resolver.fail);
	}

	function initializemap(r1)   
	{   
		let rows = r1.result[0];
		//alert(rows.latitude);
		let vloc = {lat: Number(rows.latitude), lng: Number(rows.longitude)};
		var myOptions = {  
			 zoom: 12,  
			 mapTypeId: google.maps.MapTypeId.ROADMAP,
			 zoomControl: true,
			 //center: new google.maps.LatLng(-6.26149,106.81060),
			 center: new google.maps.LatLng(vloc),  
				   mapTypeId: google.maps.MapTypeId.ROADMAP  
			   }  
		var map;
		map = new google.maps.Map(document.getElementById("maps"), myOptions);  
	 }    
	 
})();