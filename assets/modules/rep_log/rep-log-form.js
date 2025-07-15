(function () {

    const common = new Common();
    common.setTitle("Report History Login");
    // declare dom
    let uiForm = $("#fm-report-log");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");  
    let uiStartPeriode = $("#start_periode");
    let uiEndPeriode = $("#end_periode");
    
    // define from *-content.js
    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {

        uiBtnPreview.click(function () {
            if ( uiStartPeriode.val()===''){
                alert ('Periode harus di isi...!');
            } else if ( uiEndPeriode.val()===''){
                alert ('Periode harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            if ( uiStartPeriode.val()===''){
                alert ('Periode harus di isi...!');
            } else if ( uiEndPeriode.val()===''){
                alert ('Periode harus di isi...!');
            }else{
                save_xls();
            }
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
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

    function open_preview() {
		
        var start = uiStartPeriode.val();
        var end = uiEndPeriode.val();
        
        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_log/open_detail"),
            data : "start="+start+"&end="+end,
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

        common.direct("rep_log/savetoxlsx/"+start+"/"+end);
    }

})();