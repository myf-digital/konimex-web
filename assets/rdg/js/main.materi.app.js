showLoading();

const common = new Common();

$(document).ready(function () {

    let userTmp = localStorage.getItem("user.login");

    if (userTmp !== null) {
        let user = JSON.parse(userTmp);
        console.log("request materi : " + user.nip);
        common.post(common.baseURL('api_v1/materi'), {nip: user.nip}, function (res) {
            if (200 === res.code && res.result.length > 0) {
                sessionStorage.setItem("review.list", res.result);
                let param = res.result;
                var tmp = "";
                $.each(param, function (i, v) {
                    tmp += templateReview(v);
                });
                $('#review-list').html(tmp);
            } else if (200 === res.code && res.result.length === 0) {
                // no action
            } else {
                swal.fire({
                    title: 'muat ulang halaman ini?',
                    text: "Terjadi kesalahan",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Confirm'
                }).then((result) => {
                    if (result.value) {
                        location.reload();
                    }
                })

            }
        });
    } else {
        common.direct("rdg")
    }

    function templateReview(value) {
        console.log(value);
        var tmp = '' +
            '<div>' +
            '   <span> ?1 / ?2</span>' +
            '<div>' +
            '   <a data-rdg="?3" data-nip="?4" data-materi="?5 href="javascript:void(0)" onclick="review(this)" class="btn-start">Evaluasi</a>' +
            '</div>' +
            '</li>';
        if (0 < value.review) {
            tmp = '' +
                '<li>' +
                '   <span> ?1 / ?2</span>' +
                '<div style="margin-top: 20px; margin-bottom: 20px;">' +
                '   <a data-rdg="?3" data-nip="?4" data-materi="?5" href="javascript:void(0)" onclick="reviewConfirm(this)" class="btn-sucess">Done</a>' +
                '</div>' +
                '</li>';
        }
        tmp = tmp.replace("?1", value.nama_rdg);
        tmp = tmp.replace("?2", value.satker);
        tmp = tmp.replace("?3", value.id_rdg);
        tmp = tmp.replace("?4", value.nip);
        tmp = tmp.replace("?5", value.nama_rdg);
        tml = '<div class="row">\n' +
            '                            <div class="col-md-6">\n' +
            '                                <div class="form-group row">\n' +
            '                                    <label class="col-sm-4 col-form-label">Product</label>\n' +
            '                                    <div class="col-sm-8">\n' +
            '                                        <select id="product" class="form-control" th:field="*{productId}" th:required="true">\n' +
            '                                            <option value="" selected disabled >Vendor</option>\n' +
            '                                            <option th:each="i : ${products}" th:value="${i?.productId}" th:text="${i?.productName}" th:selected="${i?.productId == productId}"></option>\n' +
            '                                        </select>\n' +
            '                                    </div>\n' +
            '                                    <span class="col-sm-3 col-form-label" th:if="${#fields.hasErrors(\'productId\')}"></span>\n' +
            '                                    <span class="col-sm-9 error error-field text-danger" th:if="${#fields.hasErrors(\'productId\')}" th:errors="*{productId}"></span>\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                            <div class="col-md-4">\n' +
            '                                <div class="form-group row">\n' +
            '                                    <label class="col-sm-4 col-form-label">Qty</label>\n' +
            '                                    <div class="col-sm-8">\n' +
            '                                        <input type="text" class="form-control" th:field="*{qty}">\n' +
            '                                    </div>\n' +
            '                                    <span class="col-sm-4 col-form-label" th:if="${#fields.hasErrors(\'qty\')}"></span>\n' +
            '                                    <span class="col-sm-8 error error-field text-danger" th:if="${#fields.hasErrors(\'qty\')}" th:errors="*{qty}"></span>\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '                            <div class="col-md-2">\n' +
            '                                <a class="btn btn-success mr-2" href="javascript:void(0)" onclick="addNew(this)">Add</a>\n' +
            '                            </div>\n' +
            '                        </div>';
        return tmp;
    }

});

function reviewConfirm(rdg) {
    let materi = rdg.getAttribute("data-materi");
    swal.fire({
        title: 'Review ulang Materi?',
        text: materi,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Confirm'
    }).then((result) => {
        if(result.value) {
            review(rdg);
        }
    });
}

function review(rdg) {
    let idRgd = rdg.getAttribute("data-rdg");
    let nip = rdg.getAttribute("data-nip");
    showLoading();
    common.post(common.baseURL('api_v1/aspek'), {nip: nip, idrdg: idRgd}, function (res) {
        hideLoading();
        if (200 === res.code) {
            var aspek = res.result;
            sessionStorage.setItem("review.nip", JSON.stringify({nip: nip, id_rdg: idRgd}));
            sessionStorage.setItem("review.list.aspek", JSON.stringify(aspek));
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

function logout() {
    swal.fire({
        title: 'keluar aplikasi?',
        text: "Terimakasih atas partisipasi anda",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Confirm'
    }).then((result) => {
        if (result.value) {
            swal.fire(
                'Success!',
                'Logout berhasil',
                'success'
            );
            setTimeout(function () {
                sessionStorage.clear();
                localStorage.clear();
                common.direct('rdg');
            }, 1000);
        }
    });
}