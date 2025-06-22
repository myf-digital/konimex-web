(function () {

    const common = new Common();
    common.setTitle("Kendaraan");
    // declare dom
    let uiForm = $("#fm-kendaraan");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSiteid = $("#siteid-id");
    let uiSelectSalesman = $("#salesmanid-id");

    // define from *-content.js
    let param = common.getCookie("module.kendaraan.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_kendaraan/create") : common.baseURL("ref_kendaraan/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_kendaraan",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                salesmanid: {
                    required: true
                },
                kendaraanid: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_kendaraan");
        });
        
        uiSelectSiteid.on('select2:select', function (e) {
            siteidSelected = e.params.data;
            loadSalesman(siteidSelected);
        });

        uiSelectSalesman.select2({
            placeholder: 'Select Salesman',
            allowClear: true
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            let sval = {siteid:param.siteid};
            loadSalesman(sval);
        }        
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1) {
            common.loadingClose();
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);

    }

    function setupForm(r1) {
        let rows1 = r1.rows;

        uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
        }else{
            uiSelectSiteid.val(null).trigger('change');
        }

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {siteid:data.siteid}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select Salesman",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman;
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

})();