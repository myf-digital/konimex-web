(function () {

    const common = new Common();
    common.setTitle("Download Setup Pjp");
    // declare dom
    let uiForm = $("#fm-download-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectSalesman = $("#salesmanid-id");
    let uiAlertNotif = $("#alertnotif");
    
    // define from *-content.js
    //let param = common.getCookie("module.setup.pjp.update");
    let paramsession = common.getCookie("session");

    initialize();

    function initialize() {

        loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan,restrict_level: paramsession.restrict_level});
        uiSelectSalesman.on('select2:select', function (e) {
            valselected = e.params.data;
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            //if (uiSelectSalesman.val()===null){
            //    uiAlertNotif.show();
                //alert ('GFF (MD/SPG/SALESMAN) harus di isi...!');
            //}else{
                common.direct("conf_setup_pjp/savetoxlsx/"+uiSelectSalesman.val()+"/"+paramsession.username+"/"+paramsession.idjabatan+"/"+paramsession.restrict_level);
            //}
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession, restrict_level: data.restrict_level}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "All GFF",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman + " - " + o.tipe_sales;
                    return o;
                }),
            });
            
            uiSelectSalesman.val(null).trigger('change');
            common.loadingClose();
        });
    }


})();