(function () {

    const common = new Common();
    common.setTitle("Area Propinsi");
    // declare dom
    let uiForm = $("#fm-area-propinsi");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSiteid = $("#siteid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.propinsi.update");
    let isUpdate = param !== undefined; // flag create update

    //setupFormUI();
    initialize();
    //initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_propinsi/create") : common.baseURL("ref_area_propinsi/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_propinsi",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'propinsiid', value: param.propinsiid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                propinsi: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_propinsi");
        });
    }

    /*function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1, r2) {
            common.loadingClose();
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);
    }


    function setupFormUI() {
        uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true
        });
    }

    function setupForm(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        
        uiSelectSiteid.select2({
            data: $.map(rows, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectSiteid.val(param.siteid).trigger('change');

    }*/

})();