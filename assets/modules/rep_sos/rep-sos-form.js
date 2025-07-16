(function () {

    const common = new Common();
    common.setTitle("Report SOS");
    // declare dom
    let uiForm = $("#fm-report-sos");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectTypesos = $("#typesos-id");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiSelectAccount = $("#account-id");   

    //let uiTblReport = $("#tbl-content"); 
    
    // define from *-content.js
    //let param = common.getCookie("module.report.promo.update");
    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        //common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            //$.post(common.baseURL("rep_promo/load_promo"), filter.build()),
            $.post(common.baseURL("rep_promo/load_account"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
        
        uiBtnPreview.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectTypesos.val()===null){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else if ( uiSelectAccount.val()===null){
                //uiAlertNotif.show();
                alert ('Account harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectTypesos.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else if ( uiSelectAccount.val()===null){
                //uiAlertNotif.show();
                alert ('Account harus di isi...!');
            }else{
                save_xls();
            }
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });


        uiSelectTypesos.select2({
            placeholder: 'Select Option',
            allowClear: true,
        });
        uiSelectTypesos.val(null).trigger('change');
        
        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            var endDate = new Date(selected.date.valueOf());
            endDate.setDate(endDate.getDate() + 7);
            uiEndPeriode.datepicker('setStartDate', startDate);
            uiEndPeriode.datepicker('setEndDate', endDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

    }

    function setupForm(r1) {
        let rows1 = r1.rows;

        uiSelectAccount.select2({
            placeholder: 'Select Account',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.classid; // replace name with the property used for the text
                o.text = o.nama_class; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectAccount.val(null).trigger('change');
    }    

    function open_preview() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var account = uiSelectAccount.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("rep_sos/open_detail"),
                data : "start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&idaccount="+account,
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

    function save_xls() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var account = uiSelectAccount.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        common.direct("rep_sos/savetoxlsx/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level+"/"+account);
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