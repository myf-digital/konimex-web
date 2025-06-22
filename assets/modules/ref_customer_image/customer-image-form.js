(function () {

    const common = new Common();
    common.setTitle("Customer Image");
    // declare dom
    let uiForm = $("#fm-customer-image");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.customer.image.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_customer_image/create") : common.baseURL("ref_customer_image/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_customer_image",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_customer_image");
        });
    }

})();