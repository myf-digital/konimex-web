(function () {

    const common = new Common();
    common.setTitle("Setup Pjp");
    // declare dom
    let uiForm = $("#fm-add-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectWeeks1 = $("#week1-id");
    let uiSelectWeeks2 = $("#week2-id");
    let uiSelectWeeks3 = $("#week3-id");
    let uiSelectWeeks4 = $("#week4-id");
    let uiSelectSalesman = $("#salesmanid-id");
    let uiSelectOutlet = $("#customerid-id");

    // define from *-content.js
    let param = common.getCookie("module.setup.pjp.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("conf_setup_pjp/create") : common.baseURL("conf_setup_pjp/update");
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

        loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan});
        uiSelectSalesman.on('select2:select', function (e) {
            valselected = e.params.data;
            loadOutlet(valselected);
        });

        uiSelectOutlet.select2({
            placeholder: 'Select Outler',
            //minimumSelectionLength: vmin,
            //maximumSelectionLength: vmaxweeks,
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

        uiSelectWeeks1.select2({
            placeholder: 'Select Hari',
            //minimumSelectionLength: vmin,
            //maximumSelectionLength: vmaxweeks,
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks2.select2({
            placeholder: 'Select Hari',
            //minimumSelectionLength: vmin,
            //maximumSelectionLength: vmaxweeks,
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks3.select2({
            placeholder: 'Select Hari',
            //minimumSelectionLength: vmin,
            //maximumSelectionLength: vmaxweeks,
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks4.select2({
            placeholder: 'Select Hari',
            //minimumSelectionLength: vmin,
            //maximumSelectionLength: vmaxweeks,
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            //$.post(common.baseURL("conf_setup_site/load"), filter.build()),
            //$.post(common.baseURL("api_v1/call_frequency"), filter.build()),
            //$.post(common.baseURL("api_v1/call_weeks"), filter.build()),
            $.post(common.baseURL("api_v1/call_days"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);

    }

    function setupForm(r1) {
        let rows1 = r1.result;

        uiSelectWeeks1.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            //minimumSelectionLength: 1,
            //maximumSelectionLength: 4,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectWeeks2.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            //minimumSelectionLength: 1,
            //maximumSelectionLength: 4,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectWeeks3.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            //minimumSelectionLength: 1,
            //maximumSelectionLength: 4,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectWeeks4.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            //minimumSelectionLength: 1,
            //maximumSelectionLength: 4,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });

            uiSelectWeeks1.val(null).trigger('change');
            uiSelectWeeks2.val(null).trigger('change');
            uiSelectWeeks3.val(null).trigger('change');
            uiSelectWeeks4.val(null).trigger('change');

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select Medrep",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman + " - " + o.tipe_sales;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSalesman.val(param.salesmanid).trigger('change');
            }else{
                uiSelectSalesman.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadOutlet(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_outlet_pjp"), {salesmanid:data.salesmanid}, function (res) {
            uiSelectOutlet.empty();
            uiSelectOutlet.select2({
                placeholder: "Select Outlet",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.customerid; // replace name with the property used for the text
                    o.text = o.kode_outlet + " - " +o.outlet + " - " + o.account + " - " + o.dc;
                    return o;
                }),
            });
            
            common.loadingClose();
        });
    }

})();