showLoading();

const common = new Common();

$(document).ready(function () {

    const isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;

    let eventTmp = localStorage.getItem("user.event");
    let materiTmp = sessionStorage.getItem("quality.materi");

    console.log(isMobile);

    $("#btn-back").on('click', function () {
        backToMateri();
    });

    if (materiTmp !== null) {
        let materi = JSON.parse(materiTmp);
        let event = JSON.parse(eventTmp);
        common.post(common.baseURL('api_v1/grafik_per_materi'), {
            id_rdg: materi.idrdg,
            id_event: event.event_id
        }, function (res, text) {
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
            // resizeContentToMin();
        });
    } else {
        backToMateri();
    }


    function randomColor() {
        const hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
        return hue;
    }

    function generateValue(index, length, value) {
        var tmp = [];
        for (var i = 0; i < length; i++) {
            if (index === i) {
                tmp.push(value);
            } else {
                tmp.push(0);
            }
        }
        return tmp;
    }

    function replaceMaxLength(text, max) {
        var name = text;
        if (name.length > max) {
            return name.substring(0, max) + " ...";
        }
        return text;
    }

    function initChart(result) {
        var satker = '';
        var labels = [];
        var titles = '';
        var datas = [];
        var colors = [];
        var colorsBackground = [];
        var dataset = [];
        var lengthOfArray = result.length;
        $.each(result, function (i, v) {
            labels.push(replaceMaxLength(v.aspek, 10));
            datas.push(v.value_avg);
            titles = v.nama_rdg;
            satker = v.satker;
            colors.push(randomColor());
            colorsBackground.push(randomColor());
            const color = randomColor();
            dataset.push({
                label: v.aspek,
                backgroundColor: color,
                borderColor: color,
                hoverBackgroundColor: color,
                hoverBorderColor: color,
                borderWidth: 2,
                data: generateValue(i, lengthOfArray, v.value_avg)
            });
        });

        $("#title-satker").text(titles);


        var data = {
            labels: labels,
            datasets: dataset
        };

        var pos = (isMobile ? 'bottom' : 'right');

        var option = {
            legend: {
                position: pos,
                onHover: function (event, legendItem) {

                },
                onLeave: function (event, legendItem) {

                },
                onClick: function (event, legendItem) {

                }
            },
            scales: {
                yAxes: [{
                    stacked: true,
                    gridLines: {
                        display: true,
                        color: "rgba(255,99,132,0.2)"
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        };

        if (isMobile) {
            option.aspectRatio = 1;
            option.responsive = true;
            option.maintainAspectRatio = false;
        }

        const chartBar = new Chart('myChart', {
            type: 'bar',
            data: data,
            options: option
        });


        // let event = JSON.parse(eventTmp);
        window.setInterval(function () {
            // console.log("reload interval");
            let materi1 = JSON.parse(materiTmp);
            let event1 = JSON.parse(eventTmp);
            common.post(common.baseURL('api_v1/grafik_per_materi'), {
                id_rdg: materi1.idrdg,
                id_event: event1.event_id
            }, function (res1, text) {
                if (res1.code === 200) {
                    if (parseInt(lengthOfArray) !== parseInt(res1.result.length)) {
                        location.reload(true);
                    }
                    $.each(res1.result, function (idx, v) {
                        data.datasets[idx].data = generateValue(idx, lengthOfArray, v.value_avg);
                    });
                    chartBar.update();
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
        }, 3000);
    }

});

function backToMateri() {
    sessionStorage.removeItem("quality.materi");
    common.direct("rdg/materi");
}