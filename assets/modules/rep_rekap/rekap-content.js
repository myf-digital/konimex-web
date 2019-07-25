(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    //
    common.setTitle("Rekapitulasi Nilai");
    // ui components
    let uiLabelRiset = $("#labelriset")
	let uiSelectEvent = $("#satker");
    let uiSelectRDG = $("#riset");
    let uiTblEvent = $("#tblevent");
	let uiTblEventDetail = $("#tbldetail");
	
	//start generate header
	var metadata = [{"colIndex": 0, "colType": "nocols", "colName": "No"}, 
					{"colIndex": 1, "colType": "nocols", "colName": "Materi"}, 
					{"colIndex": 2, "colType": "nocols", "colName": "Satker"}, 
					{"colIndex": 3, "colType": "nocols", "colName": "Waktu"}, 
					{"colIndex": 4, "colType": "cols", "colName": "(1) Kualitas Data|Lengkap & Utuh"}, 
					{"colIndex": 5, "colType": "cols", "colName": "(1) Kualitas Data|Terkini"}, 
					{"colIndex": 6, "colType": "cols", "colName": "(1) Kualitas Data|Akurat"}, 
					{"colIndex": 7, "colType": "cols", "colName": "(1) Kualitas Data|Total"}, 
					{"colIndex": 8, "colType": "cols", "colName": "(2) Kualitas Analisis/Asesmen/Kajian/Riset|Komperhensif"}, 
					{"colIndex": 9, "colType": "cols", "colName": "(2) Kualitas Analisis/Asesmen/Kajian/Riset|Kedalaman analisis"}, 
					{"colIndex": 10, "colType": "cols", "colName": "(2) Kualitas Analisis/Asesmen/Kajian/Riset|Metodologi sistematis"}, 
					{"colIndex": 11, "colType": "cols", "colName": "(2) Kualitas Analisis/Asesmen/Kajian/Riset|Mempertimbangkan harmonisasi dgn ketentuan lain"}, 
					{"colIndex": 12, "colType": "cols", "colName": "(2) Kualitas Analisis/Asesmen/Kajian/Riset|Total"}, 
					{"colIndex": 13, "colType": "nocols", "colName": "(3) Ketepatan Proyeksi"}, 
					{"colIndex": 14, "colType": "cols", "colName": "(4) Kualitas Rekomendasi|Memperhatikan Bauran Kebijakan"}, 
					{"colIndex": 15, "colType": "cols", "colName": "(4) Kualitas Rekomendasi|Dapat diimplementasikan"}, 
					{"colIndex": 16, "colType": "cols", "colName": "(4) Kualitas Rekomendasi|Mempertimbangkan Risiko"}, 
					{"colIndex": 17, "colType": "cols", "colName": "(4) Kualitas Rekomendasi|Memperhatikan alignment internal dan eksternal"}, 
					{"colIndex": 18, "colType": "nocols", "colName": "(5) Bahasa Mudah dipahami"}, 
					{"colIndex": 19, "colType": "nocols", "colName": "Rata-rata(1-5)"}, 
					{"colIndex": 20, "colType": "nocols", "colName": "KUALITAS SECARA KESELURUHAN"}
					];

	// First filtering objects which will form the headers
	var rowHeaders = metadata.filter(function(obj) { return obj.colType == "nocols"; });
	var colHeaders = metadata.filter(function(obj) { return obj.colType == "cols"; });
	var colHeadersMain = {};
	metadata.forEach(function(obj) { 
		if (obj.colType == "nocols") { return } // Only for cols colType
		// Check if the key has been stored in the colHeadersMain
		if (obj.colName.split('|')[0] in colHeadersMain) {
			// If yes, then increment the value of the key for object colHeadersMain
			colHeadersMain[obj.colName.split('|')[0]]++;
		} else {
			// If not, reset the value
			colHeadersMain[obj.colName.split('|')[0]] = 1;
		}
	});

	// Variables to hold jQuery elements
	var $table = $("<table>"), $thead = $("<thead>"), 
		$row1 = $("<tr>"), $row2 = $("<tr>");

	rowHeaders.forEach(function(obj, idx) {
		var $th = $("<th rowspan='2'>");
		$th.text(obj.colName);
		$row1.append($th);
	});

	for (var key in colHeadersMain) {
		var $th = $("<th colspan='" + colHeadersMain[key] + "'>");
		$th.text(key);
		$row1.append($th);
	}

	$thead.append($row1); 

	colHeaders.forEach(function(obj, idx) {
		var $th = $("<th>");
		$th.text(obj.colName.split('|')[1]);
		$row2.append($th);    
	});

	$thead.append($row2); 

	// Add to the table
	$table.append($thead);

	//end generate header//
	
    let session = common.getCookie("session");

    var eventSelected;

    initialize();
	uiTblEvent.append($table);

    function initialize() {
        uiSelectEvent.select2({
            placeholder: "Select Event",
        });
        uiSelectRDG.select2({
            placeholder: "Select Aspek(RDB)",
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
            //buildChartEvent(eventSelected);
            loadRDG(eventSelected);
        });
        uiSelectRDG.on('select2:select', function (e) {
            common.loading();
            //buildChartRDG(eventSelected, e.params.data)
        });
    }

    function loadRDG(data) {
        common.loading();
        $.post(common.baseURL("api_v1/load_aspek"), {id_event : data.id_event}, function (res) {
            uiSelectRDG.empty();
            uiSelectRDG.select2({
                placeholder: "Select Riset",
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

	function objectToTable(data) {
		var $table = $("<table>"),
			$tableRows = $("tr", $table);
		
		function recurse(data, depth) {
			if (!data) return 1;
			if (depth >= $tableRows.length) {
				$table.append($("<tr>"));
				$tableRows = $("tr", $table);
			}
			var totalSpan = 0;
			$tableRows.eq(depth).append(data.map(function(column) {
				var colspan = recurse(column.nodes, depth+1);
				// Maybe you want the deepest values to be wrapped in normal TD tags:
				var $th = $(column.nodes ? "<th>" : "<td>").text(column.data);
				if (colspan > 1) $th.attr("colspan", colspan);
				totalSpan += colspan;
				return $th;
			}));
			return totalSpan;
		}
		recurse(data, 0);
		return $table;
	}
	
/*
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
        let resolver = new HttpResolver();
        $.when(
            $.post(common.baseURL('api_v1/grafik_per_materi'), {id_rdg: param.id_rdg, id_event: event.id_event}),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (res) {
            if (res.code === 200) {
                initChartRDG(event, param, res.result);
            }
            common.loadingClose();
        }).fail(resolver.fail);
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
                labels : {
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
                    }
                },
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

        if (chartV1 !== undefined) {
            chartV1.destroy();
        }
        chartV1 = new Chart('chart-1', {
            type: 'bar',
            data: data,
            options: option
        });
        window.setInterval(function () {
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
/*
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

        var option = {
            legend: {
                position: 'right',
                labels : {
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

        window.setInterval(function () {
            common.post(common.baseURL('api_v1/grafik_per_materi'), {id_rdg: param.id_rdg, id_event: event.id_event}, function (res1, text) {
                if (res1.code === 200) {
                    if (parseInt(lengthOfArray) !== parseInt(res1.result.length)) {
                        location.reload(true);
                    }
                    $.each(res1.result, function (idx, v) {
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
        }, 3000);
    }
*/

})();
