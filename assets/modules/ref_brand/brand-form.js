(function () {

    const common = new Common();
    common.setTitle("Brand");
    // declare dom
    let uiForm = $("#fm-brand");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.brand.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_brand/create") : common.baseURL("ref_brand/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_brand",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'brandid', value: param.brandid});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_brand");
        });
    }

})();