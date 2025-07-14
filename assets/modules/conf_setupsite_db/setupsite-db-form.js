(function () {

    const common = new Common();
    common.setTitle("Setupsite Db");
    // declare dom
    let uiForm = $("#fm-setupsite-db");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.setupsite.db.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("conf_setupsite_db/create") : common.baseURL("conf_setupsite_db/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "conf_setupsite_db",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'Id', value: param.Id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("conf_setupsite_db");
        });
    }

})();