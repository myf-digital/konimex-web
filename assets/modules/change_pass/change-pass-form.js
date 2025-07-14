(function () {

    const common = new Common();
    common.setTitle("Change Password");
    // declare dom
    let uiForm = $("#fm-change-pass");
    //let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectRestrictLevel = $("#restrict_level-id");

    // define from *-content.js
    let param = common.getCookie("module.jabatan.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_jabatan/create") : common.baseURL("ref_jabatan/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_jabatan",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'idjabatan', value: param.idjabatan});
                }
                return true; // MANDATORY!
            },
            rules: {
                jabatan: {
                    required: true
                },
                restrict_level: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("change_pass");
        });

        if (isUpdate) {
            let sval = {restrict_level:param.restrict_level};
            loadParamKey(sval);
            uiSelectRestrictLevel.val(param.restrict_level).trigger('change');
        }else{
            let sval = {restrict_level:null};
            loadParamKey(sval);
        }

    }


})();