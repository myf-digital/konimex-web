(function () {

    const common = new Common();
    common.setTitle("Setup Pjp");
    // declare dom
    let uiForm = $("#fm-add-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnUpload = $("#btn-upload-form");
    let uiAlertNotif = $("#alertnotif");
    let uiFile = $("#fileuploadxls");

    // define from *-content.js
    let param = common.getCookie("module.setup.pjp.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("conf_setup_pjp/create") : common.baseURL("conf_setup_pjp/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "conf_setup_pjp",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnUpload.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiFile.val()===''){
                uiAlertNotif.show();
            }else{
                uiAlertNotif.hide();
            }
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

    }

})();