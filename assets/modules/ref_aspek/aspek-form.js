(function () {

    const common = new Common();
    common.setTitle("Aspek");
    // declare dom
    let uiForm = $("#fm-aspek");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.aspek.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_aspek/create") : common.baseURL("ref_aspek/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_aspek",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_aspek', value: param.id_aspek});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_aspek");
        });
    }

})();