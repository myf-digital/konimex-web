(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Report Join Visit");
    // ui components
    let uiTbl = $("#tbl");
    var baseurl = window.location.origin;
    let paramsession = common.getCookie("session");
    
    initializeGrid();
    setupFormUI();

    function initializeGrid() {
      let option = {
          title: "List Data Review Outlet",
          toolbar: toolbar(),
          url: common.baseURL("rep_join_visit_outlet/load"),
          pageNumber: 1,
          pageSize: commonGrid.getCurentSize(),
          pageList: commonGrid.getPageSize(),
		  scrollbarSize: 20,
          height:600,
		  columns: [[
            {field: 'periode', title: 'Period', width:20, sortable: 'true', halign: 'center', align: 'center'},
            {field: 'username', title: 'Review By', width:30, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'customerid', title: 'OutletID', width:20, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'kode_outlet', title: 'Kode Outlet', width:20, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_customer', title: 'Outlet Name', width:100, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'created_date', title: 'Review Date', width:30, sortable: 'true', halign: 'left', align: 'left'},
            //{field: 'final_score', title: 'Score', width:30, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'preparation', title: 'Preparation', width:50, sortable: 'true', halign: 'left', align: 'left'},
            //{field: 'approach', title: 'Approach', width:50, sortable: 'true', halign: 'left', align: 'center'},
            //{field: 'regular_shelf_merchandising', title: 'Regular Shelf Merchandising', width:50, sortable: 'true', halign: 'left', align: 'center'},
            //{field: 'advance_merchandising_on_regular_shelves', title: 'Advance Merchandising on Regular Shelves', width:50, sortable: 'true', halign: 'left', align: 'center'},
            //{field: 'promo_implementation', title: 'Promo Implementation', width:50, sortable: 'true', halign: 'left', align: 'center'},
            //{field: 'sell_and_secure', title: 'Sell and Secure', width:50, sortable: 'true', halign: 'left', align: 'center'},
            //{field: 'drc_filling', title: 'DRC Filling', width:50, sortable: 'true', halign: 'left', align: 'center'},
			{field:'detail', title:'Detail', halign: 'center', align: 'center', width:30, formatter: formatterButtonDetail},
          ]],
          /*frozenColumns: [[
              { 
                  field: 'options',
                  title: 'ACTION',
                  width: 100,
                  halign: 'center',
                  align: 'center',
                  formatter: formatterButton
              }
          ]],*/
          onBeforeLoad: function (param) {
              param = common.replaceGridFilterPrefix(param, "a");
              //param = common.replaceGridFilter(param,["periode"],["b.periode"]);
          },
          onLoadSuccess: function (data) {
              $(this).datagrid('resize', 'fixRowHeight');
              optionButton(data);
          }
      };
		uiTbl.datagrid(commonGrid.optionValue(option));
		uiTbl.datagrid('enableFilter');
		//common.removeFilter(['options']);
		common.removeFilter(['detail']);

    }

    function setupFormUI() {          
        loadEvent();
        //loadRegional();

        let uiSelectEvent = $("#eventid");

        uiSelectEvent.select2({
          placeholder: "Select Event",
          allowClear: true,
        });


        let uiBtnSearch = $("#btn-search");
        let uiBtnDownload = $("#btn-download");

        let uiTanggalPicker1 = $("#start_period");
        let uiTanggalPicker2 = $("#end_period");

        uiTanggalPicker1.datepicker({
            format: 'yyyy-mm-dd',
            //startDate: '-3d'
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });

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

        uiBtnSearch.click(function () {
          if ( uiTanggalPicker1.val() == null){
            alert ('Periode harus di isi...!');
          } else {
            uiTbl.datagrid('load',{
              usersession: paramsession.username,
              restrict_level: paramsession.restrict_level,
			  start_date: uiTanggalPicker1.val(),
			  end_date: uiTanggalPicker2.val()
            });
          }
        });

        uiBtnDownload.click(function () {
          if ( uiTanggalPicker1.val() == null){
            alert ('Periode harus di isi...!');
          } else {
            
            let usersession = paramsession.username;
            let restrict_level = paramsession.restrict_level;
			let start_date = uiTanggalPicker1.val();
			let end_date = uiTanggalPicker2.val();
			
            common.direct("rep_join_visit_outlet/savexls_join_visit_outlet_all/"+start_date+"/"+end_date);
          }
        });
    }

    function toolbar() {
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
        return '<div class="action-grid-toolbar">' +
			   '&nbsp;&nbsp;&nbsp; Periode : &nbsp;' +
               '<input type="text" id="start_period" value="" name="start_period" readonly>' +
               '&nbsp; - &nbsp; <input type="text" id="end_period" value="" name="end_period" readonly>' +
                btnSearch + btnDownload + 
				'</div>';
    }
    
	function formatAmount(val, row, index) {
		var result;
		var valData = row.amount;
		
		result = CurrencyFormatted(valData);
		return result;
	}
	
	function CurrencyFormatted(amount) {
		var delimiter = ","; // replace comma if desired
		var a = amount.split('.',2);
		var d = a[1];
		var i = parseInt(a[0]);
		if(isNaN(i)) { return ''; }
		var minus = '';
		if(i < 0) { minus = '-'; }
		i = Math.abs(i);
		var n = new String(i);
		var a = [];
		while(n.length > 3)
		{
			var nn = n.substr(n.length-3);
			a.unshift(nn);
			n = n.substr(0,n.length-3);
		}
		if(n.length > 0) { a.unshift(n); }
		n = a.join(delimiter);
		if(d.length < 1) { amount = n; }
		else { amount = n + '.' + d; }
		amount = minus + amount;
		return amount;
	}

  function optionButton(data) {
    let btnContent = $(".action-grid");
	let btnContentdtl = $(".action-grid-detail");
	let indexd = 0;
		for (const btnsd of btnContentdtl) {
			const param = data.rows[indexd];
			const btnDetailAnswer = $(btnsd).find("a.btn-primary");
			btnDetailAnswer.click(function () {
				viewAnswer(param);
			});
			indexd++;
		}
	}

	/*
	* action button generator
	*/
	function formatterButton(val, row, index) {
		const btnDetail = commonGrid.btnBuilderText('btn-detail', 'success', 'fa fa-calculator', 'detail');
		//const btnInvoice = commonGrid.btnBuilderText('btn-invoice', 'info', 'fa fa-calendar-check-o', ' Invoice');

		return '<div class="action-grid">' + btnDetail + '</div>';
	}

	function formatterButtonDetail(val, row, index) {
		//let arrgff = row.usergff;
		//let varrgff = arrgff.split(',');
		//if ( varrgff.length > 1 ){
			const btnDetail = commonGrid.btnBuilderText('btn-detail', 'primary', 'fa fa-file-movie-o', '&nbsp; View Detail');
			return '<div class="action-grid-detail">' + btnDetail +'</div>';
		//}
	}


	function loadEvent() {
	  let uiSelectEvent = $("#eventid");
	  $.post(common.baseURL("rep_join_visit_outlet/load_event"), {}, function (res) {
		  uiSelectEvent.empty();
		  uiSelectEvent.select2({
			  placeholder: "Select Event",
			  allowClear: true,
			  data: $.map(res.rows, function (o) {
				o.id = o.id_event; // replace name with the property used for the text
				o.text = o.event; // replace name with the property used for the text
				return o;
			  }),
		  });
		  uiSelectEvent.val(null).trigger('change');
	  });
	}


    function viewAnswer(val) {

        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_join_visit_outlet/open_detail_answer"),
            data : "id_evaluation_outlet="+val.id_evaluation_outlet+"&customerid="+val.customerid+"&kode_outlet="+val.kode_outlet+"&nama_customer="+val.nama_customer+"&username="+val.username,
			success:function(res){
                response = res;
				$('div .modal-header .modal-title').text('Detail Review Outlet');			
				$('#modal_detail').find('.modal-body').html(response);
				$("#modal_detail").modal('show');
            },
            error:function(){
                alert("Load failed");
            }
        });
    }

})();
