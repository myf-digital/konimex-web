(function () {

    const common = new Common();
    common.setTitle("Rdg");
    // declare dom
    let uiForm = $("#fm-rdg");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.rdg.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_rdg/create") : common.baseURL("ref_rdg/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_rdg",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_rdg', value: param.id_rdg});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_rdg");
        });
    }

})();