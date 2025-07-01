const common = new Common();

let user = localStorage.getItem("user.login");
let events = localStorage.getItem("user.events");
let event = localStorage.getItem("user.event");

if (events === null) {
    common.direct("rdg/login");
} else if (event !== null) {
    common.direct("rdg/materi");
}

$(document).ready(function () {
    var option = '';
    $.each(JSON.parse(events), function (i, v) {
        option += '<option value="' + v.id_event + '">'+ v.event +'</option>';
    });
    $("#event").html(option);
    $("#nip").val(JSON.parse(user).nip);

    const options = {
        type: 'post',
        beforeSubmit: showRequest,
        success: processJson
    };
    //
    $('#login-form').submit(function () {
        var eventId = $("#event").val();
        if (eventId > 0 ) {
            localStorage.setItem("user.event", JSON.stringify({event_id: eventId}));
            common.direct("rdg/materi");
        }
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
    if ("success" === statusText) {
        if (200 === responseText.code && responseText.result) {
            localStorage.setItem("user.login", JSON.stringify(responseText.result));
            common.direct("rdg/event");
        } else {
            Swal.fire({
                type: 'error',
                title: 'Oops... Invalid username or password',
                text: responseText.message
            });
        }
    }

}