(function () {

    const common = new Common();
    common.setTitle("Area Kelurahan");
    // declare dom
    let uiForm = $("#fm-area-kelurahan");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSiteid = $("#siteid-id");
    let uiSelectPropinsi = $("#propinsiid-id");
    let uiSelectKota = $("#kotaid-id");
    let uiSelectKecamatan = $("#kecamatanid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.kelurahan.update");
    let isUpdate = param !== undefined; // flag create update
    // define from *-content.js

    initialize();
    //initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_kelurahan/create") : common.baseURL("ref_area_kelurahan/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_kelurahan",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'kelurahanid', value: param.kelurahanid});
                }
                return true; // MANDATORY!
            },
            rules: {
                propinsiid: {
                    required: true
                },
                kotaid: {
                    required: true
                },
                kecamatanid: {
                    required: true
                },
                nama_kelurahan: {
                    required: true
                }                
            }
        });
        
        uiBtnCancel.click(function () {
            common.direct("ref_area_kelurahan");
        });

        /*uiSelectSiteid.on('select2:select', function (e) {
            siteidSelected = e.params.data;
            // buildChartEvent(eventSelected);
            loadPropinsi(siteidSelected);
        });*/

        uiSelectPropinsi.on('select2:select', function (e) {
            propinsiSelected = e.params.data;
            // buildChartEvent(eventSelected);
            loadKota(propinsiSelected);
        });

        uiSelectKota.on('select2:select', function (e) {
            propinsiSelected = e.params.data;
            kotaSelected = uiSelectPropinsi.val();
            let gval = propinsiSelected;
            gval.push = {kotaid:kotaSelected};
            //gval.push({propinsiid:propinsiSelected,kotaid:kotaSelected});
            // buildChartEvent(eventSelected);
            loadKecamatan(gval);
        });

        uiSelectPropinsi.select2({
            placeholder: 'Select Province',
            allowClear: true
        });

        uiSelectKota.select2({
            placeholder: 'Select City',
            allowClear: true
        });

        uiSelectKecamatan.select2({
            placeholder: 'Select Districts',
            allowClear: true
        });
        
        if (isUpdate) {
            //uiSelectSiteid.val(param.siteid).trigger('change');

            let pval = {siteid:"GSK01"};
            loadPropinsi(pval);
            let kval = {propinsiid:param.propinsiid};
            loadKota(kval);
            let pkval = {propinsiid:param.propinsiid,kotaid:param.kotaid};
            loadKecamatan(pkval);
        }else{
            let pval = {siteid:"GSK01"};
            loadPropinsi(pval);
        }
    }

    function loadPropinsi(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_propinsi"), {siteid: data.siteid}, function (res) {
            uiSelectPropinsi.empty();
            uiSelectPropinsi.select2({
                placeholder: "Select Province",
                data: $.map(res.result, function (o) {
                    o.id = o.propinsiid; // replace name with the property used for the text
                    o.text = o.nama_propinsi;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectPropinsi.val(param.propinsiid).trigger('change');
            }else{
                uiSelectPropinsi.val(null).trigger('change');
            }
                common.loadingClose();
        });

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

    function loadKecamatan(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_kecamatan"), {propinsiid: data.propinsiid, kotaid: data.kotaid}, function (res) {
            uiSelectKecamatan.empty();
            uiSelectKecamatan.select2({
                placeholder: "Select Districts",
                data: $.map(res.result, function (o) {
                    o.id = o.kecamatanid; // replace name with the property used for the text
                    o.text = o.nama_kecamatan;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectKecamatan.val(param.kecamatanid).trigger('change');
            }else{
                uiSelectKecamatan.val(null).trigger('change');
            }
            common.loadingClose();
        });

    }

})();