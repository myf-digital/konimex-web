const common = new Common();

let userTmp = localStorage.getItem("user.login");

if (userTmp !== null) {
    common.direct("rdg/materi");
}

$(document).ready(function () {
    const options = {
        type: 'post',
        beforeSubmit: showRequest,
        success: processJson
    };
    //
    $('#login-form').submit(function () {
        $(this).ajaxSubmit(options);
        return false;
    });

});

// pre-submit callback
function showRequest(formData, jqForm, options) {
    showLoading();
    return true;
}

// post-submit callback
function processJson(responseText, statusText, xhr, $form) {
    hideLoading();
    console.log("OK");
    if ("success" === statusText) {
        if (200 === responseText.code && responseText.result) {
            localStorage.setItem("user.login", JSON.stringify(responseText.result));
            common.direct("rdg/materi");
        } else {
            Swal.fire({
                type: 'error',
                title: 'Oops... Invalid username or password',
                text: responseText.message
            });
        }
    }

}