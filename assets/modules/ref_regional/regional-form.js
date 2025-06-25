(function () {

    const common = new Common();
    common.setTitle("Regional");
    // declare dom
    let uiForm = $("#fm-area-regional");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.regional.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update
    let today = new Date();

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
                regional: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_regional");
        });
    }

})();