(function () {

    const common = new Common();
    common.setTitle("Outlet Type");
    // declare dom
    let uiForm = $("#fm-customer-type");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.customer.type.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_customer_type/create") : common.baseURL("ref_customer_type/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_customer_type",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'typeid', value: param.typeid});
                }
                return true; // MANDATORY!
            },
            rules: {
                typeid: {
                    required: true
                },
                nama_type: {
                    required: true
                },
                group1: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_customer_type");
        });
    }

})();