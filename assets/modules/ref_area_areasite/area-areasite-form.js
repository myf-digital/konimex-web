(function () {

    const common = new Common();
    common.setTitle("Area Areasite");
    // declare dom
    let uiForm = $("#fm-area-areasite");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSite = $("#siteid-id");
    let uiSelectReginalid = $("#regionalid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.areasite.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_areasite/create") : common.baseURL("ref_area_areasite/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_areasite",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'areaid', value: param.areaid});
                }
                return true; // MANDATORY!
            },
            rules: {
                regionalid: {
                    required: true
                },
                area: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_areasite");
        });

        if (isUpdate) {
            //loadSiteid(param.siteid);
            let sval = {siteid:"GSK01"};
            loadRegional(sval);
            uiSelectReginalid.val(param.regionalid).trigger('change');
        }else{
            let sval = {siteid:"GSK01"};
            loadRegional(sval);
        }

    }

    function loadRegional(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), {siteid:data.siteid}, function (res) {
            uiSelectReginalid.empty();
            uiSelectReginalid.select2({
                placeholder: "Select Regional",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.regionalid; // replace name with the property used for the text
                    o.text = o.nama_regional;
                    return o;
                }),
            });

            if (isUpdate) {
                uiSelectReginalid.val(param.regionalid).trigger('change');
            }else{
                uiSelectReginalid.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }
    
})();