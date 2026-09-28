let DashboardTTS = (function ($) {
    'use strict';

    const common = new Common();
    const commonGrid = new CommonGrid();

    let chartTrendInstance = null;
    let rawTrendData = null;

    let dtPeriode = $('#periode');
    let dtAbsensi = null;
    let dtCall = null;
    let dtKategori = null;
    let dtUserWajib = null;
    let dtDetailing = null;
    let dtSelling = null;
    let dtJoinVisit = null;
    let dtTpeAktif = null;

    let dtModalKunjungan = null;
    let dtModalDetailing = null;
    let dtModalAbsensi = null;

    let dtLanguageIndo = {
        search: "🔍 Cari Data:",
        lengthMenu: "Tampilkan _MENU_ baris",
        info: "Menampilkan _START_ s/d _END_ dari total _TOTAL_ data",
        infoEmpty: "Menampilkan 0 data",
        infoFiltered: "(difilter dari _MAX_ total data)",
        zeroRecords: "Tidak ada data yang cocok dengan pencarian",
        paginate: {
            first: "Awal",
            last: "Akhir",
            next: "Berikutnya",
            previous: "Sebelumnya"
        }
    };

    function init() {
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                allowClear: true
            });
        }

        dtPeriode
            .datepicker({
            format: "MM yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true,
            language: "id",
            })
            .datepicker("setDate", new Date());

        bindEvents();
        loadSalesman();
        loadAllDashboardData();
    }

    function bindEvents() {
        $('#filter_regionalid').on('change', function () {
            let regionalid = $(this).val();
            $('#filter_areaid').html('<option value="">Semua Area</option>');
            $('#filter_subareaid').html('<option value="">Semua Sub Area</option>');
            $('#filter_salesmanid').html('<option value="">Semua TPE</option>');

            if (regionalid) {
                $.ajax({
                    url: common.baseURL("dashboard_tts/get_area_by_regional"),
                    type: 'POST',
                    dataType: 'json',
                    data: { regionalid: regionalid },
                    success: function (res) {
                        let options = '<option value="">Semua Area</option>';
                        if (res && res.length > 0) {
                            $.each(res, function (i, item) {
                                options += '<option value="' + item.areaid + '">' + item.nama_area + '</option>';
                            });
                        }
                        $('#filter_areaid').html(options).trigger('change.select2');
                    }
                });
            }
        });

        $('#filter_areaid').on('change', function () {
            let areaid = $(this).val();
            $('#filter_subareaid').html('<option value="">Semua Sub Area</option>');
            $('#filter_salesmanid').html('<option value="">Semua TPE</option>');

            if (areaid) {
                $.ajax({
                    url: common.baseURL("dashboard_tts/get_subarea_by_area"),
                    type: 'POST',
                    dataType: 'json',
                    data: { areaid: areaid },
                    success: function (res) {
                        let options = '<option value="">Semua Sub Area</option>';
                        if (res && res.length > 0) {
                            $.each(res, function (i, item) {
                                options += '<option value="' + item.subareaid + '">' + item.nama_subarea + '</option>';
                            });
                        }
                        $('#filter_subareaid').html(options).trigger('change.select2');
                    }
                });
            }
        });

        $('#filter_subareaid').on('change', function () {
            loadSalesman();
        });

        $('#btn_apply_filter').on('click', function () {
            loadAllDashboardData();
        });

        $('#btn_reset_filter').on('click', function () {
            $('#form_filter_dashboard')[0].reset();
            $('#periode').datepicker("setDate", new Date());
            $('#filter_regionalid').val('');
            $('#filter_areaid').html('<option value="">Semua Area</option>');
            $('#filter_subareaid').html('<option value="">Semua Sub Area</option>');
            $('.select2').trigger('change.select2');
            loadSalesman();
            loadAllDashboardData();
        });

        $('#btn_toggle_metrics button').on('click', function () {
            $('#btn_toggle_metrics button').removeClass('active btn-primary').addClass('btn-default');
            $(this).addClass('active btn-primary').removeClass('btn-default');
            let metric = $(this).data('metric');
            renderTrendChart(metric);
        });

        $(document).on('click', '.kpi-card-trigger, .small-box', function () {
            let kpi = $(this).data('kpi') || $(this).find('.kpi-card-trigger').data('kpi');
            if (kpi) {
                switch (kpi) {
                    case 'absensi':
                        $('#tab_link_absensi').tab('show');
                        break;
                    case 'call':
                        $('#tab_link_call').tab('show');
                        break;
                    case 'kategori':
                    case 'kat_a':
                        $('#tab_link_kategori').tab('show');
                        break;
                    case 'user_wajib':
                        $('#tab_link_user_wajib').tab('show');
                        break;
                    case 'detailing_produk':
                        $('#tab_link_detailing').tab('show');
                        break;
                    case 'selling_produk':
                        $('#tab_link_selling').tab('show');
                        break;
                    case 'join_visit':
                    case 'join_activity':
                        $('#tab_link_join_visit').tab('show');
                        break;
                    case 'tpe_aktif':
                    case 'all':
                    case 'tpe':
                        $('#tab_link_tpe_aktif').tab('show');
                        break;
                    default:
                        $('#tab_link_absensi').tab('show');
                        break;
                }

                $('html, body').animate({
                    scrollTop: $('#section_breakdown').offset().top - 20
                }, 400);
            }
        });

        // Adjust DataTables Columns on Tab Switch
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            if ($.fn.dataTable) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            }
        });

        $(document).on('click', '.btn-detail-tpe', function (e) {
            e.stopPropagation();
            let sid = $(this).data('sid');
            let sname = $(this).data('sname');
            openDetailReportModal(sid, sname);
        });

        $(document).on('click', '.btn-detail-join-visit', function (e) {
            e.stopPropagation();
            let reqNo = $(this).data('reqno');
            openDetailJoinVisitModal(reqNo);
        });

        $(document).on('click', '.img-gallery-thumb', function () {
            let src = $(this).attr('src');
            let title = $(this).data('title') || 'Preview Foto';
            $('#preview_img_element').attr('src', src);
            $('#preview_image_title').text(title);
            $('#preview_img_download_link').attr('href', src);
            $('#modal_preview_image').modal('show');
        });
    }

    function getFilterParams() {
        let dateObj = dtPeriode.datepicker('getDate') || new Date();
        let tahun = dateObj.getFullYear();
        let bulan = ('0' + (dateObj.getMonth() + 1)).slice(-2);

        return {
            tahun: tahun,
            bulan: bulan,
            regionalid: $('#filter_regionalid').val(),
            areaid: $('#filter_areaid').val(),
            subareaid: $('#filter_subareaid').val(),
            salesmanid: $('#filter_salesmanid').val()
        };
    }

    function loadAllDashboardData() {
        loadKpiSummary();
        loadMonthlyTrend();
        loadBreakdownAbsensi();
        loadBreakdownCall();
        loadBreakdownKategori();
        loadBreakdownUserWajib();
        loadBreakdownDetailing();
        loadBreakdownSelling();
        loadBreakdownJoinVisit();
        loadBreakdownTpeAktif();
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }

    function formatPctBadge(pct) {
        let cls = 'label-danger';
        if (pct >= 100) cls = 'label-success';
        else if (pct >= 80) cls = 'label-warning';
        return '<span class="label ' + cls + '" style="font-size: 11px;">' + pct + '%</span>';
    }

    function loadSalesman() {
        let params = getFilterParams();
        $.ajax({
            url: common.baseURL("dashboard_tts/get_salesman_by_filter"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                let options = '<option value="">Semua TPE</option>';
                if (res && res.length > 0) {
                    $.each(res, function (i, item) {
                        options += '<option value="' + item.salesmanid + '">' + item.nama_salesman + ' (' + item.salesmanid + ')</option>';
                    });
                }
                $('#filter_salesmanid').html(options).trigger('change.select2');
            }
        });
    }

    function loadKpiSummary() {
        let params = getFilterParams();
        $('#loading_kpi').show();

        $.ajax({
            url: common.baseURL("dashboard_tts/load_kpi"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_kpi').hide();
                if (res && res.kpi) {
                    let kpi = res.kpi;

                    let abs = kpi.absensi;
                    if (abs) {
                        $('#kpi_absensi_pct').text(abs.percentage + '%');
                        $('#kpi_absensi_act').text(formatNumber(abs.actual));
                        $('#kpi_absensi_tgt').text(formatNumber(abs.total));
                        $('#kpi_absensi_hk').text(abs.hari_kerja || 0);

                        let rincianAbs = '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Hadir: <b>' + formatNumber(abs.actual_fisik || abs.actual) + '</b></span></div>' +
                                         '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Izin: <b>' + formatNumber(abs.izin || 0) + '</b></span></div>' +
                                         '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Sakit: <b>' + formatNumber(abs.sakit || 0) + '</b></span></div>' +
                                         '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Cuti: <b>' + formatNumber(abs.cuti || 0) + '</b></span></div>';
                        $('#kpi_absensi_rincian').html(rincianAbs);
                    }

                    let call = kpi.call;
                    if (call) {
                        $('#kpi_call_pct').text(call.percentage_ec + '%');
                        $('#kpi_call_act').text(formatNumber(call.actual));
                        $('#kpi_call_tgt').text(formatNumber(call.planned_call));

                        let rincianCall = '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Call: <b>' + formatNumber(call.actual_call || 0) + '</b></span></div>' +
                                          '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Extra Call: <b>' + formatNumber(call.extra_call || call.actual_ec || 0) + '</b></span></div>';
                        $('#kpi_call_rincian').html(rincianCall);
                    }

                    let kat = kpi.kategori;
                    if (kat) {
                        $('#kpi_kategori_total').text(formatNumber(kat.total_outlet));
                        if (kat.rincian && kat.rincian.length > 0) {
                            let rincianHtml = '';
                            $.each(kat.rincian.slice(0, 6), function (i, item) {
                                rincianHtml += '<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' +
                                    item.typeid + ': <b>' + formatNumber(item.total_outlet) + '</b>' +
                                    '</div>';
                            });
                            $('#kpi_kategori_rincian').html(rincianHtml);
                        } else {
                            $('#kpi_kategori_rincian').html('-');
                        }
                    }

                    let uw = kpi.user_wajib;
                    if (uw) {
                        $('#kpi_user_pct').text(uw.percentage + '%');
                        $('#kpi_user_act').text(formatNumber(uw.actual));
                        $('#kpi_user_tgt').text(formatNumber(uw.target));
                        if (uw.rincian && uw.rincian.length > 0) {
                            let rincianHtml = '';
                            $.each(uw.rincian.slice(0, 4), function (i, item) {
                                rincianHtml += '<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' +
                                    item.nama_spesialisasi + ': <b>' + formatNumber(item.actual) + '/' + formatNumber(item.target) + '</b>' +
                                    '</div>';
                            });
                            $('#kpi_user_rincian').html(rincianHtml);
                        } else {
                            $('#kpi_user_rincian').html('-');
                        }
                    }

                    let det = kpi.detailing_produk;
                    if (det) {
                        $('#kpi_detailing_pct').text(det.percentage + '%');
                        $('#kpi_detailing_act').text(formatNumber(det.actual));
                        $('#kpi_detailing_tgt').text(formatNumber(det.target));
                        if (det.rincian && det.rincian.length > 0) {
                            let rincianHtml = '';
                            $.each(det.rincian.slice(0, 4), function (i, item) {
                                rincianHtml += '<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' +
                                    item.nama_produk + ' ' + item.percentage + '%: <b>' + formatNumber(item.actual) + '/' + formatNumber(item.target) + '</b>' +
                                    '</div>';
                            });
                            $('#kpi_detailing_rincian').html(rincianHtml);
                        } else {
                            $('#kpi_detailing_rincian').html('-');
                        }
                    }

                    let sel = kpi.selling_produk;
                    if (sel) {
                        $('#kpi_selling_pct').text(sel.percentage + '%');
                        $('#kpi_selling_act').text(formatNumber(sel.actual));
                        $('#kpi_selling_tgt').text(formatNumber(sel.target));
                        if (sel.rincian && sel.rincian.length > 0) {
                            let rincianHtml = '';
                            $.each(sel.rincian.slice(0, 4), function (i, item) {
                                rincianHtml += '<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' +
                                    item.nama_produk + ' ' + item.percentage + '%: <b>' + formatNumber(item.actual) + '/' + formatNumber(item.target) + '</b>' +
                                    '</div>';
                            });
                            $('#kpi_selling_rincian').html(rincianHtml);
                        } else {
                            $('#kpi_selling_rincian').html('-');
                        }
                    }

                    let jn = kpi.join_activity;
                    if (jn) {
                        $('#kpi_join_pct').text((jn.percentage || 0) + '%');
                        $('#kpi_join_act').text(formatNumber(jn.actual_approved || jn.approved || jn.actual || 0));
                        $('#kpi_join_tgt').text(formatNumber(jn.target || 0));
                        let rincianJoin = '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Pending: <b>' + formatNumber(jn.pending || 0) + '</b></span></div>' +
                                          '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Approved: <b>' + formatNumber(jn.approved || 0) + '</b></span></div>' +
                                          '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Sudah Detailing: <b>' + formatNumber(jn.sudah_detailing || 0) + '</b></span></div>' +
                                          '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>Reject: <b>' + formatNumber(jn.reject || 0) + '</b></span></div>';
                        $('#kpi_join_rincian').html(rincianJoin);
                    }

                    let tp = kpi.tpe;
                    if (tp) {
                        $('#kpi_tpe_act').text(formatNumber(tp.actual));
                        if (tp.rincian && tp.rincian.length > 0) {
                            let rincianTpe = '';
                            $.each(tp.rincian.slice(0, 4), function (i, item) {
                                rincianTpe += '<div style="display: flex; justify-content: flex-end; gap: 8px;"><span>' + item.tipe_sales + ': <b>' + formatNumber(item.total) + '</b></span></div>';
                            });
                            $('#kpi_tpe_rincian').html(rincianTpe);
                        } else {
                            $('#kpi_tpe_rincian').html('-');
                        }
                    }
                }
            },
            error: function () {
                $('#loading_kpi').hide();
            }
        });
    }

    function loadMonthlyTrend() {
        let params = getFilterParams();
        $('#label_trend_year').text('(' + params.tahun + ')');
        $('#loading_trend').show();

        $.ajax({
            url: common.baseURL("dashboard_tts/load_trend"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_trend').hide();
                if (res && res.series) {
                    rawTrendData = res;
                    let currentMetric = $('#btn_toggle_metrics button.active').data('metric') || 'all';
                    renderTrendChart(currentMetric);
                }
            },
            error: function () {
                $('#loading_trend').hide();
            }
        });
    }

    function renderTrendChart(metricFilter) {
        if (!rawTrendData || !rawTrendData.series) return;

        let ctx = document.getElementById('chart_monthly_trend');
        if (!ctx) return;

        if (chartTrendInstance) {
            chartTrendInstance.destroy();
        }

        let metricConfigs = {
            absensi: { label: 'Kehadiran TPE', color: '#2e7d32', bg: 'rgba(46, 125, 50, 0.12)', unit: 'HK' },
            call: { label: 'Performa TPE', color: '#00acc1', bg: 'rgba(0, 172, 193, 0.12)', unit: 'Call' },
            user_wajib: { label: 'User Wajib', color: '#f39c12', bg: 'rgba(243, 156, 18, 0.12)', unit: 'User' },
            detailing: { label: 'Detailing Produk', color: '#00897b', bg: 'rgba(0, 137, 123, 0.12)', unit: 'Detailing' },
            selling: { label: 'Selling Produk', color: '#e53935', bg: 'rgba(229, 57, 53, 0.12)', unit: 'Qty' }
        };

        if (!metricFilter || metricFilter === 'all') {
            let datasets = [];
            Object.keys(metricConfigs).forEach(function (key) {
                let cfg = metricConfigs[key];
                let sData = rawTrendData.series[key];
                if (sData && sData.pct) {
                    datasets.push({
                        label: cfg.label + ' (%)',
                        data: sData.pct,
                        borderColor: cfg.color,
                        backgroundColor: cfg.bg,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                        metricKey: key
                    });
                }
            });

            chartTrendInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: rawTrendData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: { size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(44, 62, 80, 0.95)',
                            titleFont: { size: 13, weight: '700' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 6,
                            callbacks: {
                                label: function (context) {
                                    let mKey = context.dataset.metricKey;
                                    let idx = context.dataIndex;
                                    let pct = context.parsed.y;
                                    let sData = rawTrendData.series[mKey];
                                    let act = sData && sData.actual ? sData.actual[idx] : 0;
                                    let tgt = sData && sData.target ? sData.target[idx] : 0;
                                    let label = metricConfigs[mKey] ? metricConfigs[mKey].label : (context.dataset.label || '');
                                    return label + ': ' + pct + '% (Realisasi: ' + formatNumber(act) + ' / Target: ' + formatNumber(tgt) + ')';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f2f5' },
                            ticks: {
                                font: { size: 11 },
                                callback: function (val) {
                                    return val + '%';
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        } else {
            let cfg = metricConfigs[metricFilter] || { label: 'Metrik', color: '#3c8dbc', bg: 'rgba(60, 141, 188, 0.2)', unit: '' };
            let sData = rawTrendData.series[metricFilter] || { actual: [], target: [], pct: [] };

            let datasets = [
                {
                    label: 'Target ' + cfg.label,
                    data: sData.target,
                    backgroundColor: 'rgba(243, 156, 18, 0.75)',
                    borderColor: '#e67e22',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Realisasi ' + cfg.label,
                    data: sData.actual,
                    backgroundColor: metricFilter === 'user_wajib' ? '#3c8dbc' : cfg.color,
                    borderColor: metricFilter === 'user_wajib' ? '#367fa9' : cfg.color,
                    borderWidth: 1,
                    borderRadius: 4
                }
            ];

            chartTrendInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: rawTrendData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: { size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(44, 62, 80, 0.95)',
                            titleFont: { size: 13, weight: '700' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 6,
                            callbacks: {
                                label: function (context) {
                                    let idx = context.dataIndex;
                                    let val = context.parsed.y;
                                    let pct = sData.pct && sData.pct[idx] !== undefined ? sData.pct[idx] : 0;
                                    let label = context.dataset.label || '';
                                    if (context.datasetIndex === 1) {
                                        return label + ': ' + formatNumber(val) + ' ' + (cfg.unit || '') + ' (' + pct + '% tercapai)';
                                    }
                                    return label + ': ' + formatNumber(val) + ' ' + (cfg.unit || '');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f2f5' },
                            ticks: {
                                font: { size: 11 },
                                callback: function (val) {
                                    return formatNumber(val);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }
    }

    function loadBreakdownAbsensi() {
        let params = getFilterParams();
        $('#loading_breakdown_absensi').show();

        if (dtAbsensi) {
            dtAbsensi.destroy();
            dtAbsensi = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_absensi"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_absensi').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.salesmanid + '</code></td>';
                        html += '<td><strong>' + (row.nama_salesman || '-') + '</strong></td>';
                        html += '<td>' + (row.nama_subarea || row.nama_area || '-') + '</td>';
                        html += '<td class="text-center"><strong>' + row.hari_kerja + '</strong> HK</td>';
                        html += '<td class="text-center">' + formatNumber(row.hadir) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.izin) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.sakit) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.cuti) + '</td>';
                        html += '<td class="text-center">' + formatPctBadge(row.percentage) + '</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_absensi tbody').html(html);

                if ($.fn.DataTable) {
                    dtAbsensi = $('#table_breakdown_absensi').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[0, 'asc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_absensi').hide();
            }
        });
    }

    function loadBreakdownCall() {
        let params = getFilterParams();
        $('#loading_breakdown_call').show();

        if (dtCall) {
            dtCall.destroy();
            dtCall = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_call"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_call').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.salesmanid + '</code></td>';
                        html += '<td><strong>' + (row.nama_salesman || '-') + '</strong></td>';
                        html += '<td>' + (row.nama_subarea || row.nama_area || '-') + '</td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.target_visit) + '</strong></td>';
                        html += '<td class="text-center">' + formatNumber(row.call_act) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.extra_call) + '</td>';
                        html += '<td class="text-center">' + formatPctBadge(row.percentage) + '</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_call tbody').html(html);

                if ($.fn.DataTable) {
                    dtCall = $('#table_breakdown_call').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[0, 'asc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_call').hide();
            }
        });
    }

    function loadBreakdownKategori() {
        let params = getFilterParams();
        $('#loading_breakdown_kategori').show();

        if (dtKategori) {
            dtKategori.destroy();
            dtKategori = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_kategori"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_kategori').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.typeid + '</code></td>';
                        html += '<td><strong>' + (row.nama_type || row.typeid) + '</strong></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.jumlah_outlet) + '</strong> Outlet</td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.jumlah_visit) + '</strong> Visit</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_kategori tbody').html(html);

                if ($.fn.DataTable) {
                    dtKategori = $('#table_breakdown_kategori').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[3, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_kategori').hide();
            }
        });
    }

    function loadBreakdownUserWajib() {
        let params = getFilterParams();
        $('#loading_breakdown_user_wajib').show();

        if (dtUserWajib) {
            dtUserWajib.destroy();
            dtUserWajib = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_user_wajib"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_user_wajib').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><i class="fa fa-user-md text-warning"></i> <strong>' + (row.nama_spesialisasi || '-') + '</strong></td>';
                        html += '<td><span class="label label-default">' + (row.tipe_spesialis || 'Dokter') + '</span></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.target_spesialis) + '</strong></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.aktual) + '</strong></td>';
                        html += '<td class="text-center">' + formatPctBadge(row.percentage) + '</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_user_wajib tbody').html(html);

                if ($.fn.DataTable) {
                    dtUserWajib = $('#table_breakdown_user_wajib').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[4, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_user_wajib').hide();
            }
        });
    }

    function loadBreakdownDetailing() {
        let params = getFilterParams();
        $('#loading_breakdown_detailing').show();

        if (dtDetailing) {
            dtDetailing.destroy();
            dtDetailing = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_detailing"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_detailing').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.productid + '</code></td>';
                        html += '<td><strong>' + (row.nama_invoice || row.productid) + '</strong></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.target) + '</strong></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.aktual) + '</strong></td>';
                        html += '<td class="text-center">' + formatPctBadge(row.percentage) + '</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_detailing tbody').html(html);

                if ($.fn.DataTable) {
                    dtDetailing = $('#table_breakdown_detailing').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[4, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_detailing').hide();
            }
        });
    }

    function loadBreakdownSelling() {
        let params = getFilterParams();
        $('#loading_breakdown_selling').show();

        if (dtSelling) {
            dtSelling.destroy();
            dtSelling = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_selling"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_selling').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.productid + '</code></td>';
                        html += '<td><strong>' + (row.nama_invoice || row.productid) + '</strong></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.target_qty) + '</strong></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.aktual) + '</strong></td>';
                        html += '<td class="text-center">' + formatPctBadge(row.percentage) + '</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_selling tbody').html(html);

                if ($.fn.DataTable) {
                    dtSelling = $('#table_breakdown_selling').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[4, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_selling').hide();
            }
        });
    }

    function loadBreakdownJoinVisit() {
        let params = getFilterParams();
        $('#loading_breakdown_join_visit').show();

        if (dtJoinVisit) {
            dtJoinVisit.destroy();
            dtJoinVisit = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_join_visit"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_join_visit').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        let statusBadge = '<span class="label label-danger">Reject</span>';
                        if (row.status == '1') {
                            statusBadge = '<span class="label label-warning">Pending</span>';
                        } else if (row.status == '3') {
                            let isDetailing = (row.visit_keterangan && row.visit_keterangan.indexOf('Sudah Detailing') !== -1) ||
                                              (row.keterangan && row.keterangan.indexOf('Sudah Detailing') !== -1);
                            statusBadge = '<span class="label label-success">Disetujui</span>' + (isDetailing ? ' <span class="label label-info" style="font-size: 10px;">Sudah Detailing</span>' : '');
                        }

                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td class="text-center">' + (row.periode || '-') + '</td>';
                        html += '<td><strong>' + (row.nama_salesman || '-') + '</strong><br><small class="text-muted">' + row.salesmanid + '</small></td>';
                        html += '<td><strong>' + (row.nama_outlet || '-') + '</strong><br><small class="text-muted">' + (row.customerid || '-') + '</small></td>';
                        html += '<td><i class="fa fa-user-md text-maroon"></i> <strong>' + (row.nama_user || '-') + '</strong></td>';
                        html += '<td class="text-center">' + statusBadge + '</td>';
                        html += '<td class="text-center"><button type="button" class="btn btn-xs btn-primary btn-detail-join-visit" data-reqno="' + row.req_no + '" title="Lihat Detail Join Visit"><i class="fa fa-search"></i> Detail</button></td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_join_visit tbody').html(html);

                if ($.fn.DataTable) {
                    dtJoinVisit = $('#table_breakdown_join_visit').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[1, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_join_visit').hide();
            }
        });
    }

    function loadBreakdownTpeAktif() {
        let params = getFilterParams();
        $('#loading_breakdown_tpe_aktif').show();

        if (dtTpeAktif) {
            dtTpeAktif.destroy();
            dtTpeAktif = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_tpe_aktif"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_tpe_aktif').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.salesmanid + '</code></td>';
                        html += '<td><strong>' + (row.nama_salesman || '-') + '</strong></td>';
                        html += '<td><span class="label label-primary">' + (row.tipe_sales || '-') + '</span></td>';
                        html += '<td>' + (row.nama_subarea || row.nama_area || '-') + '</td>';
                        html += '<td><strong>' + (row.nama_supervisor || '-') + '</strong>' + (row.supervisorid ? ' <br><small class="text-muted">' + row.supervisorid + '</small>' : '') + '</td>';
                        html += '<td class="text-center"><button type="button" class="btn btn-xs btn-info btn-detail-tpe" data-sid="' + row.salesmanid + '" data-sname="' + row.nama_salesman + '" title="Lihat Detail Log Aktivitas TPE"><i class="fa fa-user"></i> Detail</button></td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_tpe_aktif tbody').html(html);

                if ($.fn.DataTable) {
                    dtTpeAktif = $('#table_breakdown_tpe_aktif').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[0, 'asc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_tpe_aktif').hide();
            }
        });
    }

    function openDetailJoinVisitModal(reqNo) {
        if (!reqNo) return;

        $('#detail_visit_periode').text('-');
        $('#detail_visit_salesman_name').text('-');
        $('#detail_visit_salesman_id').text('(-)');
        $('#detail_visit_customer_name').text('-');
        $('#detail_visit_customer_id').text('(-)');
        $('#detail_visit_prof_name').text('-');
        $('#detail_visit_prof_id').text('(-)');
        $('#detail_visit_status').html('-');
        $('#detail_visit_keterangan').text('-');
        $('#detail_visit_keterangan_visit').text('-');
        $('#row_detail_visit_reason').hide();
        $('#row_detail_visit_approval').hide();
        $('#detail_visit_gallery').html('');
        $('#detail_visit_gallery_empty').hide();
        $('#detail_visit_doc_count').text('0');

        $('#modal_detail_visit').modal('show');

        $.ajax({
            url: common.baseURL("dashboard_tts/get_detail_join_visit"),
            type: 'POST',
            dataType: 'json',
            data: { req_no: reqNo },
            success: function (res) {
                if (res) {
                    $('#detail_visit_periode').text(res.periode || '-');
                    $('#detail_visit_salesman_name').text(res.nama_salesman || '-');
                    $('#detail_visit_salesman_id').text('(' + (res.salesmanid || '-') + ')');
                    $('#detail_visit_customer_name').text(res.nama_customer || '-');
                    $('#detail_visit_customer_id').text('(' + (res.customerid || '-') + ')');
                    $('#detail_visit_prof_name').text(res.nama_professional || '-');
                    $('#detail_visit_prof_id').text('(' + (res.user_id || '-') + ')');

                    let statusBadge = '<span class="label label-danger">Reject</span>';
                    if (res.status == '1') {
                        statusBadge = '<span class="label label-warning">Pending</span>';
                    } else if (res.status == '3') {
                        statusBadge = '<span class="label label-success">Disetujui</span>';
                    }
                    $('#detail_visit_status').html(statusBadge);

                    $('#detail_visit_keterangan').text(res.keterangan || '-');
                    $('#detail_visit_keterangan_visit').text(res.visit_keterangan || '-');

                    if (res.status != '1' && res.status != '3' && res.reason) {
                        $('#detail_visit_reason').text(res.reason);
                        $('#row_detail_visit_reason').show();
                    }

                    if (res.approved_by) {
                        $('#detail_visit_approved_by').text(res.approved_by);
                        $('#detail_visit_approved_date').text(res.approved_date || '-');
                        $('#row_detail_visit_approval').show();
                    }

                    // Render Foto Dokumentasi
                    let docs = [];
                    if (res.visit_doc) {
                        try {
                            if (typeof res.visit_doc === 'string') {
                                if (res.visit_doc.startsWith('[') || res.visit_doc.startsWith('{')) {
                                    docs = JSON.parse(res.visit_doc);
                                } else {
                                    docs = res.visit_doc.split(',');
                                }
                            } else if (Array.isArray(res.visit_doc)) {
                                docs = res.visit_doc;
                            }
                        } catch (e) {
                            docs = res.visit_doc ? [res.visit_doc] : [];
                        }
                    }

                    if (docs && docs.length > 0) {
                        let validDocs = [];
                        $.each(docs, function (idx, item) {
                            let docUrl = typeof item === 'object' && item !== null ? (item.url || item.path || item.file) : item;
                            if (docUrl && typeof docUrl === 'string' && docUrl.trim() !== '') {
                                validDocs.push(docUrl.trim());
                            }
                        });

                        $('#detail_visit_doc_count').text(validDocs.length);

                        if (validDocs.length > 0) {
                            let galleryHtml = '';
                            $.each(validDocs, function (idx, url) {
                                let fullUrl = url.startsWith('http') ? url : (common.baseURL() + url.replace(/^\//, ''));
                                galleryHtml += '<div class="img-gallery-card" style="border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; background: #fff; width: calc(33.333% - 10px); min-width: 180px; display: flex; flex-direction: column;">' +
                                    '<img src="' + fullUrl + '" class="img-gallery-thumb" data-title="Foto ' + (idx + 1) + ' - ' + (res.nama_professional || 'Join Visit') + '" style="width: 100%; height: 160px; object-fit: cover; cursor: pointer;" alt="Foto Dokumentasi">' +
                                    '<div class="card-footer-info" style="padding: 8px 10px; font-size: 11px; color: #6b7280; background: #f9fafb; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between;">' +
                                    '<span>Foto ' + (idx + 1) + '</span>' +
                                    '<a href="' + fullUrl + '" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-external-link"></i></a>' +
                                    '</div>' +
                                    '</div>';
                            });
                            $('#detail_visit_gallery').html(galleryHtml);
                        } else {
                            $('#detail_visit_gallery_empty').show();
                        }
                    } else {
                        $('#detail_visit_gallery_empty').show();
                    }
                }
            }
        });
    }

    function openDetailReportModal(salesmanid, salesmanName) {
        let params = getFilterParams();
        params.salesmanid = salesmanid;

        $('#modal_tpe_name').text(salesmanName + ' (' + salesmanid + ')');
        $('#modal_tpe_area').text('');
        
        if (dtModalKunjungan) { dtModalKunjungan.destroy(); dtModalKunjungan = null; }
        if (dtModalDetailing) { dtModalDetailing.destroy(); dtModalDetailing = null; }
        if (dtModalAbsensi) { dtModalAbsensi.destroy(); dtModalAbsensi = null; }

        $('#table_modal_kunjungan tbody').html('');
        $('#table_modal_detailing tbody').html('');
        $('#table_modal_absensi tbody').html('');
        $('#modal_detail_tpe').modal('show');
        $('#loading_modal_detail').show();

        $.ajax({
            url: common.baseURL("dashboard_tts/load_detail_tpe"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_modal_detail').hide();
                if (res) {
                    if (res.salesman) {
                        let sls = res.salesman;
                        $('#modal_tpe_area').text('• Regional: ' + (sls.nama_regional || '-') + ' | Area: ' + (sls.nama_area || '-') + ' | Sub Area: ' + (sls.nama_subarea || '-'));
                    }

                    let htmlK = '';
                    if (res.kunjungan && res.kunjungan.length > 0) {
                        $('#count_log_kunjungan').text(res.kunjungan.length);
                        $.each(res.kunjungan, function (i, r) {
                            let ecBadge = r.is_effective_call == 1 ? '<span class="label label-success">Ya (Order)</span>' : '<span class="label label-default">Tidak</span>';
                            htmlK += '<tr>';
                            htmlK += '<td class="text-center">' + (i + 1) + '</td>';
                            htmlK += '<td>' + r.tanggal + '</td>';
                            htmlK += '<td><code>' + r.customerid + '</code></td>';
                            htmlK += '<td><strong>' + r.nama_outlet + '</strong></td>';
                            htmlK += '<td>' + r.tipe_outlet + '</td>';
                            htmlK += '<td class="text-center">' + (r.check_in || '-') + '</td>';
                            htmlK += '<td class="text-center">' + (r.check_out || '-') + '</td>';
                            htmlK += '<td class="text-center">' + (r.durasi_menit !== null ? r.durasi_menit + ' mnt' : '-') + '</td>';
                            htmlK += '<td class="text-center">' + ecBadge + '</td>';
                            htmlK += '<td class="text-center">' + r.has_crc + '</td>';
                            htmlK += '<td class="text-center">' + r.has_promo + '</td>';
                            htmlK += '</tr>';
                        });
                    } else {
                        $('#count_log_kunjungan').text('0');
                    }
                    $('#table_modal_kunjungan tbody').html(htmlK);

                    let htmlD = '';
                    if (res.detailing && res.detailing.length > 0) {
                        $('#count_log_detailing').text(res.detailing.length);
                        $.each(res.detailing, function (i, r) {
                            htmlD += '<tr>';
                            htmlD += '<td class="text-center">' + (i + 1) + '</td>';
                            htmlD += '<td>' + r.tanggal + '</td>';
                            htmlD += '<td><i class="fa fa-user-md text-primary"></i> <strong>' + r.nama_dokter + '</strong></td>';
                            htmlD += '<td>' + r.spesialisasi + '</td>';
                            htmlD += '<td>' + r.nama_instansi + '</td>';
                            htmlD += '<td>' + r.jenis_channel + '</td>';
                            htmlD += '<td><strong>' + r.nama_produk + '</strong></td>';
                            htmlD += '<td class="text-center"><span class="label label-info">' + r.status_detailing + '</span></td>';
                            htmlD += '</tr>';
                        });
                    } else {
                        $('#count_log_detailing').text('0');
                    }
                    $('#table_modal_detailing tbody').html(htmlD);

                    let htmlA = '';
                    if (res.absensi && res.absensi.length > 0) {
                        $('#count_log_absensi').text(res.absensi.length);
                        $.each(res.absensi, function (i, r) {
                            let st = r.status_computed || r.status;
                            let statusCls = st === 'H' ? 'label-success' : (st === 'S' ? 'label-warning' : (st === 'C' ? 'label-info' : 'label-danger'));
                            let imgIn = r.foto_checkin ? '<a href="' + r.foto_checkin + '" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-image"></i> Foto In</a>' : '-';
                            let imgOut = r.foto_checkout ? '<a href="' + r.foto_checkout + '" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-image"></i> Foto Out</a>' : '-';
                            htmlA += '<tr>';
                            htmlA += '<td class="text-center">' + (i + 1) + '</td>';
                            htmlA += '<td>' + r.tanggal + '</td>';
                            htmlA += '<td class="text-center"><span class="label ' + statusCls + '">' + r.status_label + '</span></td>';
                            htmlA += '<td class="text-center">' + (r.parma_checkin || r.checkin || '-') + '</td>';
                            htmlA += '<td class="text-center">' + (r.parma_checkout || r.checkout || '-') + '</td>';
                            htmlA += '<td>' + (r.keterangan || '-') + '</td>';
                            htmlA += '<td class="text-center">' + imgIn + '</td>';
                            htmlA += '<td class="text-center">' + imgOut + '</td>';
                            htmlA += '</tr>';
                        });
                    } else {
                        $('#count_log_absensi').text('0');
                    }
                    $('#table_modal_absensi tbody').html(htmlA);

                    if ($.fn.DataTable) {
                        dtModalKunjungan = $('#table_modal_kunjungan').DataTable({
                            pageLength: 10,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                            language: dtLanguageIndo,
                            order: [[1, 'desc']],
                            responsive: true
                        });

                        dtModalDetailing = $('#table_modal_detailing').DataTable({
                            pageLength: 10,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                            language: dtLanguageIndo,
                            order: [[1, 'desc']],
                            responsive: true
                        });

                        dtModalAbsensi = $('#table_modal_absensi').DataTable({
                            pageLength: 10,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                            language: dtLanguageIndo,
                            order: [[1, 'desc']],
                            responsive: true
                        });
                    }
                }
            },
            error: function () {
                $('#loading_modal_detail').hide();
            }
        });
    }

    $(document).ready(function () {
        init();
    });

    return {
        reload: loadAllDashboardData,
        openDetail: openDetailReportModal,
        openJoinDetail: openDetailJoinVisitModal
    };
})(jQuery);
