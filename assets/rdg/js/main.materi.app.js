showLoading();

const common = new Common();

$(document).ready(function () {

    let userTmp = localStorage.getItem("user.login");

    setInterval(function () {
        let date = new Date();
        let options = {
            weekday: "long", year: "numeric", month: "short",
            day: "numeric", hour: "2-digit", minute: "2-digit"
        };
        $('#event-date').text(date.toLocaleDateString("in-ID", options));
    }, 1000);

    if (userTmp !== null) {
        let user = JSON.parse(userTmp);
        common.post(common.baseURL('api_v1/materi'), {nip: user.nip}, function (res) {
            if (200 === res.code && res.result.length > 0) {
                sessionStorage.setItem("review.list", res.result);
                let param = res.result;
                var tmp = "";
                var titles = "";
                $.each(param, function (i, v) {
                    tmp += templateReview(v);
                    titles = v.event;
                });
                $('#title-event').text(titles);

                $('#review-list').html(tmp);
                resizeContentToMin();
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
                });

            }
        });
    } else {
        common.direct("rdg")
    }

    function templateReview(value) {
        // console.log(value);
        var tmp = '' +
            '   <span> ?1 / ?2</span>' +
            '   <a data-rdg="?3" data-nip="?4" data-materi="?5 href="javascript:void(0)" onclick="review(this)" class="btn-start">Evaluasi</a>' +
            '</li>';
        if (0 < value.review) {
            tmp = '' +
                '<li>' +
                '   <span> ?1 / ?2</span>' +
                '   <a data-rdg="?3" data-nip="?4" data-materi="?5" href="javascript:void(0)" onclick="reviewConfirm(this)" class="btn-sucess">Done</a>' +
                '   <a data-rdg="?6" data-nip="?7" data-materi="?8" href="javascript:void(0)" onclick="showQuality(this)" class="btn-view">View Quality</a>' +
                '</li>';
        }
        tmp = tmp.replace("?1", value.nama_rdg);
        tmp = tmp.replace("?2", value.satker);
        tmp = tmp.replace("?3", value.id_rdg);
        tmp = tmp.replace("?4", value.nip);
        tmp = tmp.replace("?5", value.nama_rdg);
        tmp = tmp.replace("?6", value.id_rdg);
        tmp = tmp.replace("?7", value.nip);
        tmp = tmp.replace("?8", '#');
        tmp = tmp.replace("?5", value.nama_rdg);
        return tmp;
    }

});

function showQualityAll(rdg) {
    common.direct("rdg/quality");
}

function showQuality(rdg) {
    let nip = rdg.getAttribute("data-nip");
    let idRgd = rdg.getAttribute("data-rdg");
    sessionStorage.setItem("quality.materi", JSON.stringify({nip: nip, idrdg: idRgd}));
    common.direct("rdg/quality_materi");
}

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