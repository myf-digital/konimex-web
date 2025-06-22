(function () {

    const common = new Common();
    common.setTitle("Gl Coa");
    // declare dom
    let uiForm = $("#fm-gl-coa");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.gl.coa.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_gl_coa/create") : common.baseURL("ref_gl_coa/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_gl_coa",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'coa_id', value: param.coa_id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_gl_coa");
        });
    }

})();