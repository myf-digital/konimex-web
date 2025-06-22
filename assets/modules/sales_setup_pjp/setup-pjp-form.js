(function () {

    const common = new Common();
    common.setTitle("Setup Pjp");
    // declare dom
    let uiForm = $("#fm-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.setup.pjp.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("sales_setup_pjp/create") : common.baseURL("sales_setup_pjp/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "sales_setup_pjp",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("sales_setup_pjp");
        });
    }

})();