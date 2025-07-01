(function () {

    const common = new Common();
    common.setTitle("Area Propinsi");
    // declare dom
    let uiForm = $("#fm-area-propinsi");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSiteid = $("#siteid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.propinsi.update");
    let isUpdate = param !== undefined; // flag create update

    //setupFormUI();
    initialize();
    //initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_propinsi/create") : common.baseURL("ref_area_propinsi/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_propinsi",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'propinsiid', value: param.propinsiid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                propinsi: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_propinsi");
        });
    }
})();