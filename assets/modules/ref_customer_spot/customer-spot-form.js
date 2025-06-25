(function () {

    const common = new Common();
    common.setTitle("Customer Spot");
    // declare dom
    let uiForm = $("#fm-customer-spot");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.customer.spot.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_customer_spot/create") : common.baseURL("ref_customer_spot/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_customer_spot",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: '', value: param.});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_customer_spot");
        });
    }

})();