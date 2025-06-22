(function () {

    const common = new Common();
    common.setTitle("Area Kota");
    // declare dom
    let uiForm = $("#fm-area-kota");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSiteid = $("#siteid-id");
    let uiSelectPropinsi = $("#propinsiid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.kota.update");
    let isUpdate = param !== undefined; // flag create update

    setupFormUI();
    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_kota/create") : common.baseURL("ref_area_kota/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_kota",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'kotaid', value: param.kotaid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                propinsiid: {
                    required: true
                },
                nama_kota: {
                    required: true
                }                
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_kota");
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            //$.post(common.baseURL("conf_setup_site/load"), filter.build()),
            $.post(common.baseURL("ref_area_propinsi/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1) {
            common.loadingClose();
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);
    }


    function setupFormUI() {
        /*uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true
        });*/

        uiSelectPropinsi.select2({
            placeholder: 'Select Propinsi',
            allowClear: true
        });
    }

    function setupForm(r1, r2) {
        let rows = r1.rows;
        //let rowsProp = r2.rows;
        
        /*uiSelectSiteid.select2({
            data: $.map(rows, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });*/
        uiSelectPropinsi.select2({
            data: $.map(rows, function (o) {
                o.id = o.propinsiid; // replace name with the property used for the text
                o.text = o.nama_propinsi; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            //uiSelectSiteid.val(param.siteid).trigger('change');
            uiSelectPropinsi.val(param.propinsiid).trigger('change');
        }

    }
})();