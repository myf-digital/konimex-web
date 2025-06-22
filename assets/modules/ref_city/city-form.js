(function () {

    const common = new Common();
    common.setTitle("City");
    // declare dom
    let uiForm = $("#fm-city");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.city.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_city/create") : common.baseURL("ref_city/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_city",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_city");
        });
    }

})();