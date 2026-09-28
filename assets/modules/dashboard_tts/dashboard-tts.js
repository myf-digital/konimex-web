let DashboardTTS = (function ($) {
    'use strict';

    const common = new Common();
    const commonGrid = new CommonGrid();

    let chartTrendInstance = null;
    let rawTrendData = null;

    let dtPeriode = $('#periode');
    let dtArea = null;
    let dtChannel = null;
    let dtProduk = null;
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
                if (kpi === 'detailing_produk' || kpi === 'selling_produk') {
                    $('#tab_link_produk').tab('show');
                } else if (kpi === 'join_activity') {
                    $('#tab_link_channel').tab('show');
                } else {
                    $('#tab_link_area').tab('show');
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

        $('#btn_export_area_excel').on('click', function () {
            let params = getFilterParams();
            window.location.href = common.baseURL("dashboard_tts/export_excel_area?" + $.param(params));
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
        loadBreakdownArea();
        loadBreakdownChannel();
        loadBreakdownProduk();
    }

    function getBadgeClass(pct) {
        if (pct >= 100) return 'badge-success-custom';
        if (pct >= 80) return 'badge-warning-custom';
        return 'badge-danger-custom';
    }

    function getBadgeText(pct) {
        if (pct >= 100) return 'Tercapai (' + pct + '%)';
        if (pct >= 80) return 'Mendekati (' + pct + '%)';
        return 'Kurang (' + pct + '%)';
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
                        $('#kpi_absensi_hadir').text(formatNumber(abs.actual));
                        $('#kpi_absensi_total').text(formatNumber(abs.total));
                        
                        let absHtml = '';
                        absHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Hadir: <b>' + formatNumber(abs.actual_fisik || 0) + '</b></div>';
                        absHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Izin: <b>' + formatNumber(abs.izin || abs.hf || 0) + '</b></div>';
                        absHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Sakit: <b>' + formatNumber(abs.sakit || 0) + '</b></div>';
                        absHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Cuti: <b>' + formatNumber(abs.cuti || 0) + '</b></div>';
                        $('#kpi_absensi_rincian').html(absHtml);
                    }

                    let cal = kpi.call;
                    if (cal) {
                        let totalRealisasiCall = cal.actual !== undefined ? cal.actual : ((cal.actual_call || 0) + (cal.extra_call || 0));
                        $('#kpi_ec_pct').text(cal.percentage_ec + '%');
                        $('#kpi_call_total').text(formatNumber(totalRealisasiCall));
                        $('#kpi_call_plan').text(formatNumber(cal.planned_call));

                        let calHtml = '';
                        calHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Call: <b>' + formatNumber(cal.actual_call || 0) + '</b></div>';
                        calHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Extra Call: <b>' + formatNumber(cal.extra_call || cal.actual_ec || 0) + '</b></div>';
                        $('#kpi_call_rincian').html(calHtml);
                    }

                    let kat = kpi.kategori;
                    let totalOutletKat = kat.total_outlet !== undefined ? kat.total_outlet : (kat.actual || 0);
                    $('#kpi_kategori_total').text(formatNumber(totalOutletKat));

                    if (Array.isArray(kat.rincian) && kat.rincian.length > 0) {
                        let rincianHtml = '';
                        kat.rincian.forEach(function (item) {
                            let typeName = item.typeid || 'Lainnya';
                            let count = formatNumber(item.total_outlet);
                            rincianHtml += '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="' + typeName + ': ' + count + '">' + typeName + ': <b>' + count + '</b></div>';
                        });
                        $('#kpi_kategori_rincian').html(rincianHtml).attr('title', kat.rincian_text || '');
                    } else {
                        $('#kpi_kategori_rincian').html('<div>-</div>').attr('title', '');
                    }

                    let usr = kpi.user_wajib;
                    $('#kpi_user_pct').text(usr.percentage + '%');
                    $('#kpi_user_act').text(formatNumber(usr.actual));
                    $('#kpi_user_tgt').text(formatNumber(usr.target));

                    if (Array.isArray(usr.rincian) && usr.rincian.length > 0) {
                        let userRincianHtml = '';
                        let top4 = usr.rincian.slice(0, 4);
                        top4.forEach(function (item) {
                            let spName = item.nama_spesialisasi || 'Lainnya';
                            let act = formatNumber(item.actual);
                            let tgt = formatNumber(item.target);
                            userRincianHtml += '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px;" title="' + spName + ': ' + act + ' / ' + tgt + '">' + spName + ': <b>' + act + '</b>/' + tgt + '</div>';
                        });
                        $('#kpi_user_rincian').html(userRincianHtml).attr('title', usr.rincian_text || '');
                    } else {
                        $('#kpi_user_rincian').html('<div>-</div>').attr('title', '');
                    }

                    let det = kpi.detailing_produk;
                    $('#kpi_detailing_pct').text(det.percentage + '%');
                    $('#kpi_detailing_act').text(formatNumber(det.actual));
                    $('#kpi_detailing_tgt').text(formatNumber(det.target));

                    if (Array.isArray(det.rincian) && det.rincian.length > 0) {
                        let detRincianHtml = '';
                        let top4Det = det.rincian.slice(0, 4);
                        top4Det.forEach(function (item) {
                            let prdName = item.nama_produk || item.product_id || 'Produk';
                            let act = formatNumber(item.actual);
                            let tgt = formatNumber(item.target);
                            detRincianHtml += '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px;" title="' + prdName + ': ' + act + ' / ' + tgt + '">' + prdName + ': <b>' + act + '</b>/' + tgt + '</div>';
                        });
                        $('#kpi_detailing_rincian').html(detRincianHtml).attr('title', det.rincian_text || '');
                    } else {
                        $('#kpi_detailing_rincian').html('<div>-</div>').attr('title', '');
                    }

                    let sel = kpi.selling_produk;
                    $('#kpi_selling_pct').text(sel.percentage + '%');
                    $('#kpi_selling_act').text(formatNumber(sel.actual));
                    $('#kpi_selling_tgt').text(formatNumber(sel.target));

                    if (Array.isArray(sel.rincian) && sel.rincian.length > 0) {
                        let selRincianHtml = '';
                        let top4Sel = sel.rincian.slice(0, 4);
                        top4Sel.forEach(function (item) {
                            let prdName = item.nama_produk || item.product_id || 'Produk';
                            let act = formatNumber(item.actual);
                            let tgt = formatNumber(item.target);
                            selRincianHtml += '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px;" title="' + prdName + ': ' + act + ' / ' + tgt + '">' + prdName + ': <b>' + act + '</b>/' + tgt + '</div>';
                        });
                        $('#kpi_selling_rincian').html(selRincianHtml).attr('title', sel.rincian_text || '');
                    } else {
                        $('#kpi_selling_rincian').html('<div>-</div>').attr('title', '');
                    }

                    let jn = kpi.join_activity;
                    if (jn) {
                        $('#kpi_join_act').text(formatNumber(jn.actual));
                        let joinHtml = '';
                        joinHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Pending: <b>' + formatNumber(jn.pending || 0) + '</b></div>';
                        joinHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Approved: <b>' + formatNumber(jn.approved || 0) + '</b></div>';
                        joinHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Sudah Detailing: <b>' + formatNumber(jn.sudah_detailing || 0) + '</b></div>';
                        joinHtml += '<div style="white-space: nowrap; margin-bottom: 1px;">Reject: <b>' + formatNumber(jn.reject || 0) + '</b></div>';
                        $('#kpi_join_rincian').html(joinHtml);
                    }

                    let tpe = kpi.tpe;
                    if (tpe) {
                        $('#kpi_tpe_act').text(formatNumber(tpe.actual !== undefined ? tpe.actual : (tpe.total || 0)));
                        if (Array.isArray(tpe.rincian) && tpe.rincian.length > 0) {
                            let tpeHtml = '';
                            let top4Tpe = tpe.rincian.slice(0, 4);
                            top4Tpe.forEach(function (item) {
                                let tName = item.tipe_sales || 'Lainnya';
                                let count = formatNumber(item.total || 0);
                                tpeHtml += '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px;" title="' + tName + ': ' + count + '">' + tName + ': <b>' + count + '</b></div>';
                            });
                            $('#kpi_tpe_rincian').html(tpeHtml);
                        } else {
                            $('#kpi_tpe_rincian').html('<div>-</div>');
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
        $('#loading_trend').show();
        $('#label_trend_year').text('(' + params.tahun + ')');

        $.ajax({
            url: common.baseURL("dashboard_tts/load_trend"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_trend').hide();
                rawTrendData = res;
                let activeMetric = $('#btn_toggle_metrics button.active').data('metric') || 'all';
                renderTrendChart(activeMetric);
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

        let allDatasets = [
            {
                label: 'Ketercapaian Kat A',
                data: rawTrendData.series.kat_a,
                borderColor: '#7b1fa2',
                backgroundColor: 'rgba(123, 31, 162, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                metricKey: 'kat_a'
            },
            {
                label: 'User Wajib',
                data: rawTrendData.series.user_wajib,
                borderColor: '#e65100',
                backgroundColor: 'rgba(230, 81, 0, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                metricKey: 'user_wajib'
            },
            {
                label: 'Detailing Produk',
                data: rawTrendData.series.detailing,
                borderColor: '#00897b',
                backgroundColor: 'rgba(0, 137, 123, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                metricKey: 'detailing'
            },
            {
                label: 'Selling Produk',
                data: rawTrendData.series.selling,
                borderColor: '#e53935',
                backgroundColor: 'rgba(229, 57, 53, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                metricKey: 'selling'
            },
            {
                label: 'Call Kunjungan',
                data: rawTrendData.series.call,
                borderColor: '#1976d2',
                backgroundColor: 'rgba(25, 118, 210, 0.1)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                metricKey: 'call'
            }
        ];

        let visibleDatasets = allDatasets;
        if (metricFilter && metricFilter !== 'all') {
            visibleDatasets = allDatasets.filter(function (d) {
                return d.metricKey === metricFilter;
            });
        }

        chartTrendInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: rawTrendData.labels,
                datasets: visibleDatasets
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
                        cornerRadius: 6
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f2f5' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    function loadBreakdownArea() {
        let params = getFilterParams();
        $('#loading_breakdown_area').show();

        if (dtArea) {
            dtArea.destroy();
            dtArea = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_area"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_area').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td>' + (row.nama_regional || '-') + '</td>';
                        html += '<td>' + (row.nama_area || '-') + '</td>';
                        html += '<td>' + (row.nama_subarea || '-') + '</td>';
                        html += '<td><strong>' + (row.nama_salesman || '-') + '</strong> <br><small class="text-muted">' + row.salesmanid + ' (' + (row.tipe_sales || '-') + ')</small></td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_absensi) + '<br><small>' + row.total_hadir + '/' + row.total_hari + ' HK</small></td>';
                        html += '<td class="text-center">' + formatNumber(row.total_call) + '</td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_ec) + '<br><small>' + row.total_ec + ' EC</small></td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_kat_a) + '<br><small>' + row.act_kat_a + '/' + row.tgt_kat_a + '</small></td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_user_wajib) + '<br><small>' + row.act_user_wajib + '/' + row.tgt_user_wajib + '</small></td>';
                        html += '<td class="text-center">' + formatNumber(row.total_detailing) + '</td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_selling) + '<br><small>' + row.act_selling + '/' + row.tgt_selling + '</small></td>';
                        html += '<td class="text-center"><button type="button" class="btn btn-xs btn-primary btn-detail-tpe" data-sid="' + row.salesmanid + '" data-sname="' + row.nama_salesman + '" title="Lihat Detail Log Transaksi"><i class="fa fa-search"></i> Detail</button></td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_area tbody').html(html);

                if ($.fn.DataTable) {
                    dtArea = $('#table_breakdown_area').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[0, 'asc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_area').hide();
            }
        });
    }

    function loadBreakdownChannel() {
        let params = getFilterParams();
        $('#loading_breakdown_channel').show();

        if (dtChannel) {
            dtChannel.destroy();
            dtChannel = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_channel_spesialis"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_channel').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        let avg = (row.total_user > 0) ? (row.total_kunjungan_detailing / row.total_user).toFixed(1) : 0;
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><i class="fa fa-stethoscope text-success"></i> <strong>' + (row.nama_spesialisasi || '-') + '</strong></td>';
                        html += '<td><span class="label label-default" style="font-size: 11px;">' + (row.jenis_channel || '-') + '</span></td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.total_user) + '</strong> User</td>';
                        html += '<td class="text-center"><strong>' + formatNumber(row.total_kunjungan_detailing) + '</strong> Kunjungan</td>';
                        html += '<td class="text-center">' + avg + ' visit / user</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_channel tbody').html(html);

                // Inisialisasi DataTables
                if ($.fn.DataTable) {
                    dtChannel = $('#table_breakdown_channel').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[4, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_channel').hide();
            }
        });
    }

    function loadBreakdownProduk() {
        let params = getFilterParams();
        $('#loading_breakdown_produk').show();

        if (dtProduk) {
            dtProduk.destroy();
            dtProduk = null;
        }

        $.ajax({
            url: common.baseURL("dashboard_tts/load_breakdown_produk"),
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (res) {
                $('#loading_breakdown_produk').hide();
                let html = '';
                if (res && res.length > 0) {
                    $.each(res, function (i, row) {
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td><code>' + row.product_id + '</code></td>';
                        html += '<td><strong>' + (row.nama_produk || row.product_id) + '</strong></td>';
                        html += '<td class="text-center">' + formatNumber(row.actual_detailing) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.target_detailing) + '</td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_detailing) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.actual_selling_qty) + '</td>';
                        html += '<td class="text-center">' + formatNumber(row.target_selling_qty) + '</td>';
                        html += '<td class="text-center">' + formatPctBadge(row.pct_selling) + '</td>';
                        html += '</tr>';
                    });
                }
                $('#table_breakdown_produk tbody').html(html);

                if ($.fn.DataTable) {
                    dtProduk = $('#table_breakdown_produk').DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                        language: dtLanguageIndo,
                        order: [[3, 'desc']],
                        responsive: true
                    });
                }
            },
            error: function () {
                $('#loading_breakdown_produk').hide();
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
        openDetail: openDetailReportModal
    };
})(jQuery);
