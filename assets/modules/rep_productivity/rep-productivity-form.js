(function () {

    const common = new Common();
    common.setTitle("Report Productivity");
    // declare dom
    let uiForm = $("#fm-report-productivity");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");

    let uiSelectMonth = $("#month-id");
    let uiSelectYear = $("#year-id");
    let uiSelectPosition = $("#tipe_sales-id"); 
    let uiSelectRegional = $("#regional-id");
    let uiSelectCity = $("#city-id");

    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("rep_productivity/load_regional"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1) {
            common.loadingClose();
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);

        uiBtnPreview.click(function () {

            if (uiSelectYear.val()===null){
                alert ('Tahun harus di isi...!');
            }else if (uiSelectMonth.val()===null){
                alert ('Bulan harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            if (uiSelectYear.val()===null){
                alert ('Tahun harus di isi...!');
            }else if (uiSelectMonth.val()===null){
                alert ('Bulan harus di isi...!');
            }else{
                save_xls();
            }
        });

        uiSelectRegional.on('select2:select', function (e) {
            regional = e.params.data;

            loadArea(regional);
        });
        load_tipegff();
    }

    function setupForm(r1) {
        let rows1 = r1.rows;

        uiSelectRegional.select2({
            placeholder: 'Select Regional',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.regionalid; // replace name with the property used for the text
                o.text = o.nama_regional; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectYear.select2({
            placeholder: 'Select Year Period'
        });

        uiSelectMonth.select2({
            placeholder: 'Select Month Period'
        });

        uiSelectCity.select2({
            placeholder: 'Select City',
            allowClear: true
        });

        uiSelectRegional.val(null).trigger('change');
    }    

    function open_preview() {
		
        var year = uiSelectYear.val();
        var month = uiSelectMonth.val();
        var regionalid = uiSelectRegional.val();
        var areaid = uiSelectCity.val();
        var position = uiSelectPosition.val();

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        
        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_productivity/open_detail"),
            data : "year="+year+"&month="+month+"&position="+position+"&regionalid="+regionalid+"&areaid="+areaid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level,
            success:function(res){
                response = res;
                //$('div .modal-header .modal-title').text('Detail Productifity Sales');            
                $('#tbl-content').html(response);
                //$("#modal_detail").modal('show');
            },
            error:function(){
                alert("Load failed");
            }
        });
        
    }

    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("rep_productivity/load_city"), {regionalid: data.regionalid}, function (res) {
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
	
    function load_tipegff() {
        common.loading();
        $.post(common.baseURL("api_v1/call_tipesalesman"), function (res) {
            uiSelectPosition.empty();
            uiSelectPosition.select2({
                placeholder: "Select Position",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.idtipesales; // replace name with the property used for the text
                    o.text = o.tipesales;
                    return o;
                }),
            });
            
            uiSelectPosition.val(null).trigger('change');
            common.loadingClose();
        });
    }

    function save_xls() {
		
        var year = uiSelectYear.val();
        var month = uiSelectMonth.val();
        var position = uiSelectPosition.val();
        var regionalid = uiSelectRegional.val();
        var areaid = uiSelectCity.val();

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        
        common.direct("rep_productivity/savetoxlsx/"+year+"/"+month+"/"+position+"/"+regionalid+"/"+areaid+"/"+usersession+"/"+restrict_level+"/"+idjabatan);
    }

})();