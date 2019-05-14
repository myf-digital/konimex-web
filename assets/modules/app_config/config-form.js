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
        let url = param === undefined ? common.baseURL("app_config/create") : common.baseURL("app_config/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_config",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'configuration_id', value: param.configuration_id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_config");
        });
    }

})();