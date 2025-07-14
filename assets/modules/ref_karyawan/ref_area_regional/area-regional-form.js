(function () {

    const common = new Common();
    common.setTitle("Area Regional");
    // declare dom
    let uiForm = $("#fm-area-regional");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSiteid = $("#siteid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.regional.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_regional/create") : common.baseURL("ref_area_regional/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_regional",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'regionalid', value: param.regionalid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                regionalid: {
                    required: true
                }
            }
        });
        
        uiBtnCancel.click(function () {
            common.direct("ref_area_regional");
        });
        

    }

    /*function loadSiteid(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_siteid"), function (res) {
            uiSelectSiteid.empty();
            uiSelectSiteid.select2({
                placeholder: "Select SiteId",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.siteid; // replace name with the property used for the text
                    o.text = o.nama_site;
                    return o;
                }),
            });
            uiSelectSiteid.val(data).trigger('change');
            common.loadingClose();
        });
    }*/

})();