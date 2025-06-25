(function () {

    const common = new Common();
    common.setTitle("Report Promo");
    // declare dom
    let uiForm = $("#fm-report-promo");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiTblReport = $("#tbl-content"); 
    
    // define from *-content.js
    //let param = common.getCookie("module.report.promo.update");
    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        uiBtnPreview.click(function () {
            open_preview();
        });

        uiBtnDownload.click(function () {
            save_xls();
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
        
        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

    }

    function open_preview() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        alert("start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession);

        /*$.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_gffaktif/load_data_att"),
            data : "start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);
            },
            error:function(){
                alert("Load failed");
            }
        });*/
        
    }


    function save_xls() {
		
        var idpromo = uiSelectPromo.val();
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        common.direct("rep_promo/savetoxls/"+idpromo+"/"+start+"/"+end+"/"+idjabatan+"/"+usersession);
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