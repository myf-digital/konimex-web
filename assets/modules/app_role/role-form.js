(function () {

    const common = new Common();
    common.setTitle("Role");
    // declare dom
    let uiForm = $("#fm-role");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.role.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_role/create") : common.baseURL("app_role/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_role",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({name: 'role_id', value: param.role_id});
                }
                return true; // MANDATORY!
            },
            rules: {
                role_name: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_role");
        });
    }

})();