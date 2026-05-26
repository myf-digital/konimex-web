$(function () {
    var common = new Common();
    var paramsession = common.getCookie("session");
    var API_URL = 'https://konimex-api.product-act.com/api_v1/dash_pjp_daily_history';
    var API_URL_REQ = 'https://konimex-api.product-act.com/api_v1/req_pjp_daily_history';
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
        $('#row-detail, #row-detail-area, #row-detail-subarea, #row-detail-salesman').hide();
        exportData = { summary: [], area: null, subarea: null, salesman: null, visits: null };
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
        $('#row-detail-salesman').hide();

        scrollTo('#row-detail-subarea');

        apiCall({ regionalid: regionalid, areaid: areaid, subareaid: subareaid }).done(function (res) {
            if (res.code !== 200) return;
            exportData.salesman = { label: nama, data: res.result };
            renderSubareaDetail(res.result);
        });
    }

    function apiCallReq(salesmanid) {
        var fd = new FormData();
        fd.append('salesmanid', salesmanid);
        return $.ajax({
            url: API_URL_REQ,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-Token': paramsession ? paramsession.token || 'expired' : 'expired' },
        });
    }

    function loadSalesmanDetail(salesmanid, nama) {
        $('#title-detail-salesman').text(nama + ' (' + salesmanid + ')');
        $('#col-detail-salesman').html('<p class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
        $('#row-detail-salesman').show();

        scrollTo('#row-detail-salesman');

        apiCallReq(salesmanid).done(function (res) {
            if (res.code !== 200) return;
            exportData.visits = { label: nama + ' (' + salesmanid + ')', data: res.result };
            renderSalesmanVisits(res.result);
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

        var hdrVisit = ['Periode', 'Customer ID', 'Nama Customer', 'Check In', 'Check Out', 'Keterangan', 'Alasan'];
        function visitRows(visits) {
            return (visits || []).map(function (r) {
                return [r.periode, r.customerid, r.nama_customer, r.check_in, r.check_out,
                    r.keterangan || '', r.alasan || ''];
            });
        }

        if (exportData.visits) {
            var vLabel = exportData.visits.label || 'Salesman';
            var vData = exportData.visits.data;
            addSection('Plan Outlet — ' + vLabel, hdrVisit, visitRows(vData.plan_outlet));
            addSection('Unplan Outlet — ' + vLabel, hdrVisit, visitRows(vData.unplan_outlet));
        }

        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(rows);
        var period = $('#start_date').val() + ' sd ' + $('#end_date').val();
        XLSX.utils.book_append_sheet(wb, ws, 'PJP Daily');
        XLSX.writeFile(wb, 'PJP_Daily_' + period + '.xlsx');
    }

    function renderSubareaDetail(data) {
        var $col = $('#col-detail-subarea').empty();

        function val(v) { return (v === null || v === undefined || v === 'null' || v === '') ? '-' : v; }

        var rows = data.map(function (r, i) {
            return '<tr style="cursor:pointer;" data-salesmanid="' + r.salesmanid + '" data-nama="' + (r.nama_salesman || '') + '">' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + val(r.nama_salesman) + '<br><small class="text-muted">' + val(r.salesmanid) + '</small>' +
                ' <small style="color:#aaa;font-size:10px;"><i class="fa fa-hand-pointer-o"></i></small></td>' +
                '<td>' + val(r.tipe_sales) + '</td>' +
                '<td>' + val(r.jabatan) + '</td>' +
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
            '<table id="tbl-salesman" class="table table-bordered table-striped table-hover" style="margin-bottom:0;">' +
            '<thead><tr>' +
            '<th>#</th><th>Nama Salesman</th><th>Tipe</th><th>Jabatan</th>' +
            '<th style="background:#00a65a;color:#fff;">Plan Outlet</th>' +
            '<th style="background:#dd4b39;color:#fff;">Unplan Outlet</th>' +
            '<th style="background:#0073b7;color:#fff;">Plan User</th>' +
            '<th style="background:#f39c12;color:#fff;">Unplan User</th>' +
            '</tr></thead>' +
            '<tbody>' + (rows || '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>') + '</tbody>' +
            '</table></div></div></div>'
        );

        $col.find('#tbl-salesman tbody tr[data-salesmanid]').on('click', function () {
            var salesmanid = $(this).data('salesmanid');
            var nama = $(this).data('nama');
            loadSalesmanDetail(salesmanid, nama);
        });
    }

    function renderSalesmanVisits(result) {
        var $col = $('#col-detail-salesman').empty();
        var visitStore = {};

        function val(v) { return (v === null || v === undefined || v === 'null' || v === '') ? '-' : v; }
        function fmtDt(v) { return v ? v.replace('T', ' ').substring(0, 16) : '-'; }

        function buildVisitTable(visits, label, headerColor) {
            if (!visits || visits.length === 0) {
                return '<p class="text-muted" style="margin:0;">Tidak ada data ' + label + '</p>';
            }
            var rows = visits.map(function (r, i) {
                var key = label + '_' + i;
                visitStore[key] = r;
                return '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + val(r.periode) + '</td>' +
                    '<td>' + val(r.nama_customer) + '<br><small class="text-muted">' + val(r.customerid) + '</small></td>' +
                    '<td>' + fmtDt(r.check_in) + '</td>' +
                    '<td>' + fmtDt(r.check_out) + '</td>' +
                    '<td>' + val(r.keterangan) + '</td>' +
                    '<td><button class="btn btn-xs btn-info btn-visit-detail" data-key="' + key + '"><i class="fa fa-search"></i> Detail</button></td>' +
                    '</tr>';
            }).join('');
            return '<table class="table table-bordered table-striped" style="margin-bottom:0;">' +
                '<thead><tr>' +
                '<th>#</th>' +
                '<th style="background:' + headerColor + ';color:#fff;">Periode</th>' +
                '<th style="background:' + headerColor + ';color:#fff;">Customer</th>' +
                '<th style="background:' + headerColor + ';color:#fff;">Check In</th>' +
                '<th style="background:' + headerColor + ';color:#fff;">Check Out</th>' +
                '<th style="background:' + headerColor + ';color:#fff;">Keterangan</th>' +
                '<th style="background:' + headerColor + ';color:#fff;">Action</th>' +
                '</tr></thead>' +
                '<tbody>' + rows + '</tbody>' +
                '</table>';
        }

        $col.append(
            '<div style="padding-left:10px;padding-right:10px;">' +
            '<div class="dashboard-card">' +
            '<h5 style="margin:0 0 12px;font-weight:600;color:#00a65a;">Plan Outlet <span class="badge" style="background:#00a65a;">' + ((result.plan_outlet && result.plan_outlet.length) || 0) + '</span></h5>' +
            '<div class="table-responsive" style="margin-bottom:24px;">' + buildVisitTable(result.plan_outlet, 'Plan Outlet', '#00a65a') + '</div>' +
            '<h5 style="margin:0 0 12px;font-weight:600;color:#dd4b39;">Unplan Outlet <span class="badge" style="background:#dd4b39;">' + ((result.unplan_outlet && result.unplan_outlet.length) || 0) + '</span></h5>' +
            '<div class="table-responsive">' + buildVisitTable(result.unplan_outlet, 'Unplan Outlet', '#dd4b39') + '</div>' +
            '</div></div>'
        );

        $col.on('click', '.btn-visit-detail', function () {
            var r = visitStore[$(this).data('key')];
            showVisitModal(r);
        });
    }

    function showVisitModal(r) {
        function val(v) { return (v === null || v === undefined || v === 'null' || v === '') ? '-' : v; }
        function fmtDt(v) { return v ? v.replace('T', ' ').substring(0, 16) : '-'; }

        var infoRows = [
            ['Customer', val(r.nama_customer) + ' <small class="text-muted">(' + val(r.customerid) + ')</small>'],
            ['Periode', val(r.periode)],
            ['Salesman', val(r.nama_salesman) + ' <small class="text-muted">(' + val(r.salesmanid) + ')</small>'],
            ['Check In', fmtDt(r.check_in)],
            ['Check Out', fmtDt(r.check_out)],
            ['Keterangan', val(r.keterangan)],
            ['Alasan', val(r.alasan)],
        ].map(function (row) {
            return '<tr><td style="width:35%;font-weight:600;background:#f9f9f9;">' + row[0] + '</td><td>' + row[1] + '</td></tr>';
        }).join('');

        var detailTable = '';
        if (r.detail_user && r.detail_user.length > 0) {
            var detailRows = r.detail_user.map(function (d, i) {
                return '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + val(d.tipe_pic) + '</td>' +
                    '<td>' + val(d.professional_name) + '</td>' +
                    '<td>' + val(d.array_product) + '</td>' +
                    '<td>' + fmtDt(d.start_detailing) + '</td>' +
                    '<td>' + fmtDt(d.end_detailing) + '</td>' +
                    '<td>' + val(d.reason) + '</td>' +
                    '<td>' + val(d.keterangan) + '</td>' +
                    '</tr>';
            }).join('');
            detailTable =
                '<h5 style="font-weight:600;margin:16px 0 8px;border-left:4px solid #0073b7;padding-left:8px;">Detailing</h5>' +
                '<div class="table-responsive">' +
                '<table class="table table-bordered table-condensed" style="margin-bottom:0;">' +
                '<thead style="background:#0073b7;color:#fff;"><tr>' +
                '<th>#</th><th>Tipe</th><th>Nama Professional</th><th>Produk</th><th>Mulai</th><th>Selesai</th><th>Reason</th><th>Keterangan</th>' +
                '</tr></thead><tbody>' + detailRows + '</tbody></table></div>';
        }

        $('#modal-visit-title').text(val(r.nama_customer) + ' — ' + val(r.periode));
        $('#modal-visit-body').html(
            '<table class="table table-bordered" style="margin-bottom:0;">' +
            '<tbody>' + infoRows + '</tbody></table>' +
            detailTable
        );
        $('#modal-visit-detail').modal('show');
    }
});
