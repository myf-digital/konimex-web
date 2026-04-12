(function () {

    const common = new Common();
    common.setTitle("Config");
    // declare dom
    let uiForm = $("#fm-config");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.config.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_config/create") : common.baseURL("ref_config/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_config",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'Id', value: param.Id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_config");
        });
    }

})();