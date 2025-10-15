(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Dashboard Maps");
    // ui components
    let uiMaps = $("#maps");
    let uiTbl = $("#tbl");
    let uiTanggalPicker = $("#get_date"); 
    var baseurl = window.location.origin;
    let today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;
    let paramsession = common.getCookie("session");
    let datas = null;

    /*function initMap(vaction) {
      map = new google.maps.Map(document.getElementById('maps'), {
        zoom: 7,
        center: vloc,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoomControl: true,
      });

      var marker = new google.maps.Marker({position: vloc, map: map});
    }*/
    //initialize();
    initializeGrid();
    setupFormUI();
    initializeParamMaps();

    //alert(baseurl);
    function initializeGrid() {
      let option = {
          title: "Table Performance PAR-MA",
          toolbar: toolbar(),
          url: common.baseURL("app_dashboard/load"),
          queryParams: {
            get_date: new Intl.DateTimeFormat('sv-SE').format(new Date()),
            usersession: paramsession.username,
            idjabatan: paramsession.idjabatan,
            restrict_level: paramsession.restrict_level,
            restrict_bu: paramsession.restrict_bu
          },
          pageNumber: 1,
          pageSize: commonGrid.getCurentSize(),
          pageList: commonGrid.getPageSize(),
          height:400,
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
            {field: 'salesmanid', title: 'Kode GFF', width: 60, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'nama_salesman', title: 'Nama GFF', width: 120, sortable: 'true', halign: 'left', align: 'left'},
            {field: 'start_time', title: 'Check In', width: 80, sortable: 'true', halign: 'left', align: 'left', formatter: formatterFileStart},
            {field: 'end_time', title: 'Check Out', width: 80, sortable: 'true', halign: 'left', align: 'left', formatter: formatterFileEnd},
            {field: 'city', title: 'City', width: 100, sortable: 'true', halign: 'left', align: 'left'},
            {field: '_jadwal', title: 'Schedule', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_call', title: '<img class="color" src="'+baseurl+'/assets/images/ic_call.png"></img> Call', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_extra_call', title: '<img class="color" src="'+baseurl+'/assets/images/ic_extra_call.png"></img> Extra Call', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_crc', title: 'CRC', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            {field: '_order', title: 'Order', width: 70, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'noo', title: '<img class="color" src="'+baseurl+'/assets/mapIcon/legend_5.png" title="Register Titik Outlet"></img>Check In', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'effectivecall', title: '<img class="image" src="'+baseurl+'/assets/mapIcon/legend_1.png"></img> Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'ExtraCall', title: '<img class="color" src="'+baseurl+'/assets/mapIcon/legend_2.png"></img>Check Out', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'InvalidCall', title: '<img class="color" src="'+baseurl+'/assets/mapIcon/legend_4.png"></img> Inv Call', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'eff_time', title: 'Eff Time', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'eff_time', title: 'Eff Time', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'eff_order', title: 'Eff Order', width: 75, sortable: 'true', halign: 'center', align: 'center'},
            //{field: 'amount', title: 'Amount', width: 150, sortable: 'true', halign: 'center', align: 'right', formatter: formatAmount},
            //{field: 'longlatnull', title: 'Blank Posisi', width: 100, sortable: 'true', halign: 'center', align: 'center'},
          ]],
          onBeforeLoad: function (param) {
              param = common.replaceGridFilterPrefix(param, "a");
              //param = common.replaceGridFilter(param,["periode"],["b.periode"]);
          },
          onLoadSuccess: function (data) {
              datas = data.rows;
              $(this).datagrid('resize', 'fixRowHeight');
              optionButton(data);
          }
      };
      uiTbl.datagrid(commonGrid.optionValue(option));
      uiTbl.datagrid('enableFilter');
      common.removeFilter(['options']);        

    }

    function formatterFileStart(val, row, index) {
      let html = '-';
      if (val && row.url_start_image) {
        html = `<a tabindex="0" class="pointer btn-preview_start_image" data-index="${index}">${formatDatetime(val)}</a>`;
      } else if (val) {
        html = formatDatetime(val);
      }
      return html;
    }

    function formatterFileEnd(val, row, index) {
      let html = '-';
      if (val && row.url_end_image) {
        html = `<a tabindex="0" class="pointer btn-preview_end_image" data-index="${index}">${formatDatetime(val)}</a>`;
      } else if (val) {
        html = formatDatetime(val);
      }
      return html;
    }

    function formatDatetime(datetime) {
      if (!datetime) return '-';

        const d = new Date(datetime.replace(' ', 'T'));
        if (isNaN(d)) return '-';

        const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const tgl = String(d.getDate()).padStart(2, '0');
        const bln = bulan[d.getMonth()];
        const thn = d.getFullYear();
        const jam = String(d.getHours()).padStart(2, '0');
        const menit = String(d.getMinutes()).padStart(2, '0');

        return `${tgl} ${bln} ${thn} ${jam}:${menit}`;
    }

    function setupFormUI() {
        let uiTanggalPicker = $("#get_date"); 
        uiTanggalPicker.datepicker({
            format: 'yyyy-mm-dd',
            //startDate: '-3d'
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });
 
        let uiBtnSearch = $("#btn-search");
        uiBtnSearch.click(function () {
            //alert (paramsession.username);
            uiTbl.datagrid('load',{
                get_date: uiTanggalPicker.val(),
                usersession: paramsession.username,
                idjabatan: paramsession.idjabatan,
                restrict_level: paramsession.restrict_level,
                restrict_bu: paramsession.restrict_bu
            });
        });
    }

    function toolbar() {
        const btnSearch = commonGrid.btnBuilder('btn-search', 'success', 'fa fa-search');
        return '<div class="action-grid-toolbar"> &nbsp;&nbsp;&nbsp; Periode : &nbsp; <input type="text" id="get_date" value="'+today+'" name="get_date">'+ btnSearch + '</div>';
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
    let btnContent = $('.action-grid');
    let index = 0;
    for (const btns of btnContent) {
        const param = data.rows[index];
        const btnMaps = $(btns).find('a#btn-maps');
        const btnTrackings = $(btns).find('a#btn-tracking');
        const btnReports = $(btns).find('a#btn-report');
        btnMaps.click(function () {
            get_map(param.salesmanid,param.periode);
        });
        btnTrackings.click(function () {
            get_maptracking(param.salesmanid,param.periode)
        });
        btnReports.click(function () {
            open_detail(param.salesmanid, param.siteid);
        });
        index++;
    }
  }

  $(document).on('click', '.btn-preview_start_image', function() {
    let index = $(this).attr('data-index');
    previewFiles(datas[index], 'start');
  });

  $(document).on('click', '.btn-preview_end_image', function() {
    let index = $(this).attr('data-index');
    previewFiles(datas[index], 'end');
  });

