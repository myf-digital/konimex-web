(function () {

    const common = new Common();
    common.setTitle("Satker");
    // declare dom
    let uiForm = $("#fm-satker");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.satker.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_satker/create") : common.baseURL("ref_satker/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_satker",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_satker', value: param.id_satker});
                }
                return true; // MANDATORY!
            },
            rules: {
                kode_satker: {
                    required: true
                },
                satker: {
                    required: true
                }
            }
			
        });
        uiBtnCancel.click(function () {
            common.direct("ref_satker");
        });
    }

})();