(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Dashboard");
    // ui components
    let uiLabelRiset = $("#labelriset")
	let uiSelectSatker = $("#satker");
    let uiSelectRiset = $("#riset");
    let uiChart = $("#chart");

    let session = common.getCookie("session");

    initialize();

    function initialize() {
        console.log(session);
        uiSelectSatker.select2({
            placeholder: "Select Satker",
        });
        uiSelectRiset.select2({
            placeholder: "Select Riset",
        });
        if (session.role_id == 1) {
            common.loading();
            let resolver = new HttpResolver();
            let param = new Filter();
            $.when(
                $.post(common.baseURL("ref_satker/load"), param.build()),
            ).done(function (data, textStatus, jqXHR) {

            }).then(function (res1, res2) {
                common.loadingClose();
                uiSelectSatker.select2({
                    placeholder: "Select Satker",
                    //allowClear: true,
                    data: $.map(res1.rows, function (o) {
                        o.id = o.id_satker; // replace name with the property used for the text
                        o.text = o.satker + " (" + o.kode_satker + ")"; // replace name with the property used for the text
                        return o;
                    }),
                });
                uiSelectSatker.val(null).trigger('change')
            }).fail(resolver.fail);
        } else {
            let row = [{
                id_satker: session.id_satker,
                kode_satker: session.kode_satker,
                satker: session.satker
            }];
            uiSelectSatker.select2({
                placeholder: "Select Satker",
                //allowClear: true,
                data: $.map(row, function (o) {
                    o.id = o.id_satker; // replace name with the property used for the text
                    o.text = o.satker + " (" + o.kode_satker + ")"; // replace name with the property used for the text
                    return o;
                }),
            });
            loadDataRiset(row[0]);
        }
        uiSelectSatker.on('select2:select', function (e) {
            var data = e.params.data;
            console.log(data);
            loadDataRiset(data);

        });
        uiSelectRiset.on('select2:select', function (e) {
            common.loading();
            var data = e.params.data;
            initializeChart(data);
        });
    }

    function loadDataRiset(data) {
        common.loading();
        let paramRiset = new Filter();
        paramRiset.add("a.kode_satker", 'contains', data.kode_satker);
        $.post(common.baseURL("ref_riset/load"), paramRiset.build(), function (res) {
            console.log(res);
            uiSelectRiset.empty();
            uiSelectRiset.select2({
                placeholder: "Select Riset",
                data: $.map(res.rows, function (o) {
                    o.id = o.id_riset; // replace name with the property used for the text
                    o.text = o.riset;
                    return o;
                }),
            });
            uiSelectRiset.val(null).trigger('change');
            common.loadingClose();
        });
    }

    function initializeChart(param) {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        filter.add("a.id_riset", "equal", param.id_riset);
        filter.sortOrder("a.id_riset_kegiatan", "asc");
        $.when(
            $.post(common.baseURL("input_riset_data/load_kegiatan"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            //console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            var template = "";
			var totalprog = 0;
            for (value of r1.rows) {
                console.log(value);
                template = template + templateChart(value);
				totalprog = totalprog + Number(value.progress_kegiatan);
			}
            uiChart.html(template);
			var vlabel = '';
				vlabel += '<p class="text-center"><strong>Progress Riset ('+totalprog+'%)</strong></p>';
			uiLabelRiset.html(vlabel);
        }).fail(resolver.fail);
    }

    function templateChart(value) {
        var colorValue = "green";
        if (value.progress < 60) {
            colorValue = "yellow";
        }

        if (value.finish_riset > value.finish_date_kegiatan) {
            colorValue = "red";
        }
        // finish_date_kegiatan: "2019-12-31"
        // finish_riset: "2018-01-01"
        var template = '';
        template += '<div class="progress-group">';
        template += '    <span class="progress-text">' + value.kegiatan + ' ('+ value.progress +'%)</span>';
        template += '    <span class="progress-number"><b>' + value.progress + '</b>/100</span>';
        template += '    <div class="progress sm">';
        template += '        <div class="progress-bar progress-bar-' + colorValue + '" style="width: ' + value.progress + '%"></div>';
        template += '     </div>';
        template += '</div>';
        return template;

    }


})();
