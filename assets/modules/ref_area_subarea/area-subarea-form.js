(function () {

    const common = new Common();
    common.setTitle("Area Subarea");
    // declare dom
    let uiForm = $("#fm-area-subarea");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSite = $("#siteid-id");
    let uiSelectRegional = $("#regionalid-id");
    let uiSelectArea = $("#areaid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.subarea.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_subarea/create") : common.baseURL("ref_area_subarea/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_subarea",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'subareaid', value: param.subareaid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                regionalid: {
                    required: true
                },
                areaid: {
                    required: true
                },
                nama_subarea: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_subarea");
        });

        uiSelectSite.on('select2:select', function (e) {
            siteidSelected = e.params.data;
            loadRegional(siteidSelected);
        });

        uiSelectRegional.on('select2:select', function (e) {
            regionalSelected = e.params.data;
            siteSelected = uiSelectSite.val();
            let srval = regionalSelected;
            srval.push = {siteid:siteSelected};
            loadArea(srval);
        });

        if (isUpdate) {
            loadSiteid(param.siteid);
            let sval = {siteid:param.siteid};

            loadRegional(sval);
            uiSelectRegional.val(param.regionalid).trigger('change');

            let srval = {siteid:param.siteid,regionalid:param.regionalid};
            loadArea(srval);

        }else{
            loadSiteid(null);

            let sval = {siteid:null};
            loadRegional(sval);

            let srval = {siteid:null,regionalid:null};
            loadArea(srval);
        }

    }

    function loadSiteid(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_siteid"), function (res) {
            uiSelectSite.empty();
            uiSelectSite.select2({
                placeholder: "Select SiteId",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.siteid; // replace name with the property used for the text
                    o.text = o.nama_site;
                    return o;
                }),
            });
            uiSelectSite.val(data).trigger('change');
            common.loadingClose();
        });
    }

    function loadRegional(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), {siteid:data.siteid}, function (res) {
            uiSelectRegional.empty();
            uiSelectRegional.select2({
                placeholder: "Select Regional",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.regionalid; // replace name with the property used for the text
                    o.text = o.nama_regional;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectRegional.val(param.regionalid).trigger('change');
            }else{
                uiSelectRegional.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_area"), {siteid:data.siteid, regionalid:data.regionalid}, function (res) {
            uiSelectArea.empty();
            uiSelectArea.select2({
                placeholder: "Select Area",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.areaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectArea.val(param.areaid).trigger('change');
            }else{
                uiSelectArea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

})();