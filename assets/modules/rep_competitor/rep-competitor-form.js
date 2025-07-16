(function () {

    const common = new Common();
    common.setTitle("Report Competitor");
    // declare dom
    let uiForm = $("#fm-report-competitor");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiBtnDownloadText = $("#btn-download-text-form");
    let uiSelectTypeComp = $("#typecompetitor-id");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    //let uiTblReport = $("#tbl-content"); 
    
    // define from *-content.js
    //let param = common.getCookie("module.report.promo.update");
    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        //common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();

        uiBtnPreview.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectTypeComp.val()===null){
                //uiAlertNotif.show();
                alert ('Option harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectTypeComp.val()===null){
                //uiAlertNotif.show();
                alert ('Option harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else{
                save_xls();
            }
        });

        uiBtnDownloadText.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectTypeComp.val()===null){
                //uiAlertNotif.show();
                alert ('Option harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else{
                save_xls_text();
            }
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        uiSelectTypeComp.select2({
            placeholder: 'Select Option',
            allowClear: true,
        });
        uiSelectTypeComp.val(null).trigger('change');
        

        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            var endDate = new Date(selected.date.valueOf());
            endDate.setDate(endDate.getDate() + 31);
            uiEndPeriode.datepicker('setStartDate', startDate);
            uiEndPeriode.datepicker('setEndDate', endDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

        /*uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });*/

    }

    function open_preview() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        if (uiSelectTypeComp.val()==='PC'){
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("rep_competitor/open_detail"),
                data : "start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level,
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
        } else {
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("rep_competitor/open_detail_npd"),
                data : "start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level,
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
       
    }

    function save_xls() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        if (uiSelectTypeComp.val()==='PC'){
            common.direct("rep_competitor/savetoxlsx/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level);
        }else{
            common.direct("rep_competitor/savetoxlsx_npd/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level);
        }
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

    function save_xls_text() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        if (uiSelectTypeComp.val()==='PC'){
            common.direct("rep_competitor/savetoxlsx_textonly/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level);
        }else{
            common.direct("rep_competitor/savetoxlsx_npd_textonly/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level);
        }
    }

})();