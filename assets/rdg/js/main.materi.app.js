showLoading();

const common = new Common();

$(document).ready(function () {

    let param = common.getCookie("review.list");

    if (param !== undefined) {
        var tmp = "";
        $.each(param, function (i, v) {
            console.log(i);
            console.log(v);
            tmp += templateReview(v);
        });
        $('#review-list').html(tmp);
    } else {
        common.direct("rdg")
    }

    function templateReview(value) {
        var tmp = '<li>' +
            '   <span> ?1 / ?2</span>' +
            '   <a data-rdg="?3" data-nip="?4" href="javascript:void(0)" onclick="review(this)" class="btn-start">Evaluasi</a>' +
            '</li>';
        tmp = tmp.replace("?1", value.nama_rdg);
        tmp = tmp.replace("?2", value.satker);
        tmp = tmp.replace("?3", value.id_rdg);
        tmp = tmp.replace("?4", value.nip);
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
            common.setCookie("review.nip", {nip: nip, id_rdg: idRgd});
            common.setCookie("review.list.aspek", aspek);
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