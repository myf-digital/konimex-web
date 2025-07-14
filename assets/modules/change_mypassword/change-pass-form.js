(function () {

    const common = new Common();
    common.setTitle("Ganti Password");
    // declare dom
    let uiForm = $("#fm-ganti-password");

    initialize();

    function initialize() {
        let url = common.baseURL("change_mypassword/update");
        uiForm.initForm({
            url: url,
            beforeSubmit: function (form, options) {
                return true; // MANDATORY!
            },
            afterSuccess: function (response) {
                let result = response.result;
                if (result.status == 'ok') {
                    $.alert({
                        title: 'Sukses ',
                        content: result.message,
                        containerFluid: true
                    });
                } else {
                    $.alert({
                        title: 'Error ',
                        content: result.message,
                        containerFluid: true
                    });
                }
            },
            rules: {
                old_password: {
                    required: true
                },
                new_password: {
                    required: true
                },
                confir_password: {
                    required: true
                }
            }
        });

    }
})();