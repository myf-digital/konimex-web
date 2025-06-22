(function () {

    const common = new Common();
    common.setTitle("Upload Setup Pjp");
    // declare dom
    let uiForm = $("#fm-upload-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnUpload = $("#btn-upload-form");
    let uiAlertNotif = $("#alertnotif");
    let uiFile = $("#fileuploadxls");
    let paramsession = common.getCookie("session");

    uiBtnUpload.click(function () {
        if (uiFile.val()===''){
            uiAlertNotif.show();
        }else{
            uiAlertNotif.hide();
        }
    });

    let paramsession = common.getCookie("session");

    initialize();

    function initialize() {
        alert (paramsession.username);
        let url = common.baseURL("conf_setup_pjp/upload");
        uiForm.initForm({
            url: url,
            directUrl: "conf_setup_pjp",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

    }

})();