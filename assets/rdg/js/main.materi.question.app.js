showLoading();

const common = new Common();

$(document).ready(function () {

    let event = JSON.parse(localStorage.getItem("user.event"));
    let reviewer = sessionStorage.getItem("review.nip");
    let param = sessionStorage.getItem("review.list.aspek");

    if (param !== null) {
        param = JSON.parse(param);
        if (reviewer !== null) {
            reviewer = JSON.parse(reviewer);
            $("#title-rdg").text(reviewer.rdgname);
        }
        var matrixQuestion = [];
        var tmp = "";
        $.each(param, function (i, v) {
            if (1 == v.tipe_soal) {
                tmp += templateAspekType1(v);
            } else if (2 == v.tipe_soal) {
                tmp += templateAspekType2(v);
            } else if (3 == v.tipe_soal) {
                tmp += templateAspekType3(v);
            } else if (4 == v.tipe_soal) {
                matrixQuestion.push(v);
            }
        });
        let tmpTable = templateTable();
        let tmpTableRow = '';
        // generated header
        let maxColumn = 0;
        $.each(matrixQuestion, function (i, v) {
            if (i == 0 && '#' == v.listjawaban && matrixQuestion.length > 0) {
                let answer = matrixQuestion[i + 1].listjawaban.split('|');
                let ansCount = answer.length;
                let tmpHeader = '';
                maxColumn = ansCount;
                tmpTableRow = tmpTableRow + '<tr>';
                for (let x = 0; x <= ansCount; x++) {
                    tmpHeader = tmpHeader + templateTableHeader();
                    if (x == 0) {
                        tmpHeader = tmpHeader.replace("?1", "");
                        tmpTableRow = tmpTableRow + '<th scope="row"></th>';
                    } else {
                        tmpHeader = tmpHeader.replace("?1", 'style="width: 10%"');
                        tmpTableRow = tmpTableRow + templateTableRow(answer[x - 1]);
                    }
                }
                tmpTable = tmpTable.replace("?1", tmpHeader);
                tmpTableRow = tmpTableRow + '</tr>';
            } else if ('#' == v.listjawaban && matrixQuestion.length > 0) { // generated header
                let answer = v.listjawaban.split('|');
                let ansCount = answer.length;
                let tmpHeader = '';
                maxColumn = ansCount;
                tmpTableRow = tmpTableRow + '<tr>';
                for (let x = 0; x <= ansCount; x++) {
                    tmpHeader = tmpHeader + templateTableHeader(x);
                    if (x === 0) {
                        tmpHeader = tmpHeader.replace("?1", "");
                        tmpTableRow = tmpTableRow + '<th scope="row"></th>';
                    } else {
                        tmpHeader = tmpHeader.replace("?1", 'style="width: 10%"');
                        tmpTableRow = tmpTableRow + templateTableRow(answer[x]);
                    }
                }
                tmpTable = tmpTable.replace("?1", tmpHeader);
                tmpTableRow = tmpTableRow + '</tr>';
            }
            return false; // break
        });
        // generated row
        $.each(matrixQuestion, function (i, v) {
            if ('#' == v.listjawaban) {
                tmpTableRow = tmpTableRow + '<tr>';
                tmpTableRow = tmpTableRow + templateTableRowHeadQuestion(v, (maxColumn + 1));
                tmpTableRow = tmpTableRow + '</tr>';
            } else {
                let answer = v.listjawaban.split('|');
                let ansCount = answer.length;
                tmpTableRow = tmpTableRow + '<tr>';
                for (let x = 0; x < ansCount; x++) {
                    if (x == 0) {
                        tmpTableRow = tmpTableRow + templateTableRowQuestion(true, (x + 1), v);
                        tmpTableRow = tmpTableRow + templateTableRowQuestion(false, (x + 1), v);
                    } else {
                        tmpTableRow = tmpTableRow + templateTableRowQuestion(false, (x + 1), v);
                    }
                }
                tmpTableRow = tmpTableRow + '</tr>';
            }

        });
        tmpTable = tmpTable.replace("?2", tmpTableRow);
        $('#aspek-list').html(tmpTable + tmp);

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
        // console.log("type - 1");
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

        var answer = '' +
            '                            <div>\n' +
            '                                <label class="checkbox">\n' +
            '                                    <input type="radio" name="radio-?1" value="?2" ?3>&nbsp;&nbsp; ?4\n' +
                '                                <span class="checkmark"></span>\n' +
            '                                </label>\n' +
            '                            </div>\n';

        tmp = '' +
            '                       <div class="form-group">\n' +
            '                            <label>?1. ?2</label>\n' +
            '                            ?3\n' +
            '                        </div>';

        var answerTemplate = '';
        if (value.listjawaban != undefined) {
            var jawaban = value.listjawaban.split('|');
            $.each(jawaban, function (i, v) {
                var newTmp = answer;
                newTmp = newTmp.replace("?1", value.nourut);
                newTmp = newTmp.replace("?2", v);
                if (v == value.nilai) {
                    newTmp = newTmp.replace("?3", "checked");
                } else {
                    newTmp = newTmp.replace("?3", "");
                }
                newTmp = newTmp.replace("?4", v);
                answerTemplate = answerTemplate + newTmp;
            });
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
        // tmp = tmp.replace("?3", value.nourut);
        tmp = tmp.replace("?3", answerTemplate);
        tmp = tmp.replace("?4", value.nourut);
        tmp = tmp.replace("?5", value.nourut);
        return tmp;
    }

    function templateAspekType2(value) {
        var tmp = '' +
            '                       <div class="form-group">\n' +
            '                            <label>?1. ?2</label>\n' +
            '                                <select class="form-control" name="?3">\n' +
            '                                    <option class="form-control" value="" disabled ?5> -- Pilih Jawaban -- </option>\n' +
            '                                    ?4\n' +
            '                                </select>\n' +
            '                        </div>';


        if (value.listjawaban != undefined) {
            var jawaban = value.listjawaban.split('|');
            var valueOption = "";
            $.each(jawaban, function (i, v) {
                var option = "<option value='" + v + "'>" + v + "</option>";
                if (v == value.nilai) {
                    option = "<option value='" + v + "' selected>" + v + "</option>";
                }
                valueOption = valueOption + option;
            });
            // console.log(valueOption);
            tmp = tmp.replace("?4", valueOption);
        }
        tmp = tmp.replace("?1", value.nourut);
        tmp = tmp.replace("?2", value.aspek);
        tmp = tmp.replace("?3", value.nourut);
        if (undefined == value.nilai) {
            tmp = tmp.replace("?5", "selected");
        } else {
            tmp = tmp.replace("?5", "");
        }
        return tmp;
    }

    function templateAspekType3(value) {
        var tmp = '' +
            '                       <div class="form-group">\n' +
            '                            <label>?1. ?2</label>\n' +
            '                            <input class="form-control" type="text" name="?3" value="?4">\n' +
            '                        </div>';
        tmp = tmp.replace("?1", value.nourut);
        tmp = tmp.replace("?2", value.aspek);
        tmp = tmp.replace("?3", value.nourut);
        if (undefined == value.nilai) {
            tmp = tmp.replace("?4", "");
        } else {
            tmp = tmp.replace("?4", value.nilai);
        }


        return tmp;
    }

    function templateAspekType4(value) {
        var tmp = '';
        if ("#" == value.listjawaban) { // header
            tmp = '' +
                '                       <div class="form-group">\n' +
                '                            <label>?1. ?2</label>\n' +
                '                        </div>';
            tmp = tmp.replace("?1", value.nourut);
            tmp = tmp.replace("?2", value.aspek);
            tmp = tmp.replace("?3", value.nourut);
        } else {
            /*
            tmp = '' +
                '                       <div class="form-group">\n' +
                '                           <label class="col-sm-5 col-md-5 control-label">?1. ?2</label>\n' +
                '                           <div class="col-md-7">\n' +
                '                               <div class="form-inline">\n' +
                '                                   <div class="form-group">\n' +
                '                                       <input class="form-control" type="radio" name="radio-?3" value="?6">&nbsp;&nbsp;?9\n' +
                '                                   </div>\n' +
                '                                   <div class="form-group">\n' +
                '                                       <input class="form-control" type="radio" name="radio-?4" value="?7">&nbsp;&nbsp;?10\n' +
                '                                   </div>\n' +
                '                                   <div class="form-group">\n' +
                '                                       <input class="form-control" type="radio" name="radio-?5" value="?8">&nbsp;&nbsp;?11\n' +
                '                                   </div>\n' +
                '                               </div>\n' +
                '                           </div>\n' +
                '                        </div>';
            tmp = tmp.replace("?1", value.nourut);
            tmp = tmp.replace("?2", value.aspek);
            tmp = tmp.replace("?3", value.nourut);
            */
        }
        return tmp;
    }

    function templateTable() {
        return '<div class="table-responsive"><table class="table table-bordered table-light table-sm table-striped"><thead><tr>?1</tr></thead><tbody>?2</tbody></table></div>'
    }

    function templateTableHeader(index) {
        var tmp = '<th scope="col-sm-1" ?1 ></th>';
        if (index == 0) {
            // tmp = '<th scope="col-sm-5" ?1 ></th>';
        }
        return tmp;
    }

    function templateTableRow(v) {
        var tmp = '' +
            '                                <td align="center" scope="col">\n' +
            '                                    <div align="center" class="d-flex justify-content-center align-self-center">\n' +
            '                                        ?1' +
            '                                    </div>\n' +
            '                                </td>\n';
        var title = v.split('(');
        if (title.length > 1) {
            // tmp = tmp.replace("?1", title[0] + "</br>(" + title[1]);
            tmp = tmp.replace("?1",  "(" + title[1] + "</br>" + title[0]);
        } else {
            tmp = tmp.replace("?1", v);
        }
        // console.log(v);
        return tmp;
    }

    function templateTableRowHeadQuestion(value, colspan) {
        var tmp = '';
        tmp = '<th scope="row" colspan="' + colspan + '">?1</th>\n';
        tmp = tmp.replace('?1', value.aspek);
        return tmp;
    }

    /**
     * radio
     * <label class="checkbox">\n' +
     '      <input type="radio" name="radio-?1" value="?2" ?3>&nbsp;&nbsp; ?4\n' +
     '      <span class="checkmark"></span>\n' +
     '</label>\n' +
     * */
    function templateTableRowQuestion(isHeader, index, value) {
        var tmp = '';
        if (isHeader) {
            tmp = '<td scope="row">?1</td>\n';
            tmp = tmp.replace('?1', value.aspek);
        } else {
            tmp = '' +
                '                                <td>\n' +
                '                                    <div class="form-check d-flex justify-content-center">\n' +
                '                                        <input class="form-check-input position-static" type="radio" name="?1"\n' +
                '                                               value="?2" aria-label="..." ?3>\n' +
                '                                    </div>\n' +
                '                                </td>';

            tmp = tmp.replace('?1', 'radio-' + value.nourut);
            tmp = tmp.replace('?2', index);
            if (index == value.nilai) {
                tmp = tmp.replace('?3', "checked");
            } else {
                tmp = tmp.replace('?3', "");
            }
        }
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
    // console.log("validate");
    var isvalidForm = true;
    var optionValue = [];
    let param = JSON.parse(sessionStorage.getItem("review.list.aspek"));
    let user = JSON.parse(sessionStorage.getItem("review.nip"));
    $.each(param, function (i, v) {
        // console.log("validate : " + v.tipe_soal);
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
            // console.log("validation selection");
            let nilai = $("select[name=" + v.nourut + "] option:selected").val();
            // console.log(nilai);
            if ("" !== nilai) {
                optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, value: nilai});
            } else {
                isvalidForm = false;
                showValidate(v);
                return false;
            }
        } else if (3 == v.tipe_soal) {
            let nilai = $("input[name=" + v.nourut + "]").val();
            // console.log(nilai);
            if ("" !== nilai) {
                optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, value: nilai});
            } else {
                isvalidForm = false;
                showValidate(v);
                return false;
            }
        } else if (4 == v.tipe_soal && v.listjawaban != '#') {
            if ($("input[name=radio-" + v.nourut + "]:checked").length === 0) {
                isvalidForm = false;
                showValidateNoNumber(v);
                return false;
            } else {
                let nilai = $("input[name=radio-" + v.nourut + "]:checked").val();
                optionValue.push({nip: user.nip, id_rdg: v.id_rdg, id_aspek: v.id_aspek, value: nilai});
            }
        }


    });
    if (isvalidForm) {
        showLoading();
    }
    let saranValue = $("textarea[name=sarandesc]").val();
    let event = JSON.parse(localStorage.getItem("user.event"));
    let saran = {nip: user.nip, id_rdg: user.id_rdg, saran: saranValue, id_event: event.event_id};
    formData.push({name: "options", value: JSON.stringify(optionValue), type: "text", required: false});
    formData.push({name: "saran", value: JSON.stringify(saran), type: "text", required: false});
    formData.push({name: "id_event", value: event.event_id, type: "text", required: false});
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

function showValidateNoNumber(v) {
    hideLoading();
    Swal.fire({
        type: 'warning',
        title: 'Oops... please select',
        text: v.aspek
    });
}

// post-submit callback
function processJson(responseText, statusText, xhr, $form) {
    const common = new Common();
    hideLoading();
    if ("success" === statusText) {
        if (200 === responseText.code) {
            sessionStorage.removeItem("review.list.aspek");
            sessionStorage.removeItem("review.nip");
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