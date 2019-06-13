showLoading();

const common = new Common();

$(document).ready(function () {

    let param = sessionStorage.getItem("review.list.aspek");
    console.log(param);
    if (param !== null) {
        param = JSON.parse(param);
        var tmp = "";
        $.each(param, function (i, v) {
            console.log("tipe soal : " + v.tipe_soal);
            if (1 == v.tipe_soal) {
                tmp += templateAspekType1(v);
            } else if (2 == v.tipe_soal) {
                tmp += templateAspekType2(v);
            } else if (3 == v.tipe_soal) {
                tmp += templateAspekType3(v);
            }
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

    function templateAspekType1(value) {
        var tmp = '' +
            '                       <div class="form-group">\n' +
            '                            <label>?1. ?2</label>\n' +
            '                            <div class="radio">\n' +
            '                                <label>\n' +
            '                                    <input type="radio" name="radio-?3" value="?6">&nbsp;&nbsp;?9\n' +
            '                                </label>\n' +
            '                            </div>\n' +
            '                            <div class="radio">\n' +
            '                                <label>\n' +
            '                                    <input type="radio" name="radio-?4" value="?7">&nbsp;&nbsp;?10\n' +
            '                                </label>\n' +
            '                            </div>\n' +
            '                            <div class="radio">\n' +
            '                                <label>\n' +
            '                                    <input type="radio" name="radio-?5" value="?8">&nbsp;&nbsp;?11\n' +
            '                                </label>\n' +
            '                            </div>\n' +
            '                        </div>';


        if (value.listjawaban != undefined) {
            var jawaban = value.listjawaban.split('|');
            tmp = tmp.replace("?9", jawaban[0]);
            tmp = tmp.replace("?10", jawaban[1]);
            tmp = tmp.replace("?11", jawaban[2]);
            tmp = tmp.replace("?6", jawaban[0]);
            tmp = tmp.replace("?7", jawaban[1]);
            tmp = tmp.replace("?8", jawaban[2]);
        } else {
            tmp = tmp.replace("?9", 1);
            tmp = tmp.replace("?10", 2);
            tmp = tmp.replace("?11", 3);
            tmp = tmp.replace("?6", 1);
            tmp = tmp.replace("?7", 2);
            tmp = tmp.replace("?8", 3);
        }

        tmp = tmp.replace("?1", value.nourut);
        tmp = tmp.replace("?2", value.aspek);
        tmp = tmp.replace("?3", value.nourut);
        tmp = tmp.replace("?4", value.nourut);
        tmp = tmp.replace("?5", value.nourut);

        return tmp;
    }

    function templateAspekType2(value) {
        var tmp = '' +
            '                       <div class="form-group">\n' +
            '                            <label>?1. ?2</label>\n' +
            '                                <select class="form-control" name="?3">\n' +
            '                                    <option class="form-control" value="" disabled selected> -- Pilih Jawaban -- </option>\n' +
            '                                    ?4\n' +
            '                                </select>\n' +
            '                        </div>';


        if (value.listjawaban != undefined) {
            var jawaban = value.listjawaban.split('|');
            var valueOption = "";
            $.each(jawaban, function (i, v) {
                var option = "<option value='" + v + "'>" + v + "</option>";
                valueOption = valueOption + option;
            });
            console.log(valueOption);
            tmp = tmp.replace("?4", valueOption);
        }
        tmp = tmp.replace("?1", value.nourut);
        tmp = tmp.replace("?2", value.aspek);
        tmp = tmp.replace("?3", value.nourut);

        return tmp;
    }

    function templateAspekType3(value) {
        var tmp = '' +
            '                       <div class="form-group">\n' +
            '                            <label>?1. ?2</label>\n' +
            '                            <input class="form-control" type="text" name="?3">\n' +
            '                        </div>';
        tmp = tmp.replace("?1", value.nourut);
        tmp = tmp.replace("?2", value.aspek);
        tmp = tmp.replace("?3", value.nourut);

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
    let param = JSON.parse(sessionStorage.getItem("review.list.aspek"));
    let user = JSON.parse(sessionStorage.getItem("review.nip"));
    $.each(param, function (i, v) {
        console.log("validate : " + v.tipe_soal);
        if (1 == v.tipe_soal) {
            if ($("input[name=radio-" + v.nourut + "]:checked").length === 0) {
                isvalidForm = false;
                showValidate(v);
                return false;
            } else {
                let nilai = $("input[name=radio-" + v.nourut + "]:checked").val();
                optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, value: nilai});
            }
        } else if (2 == v.tipe_soal) {
            console.log("validation selection");
            let nilai = $("select[name=" + v.nourut + "] option:selected").val();
            console.log(nilai);
            if ("" !== nilai) {
                optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, value: nilai});
            } else {
                isvalidForm = false;
                showValidate(v);
                return false;
            }

        } else if (3 == v.tipe_soal) {
            let nilai = $("input[name=" + v.nourut + "]").val();
            console.log(nilai);
            if ("" !== nilai) {
                optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, value: nilai});
            } else {
                isvalidForm = false;
                showValidate(v);
                return false;
            }
        }


    });
    if (isvalidForm) {
        showLoading();
    }
    let saranValue = $("textarea[name=sarandesc]").val();
    let saran = {nip: user.nip, id_rdg: user.id_rdg, saran: saranValue};
    formData.push({name: "options", value: JSON.stringify(optionValue), type: "text", required: false});
    formData.push({name: "saran", value: JSON.stringify(saran), type: "text", required: false});
    hideLoading();
    return isvalidForm;
}

function showValidate(v) {
    hideLoading();
    Swal.fire({
        type: 'warning',
        title: 'Oops... please select',
        text: "" + v.nourut + ". " + v.aspek
    });
}

// post-submit callback
function processJson(responseText, statusText, xhr, $form) {
    const common = new Common();
    hideLoading();
    if ("success" === statusText) {
        if (200 === responseText.code) {
            sessionStorage.removeItem("review.list.aspek");
            common.direct("rdg/materi");
        } else {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: responseText.message
            });
        }
    }
}