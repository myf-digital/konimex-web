(function () {

    const common = new Common();
    common.setTitle("Customer");
    // declare dom
    let uiForm = $("#fm-customer");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.customer.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("m_customer/create") : common.baseURL("m_customer/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "m_customer",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("m_customer");
        });
    }

})();