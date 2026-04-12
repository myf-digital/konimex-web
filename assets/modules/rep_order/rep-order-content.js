(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Report Order");
    // ui components
    let uiTbl = $("#tbl");
    var baseurl = window.location.origin;
    let paramsession = common.getCookie("session");
    
    initializeGrid();
    setupFormUI();

    function initializeGrid() {
      let option = {
          title: "Table Sales Order",
          toolbar: toolbar(),
          url: common.baseURL("rep_order/load"),
          pageNumber: 1,
          pageSize: commonGrid.getCurentSize(),
          pageList: commonGrid.getPageSize(),
          height:400,
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
            {field: 'salesmanid', title: 'Kode MEDREP', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_salesman', title: 'Nama MEDREP', width: 120, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'tipe_sales', title: 'Posisi', width: 80, sortable: 'true', halign: 'left', align: 'left'},
            // {field: 'city', title: 'City', width: 100, sortable: 'true', halign: 'left', align: 'left'},
            {field: '_jadwal', title: 'Schedule', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_call', title: '<img class="color" src="'+baseurl+'/assets/images/ic_call.png"></img> Call', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_extra_call', title: '<img class="color" src="'+baseurl+'/assets/images/ic_extra_call.png"></img> Extra Call', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_crc', title: 'CRC', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_order', title: 'Order', width: 70, sortable: 'true', halign: 'center', align: 'center', formatter: formatNumber},
          ]],
          onBeforeLoad: function (param) {
              //param = common.replaceGridFilterPrefix(param, "a");
              //param = common.replaceGridFilter(param,["periode"],["b.periode"]);
          },
          onLoadSuccess: function (data) {
              $(this).datagrid('resize', 'fixRowHeight');
              optionButton(data);
          }
      };
      uiTbl.datagrid(commonGrid.optionValue(option));

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

        loadRegional();

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

          if ( uiTanggalPicker1.val() === ''){
            alert ('Periode harus di isi...!');
          } else if(uiTanggalPicker2.val() === '') {
            alert ('Periode harus di isi...!');
          } else {
            uiTbl.datagrid('load',{
              get_date1: uiTanggalPicker1.val(),
              get_date2: uiTanggalPicker2.val(),
              regionalid: uiSelectRegional.val(),
              areaid: uiSelectArea.val(),
              subareaid: uiSelectCity.val(),
              usersession: paramsession.username,
              restrict_level: paramsession.restrict_level
            });
          }
        });

        uiBtnDownload.click(function () {
          if ( uiTanggalPicker1.val() === ''){
            alert ('Periode harus di isi...!');
          } else if(uiTanggalPicker2.val() === '') {
            alert ('Periode harus di isi...!');
          } else {
            let start = uiTanggalPicker1.val();
            let end = uiTanggalPicker2.val();
            
            let usersession = paramsession.username;
            let restrict_level = paramsession.restrict_level;

            let regionalid = uiSelectRegional.val();
            let areaid = uiSelectArea.val();
            let subareaid = uiSelectCity.val();

            common.direct("rep_order/savexls_order_all_salesman/"+start+"/"+end+"/"+usersession+"/"+restrict_level+"/"+regionalid+"/"+areaid+"/"+subareaid);
          }
        });
    }

    function toolbar() {
        const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
        const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
        return '<div class="action-grid-toolbar"> &nbsp;&nbsp;&nbsp; Periode : &nbsp;' +
               '<input type="text" id="get_date1" value="" name="get_date1" readonly style="height: 33px;">' +
               '&nbsp; - &nbsp; <input type="text" id="get_date2" value="" name="get_date2" readonly style="height: 33px;">' +
               '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="regional-id" name="regional" placeholder="Regional"> '+
               '</select>' +
               '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="area-id" name="area" placeholder="Area"> '+
               '</select>' +
              //  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="subarea-id" name="subarea" placeholder="City"> '+
              //  '</select>' +
                btnSearch + btnDownload + '</div>';
    }

  function formatNumber(val, row, index) {
    return Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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
    let index = 0;
    for (const btns of btnContent) {
        const param = data.rows[index];
        const btnOrder = $(btns).find("a.btn-success");
        const btnInvoice = $(btns).find("a.btn-info");

        btnOrder.click(function () {
          get_order_all(param.salesmanid);
          var link = document.getElementById('click-scroll');
          link.click();
        });
        btnInvoice.click(function () {
          get_data_tagihan(param.salesmanid);
          var link = document.getElementById('click-scroll');
          link.click();
        });
        index++;
    }
}

/*
* action button generator
*/
function formatterButton(val, row, index) {
    const btnOrder = commonGrid.btnBuilderText('btn-order', 'success', 'fa fa-calculator', ' Order');
    //const btnInvoice = commonGrid.btnBuilderText('btn-invoice', 'info', 'fa fa-calendar-check-o', ' Invoice');

    return '<div class="action-grid">' + btnOrder + '</div>';
}

function loadRegional() {
  let uiSelectRegional = $("#regional-id");
  $.post(common.baseURL("rep_order/load_regional"), {}, function (res) {
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
  $.post(common.baseURL("rep_order/load_area"), {regionalid: data.regionalid}, function (res) {
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
  $.post(common.baseURL("rep_order/load_city"), {areaid: data.areaid}, function (res) {
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

function get_data_tagihan(salesmanid) {
    var startdate1 = $("#get_date1");
    var startdate2 = $("#get_date2");
    
    $.ajax({
        type:"POST",
        dataType: "html",
        beforeSend : function() {
            //$("#map-content").html('Populating data, please wait..');
        },
        url: common.baseURL("rep_order/get_tagihan_all"),
        data : "salesmanid="+salesmanid+"&startdate1="+startdate1.val()+"&startdate2="+startdate2.val(),
        success:function(res){
            response = res;
            $('#tbl-content').html(response);
        },
        error:function(){
            alert("Load failed");
        }
    });
}

function get_order_all(salesmanid) {
    var startdate1 = $("#get_date1");
    var startdate2 = $("#get_date2");

    $.ajax({
        type:"POST",
        dataType: "html",
        beforeSend : function() {
            //$("#map-content").html('Populating data, please wait..');
        },
        url: common.baseURL("rep_order/get_order_all"),
        data : "salesmanid="+salesmanid+"&startdate1="+startdate1.val()+"&startdate2="+startdate2.val(),
        success:function(res){
            response = res;
            $('#tbl-content').html(response);
        },
        error:function(){
            alert("Load failed");
        }
    });
}
})();
