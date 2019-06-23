showLoading();

const common = new Common();

$(document).ready(function () {

    let materiTmp = sessionStorage.getItem("quality.materi");

    $("#btn-back").on('click', function () {
        backToMateri();
    });

    if (materiTmp !== null) {
        let materi = JSON.parse(materiTmp);
        console.log(materi.idrdg);
        common.post(common.baseURL('api_v1/grafik_per_materi'), {id_rdg : materi.idrdg}, function (res, text) {
            if (res.code === 200) {
                initChart(res.result);
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
        backToMateri();
    }


    function randomColor() {
        var hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
        return hue;
    }

    function initChart(result) {
        var satker = '';
        var labels = [];
        var titles = '';
        var datas = [];
        var colors = [];
        var colorsBackground = [];
        var yLabels = {
            0: '0',
            1: '1',
            2: '2',
            3: '3',
            4: '4',
            5: '5',
            6: '6',
            7: '7',
            8: '8',
            9: '9',
            10: '10'
        };
        $.each(result, function (i, v) {
            labels.push(v.aspek);
            datas.push(v.value_avg);
            titles = v.event;
            satker = v.satker;
            colors.push(randomColor());
            colorsBackground.push(randomColor());
        });
        $("#title-satker").text(satker);

        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: datas,
                    backgroundColor: colorsBackground,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (value, index, values) {
                                return yLabels[value];
                            }
                        }
                    }]
                },
                title: {
                    display: true,
                    text: titles
                }
            }
        });
    }

});

function backToMateri() {
    sessionStorage.removeItem("quality.materi");
    common.direct("rdg/materi");
}