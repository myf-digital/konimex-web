(function () {

    const common = new Common();
    common.setTitle("Report Absensi");
    // declare dom
    let uiForm = $("#fm-report-absensi");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiSelectMedrep = $("#salesmanid-id");   

    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        //common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("rep_absensi/load_parma"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
        
        uiBtnPreview.click(function () {
            if ( uiStartPeriode.val()===''){
                alert ('Periode harus di isi...!');
            }else if ( uiSelectMedrep.val()===null){
                alert ('TPE harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            if ( uiStartPeriode.val()===''){
                alert ('Periode harus di isi...!');
            }else if ( uiSelectMedrep.val()===null){
                alert ('TPE harus di isi...!');
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
        uiSelectMedrep.select2({
            placeholder: 'Select User TPE',
            allowClear: true,
            data: $.map(rows1, function (o) {
                let locationName = o.nama_subarea || o.nama_area || o.nama_regional || '';
                o.id = o.salesmanid; // replace name with the property used for the text
                o.text = o.salesmanid + '-' + o.nama_salesman + '-' + locationName; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectMedrep.val(null).trigger('change');
    }    


    function open_preview() {
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var salesmanid = uiSelectMedrep.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
            $.ajax({
                type:"POST",
                dataType: "html",
                url: common.baseURL("rep_absensi/open_detail"),
                data : "start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&salesmanid="+salesmanid,
                success:function(res){
                    response = res;
                    $('#tbl-content').html(response);
                },
                error:function(){
                    alert("Load failed");
                }
            });
        
    }

    function save_xls() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        var salesmanid = uiSelectMedrep.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        common.direct("rep_absensi/savetoxlsx/"+start+"/"+end+"/"+idjabatan+"/"+usersession+"/"+restrict_level+"/"+salesmanid);
    }

})();