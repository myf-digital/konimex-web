(function () {

    const common = new Common();
    common.setTitle("From Generator");
    // declare dom
    let uiForm = $("#fm-from-generator");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.from.generator.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("smpl_from_generator/create") : common.baseURL("smpl_from_generator/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "smpl_from_generator",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("smpl_from_generator");
        });
    }

})();