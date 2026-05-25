$(function () {
    var common = new Common();
    var paramsession = common.getCookie("session");
    var API_URL = 'https://konimex-api.product-act.com/api_v1/dash_pjp_daily_history';
    var chartInstances = {};

    // stored data for export
    var exportData = { summary: [], area: null, subarea: null };

    var now = new Date();
    var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    var lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

    function padZ(n) { return String(n).padStart(2, '0'); }
    function fmtDate(d) { return d.getFullYear() + '-' + padZ(d.getMonth() + 1) + '-' + padZ(d.getDate()); }

    $('#start_date').val(fmtDate(firstDay));
    $('#end_date').val(fmtDate(lastDay));

    $('#btn_load').on('click', function () {
        $('#row-detail, #row-detail-area, #row-detail-subarea').hide();
        exportData = { summary: [], area: null, subarea: null };
        $('#btn_export').prop('disabled', true);
        loadSummary();
    });

    $('#btn_export').on('click', exportExcel);
    loadSummary();

    function apiCall(extraData) {
        var fd = new FormData();
        fd.append('start_date', $('#start_date').val());
        fd.append('end_date', $('#end_date').val());
        if (extraData) {
            $.each(extraData, function (k, v) { fd.append(k, v); });
        }
        return $.ajax({
            url: API_URL,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-Token': paramsession ? paramsession.token || 'expired' : 'expired' },
        });
    }

    function loadSummary() {
        $('#btn_load').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        apiCall().done(function (res) {
            if (res.code === 200) {
                exportData.summary = res.result;
                $('#btn_export').prop('disabled', false);
                renderTop(res.result);
            }
        }).always(function () {
            $('#btn_load').prop('disabled', false).html('<i class="fa fa-refresh"></i> Load Data');
        });
    }

    function loadRegionDetail(regionalid, nama) {
        $('#title-detail').text(nama);
        $('#col-detail').html('<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $('#row-detail').show();
        $('#row-detail-area, #row-detail-subarea').hide();

        scrollTo('#row-detail');

        apiCall({ regionalid: regionalid }).done(function (res) {
            if (res.code !== 200) return;
            exportData.area = { label: nama, data: res.result };
            exportData.subarea = null;
            renderDetail(res.result, regionalid);
        });
    }

    function loadAreaDetail(regionalid, areaid, nama) {
        $('#title-detail-area').text(nama);
        $('#col-detail-area').html('<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $('#row-detail-area').show();
        $('#row-detail-subarea').hide();

        scrollTo('#row-detail-area');

        apiCall({ regionalid: regionalid, areaid: areaid }).done(function (res) {
            if (res.code !== 200) return;
            exportData.subarea = { label: nama, data: res.result };
            renderAreaDetail(res.result, regionalid, areaid);
        });
    }

    function loadSubareaDetail(regionalid, areaid, subareaid, nama) {
        $('#title-detail-subarea').text(nama);
        $('#col-detail-subarea').html('<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $('#row-detail-subarea').show();

        scrollTo('#row-detail-subarea');

        apiCall({ regionalid: regionalid, areaid: areaid, subareaid: subareaid }).done(function (res) {
            if (res.code !== 200) return;
            exportData.salesman = { label: nama, data: res.result };
            renderSubareaDetail(res.result);
        });
    }

    function scrollTo(selector) {
        $('html, body').animate({ scrollTop: $(selector).offset().top - 20 }, 400);
    }

    var COLORS = {
        plan_outlet: { bg: 'rgba(0, 166, 90, 0.8)', border: '#00a65a' },
        unplan_outlet: { bg: 'rgba(221, 75, 57, 0.8)', border: '#dd4b39' },
        plan_user: { bg: 'rgba(0, 115, 183, 0.8)', border: '#0073b7' },
        unplan_user: { bg: 'rgba(243, 156, 18, 0.8)', border: '#f39c12' },
    };

    function makeDatasets(row) {
        return [
            { label: 'Plan Outlet', data: [row.plan_outlet], backgroundColor: COLORS.plan_outlet.bg, borderColor: COLORS.plan_outlet.border, borderWidth: 1 },
            { label: 'Unplan Outlet', data: [row.unplan_outlet], backgroundColor: COLORS.unplan_outlet.bg, borderColor: COLORS.unplan_outlet.border, borderWidth: 1 },
            { label: 'Plan User', data: [row.plan_user], backgroundColor: COLORS.plan_user.bg, borderColor: COLORS.plan_user.border, borderWidth: 1 },
            { label: 'Unplan User', data: [row.unplan_user], backgroundColor: COLORS.unplan_user.bg, borderColor: COLORS.unplan_user.border, borderWidth: 1 },
        ];
    }

    function makeTotals(data) {
        var t = { plan_outlet: 0, unplan_outlet: 0, plan_user: 0, unplan_user: 0 };
        data.forEach(function (r) {
            t.plan_outlet += parseInt(r.plan_outlet) || 0;
            t.unplan_outlet += parseInt(r.unplan_outlet) || 0;
            t.plan_user += parseInt(r.plan_user) || 0;
            t.unplan_user += parseInt(r.unplan_user) || 0;
        });
        return t;
    }

    function renderChart(canvasId, label, datasets) {
        if (chartInstances[canvasId]) chartInstances[canvasId].destroy();
        var ctx = document.getElementById(canvasId);
        if (!ctx) return;
        chartInstances[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: { labels: [label], datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    function makeCard(canvasId, title, colClass, clickable) {
        var style = 'padding-left:10px;padding-right:10px;';
        var cardStyle = clickable ? 'cursor:pointer;transition:box-shadow .2s;' : '';
        var cardAttr = clickable ? 'class="dashboard-card clickable-card"' : 'class="dashboard-card"';
        return '<div class="' + (colClass || 'col-md-12') + '" style="' + style + '">' +
            '<div ' + cardAttr + ' style="' + cardStyle + '" data-canvas="' + canvasId + '">' +
            '<h5 style="margin:0 0 12px;font-weight:600;">' + title +
            (clickable ? ' <small style="font-size:11px;color:#aaa;font-weight:normal;"><i class="fa fa-hand-pointer-o"></i> Klik untuk detail</small>' : '') +
            '</h5>' +
            '<div class="chart-container"><canvas id="' + canvasId + '"></canvas></div>' +
            '</div></div>';
    }

    function renderTop(data) {
        var $row = $('#charts-row').empty();

        $row.append(makeCard('chart_nasional', 'Nasional', 'col-md-4', false));
        renderChart('chart_nasional', 'Nasional', makeDatasets(makeTotals(data)));

        data.forEach(function (row) {
            var canvasId = 'chart_summary_' + row.id;
            $row.append(makeCard(canvasId, row.nama, 'col-md-4', true));
            renderChart(canvasId, row.nama, makeDatasets(row));

            // click on card, not chart
            $(document).on('click', '[data-canvas="' + canvasId + '"]', function () {
                loadRegionDetail(row.id, row.nama);
            });
        });
    }

    function renderDetail(data, regionalid) {
        var $col = $('#col-detail').empty();
        data.forEach(function (row) {
            var canvasId = 'chart_detail_' + row.id;
            $col.append(makeCard(canvasId, row.nama, 'col-md-4', true));
            renderChart(canvasId, row.nama, makeDatasets(row));
            $(document).off('click', '[data-canvas="' + canvasId + '"]')
                .on('click', '[data-canvas="' + canvasId + '"]', function () {
                    loadAreaDetail(regionalid, row.id, row.nama);
                });
        });
    }

    function renderAreaDetail(data, regionalid, areaid) {
        var $col = $('#col-detail-area').empty();
        data.forEach(function (row) {
            var canvasId = 'chart_area_' + row.id;
            $col.append(makeCard(canvasId, row.nama, 'col-md-4', true));
            renderChart(canvasId, row.nama, makeDatasets(row));
            $(document).off('click', '[data-canvas="' + canvasId + '"]')
                .on('click', '[data-canvas="' + canvasId + '"]', function () {
                    loadSubareaDetail(regionalid, areaid, row.id, row.nama);
                });
        });
    }

    function exportExcel() {
        var hdr = ['Nama', 'Plan Outlet', 'Unplan Outlet', 'Plan User', 'Unplan User'];
        var hdrSalesman = ['Salesman ID', 'Nama Salesman', 'Tipe Sales', 'Jabatan', 'Plan Outlet', 'Unplan Outlet', 'Plan User', 'Unplan User'];
        var rows = [];

        function toRows(data) {
            return data.map(function (r) {
                return [r.nama, r.plan_outlet, r.unplan_outlet, r.plan_user, r.unplan_user];
            });
        }

        function addSection(title, header, dataRows) {
            if (rows.length) rows.push([]);      // empty separator
            rows.push([title]);                  // section title
            rows.push(header);                   // column header
            dataRows.forEach(function (r) { rows.push(r); });
        }

        if (exportData.summary.length) {
            var totals = makeTotals(exportData.summary);
            var nasional = ['Nasional', totals.plan_outlet, totals.unplan_outlet, totals.plan_user, totals.unplan_user];
            addSection('Summary', hdr, [nasional].concat(toRows(exportData.summary)));
        }
        if (exportData.area) {
            addSection('Region: ' + exportData.area.label || 'Region', hdr, toRows(exportData.area.data));
        }
        if (exportData.subarea) {
            addSection('Area: ' + (exportData.subarea.label || 'Area'), hdr, toRows(exportData.subarea.data));
        }
        if (exportData.salesman) {
            addSection('Subarea: ' + (exportData.salesman.label || 'Subarea'), hdrSalesman,
                exportData.salesman.data.map(function (r) {
                    return [r.salesmanid, r.nama_salesman, r.tipe_sales, r.jabatan,
                    r.plan_outlet, r.unplan_outlet, r.plan_user, r.unplan_user];
                })
            );
        }

        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(rows);
        var period = $('#start_date').val() + ' sd ' + $('#end_date').val();
        XLSX.utils.book_append_sheet(wb, ws, 'PJP Daily');
        XLSX.writeFile(wb, 'PJP_Daily_' + period + '.xlsx');
    }

    function renderSubareaDetail(data) {
        var $col = $('#col-detail-subarea').empty();

        var rows = data.map(function (r, i) {
            var img = r.image_profile
                ? '<img src="' + r.image_profile + '" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">'
                : '<span class="fa fa-user-circle fa-2x text-muted"></span>';
            return '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + img + '</td>' +
                '<td>' + r.nama_salesman + '<br><small class="text-muted">' + r.salesmanid + '</small></td>' +
                '<td>' + r.tipe_sales + '</td>' +
                '<td>' + r.jabatan + '</td>' +
                '<td>' + (r.plan_outlet || 0) + '</td>' +
                '<td>' + (r.unplan_outlet || 0) + '</td>' +
                '<td>' + (r.plan_user || 0) + '</td>' +
                '<td>' + (r.unplan_user || 0) + '</td>' +
                '</tr>';
        }).join('');

        $col.append(
            '<div style="padding-left:10px;padding-right:10px;">' +
            '<div class="dashboard-card">' +
            '<div class="table-responsive">' +
            '<table class="table table-bordered table-striped table-hover" style="margin-bottom:0;">' +
            '<thead><tr>' +
            '<th>#</th><th></th><th>Nama Salesman</th><th>Tipe</th><th>Jabatan</th>' +
            '<th style="background:#00a65a;color:#fff;">Plan Outlet</th>' +
            '<th style="background:#dd4b39;color:#fff;">Unplan Outlet</th>' +
            '<th style="background:#0073b7;color:#fff;">Plan User</th>' +
            '<th style="background:#f39c12;color:#fff;">Unplan User</th>' +
            '</tr></thead>' +
            '<tbody>' + (rows || '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>') + '</tbody>' +
            '</table></div></div></div>'
        );
    }
});
