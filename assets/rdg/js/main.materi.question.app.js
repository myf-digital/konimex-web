showLoading();

const common = new Common();

$(document).ready(function () {

    let param = common.getCookie("review.list.aspek");

    if (param !== undefined) {
        var tmp = "";
        $.each(param, function (i, v) {
            tmp += templateAspek(v);
        });
        $('#aspek-list').html(tmp);

        const options = {
            type: 'post',
            beforeSubmit: validate,
            success: processJson
        };

        $('#form-question').submit(function () {
            $(this).ajaxSubmit(options);
            return false;
        });

    } else {
        common.direct("rdg/materi");
    }

    function templateAspek(value) {
        var tmp = '' +
            '                       <div class="ask-list">\n' +
            '                            <div class="list-group">\n' +
            '                                <div class="nomor">\n' +
            '                                    <span>?1.</span>\n' +
            '                                </div>\n' +
            '                                <div class="pertanyaan">\n' +
            '                                    <span>?2</span>\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                            <div class="pilihan">\n' +
            '                                <label class="checkbox">One\n' +
            '                                    <input type="radio" name="radio-?3" value="1">\n' +
            '                                    <span class="checkmark"></span>\n' +
            '                                </label>\n' +
            '                                <label class="checkbox">Two\n' +
            '                                    <input type="radio" name="radio-?4" value="2">\n' +
            '                                    <span class="checkmark"></span>\n' +
            '                                </label>\n' +
            '                                <label class="checkbox">Three\n' +
            '                                    <input type="radio" name="radio-?5" value="3">\n' +
            '                                    <span class="checkmark"></span>\n' +
            '                                </label>\n' +
            '                            </div>\n' +
            '                        </div>';
        tmp = tmp.replace("?1", value.nourut);
        tmp = tmp.replace("?2", value.aspek);
        tmp = tmp.replace("?3", value.nourut);
        tmp = tmp.replace("?4", value.nourut);
        tmp = tmp.replace("?5", value.nourut);
        return tmp;
    }

});

function review(rdg) {
    let idRgd = rdg.getAttribute("data-rdg");
    let nip = rdg.getAttribute("data-nip");
    showLoading();
    common.post(common.baseURL('api_v1/aspek'), {nip: nip, idrdg: idRgd}, function (res) {
        hideLoading();
        if (200 === res.code) {
            var aspek = res.result;
            let param = common.setCookie("review.list.aspek", aspek);
            common.direct("rdg/question");
        } else {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: responseText.message
            });
        }
    }, false);
}

// pre-submit callback
function validate(formData, jqForm, options) {
    console.log("validate");
    var isvalidForm = true;
    var optionValue = [];
    let param = common.getCookie("review.list.aspek");
    let user = common.getCookie("review.nip");
    $.each(param, function (i, v) {
        if ($("input[name=radio-" + v.nourut + "]:checked").length === 0) {
            hideLoading();
            Swal.fire({
                type: 'warning',
                title: 'Oops... please select',
                text: "" + v.nourut + ". " + v.aspek
            });
            isvalidForm = false;
            return false;
        } else {
            let nilai = $("input[name=radio-" + v.nourut + "]:checked").val();
            optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, nilai: nilai});
        }
    });
    if (isvalidForm) {
        showLoading();
    }
    let saranValue = $("textarea[name=sarandesc]").val();
    let saran = {nip: user.nip, id_rdg: user.id_rdg, saran: saranValue};
    formData.push({name: "options", value: JSON.stringify(optionValue), type: "text", required: false});
    formData.push({name: "saran", value: JSON.stringify(saran), type: "text", required: false});
    return isvalidForm;
}

// post-submit callback
function processJson(responseText, statusText, xhr, $form) {
    const common = new Common();
    hideLoading();
    if ("success" === statusText) {
        if (200 === responseText.code) {
            common.removeCookie("review.list.aspek");
            common.direct("rdg/materi");
        } else {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: responseText.message
            });
            // common.removeCookie("review.list.aspek");
            // common.direct("rdg/materi");
        }
    }

}