$(document).ready(function () {

    const common = new Common();

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
    const common = new Common();
    hideLoading();
    if ("success" === statusText) {
        if (200 === responseText.code) {
            console.log(statusText);
            console.log(responseText);
            if (responseText.result.length > 0) {
                common.setCookie("user.nip", responseText.result[0].nip);
            }
            common.setCookie("review.list", responseText.result);
            common.direct("rdg/materi");
        } else {
            console.log(responseText);
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: responseText.message
            });
        }
    }

}