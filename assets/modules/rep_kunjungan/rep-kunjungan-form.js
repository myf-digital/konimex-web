(function () {

    const common = new Common();
    common.setTitle("Report Kunjungan");
    // declare dom
    let uiForm = $("#fm-report-kunjungan");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiSelectParma = $("#salesmanid-id");   

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
            $.post(common.baseURL("rep_kunjungan/load_parma"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
        
        uiBtnPreview.click(function () {
            if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else if ( uiSelectParma.val()===null){
                //uiAlertNotif.show();
                alert ('Parma harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else if ( uiSelectParma.val()===null){
                //uiAlertNotif.show();
                alert ('Parma harus di isi...!');
            }else{
                save_xls();
            }
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });


        
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

    }

        function setupForm(r1) {
            let rows1 = r1.rows;
            uiSelectParma.select2({
                placeholder: 'Select User Parma',
                allowClear: true,
                data: $.map(rows1, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid+'-'+o.nama_salesman+'-'+o.nama_area; // replace name with the property used for the text
                    return o;
                }),
            });

            uiSelectParma.val(null).trigger('change');
        }    



    function open_preview() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var salesmanid = uiSelectParma.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("rep_kunjungan/open_detail"),
                data : "start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&salesmanid="+salesmanid,
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
        var salesmanid = uiSelectParma.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        common.direct("rep_kunjungan/savetoxlsx/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level+"/"+salesmanid);
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