/*
* action button generator
*/
function formatterButton(val, row, index) {
    const btnMap = commonGrid.btnBuilderDash('btn-maps', 'default', '../assets/images/ic_route.png');
    const btnTracking = commonGrid.btnBuilderDash('btn-tracking', 'info', '../assets/images/ic_location.png');
    const btnReport = commonGrid.btnBuilderDash('btn-report', 'success', '../assets/images/ic_detail.png');
    return '<div class="action-grid">' + btnMap + ' ' + btnTracking + ' ' + btnReport + '</div>';
}

function previewFiles(data, type) {
  if (!data) return;
  
  var attendance = {
    start: 'Check In',
    end: 'Check Out',
  };
  var tempFile = [{
    href: data[`url_${type}_image`],
    title: `
      ${data.salesmanid} - ${data.nama_salesman} <br />
      ${attendance[type]}: ${formatDatetime(data[`${type}_time`])} <br />
      Keterangan: ${data[`${type}_keterangan`] || '-'}
    `
  }];

  $.fancybox.open(tempFile, {
    helpers: {
      thumbs: {
        width: 75,
        height: 50
      }
    }
  });
}

function open_detail(sid,siteid) {
		
    var get = document.getElementsByName('get_date')[0].value;
    $.ajax({
        type:"POST",
        dataType: "html",
        beforeSend : function() {
            //$("#map-content").html('Populating data, please wait..');
        },
        url: common.baseURL("app_dashboard/open_detail"),
        data : "sid="+sid+"&get_date="+get+"&siteid="+siteid,
        success:function(res){
            response = res;
            $('div .modal-header .modal-title').text('Detail Productifity Sales');			
            $('#modal_detail').find('.modal-body').html(response);
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

function get_map(sid,periode) {
    var get_date = document.getElementsByName('get_date')[0].value;
    $.ajax({
        type:"POST",
        dataType: "html",
        beforeSend : function() {
            common.loading();
        },
        url: common.baseURL("app_dashboard/get_gmap"),
        data : "sid="+sid+"&get_date="+get_date,
        success:function(msg){
            $("#maps").html(msg);
            common.loadingClose();
        },
        error:function(){
            alert("Load failed");
            common.loadingClose();
        }
    });
}


function get_maptracking(sid,periode) {
    var get_date = document.getElementsByName('get_date')[0].value;
    $.ajax({
        type:"POST",
        dataType: "html",
        beforeSend : function() {
            common.loading();
            //$("#maps").html('Populating data, please wait..');
        },
        url: common.baseURL("app_dashboard/get_gmaptracking"),
        data : "sid="+sid+"&get_date="+get_date,
        success:function(msg){
            $("#maps").html(msg);
            common.loadingClose();
        },
        error:function(){
            alert("Load failed");
            common.loadingClose();
        }
    });
}


})();
