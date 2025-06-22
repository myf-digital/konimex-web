(function () {

    const common = new Common();
    common.setTitle("Restrict Location");
    // declare dom
    let uiForm = $("#fm-restrict-location");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.restrict.location.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_restrict_location/create") : common.baseURL("app_restrict_location/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_restrict_location",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_restrict_location");
        });
    }

})();