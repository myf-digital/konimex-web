(function () {

    const common = new Common();
    common.setTitle("Switch FJP");
    // declare dom
    let uiForm = $("#fm-add-switch-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSalesmanFrom = $("#salesmanid-id-from");
    let uiSelectSalesmanTo = $("#salesmanid-id-to");
    //let uiSelectOutlet = $("#customerid-id");

    // define from *-content.js
    let param = common.getCookie("module.setup.pjp.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = common.baseURL("conf_setup_pjp/add_switch");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "conf_setup_pjp",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        loadSalesmanFrom({usersession: paramsession.username, idjabatan: paramsession.idjabatan,restrict_level: paramsession.restrict_level});
        loadSalesmanTo({usersession: paramsession.username, idjabatan: paramsession.idjabatan,restrict_level: paramsession.restrict_level});
        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

    }


    function loadSalesmanFrom(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession,restrict_level: data.restrict_level}, function (res) {
            uiSelectSalesmanFrom.empty();
            uiSelectSalesmanFrom.select2({
                placeholder: "Select Medrep",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman;
                    return o;
                }),
            });
            
            uiSelectSalesmanFrom.val(null).trigger('change');
            common.loadingClose();
        });
    }

    function loadSalesmanTo(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession,restrict_level: data.restrict_level}, function (res) {
            uiSelectSalesmanTo.empty();
            uiSelectSalesmanTo.select2({
                placeholder: "Select Medrep",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman;
                    return o;
                }),
            });
            
            uiSelectSalesmanTo.val(null).trigger('change');
            common.loadingClose();
        });
    }

    /*function loadOutlet(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_outlet_pjp"), {salesmanid:data.salesmanid}, function (res) {
            uiSelectOutlet.empty();
            uiSelectOutlet.select2({
                placeholder: "Select Customer",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.customerid; // replace name with the property used for the text
                    o.text = o.kode_outlet + " - " +o.outlet + " - " + o.account + " - " + o.dc;
                    return o;
                }),
            });
            
            common.loadingClose();
        });
    }*/

})();