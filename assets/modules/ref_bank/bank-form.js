(function () {

    const common = new Common();
    common.setTitle("Bank");
    // declare dom
    let uiForm = $("#fm-bank");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.bank.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_bank/create") : common.baseURL("ref_bank/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_bank",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'bankid', value: param.bankid});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_bank");
        });
    }

})();