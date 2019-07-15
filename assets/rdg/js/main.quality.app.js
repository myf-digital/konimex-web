showLoading();

const common = new Common();

$(document).ready(function () {

    let event = JSON.parse(localStorage.getItem("user.event"));

    $("#btn-back").on('click', function () {
        backToMateri();
    });

    initChartDashboard();

    function initChartDashboard() {
        common.post(common.baseURL('api_v1/grafik_per_event'), {id_event: event.event_id}, function (res, text) {
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
    }

    function randomColor() {
        var hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
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
        } return text;
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
            labels.push(v.nama_rdg + " - " + v.kode_satker);
            datas.push(v.value_avg);
            titles = v.event;
            satker = v.satker;
            colors.push(randomColor());
            colorsBackground.push(randomColor());
            const color = randomColor();
            dataset.push({
                label: v.nama_rdg + " - " + v.satker,
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

        var option = {
            title: {
                display: false,
                text: titles
            },
            legend: {
                position: 'right',
                onHover: function(event, legendItem) {
                },
                onLeave: function(event, legendItem) {
                },
                onClick: function(event, legendItem) {
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

        var chartBar = new Chart('myChart', {
            type: 'bar',
            data: data,
            options: option
        });

        window.setInterval(function () {
            common.post(common.baseURL('api_v1/grafik_per_event'), {id_event: event.event_id}, function (res1, text) {
            // $.getJSON(common.baseURL("api_v1/grafik_per_event"), function (res1, text) {
                if (res1.code === 200) {
                    if (parseInt(lengthOfArray) !== parseInt(res1.result.length)) {
                        location.reload(true);
                    }
                    $.each(res1.result, function (i, v) {
                        data.datasets[i].data = generateValue(i, lengthOfArray, v.value_avg);

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
        }, 5000);
    }

});

function backToMateri() {
    common.direct("rdg/materi");
}