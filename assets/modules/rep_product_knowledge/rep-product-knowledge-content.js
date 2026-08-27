(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Report Product Knowledge");
    // ui components
    let uiTbl = $("#tbl");
    var baseurl = window.location.origin;
    let paramsession = common.getCookie("session");
    
    initializeGrid();
    setupFormUI();

    function initializeGrid() {
      let option = {
          title: "List Peserta Product Knowledge",
          toolbar: toolbar(),
          url: common.baseURL("rep_product_knowledge/load"),
          pageNumber: 1,
          pageSize: commonGrid.getCurentSize(),
          pageList: commonGrid.getPageSize(),
          height:600,
          columns: [[
            {field: 'event', title: 'Event', width: 100, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'start_period', title: 'Start', width: 30, sortable: 'true', halign: 'left', align: 'center'},
            {field: 'end_period', title: 'End', width: 30, sortable: 'true', halign: 'left', align: 'center'},
            {field: 'username', title: 'Kode TPE', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_salesman', title: 'Name', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_regional', title: 'Regional', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_area', title: 'Area', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            // {field: 'city', title: 'City', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'final_score', title: 'Score', width: 25, sortable: 'true', halign: 'left', align: 'center'},
			{field:'detail', title:'Detail', halign: 'center', align: 'center', width:50, formatter: formatterButtonDetail},
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

		/*uiSelectCity.select2({
          placeholder: "Select City",
          allowClear: true,
        });

        uiSelectRegional.on('select2:select', function (e) {
          regional = e.params.data;
          loadArea(regional);
        });

        uiSelectArea.on('select2:select', function (e) {
          area = e.params.data;
          loadCity(area);
        });*/

        let uiBtnSearch = $("#btn-search");
        let uiBtnDownload = $("#btn-download");

        uiBtnSearch.click(function () {
          if ( uiSelectEvent.val() == null){
            alert ('Event harus di isi...!');
          } else {
            uiTbl.datagrid('load',{
              eventid: uiSelectEvent.val(),
              usersession: paramsession.username,
              restrict_level: paramsession.restrict_level
            });
          }
        });

        uiBtnDownload.click(function () {
          if ( uiSelectEvent.val() == null){
            alert ('Event harus di isi...!');
          } else {
            
            let usersession = paramsession.username;
            let restrict_level = paramsession.restrict_level;
			let eventid = uiSelectEvent.val();
			
            common.direct("rep_product_knowledge/savexls_product_knowledge_all/"+usersession+"/"+restrict_level+"/"+eventid);
          }
        });
    }

    function toolbar() {
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
        return '<div class="action-grid-toolbar"> &nbsp;&nbsp;&nbsp; Event : &nbsp;&nbsp;&nbsp;<select id="eventid" name="eventid" placeholder="Event Name"></select>' +
                btnSearch + btnDownload + '</div>';
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
	  $.post(common.baseURL("rep_product_knowledge/load_event"), {}, function (res) {
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

	function loadRegional() {
	  let uiSelectRegional = $("#regional-id");
	  $.post(common.baseURL("rep_product_knowledge/load_regional"), {}, function (res) {
		  uiSelectRegional.empty();
		  uiSelectRegional.select2({
			  placeholder: "Select Regional",
			  allowClear: true,
			  data: $.map(res.rows, function (o) {
				o.id = o.regionalid; // replace name with the property used for the text
				o.text = o.nama_regional; // replace name with the property used for the text
				return o;
			  }),
		  });
		  uiSelectRegional.val(null).trigger('change');
	  });
	}

	function loadArea(data) {
	  let uiSelectArea = $("#area-id");
	  common.loading();
	  $.post(common.baseURL("rep_product_knowledge/load_area"), {regionalid: data.regionalid}, function (res) {
		  uiSelectArea.empty();
		  uiSelectArea.select2({
			  placeholder: "Select Area",
			  allowClear: true,
			  data: $.map(res.rows, function (o) {
				  o.id = o.areaid; // replace name with the property used for the text
				  o.text = o.nama_area;
				  return o;
			  }),
		  });
		  uiSelectArea.val(null).trigger('change');
		  common.loadingClose();
	  });
	}

	function loadCity(data) {
	  let uiSelectCity = $("#subarea-id");
	  common.loading();
	  $.post(common.baseURL("rep_product_knowledge/load_city"), {areaid: data.areaid}, function (res) {
		  uiSelectCity.empty();
		  uiSelectCity.select2({
			  placeholder: "Select City",
			  allowClear: true,
			  data: $.map(res.rows, function (o) {
				  o.id = o.subareaid; // replace name with the property used for the text
				  o.text = o.nama_area;
				  return o;
			  }),
		  });
		  uiSelectCity.val(null).trigger('change');
		  common.loadingClose();
	  });
	}

    function viewAnswer(val) {

        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_product_knowledge/open_detail_answer"),
            data : "id_event="+val.id_event+"&username="+val.username,
            success:function(res){
                response = res;
				$('div .modal-header .modal-title').text('Detail Product Knowledge');			
				$('#modal_detail').find('.modal-body').html(response);
				$("#modal_detail").modal('show');
            },
            error:function(){
                alert("Load failed");
            }
        });
    }

})();
