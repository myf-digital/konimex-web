(function () {

    const common = new Common();
    common.setTitle("Report Promo");
    // declare dom
    let uiForm = $("#fm-report-promo");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiSelectPosition = $("#tipe_sales-id"); 
    
    // define from *-content.js
    //let param = common.getCookie("module.report.promo.update");
    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        //common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        uiBtnPreview.click(function () {
            open_preview();
        });

        uiBtnDownload.click(function () {
            save_xlsx();
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        }).datepicker("setDate", new Date());
        
        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

        load_tipegff();
    }

    function open_preview() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var position = uiSelectPosition.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrictlevel = paramsession.restrict_level;
        //alert("start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession);

        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_gffaktif/load_data_att"),
            data : "start="+start+"&end="+end+"&position="+position+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrictlevel,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);
            },
            error:function(){
                alert("Load failed");
            }
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

    function save_xlsx() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var position = uiSelectPosition.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrictlevel = paramsession.restrict_level;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        common.direct("rep_gffaktif/savetoxls/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrictlevel+"/"+position);
        /*
        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_promo/savetoxls"),
            data : "idpromo="+idpromo+"&start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);
            },
            error:function(){
                alert("Load failed");
            }
        });
        */
    }

})();