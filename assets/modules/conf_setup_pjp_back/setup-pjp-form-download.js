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

        loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan});
        uiSelectSalesman.on('select2:select', function (e) {
            valselected = e.params.data;
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectSalesman.val()===null){
                uiAlertNotif.show();
                //alert ('MEDREP (MD/SPG/MEDREP) harus di isi...!');
            }else{
                common.direct("conf_setup_pjp/savetoxlsx/"+uiSelectSalesman.val());
            }
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select MEDREP",
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