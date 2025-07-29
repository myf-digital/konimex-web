(function () {

  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  //
  common.setTitle("Report Progress Listing");
  // ui components
  let uiTbl = $("#tbl");
  var baseurl = window.location.origin;
  let paramsession = common.getCookie("session");
  
  initializeGrid();
  setupFormUI();

  function initializeGrid() {
    let option = {
      title: "Table Progress Listing",
      toolbar: toolbar(),
      url: common.baseURL("rep_progress_listing/load"),
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
        {field: 'periode', title: 'Periode', width: 60, sortable: 'true', halign: 'center', align: 'center'},
        {field: 'salesmanid', title: 'Kode Salesman', width: 60, sortable: 'true', halign: 'left', align: 'left'},
        {field: 'nama_salesman', title: 'Nama Salesman', width: 120, sortable: 'true', halign: 'left', align: 'left'},
        {field: 'tipe_sales', title: 'Posisi', width: 80, sortable: 'true', halign: 'left', align: 'left'},
        {field: 'kode_outlet', title: 'Kode Outlet', width: 100, sortable: 'true', halign: 'left', align: 'left'},
        {field: 'nama_customer', title: 'Outlet', width: 200, sortable: 'true', halign: 'left', align: 'left'},
        {field: '_brand', title: 'Total Brand', width: 80, sortable: 'true', halign: 'center', align: 'center'},
      ]],
      onBeforeLoad: function (param) {
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

        common.direct("rep_progress_listing/savexls_progress_listing_all/"+start+"/"+end);
      }
    });
  }

  function toolbar() {
    const btnSearch = commonGrid.btnBuilderText('btn-search', 'primary', 'fa fa-search', ' Search');
    const btnDownload = commonGrid.btnBuilderText('btn-download', 'success', 'fa fa-download', ' Download');
    return '<div class="action-grid-toolbar"> &nbsp;&nbsp;&nbsp; Periode : &nbsp;' +
        '<input type="text" id="get_date1" value="" name="get_date1" readonly style="height: 33px;">' +
        '&nbsp; - &nbsp; <input type="text" id="get_date2" value="" name="get_date2" readonly style="height: 33px;">' +
        btnSearch + btnDownload + '</div>';
  }

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnProgressListing = $(btns).find("a.btn-success");

      btnProgressListing.click(function () {
        get_progress_listing_all(param.siteid ,param.salesmanid, param.customerid);
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
    const btnProgressListing = commonGrid.btnBuilderText('btn-order', 'success', 'fa fa-calculator', 'Listing');
    return '<div class="action-grid">' + btnProgressListing + '</div>';
  }

  function get_progress_listing_all(siteid,  salesmanid, custoemrid) {
    var startdate = $("#get_date1");
    var enddate = $("#get_date2");

    $.ajax({
        type:"POST",
        dataType: "html",
        beforeSend : function() {
          //$("#map-content").html('Populating data, please wait..');
        },
        url: common.baseURL("rep_progress_listing/get_progress_listing_all"),
        data : "siteid="+siteid+"&salesmanid="+salesmanid+"&customerid="+custoemrid+"&startdate="+startdate.val()+"&enddate="+enddate.val(),
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
