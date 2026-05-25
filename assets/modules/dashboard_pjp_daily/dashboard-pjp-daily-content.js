$(function () {
    var common       = new Common();
    var paramsession = common.getCookie("session");
    var API_URL      = 'https://konimex-api.product-act.com/api_v1/dash_pjp_daily_history';
    var chartInstances = {};

    var now      = new Date();
    var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    var lastDay  = new Date(now.getFullYear(), now.getMonth() + 1, 0);

    function padZ(n) { return String(n).padStart(2, '0'); }
    function fmtDate(d) { return d.getFullYear() + '-' + padZ(d.getMonth() + 1) + '-' + padZ(d.getDate()); }

    $('#start_date').val(fmtDate(firstDay));
    $('#end_date').val(fmtDate(lastDay));

    $('#btn_load').on('click', function () {
        $('#row-detail').hide();
        loadSummary();
    });
    loadSummary();

    function apiCall(extraData) {
        var fd = new FormData();
        fd.append('start_date', $('#start_date').val());
        fd.append('end_date',   $('#end_date').val());
        if (extraData) {
            $.each(extraData, function (k, v) { fd.append(k, v); });
        }
        return $.ajax({
            url:         API_URL,
            method:      'POST',
            data:        fd,
            processData: false,
            contentType: false,
            headers:     { 'X-Token': paramsession ? paramsession.token || 'expired' : 'expired' },
        });
    }

    function loadSummary() {
        $('#btn_load').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        apiCall().done(function (res) {
            if (res.code === 200) renderTop(res.result);
        }).always(function () {
            $('#btn_load').prop('disabled', false).html('<i class="fa fa-refresh"></i> Load Data');
        });
    }

    function loadRegionDetail(regionalid, nama) {
        $('#title-detail').text(nama);
        $('#col-detail').html('<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $('#row-detail').show();

        $('html, body').animate({ scrollTop: $('#row-detail').offset().top - 20 }, 400);

        apiCall({ regionalid: regionalid }).done(function (res) {
            if (res.code !== 200) return;
            renderDetail(res.result);
        });
    }

    var COLORS = {
        plan_outlet:   { bg: 'rgba(0, 166, 90, 0.8)',  border: '#00a65a' },
        unplan_outlet: { bg: 'rgba(221, 75, 57, 0.8)', border: '#dd4b39' },
        plan_user:     { bg: 'rgba(0, 115, 183, 0.8)', border: '#0073b7' },
        unplan_user:   { bg: 'rgba(243, 156, 18, 0.8)',border: '#f39c12' },
    };

    function makeDatasets(row) {
        return [
            { label: 'Plan Outlet',   data: [row.plan_outlet],   backgroundColor: COLORS.plan_outlet.bg,   borderColor: COLORS.plan_outlet.border,   borderWidth: 1 },
            { label: 'Unplan Outlet', data: [row.unplan_outlet], backgroundColor: COLORS.unplan_outlet.bg, borderColor: COLORS.unplan_outlet.border, borderWidth: 1 },
            { label: 'Plan User',     data: [row.plan_user],     backgroundColor: COLORS.plan_user.bg,     borderColor: COLORS.plan_user.border,     borderWidth: 1 },
            { label: 'Unplan User',   data: [row.unplan_user],   backgroundColor: COLORS.unplan_user.bg,   borderColor: COLORS.unplan_user.border,   borderWidth: 1 },
        ];
    }

    function makeTotals(data) {
        var t = { plan_outlet: 0, unplan_outlet: 0, plan_user: 0, unplan_user: 0 };
        data.forEach(function (r) {
            t.plan_outlet   += parseInt(r.plan_outlet)   || 0;
            t.unplan_outlet += parseInt(r.unplan_outlet) || 0;
            t.plan_user     += parseInt(r.plan_user)     || 0;
            t.unplan_user   += parseInt(r.unplan_user)   || 0;
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
        var cardAttr  = clickable ? 'class="dashboard-card clickable-card"' : 'class="dashboard-card"';
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

    function renderDetail(data) {
        var $col = $('#col-detail').empty();
        data.forEach(function (row) {
            var canvasId = 'chart_detail_' + row.id;
            $col.append(makeCard(canvasId, row.nama, 'col-md-4', false));
            renderChart(canvasId, row.nama, makeDatasets(row));
        });
    }
});
