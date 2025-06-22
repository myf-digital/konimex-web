(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    const session = common.getCookie("session");

    common.setTitle("Rekapitulasi Nilai");

    let uiSelectEvent = $("#satker");
    let uiSelectRDG = $("#riset");

    initialize();

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
            loadTableEvent(eventSelected);
            loadRDG(eventSelected);
        });
        uiSelectRDG.on('select2:select', function (e) {
            common.loading();
            loadTableRekap(eventSelected, e.params.data);
            loadTableSaran(eventSelected, e.params.data);
            common.loadingClose();
            // buildChartRDG(eventSelected, e.params.data)
        });
    }

    function loadRDG(data) {
        common.loading();
        $.post(common.baseURL("api_v1/load_aspek"), {id_event: data.id_event}, function (res) {
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

    function loadTableEvent(data) {
        $.post(common.baseURL("api_v1/get_data_rekap_per_event"), {id_event: data.id_event}, function (res) {
            if (res.code === 200) {
                buildTable($("#dg"), "Event", res);
            }
            common.loadingClose();
        });
    }

    function loadTableRekap(event, param) {
        $.post(common.baseURL("api_v1/get_data_rekap_per_rdg"), {
            id_rdg: param.id_rdg,
            id_event: event.id_event
        }, function (res) {
            if (res.code === 200) {
                buildTable($("#dg-2"), "Rekap", res);
            }
            common.loadingClose();
        });
    }

    function loadTableSaran(event, param) {
        $.post(common.baseURL("api_v1/get_data_saran_per_rdg"), {
            id_rdg: param.id_rdg,
            id_event: event.id_event
        }, function (res) {
            if (res.code === 200) {
                buildTable($("#dg-3"), "Saran", res);
            }
            common.loadingClose();
        });
    }

    function buildTable(uiTbl, caption, tableData) {

        var maxWidth = [];
        var header = [];
        var headerChild = [];
        var dataRows = [];

        $.each(tableData.result.rows, function (idx, v) {
            var i = 0;
            var tmp = {};
            for (var prop in v) {
                tmp['item_' + i] = v[prop];
                if (undefined == maxWidth[i]) {
                    maxWidth[i] = widthText(v[prop]);
                } else {
                    var tmpMax = maxWidth[i];
                    var curMax = widthText(v[prop]);
                    if (tmpMax < curMax) {
                        maxWidth[i] = curMax;
                    }
                }
                i++;
            }
            dataRows.push(tmp);
        });

        var rowNum = 0;
        $.each(tableData.result.header, function (i, v) {
            var field = {title: v.title.toUpperCase(), width: widthMax(v.title, maxWidth[i]), halign: 'center'};
            if (v.child.length === 0) {
                field.field = 'item_' + rowNum;
                field.rowspan = 2;
                rowNum++;
            } else {
                $.each(v.child, function (i1, v1) {
                    console.log(i, (i1));
                    headerChild.push({
                        field: 'item_' + rowNum,
                        title: v1.title.toUpperCase(),
                        width: widthMax(v1.title, maxWidth[rowNum]),
                        colspan: 1,
                        halign: 'center',
                        align: 'center'
                    });
                    rowNum++;
                });
                field.colspan = v.child.length;
            }
            if (rowNum > 4) {
                field.align = 'center';
            }
            header.push(field);
        });

        let option = {
            title: caption,
            columns: [header, headerChild],
            data: dataRows,
            onBeforeLoad: function (param) {

            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
            },
            width: 'auto',
            // minHeight: 480,
            singleSelect: true,
            resizable: true,
            collapsible: true,
            fit: false,
            fitColumns: false,
            rownumbers: false,
            pagination: false,
            nowrap: true,
            remoteSort: true,
            remoteFilter: true,
            autoRowHeight: true,
        };
        uiTbl.datagrid(option);
    }

    function widthText(str) {
        if (undefined != str) {
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext("2d");
            ctx.font = "20px Source Sans Pro";
            return ctx.measureText(str.trim()).width;
        } else {
            return 0;
        }

    }

    function widthMax(str, width) {
        var tmp = widthText(str);
        if (tmp < width) {
            return width;
        } else {

            return tmp;
        }
    }

})();
