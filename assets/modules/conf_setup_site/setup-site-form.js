(function () {

    const common = new Common();
    common.setTitle("Setup Site");
    // declare dom
    let uiForm = $("#fm-setup-site");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.setup.site.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("conf_setup_site/create") : common.baseURL("conf_setup_site/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "conf_setup_site",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("conf_setup_site");
        });
    }

})();