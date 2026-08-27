(function () {

    const common = new Common();
    common.setTitle("Download Outlet");
    // declare dom
    //let uiForm = $("#fm-download-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectClass = $("#classid-id");
    let uiAlertNotif = $("#alertnotif");
    
    // define from *-content.js
    //let param = common.getCookie("module.setup.pjp.update");
    let paramsession = common.getCookie("session");

    initialize();

    function initialize() {

        /*loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan});
        uiSelectSalesman.on('select2:select', function (e) {
            valselected = e.params.data;
        });*/

        loadAccount();
        uiSelectClass.on('select2:select', function (e) {
            valselected = e.params.data;
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            /*if (uiSelectSalesman.val()===null){
                uiAlertNotif.show();
                //alert ('MEDREP (MD/SPG/MEDREP) harus di isi...!');
            }else{*/
                common.direct("ref_customer/savetoxls/"+uiSelectClass.val()+"/"+paramsession.username+"/"+paramsession.idjabatan+"/"+$("#classid-id option:selected").text());
            //}
        });

        uiBtnCancel.click(function () {
            common.direct("ref_customer");
        });

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select TPE",
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

    function loadAccount() {
        common.loading();
        $.post(common.baseURL("ref_customer_class/load"), function (res) {
            uiSelectClass.empty();
            uiSelectClass.select2({
                placeholder: "All Account",
                allowClear: true,
                data: $.map(res.rows, function (o) {
                    o.id = o.classid; // replace name with the property used for the text
                    o.text = o.nama_class;
                    return o;
                }),
            });
            
            uiSelectClass.val(null).trigger('change');
            common.loadingClose();
        });

    }


})();