(function () {

    const common = new Common();
    common.setTitle("Gl Jurnal Jenis Trans");
    // declare dom
    let uiForm = $("#fm-gl-jurnal-jenis-trans");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.gl.jurnal.jenis.trans.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("config_gl_jurnal_jenis_trans/create") : common.baseURL("config_gl_jurnal_jenis_trans/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "config_gl_jurnal_jenis_trans",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'jenis_trans', value: param.jenis_trans});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("config_gl_jurnal_jenis_trans");
        });
    }

})();