(function () {

    const common = new Common();
    common.setTitle("Area Kecamatan");
    // declare dom
    let uiForm = $("#fm-area-kecamatan");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSiteid = $("#siteid-id");
    let uiSelectPropinsi = $("#propinsiid-id");
    let uiSelectKota = $("#kotaid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.kecamatan.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();
    //initializeParamKota();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_kecamatan/create") : common.baseURL("ref_area_kecamatan/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_kecamatan",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'kecamatanid', value: param.kecamatanid});
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
                kotaid: {
                    required: true
                },
                nama_kecamatan: {
                    required: true
                }                
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_kecamatan");
        });
        uiSelectPropinsi.on('select2:select', function (e) {
            propinsiSelected = e.params.data;
            // buildChartEvent(eventSelected);
            loadKota(propinsiSelected);
        });

    }


    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            //$.post(common.baseURL("conf_setup_site/load"), filter.build()),
            $.post(common.baseURL("ref_area_propinsi/load"), filter.build())
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
        let rows = r1.rows;
        
        /*uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            data: $.map(rows, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });*/
        uiSelectPropinsi.select2({
            placeholder: 'Select Province',
            data: $.map(rows, function (o) {
                o.id = o.propinsiid; // replace name with the property used for the text
                o.text = o.nama_propinsi; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectKota.select2({
            placeholder: 'Select City',
            allowClear: true
        });

        //uiSelectSiteid.val(null).trigger('change')
        uiSelectPropinsi.val(null).trigger('change')

        if (isUpdate) {
            //uiSelectSiteid.val(param.siteid).trigger('change');
            uiSelectPropinsi.val(param.propinsiid).trigger('change');
            let pval = {propinsiid:param.propinsiid};
            loadKota(pval);
        }
    }

    function loadKota(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_kota"), {propinsiid: data.propinsiid}, function (res) {
            uiSelectKota.empty();
            uiSelectKota.select2({
                placeholder: "Select City",
                data: $.map(res.result, function (o) {
                    o.id = o.kotaid; // replace name with the property used for the text
                    o.text = o.nama_kota;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectKota.val(param.kotaid).trigger('change');
            }else{
                uiSelectKota.val(null).trigger('change');
            }
            common.loadingClose();
        });

    }

})();