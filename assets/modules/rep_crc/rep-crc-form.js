(function () {

    const common = new Common();
    common.setTitle("Report CRC");
    // declare dom
    let uiForm = $("#fm-report-crc");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectAccount = $("#account-id");
    let uiSelectOutlet = $("#outlet-id");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode");

    let paramsession = common.getCookie("session");

    initializeParam();

    function initializeParam() {
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);

        $.when(
            $.post(common.baseURL("rep_crc/load_account"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1) {
            common.loadingClose();
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);

        uiBtnPreview.click(function () {

            if (uiSelectAccount.val()===null){
                $.alert({
                    title: 'Error ',
                    content: 'Sub Channel / Account harus di isi...!',
                    containerFluid: true
                });
            } else if ( uiSelectOutlet.val() === null){
                $.alert({
                    title: 'Error ',
                    content: 'Outlet harus di isi...!',
                    containerFluid: true
                });
            } else if ( uiStartPeriode.val()===''){
                $.alert({
                    title: 'Error ',
                    content: 'Periode harus di isi...!',
                    containerFluid: true
                });
            } else if ( uiEndPeriode.val()===''){
                $.alert({
                    title: 'Error ',
                    content: 'Periode harus di isi...!',
                    containerFluid: true
                });
            } else {
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            if (uiSelectAccount.val() === null){
                $.alert({
                    title: 'Error ',
                    content: 'Sub Channel / Account harus di isi...!',
                    containerFluid: true
                });
            } else if ( uiSelectOutlet.val() === null){
                $.alert({
                    title: 'Error ',
                    content: 'Outlet harus di isi...!',
                    containerFluid: true
                });
            } else if ( uiStartPeriode.val()=== ''){
                $.alert({
                    title: 'Error ',
                    content: 'Periode harus di isi...!',
                    containerFluid: true
                });
            } else if ( uiEndPeriode.val()=== ''){
                $.alert({
                    title: 'Error ',
                    content: 'Periode harus di isi...!',
                    containerFluid: true
                });
            } else {
                save_xls();
            } 
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        uiSelectAccount.on('select2:select', function (e) {
            accountSelected = e.params.data;
            loadOutlet(accountSelected, paramsession);
        });

        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            var endDate = new Date(selected.date.valueOf());
            endDate.setDate(endDate.getDate() + 29);
            uiEndPeriode.datepicker('setStartDate', startDate);
            uiEndPeriode.datepicker('setEndDate', endDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

        uiSelectOutlet.select2({
            placeholder: 'Select Outlet',
            allowClear: true
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

    function loadOutlet(data, param) {
        common.loading();
        $.post(common.baseURL("rep_crc/load_outlet"), {classid: data.classid, restrict_level: param.restrict_level, usersession: param.username }, function (res) {
            uiSelectOutlet.empty();
            uiSelectOutlet.select2({
                placeholder: "Select Outlet",
                allowClear: true,
                data: $.map(res.rows, function (o) {
                    o.id = o.customerid; // replace name with the property used for the text
                    o.text = o.nama_customer+"(OutletID:"+o.customerid+")";
                    return o;
                }),
            });
            common.loadingClose();
        });

    }

    function open_preview() {

		let accountid = uiSelectAccount.val();
        let customerid = uiSelectOutlet.val();
        let start = uiStartPeriode.val();
        let end = uiEndPeriode.val();

        common.loading();

        $.ajax({
            type:"POST",
            dataType: "html",
            url: common.baseURL("rep_crc/load_view_crc"),
            data : "customerid="+customerid+"&accountid="+accountid+"&start="+start+"&end="+end,
            success:function(res){
                response = res;
                console.log(response)
                $('#tbl-content').html(response);

                common.loadingClose();
            },
            error:function(){
                alert("Load failed");

                common.loadingClose();
            }
        });
        
    }

    function save_xls() {
        
        let accountid = uiSelectAccount.val();
        let customerid = uiSelectOutlet.val();
        let start = uiStartPeriode.val();
        let end = uiEndPeriode.val();

        common.direct("rep_crc/download_to_excel_spreadsheet?customerid="+customerid+"&accountid="+accountid+"&start="+start+"&end="+end);
    }

})();