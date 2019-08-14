(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Dashboard");
    // ui components
    let uiTblSaran = $("#tbl-saran tbody");
    let uiLabelRiset = $("#labelriset");
    let uiChartRiset = $("#chartriset");
    let uiSelectEvent = $("#satker");
    let uiSelectRDG = $("#riset");
    let uiLabelResTotal = $("#res-total");
    let uiLabelResCurrent = $("#res-total-current");
    let uiChart = $("#chart");
    let session = common.getCookie("session");
    var eventSelected;
    var intervalEvent;
    var intervalRDG;

    initialize();

    function initialize() {
        uiLabelRiset.hide();
        uiChartRiset.hide();
        uiSelectEvent.select2({
            placeholder: "Select Event",
        });
        uiSelectRDG.select2({
            placeholder: "Select Aspek(RDG)",
        });
        if (session.role_id == 1) {
            common.loading();
            let resolver = new HttpResolver();
            let param = new Filter();
            $.when(
                $.post(common.baseURL("api_v1/load_all_event"), param.build()),
            ).done(function (data, textStatus, jqXHR) {

            }).then(function (res) {
                common.loadingClose();
                uiSelectEvent.select2({
                    placeholder: "Select Event",
                    //allowClear: true,
                    data: $.map(res.result, function (o) {
                        o.id = o.id_event; // replace name with the property used for the text
                        o.text = o.event + " (" + o.end_periode + ")"; // replace name with the property used for the text
                        return o;
                    }),
                });
                uiSelectEvent.val(null).trigger('change')
            }).fail(resolver.fail);
        }
        uiSelectEvent.on('select2:select', function (e) {
            eventSelected = e.params.data;
            // buildChartEvent(eventSelected);
            loadRDG(eventSelected);
        });
        uiSelectRDG.on('select2:select', function (e) {
            common.loading();
            buildChartRDG(eventSelected, e.params.data)
        });
    }

    function loadRDG(data) {
        common.loading();
        $.post(common.baseURL("api_v1/load_aspek"), {id_event: data.id_event}, function (res) {
            uiSelectRDG.empty();
            uiSelectRDG.select2({
                placeholder: "Select Aspek RDG",
                data: $.map(res.result, function (o) {
                    o.id = o.id_rdg; // replace name with the property used for the text
                    o.text = o.nama_rdg;
                    return o;
                }),
            });
            uiSelectRDG.val(null).trigger('change');
            common.loadingClose();
        });
    }

    function buildChartEvent(param) {
        common.loading();
        let resolver = new HttpResolver();
        $.when(
            $.post(common.baseURL('api_v1/grafik_per_event'), {id_event: param.id_event}),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (res) {
            if (res.code === 200) {
                initChartEvent(param, res.result);
            }
        }).fail(resolver.fail);
    }

    function buildChartRDG(event, param) {
        common.loading();
        /*
        common.post(common.baseURL('api_v1/get_data_saran_per_materi'), {
            id_rdg: param.id_rdg,
            id_event: event.id_event
        }, function (res1, text) {
            if (res1.code === 200) {
                uiTblSaran.html("");
                $.each(res1.result, function (idx, v){
                    appendRowTable(v);
                });
                console.log(res1.result);
            }
        });
        */
        let resolver = new HttpResolver();
        $.when(
            $.post(common.baseURL('api_v1/grafik_per_materi'), {id_rdg: param.id_rdg, id_event: event.id_event}),
            $.post(common.baseURL('api_v1/get_data_saran_per_materi'), {id_rdg: param.id_rdg, id_event: event.id_event}),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (resMatery, resSaran) {
            if (resMatery[0].code === 200) {
                initChartRDG(event, param, resMatery[0].result.chart);
                initResponden(resMatery[0].result.responden);
            }
            if (resSaran[0].code === 200) {
                uiTblSaran.html("");
                $.each(resSaran[0].result, function (idx, v){
                    appendRowTable(v);
                });
            }
            common.loadingClose();
        }).fail(resolver.fail);
    }

    function initResponden(responden) {
        if (undefined != responden && responden.length > 0) {
            var resTotal = responden[0];
            uiLabelResTotal.text("(" + resTotal.total + ")");
            uiLabelResCurrent.text("(" + resTotal.responden + ")");
        }
    }

    // commons charts
    function randomColor() {
        return 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
    }

    // commons charts
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

    // commons charts
    function replaceMaxLength(text, max) {
        var name = text;
        if (name.length > max) {
            return name.substring(0, max) + " ...";
        }
        return text;
    }

    isArray = Array.isArray ?
        function (obj) {
            return Array.isArray(obj);
        } :
        function (obj) {
            return Object.prototype.toString.call(obj) === '[object Array]';
        };

    var chartV1;

    function initChartEvent(param, result) {
        var satker = '';
        var labels = [];
        var titles = '';
        var datas = [];
        var colors = [];
        var colorsBackground = [];
        var dataset = [];
        var lengthOfArray = result.length;

        $.each(result, function (i, v) {
            //labels.push(v.nama_rdg + " - " + v.kode_satker);
            labels.push("");
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
                display: true,
                fullWidth: true,
                position: 'left',
                labels: {
                    useLineStyle: true,
                    /*generateLabels:  function (chart) {
                        chart.legend.afterFit = function () {
                            var width = this.width;
                            this.lineWidths = this.lineWidths.map(() => width - 12);
                            this.options.labels.padding = 30;
                            this.options.labels.boxWidth = 15;
                        };
                        var data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map((label, i) => {
                                var meta = chart.getDatasetMeta(i);
                                var ds = data.datasets[i];
                                var arc = meta.data[i];
                                var custom = arc && arc.custom || {};
                                var getValueAtIndexOrDefault = function(value, index, defaultValue){
                                        if (value === undefined || value === null) {
                                            return defaultValue;
                                        }
                                        if (this.isArray(value)) {
                                            return index < value.length ? value[index] : defaultValue;
                                        }
                                        return value;
                                    };

                                var arcOpts = chart.options.elements.arc;
                                var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
                                var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
                                var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);
                                return {
                                    text: label,
                                    fillStyle: fill,
                                    strokeStyle: stroke,
                                    lineWidth: bw,
                                    hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                                    index: i
                                };
                            });
                        }
                        return [];
                    }*/
                },
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

        if (chartV1 !== undefined) {
            chartV1.destroy();
        }
        chartV1 = new Chart('chart-1', {
            type: 'bar',
            data: data,
            options: option
        });
        if (intervalEvent != undefined) {
            window.clearInterval(intervalEvent);
        }
        intervalEvent = window.setInterval(function () {
            common.post(common.baseURL('api_v1/grafik_per_event'), {id_event: param.id_event}, function (res1, text) {
                if (res1.code === 200) {
                    if (parseInt(lengthOfArray) !== parseInt(res1.result.length)) {
                        location.reload(true);
                    }
                    $.each(res1.result, function (i, v) {
                        data.datasets[i].data = generateValue(i, lengthOfArray, v.value_avg);

                    });
                    chartV1.update();
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

    var chartV2;

    function initChartRDG(event, param, result) {
        var satker = '';
        var labels = [];
        var titles = '';
        var datas = [];
        var colors = [];
        var colorsBackground = [];
        var dataset = [];
        var lengthOfArray = result.length;
        $.each(result, function (i, v) {
            //labels.push(replaceMaxLength(v.aspek, 10));
            labels.push("");
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

        var option = {
            legend: {
                position: 'right',
                labels: {
                    useLineStyle: true,
                    /*
                    generateLabels:  function (chart) {
                        chart.legend.afterFit = function () {
                            var width = this.width;
                            this.lineWidths = this.lineWidths.map(() => width + 12);
                            this.options.labels.padding = 30;
                            this.options.labels.boxWidth = 15;
                        };
                        var data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map((label, i) => {
                                var meta = chart.getDatasetMeta(i);
                                var ds = data.datasets[i];
                                var arc = meta.data[i];
                                var custom = arc && arc.custom || {};
                                var getValueAtIndexOrDefault = function(value, index, defaultValue){
                                    if (value === undefined || value === null) {
                                        return defaultValue;
                                    }

                                    if (this.isArray(value)) {
                                        return index < value.length ? value[index] : defaultValue;
                                    }

                                    return value;
                                };
                                var arcOpts = chart.options.elements.arc;
                                var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
                                var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
                                var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);
                                return {
                                    text: label,
                                    fillStyle: fill,
                                    strokeStyle: stroke,
                                    lineWidth: bw,
                                    hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                                    index: i
                                };
                            });
                        }
                        return [];
                    }
                    */
                },
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

        if (chartV2 !== undefined) {
            chartV2.destroy();
        }
        chartV2 = new Chart('chart-2', {
            type: 'bar',
            data: data,
            options: option
        });
        if (intervalRDG != undefined) {
            window.clearInterval(intervalRDG);
        }
        intervalRDG = window.setInterval(function () {
            common.post(common.baseURL('api_v1/grafik_per_materi'), {
                id_rdg: param.id_rdg,
                id_event: event.id_event
            }, function (res1, text) {
                if (res1.code === 200) {
                    if (parseInt(lengthOfArray) !== parseInt(res1.result.chart.length)) {
                        location.reload(true);
                    }
                    $.each(res1.result.chart, function (idx, v) {
                        data.datasets[idx].data = generateValue(idx, lengthOfArray, v.value_avg);
                    });
                    chartV2.update();
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
            common.post(common.baseURL('api_v1/get_data_saran_per_materi'), {
                id_rdg: param.id_rdg,
                id_event: event.id_event
            }, function (res1, text) {
                if (res1.code === 200) {
                    uiTblSaran.html("");
                    $.each(res1.result, function (idx, v){
                        appendRowTable(v);
                    });
                }
            });

        }, 6000);
    }

    function appendRowTable(value) {
        var tmp = "<tr><td>?1</td><td>?2</td><td>?3</td><td>?4</td><td>?5</td></tr>";
        tmp = tmp.replace("?1", value.indexno);
        tmp = tmp.replace("?2", value.responden);
        tmp = tmp.replace("?3", value.satker);
        tmp = tmp.replace("?4", value.tanggal);
        tmp = tmp.replace("?5", value.saran);
        uiTblSaran.append(tmp);
    }


})();
