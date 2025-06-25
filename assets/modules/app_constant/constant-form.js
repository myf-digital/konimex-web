(function () {

    const common = new Common();
    common.setTitle("Constant");
    // declare dom
    let uiForm = $("#fm-constant");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.constant.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_constant/create") : common.baseURL("app_constant/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_constant",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'app_variable_id', value: param.app_variable_id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_constant");
        });
    }

})();