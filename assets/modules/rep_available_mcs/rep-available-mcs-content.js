(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Report Available MCS");
    // ui components
    let uiTbl = $("#tbl");
    var baseurl = window.location.origin;
    let paramsession = common.getCookie("session");
    
    initializeGrid();
    setupFormUI();

    function initializeGrid() {
      let option = {
          title: "Table Available MCS",
          toolbar: toolbar(),
          url: common.baseURL("rep_available_mcs/load"),
          pageNumber: 1,
          pageSize: commonGrid.getCurentSize(),
          pageList: commonGrid.getPageSize(),
          height:600,
          columns: [[
            {field: 'customerid', title: 'Outlet ID', width: 50, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'kode_outlet', title: 'Kode Outlet', width: 50, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_customer', title: 'Outlet Name', width: 100, sortable: 'true', halign: 'left', align: 'left'},
            //{field: 'type', title: 'Channel', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_class', title: 'Account', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_regional', title: 'Regional', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_area', title: 'Area', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'city', title: 'City', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'dc', title: 'DC', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'active_mcs', title: 'MCS', width: 50, sortable: 'true', halign: 'center', align: 'center'},
            {field: 'active_sku', title: 'SKU Last3Months', width: 50, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_percentage', title: 'Percentage', width: 50, sortable: 'true', halign: 'center', align: 'center'},
			{field:'detail', title:'Detail', halign: 'center', align: 'left', width:50, formatter: formatterButtonDetail},
            //{field: '_crc', title: 'CRC', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            //{field: '_order', title: 'Order', width: 70, sortable: 'true', halign: 'center', align: 'center'},
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
        loadAccount();
        loadRegional();

        let uiSelectAccount = $("#classid");
        let uiSelectRegional = $("#regional-id");
        let uiSelectArea = $("#area-id");
        let uiSelectCity = $("#subarea-id");

        uiSelectArea.select2({
          placeholder: "Select Area",
          allowClear: true,
        });

        uiSelectCity.select2({
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
        });

        let uiBtnSearch = $("#btn-search");
        let uiBtnDownload = $("#btn-download");

        uiBtnSearch.click(function () {
          if ( uiSelectAccount.val() == null){
            alert ('Account harus di isi...!');
          }else if ( uiSelectRegional.val() == null){
            alert ('Regional harus di isi...!');
          } else {
            uiTbl.datagrid('load',{
              classid: uiSelectAccount.val(),
              regionalid: uiSelectRegional.val(),
              areaid: uiSelectArea.val(),
              subareaid: uiSelectCity.val(),
              usersession: paramsession.username,
              restrict_level: paramsession.restrict_level
            });
          }
        });

        uiBtnDownload.click(function () {
          if ( uiSelectAccount.val() == null){
            alert ('Account harus di isi...!');
          }else if ( uiSelectRegional.val() == null){
            alert ('Regional harus di isi...!');
          } else {
            
            let usersession = paramsession.username;
            let restrict_level = paramsession.restrict_level;
			let classid = uiSelectAccount.val();
            let regionalid = uiSelectRegional.val();
            let areaid = uiSelectArea.val();
            let subareaid = uiSelectCity.val();
			
            common.direct("rep_available_mcs/savexls_available_mcs_all/"+usersession+"/"+restrict_level+"/"+classid+"/"+regionalid+"/"+areaid+"/"+subareaid);
          }
        });
    }

    function toolbar() {
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
        return '<div class="action-grid-toolbar"> &nbsp;&nbsp;&nbsp; Account : &nbsp;&nbsp;&nbsp;<select id="classid" name="classid" placeholder="Account"></select>' +
               '&nbsp;&nbsp;&nbsp; Regional : &nbsp;&nbsp;&nbsp;<select id="regional-id" name="regional" placeholder="Regional"></select>' +
               '&nbsp;&nbsp; Area : &nbsp;&nbsp;&nbsp;<select id="area-id" name="area" placeholder="Area"></select>' +
               '&nbsp;&nbsp; City : &nbsp;&nbsp;&nbsp;<select id="subarea-id" name="subarea" placeholder="City"></select>' +
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
			const btnDetailProduct = $(btnsd).find("a.btn-primary");
			btnDetailProduct.click(function () {
				viewProduct(param);
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
			const btnDetail = commonGrid.btnBuilderText('btn-detail', 'primary', 'fa fa-file-movie-o', ' View Product');
			return '<div class="action-grid-detail">' + btnDetail +'</div>';
		//}
	}


	function loadAccount() {
	  let uiSelectAccount = $("#classid");
	  $.post(common.baseURL("rep_available_mcs/load_account"), {}, function (res) {
		  uiSelectAccount.empty();
		  uiSelectAccount.select2({
			  placeholder: "Select Account",
			  allowClear: true,
			  data: $.map(res.rows, function (o) {
				o.id = o.classid; // replace name with the property used for the text
				o.text = o.nama_class; // replace name with the property used for the text
				return o;
			  }),
		  });
		  uiSelectAccount.val(null).trigger('change');
	  });
	}

	function loadRegional() {
	  let uiSelectRegional = $("#regional-id");
	  $.post(common.baseURL("rep_available_mcs/load_regional"), {}, function (res) {
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
	  $.post(common.baseURL("rep_available_mcs/load_area"), {regionalid: data.regionalid}, function (res) {
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
	  $.post(common.baseURL("rep_available_mcs/load_city"), {areaid: data.areaid}, function (res) {
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

    function viewProduct(val) {

        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_available_mcs/open_productmcs_activesku"),
            data : "customerid="+val.customerid,
            success:function(res){
                response = res;
                //$('div .modal-header .modal-title').text('Detail Productifity Sales');			
                $('#tbl-listproduct').html(response);
                $("#viewModal").modal();
                //$("#modal_detail").modal('show');
            },
            error:function(){
                alert("Load failed");
            }
        });
    }

})();
