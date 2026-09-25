<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Dashboard TTS - Konimex
        <small>Executive Overview & Performa Karyawan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Konimex</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content dashboard">

    <!-- 1. FILTER BOX (AdminLTE Theme) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Filter Periode & Hierarki Wilayah</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form id="form_filter_dashboard">
                        <div class="row">
                            <!-- Periode Tahun -->
                            <div class="form-group col-md-2 col-sm-6">
                                <label><i class="fa fa-calendar"></i> Tahun</label>
                                <select id="filter_tahun" name="tahun" class="form-control select2">
                                    <?php 
                                        $current_year = date('Y');
                                        for ($y = $current_year; $y >= $current_year - 3; $y--): 
                                    ?>
                                        <option value="<?= $y ?>" <?= ($y == $current_year) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <!-- Periode Bulan -->
                            <div class="form-group col-md-2 col-sm-6">
                                <label><i class="fa fa-calendar-o"></i> Bulan</label>
                                <select id="filter_bulan" name="bulan" class="form-control select2">
                                    <?php 
                                        $months = [
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ];
                                        $current_month = date('m');
                                        foreach ($months as $k => $v): 
                                    ?>
                                        <option value="<?= $k ?>" <?= ($k == $current_month) ? 'selected' : '' ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Area SM (Regional) -->
                            <div class="form-group col-md-2 col-sm-6">
                                <label><i class="fa fa-globe"></i> Area SM (Regional)</label>
                                <select id="filter_regionalid" name="regionalid" class="form-control select2">
                                    <option value="">-- Semua Area SM --</option>
                                    <?php if (!empty($regional_list)): ?>
                                        <?php foreach ($regional_list as $reg): ?>
                                            <option value="<?= $reg['regionalid'] ?>"><?= $reg['nama_regional'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Area ASM (Site) -->
                            <div class="form-group col-md-2 col-sm-6">
                                <label><i class="fa fa-map-marker"></i> Area ASM</label>
                                <select id="filter_areaid" name="areaid" class="form-control select2">
                                    <option value="">-- Semua Area ASM --</option>
                                </select>
                            </div>

                            <!-- Area ASS / MRC -->
                            <div class="form-group col-md-2 col-sm-6">
                                <label><i class="fa fa-sitemap"></i> Area ASS / MRC</label>
                                <select id="filter_subareaid" name="subareaid" class="form-control select2">
                                    <option value="">-- Semua ASS/MRC --</option>
                                </select>
                            </div>

                            <!-- TPE (Salesman) -->
                            <div class="form-group col-md-2 col-sm-6">
                                <label><i class="fa fa-user"></i> TPE</label>
                                <select id="filter_salesmanid" name="salesmanid" class="form-control select2">
                                    <option value="">-- Semua TPE --</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer text-right">
                    <button type="button" id="btn_reset_filter" class="btn btn-default">
                        <i class="fa fa-undo"></i> Reset Filter
                    </button>
                    <button type="button" id="btn_apply_filter" class="btn btn-warning" style="margin-left: 6px;">
                        <i class="fa fa-refresh"></i> Load Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SUMMARY KPI CARDS (AdminLTE Small Box Theme) -->
    <div class="row" id="section_kpi_cards" style="position: relative;">
        <!-- Loading spinner -->
        <div id="loading_kpi" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
            <i class="fa fa-refresh fa-spin" style="font-size: 30px; color: #3c8dbc;"></i>
            <p style="font-weight: 600; color: #333; margin-top: 10px;">Memuat Data KPI...</p>
        </div>

        <!-- Card 1: Absensi & Kehadiran (Green) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3 id="kpi_absensi_pct">0%</h3>
                    <p>Kehadiran TPE: <span id="kpi_absensi_hadir">0</span> / <span id="kpi_absensi_total">0</span> HK</p>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="absensi">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 2: Call & Effective Call (Aqua) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3 id="kpi_ec_pct">0%</h3>
                    <p>EC: <span id="kpi_ec_act">0</span> / Call: <span id="kpi_call_act">0</span></p>
                </div>
                <div class="icon">
                    <i class="fa fa-phone"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="call">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 3: Ketercapaian Kat A (Purple) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3 id="kpi_kata_pct">0%</h3>
                    <p>Kat A: <span id="kpi_kata_act">0</span> / Tgt: <span id="kpi_kata_tgt">0</span></p>
                </div>
                <div class="icon">
                    <i class="fa fa-star"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="kat_a">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 4: Ketercapaian User Wajib (Yellow) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3 id="kpi_user_pct">0%</h3>
                    <p>User Wajib: <span id="kpi_user_act">0</span> / Tgt: <span id="kpi_user_tgt">0</span></p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="user_wajib">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 5: Detailing Produk Wajib (Teal) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3 id="kpi_detailing_pct">0%</h3>
                    <p>Detailing: <span id="kpi_detailing_act">0</span> / Tgt: <span id="kpi_detailing_tgt">0</span></p>
                </div>
                <div class="icon">
                    <i class="fa fa-medkit"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="detailing_produk">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 6: Selling Produk Wajib (Red) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3 id="kpi_selling_pct">0%</h3>
                    <p>Selling: <span id="kpi_selling_act">0</span> / Tgt: <span id="kpi_selling_tgt">0</span></p>
                </div>
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="selling_produk">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 7: Join Activity (Maroon) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-maroon">
                <div class="inner">
                    <h3 id="kpi_join_act">0</h3>
                    <p>Total Supervisi Join Activity</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="join_activity">
                    Lihat Breakdown <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Card 8: Quick Action / Overview (Navy) -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-navy">
                <div class="inner">
                    <h3>TTS</h3>
                    <p>Drilldown Matriks & Report</p>
                </div>
                <div class="icon">
                    <i class="fa fa-pie-chart"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="all">
                    Eksplorasi Data <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. TREND CHART BOX (AdminLTE Box Theme) -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-line-chart text-primary"></i> Tren Performa Bulanan (Januari - Desember) <span id="label_trend_year"></span>
                    </h3>
                    <div class="box-tools pull-right">
                        <div class="btn-group btn-group-sm" id="btn_toggle_metrics">
                            <button type="button" class="btn btn-default active" data-metric="all">Semua Metrik</button>
                            <button type="button" class="btn btn-default" data-metric="kat_a">Kat A</button>
                            <button type="button" class="btn btn-default" data-metric="user_wajib">User Wajib</button>
                            <button type="button" class="btn btn-default" data-metric="detailing">Detailing</button>
                            <button type="button" class="btn btn-default" data-metric="selling">Selling</button>
                        </div>
                    </div>
                </div>
                <div class="box-body" style="position: relative;">
                    <div id="loading_trend" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:60px;">
                        <i class="fa fa-refresh fa-spin" style="font-size: 30px; color: #3c8dbc;"></i>
                        <p style="font-weight: 600; color: #333; margin-top: 10px;">Memuat Grafik Tren...</p>
                    </div>
                    <div style="height: 350px; position: relative;">
                        <canvas id="chart_monthly_trend"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. LEVEL 2: MATRIKS BREAKDOWN TABS (AdminLTE nav-tabs-custom) -->
    <div class="row" id="section_breakdown">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_breakdown_area" data-toggle="tab" id="tab_link_area">
                            <i class="fa fa-sitemap text-blue"></i> 1. Breakdown Hierarki Area & TPE
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_channel" data-toggle="tab" id="tab_link_channel">
                            <i class="fa fa-hospital-o text-green"></i> 2. Sebaran Channel & Spesialisasi Dokter
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_produk" data-toggle="tab" id="tab_link_produk">
                            <i class="fa fa-medkit text-orange"></i> 3. Target & Realisasi Produk Wajib
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding: 15px;">
                    <!-- TAB 1: BREAKDOWN AREA & TPE -->
                    <div class="tab-pane active" id="tab_breakdown_area" style="position: relative;">
                        <div id="loading_breakdown_area" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #3c8dbc;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-md-6 col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-list-alt text-primary"></i> Tabel Performa TPE per Wilayah
                                </h4>
                                <small class="text-muted">Klik tombol "Detail" pada baris TPE untuk melihat log harian</small>
                            </div>
                            <div class="col-md-6 col-xs-12 text-right">
                                <button type="button" id="btn_export_area_excel" class="btn btn-sm btn-success">
                                    <i class="fa fa-file-excel-o"></i> Export Excel
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_area" style="font-size: 12px; width: 100%;">
                                <thead style="background: #2c3e50; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Area SM (Regional)</th>
                                        <th>Area ASM</th>
                                        <th>ASS / MRC</th>
                                        <th>TPE / Medrep</th>
                                        <th class="text-center">Kehadiran</th>
                                        <th class="text-center">Call</th>
                                        <th class="text-center">EC (%)</th>
                                        <th class="text-center">Kat A (%)</th>
                                        <th class="text-center">User Wajib (%)</th>
                                        <th class="text-center">Detailing</th>
                                        <th class="text-center">Selling (%)</th>
                                        <th class="text-center" style="width: 60px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="13" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: BREAKDOWN CHANNEL & SPESIALISASI -->
                    <div class="tab-pane" id="tab_breakdown_channel" style="position: relative;">
                        <div id="loading_breakdown_channel" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #00a65a;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-user-md text-success"></i> Matriks User per Channel & Spesialisasi
                                </h4>
                                <small class="text-muted">Sebaran dokter dan fasilitas kesehatan yang dikunjungi</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_channel" style="font-size: 12px; width: 100%;">
                                <thead style="background: #00a65a; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Spesialisasi Dokter / User</th>
                                        <th>Jenis Channel (RS / Klinik / Apotek / dll)</th>
                                        <th class="text-center">Total User Terdaftar</th>
                                        <th class="text-center">Total Kunjungan Detailing</th>
                                        <th class="text-center">Rata-rata Kunjungan/User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="6" class="text-center text-muted">Pilih filter dan load data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: BREAKDOWN PRODUK WAJIB -->
                    <div class="tab-pane" id="tab_breakdown_produk" style="position: relative;">
                        <div id="loading_breakdown_produk" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #f39c12;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-cubes text-warning"></i> Target vs Realisasi Detailing & Selling SKU
                                </h4>
                                <small class="text-muted">Ketercapaian produk fokus dan wajib Konimex</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_produk" style="font-size: 12px; width: 100%;">
                                <thead style="background: #f39c12; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Kode SKU</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Act Detailing</th>
                                        <th class="text-center">Tgt Detailing</th>
                                        <th class="text-center">% Detailing</th>
                                        <th class="text-center">Act Sales Qty</th>
                                        <th class="text-center">Tgt Sales Qty</th>
                                        <th class="text-center">% Sales Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="9" class="text-center text-muted">Pilih filter dan load data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LEVEL 3: MODAL DETAIL AKTIVITAS HARIAN TPE (AdminLTE Theme matching dashboard_pjp_daily) -->
    <div class="modal fade" id="modal_detail_tpe" tabindex="-1" role="dialog" aria-labelledby="modal_detail_tpe_label">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header" style="background:#0073b7; color:#fff; border-radius:4px 4px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal_detail_tpe_label">
                        <i class="fa fa-user"></i> Detail Log Transaksi TPE: <span id="modal_tpe_name" style="font-weight: 700;">-</span>
                        <small style="color: #dbeafe; font-size: 12px; margin-left: 10px;" id="modal_tpe_area"></small>
                    </h4>
                </div>
                <div class="modal-body" style="padding: 15px; position: relative;">
                    <div id="loading_modal_detail" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                        <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #0073b7;"></i>
                        <p style="font-weight: 600; color: #333; margin-top: 10px;">Memuat Detail Log Aktivitas...</p>
                    </div>

                    <div class="nav-tabs-custom" style="margin-bottom: 0;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_sub_kunjungan" data-toggle="tab"><i class="fa fa-map-marker text-blue"></i> A. Log Kunjungan & Call (<span id="count_log_kunjungan">0</span>)</a></li>
                            <li><a href="#tab_sub_detailing" data-toggle="tab"><i class="fa fa-stethoscope text-green"></i> B. Log Detailing Dokter (<span id="count_log_detailing">0</span>)</a></li>
                            <li><a href="#tab_sub_absensi" data-toggle="tab"><i class="fa fa-calendar-check-o text-purple"></i> C. Log Presensi Parma & Kehadiran (<span id="count_log_absensi">0</span>)</a></li>
                        </ul>
                        <div class="tab-content" style="padding: 15px 0;">
                            <!-- SUB-TAB A: KUNJUNGAN -->
                            <div class="tab-pane active" id="tab_sub_kunjungan">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover fixed-table" id="table_modal_kunjungan" style="font-size: 12px; width: 100%;">
                                        <thead style="background: #eef2f7;">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th>Tanggal</th>
                                                <th>Kode Outlet</th>
                                                <th>Nama Outlet</th>
                                                <th>Tipe</th>
                                                <th class="text-center">Jam Check-In</th>
                                                <th class="text-center">Jam Check-Out</th>
                                                <th class="text-center">Durasi</th>
                                                <th class="text-center">Effective Call</th>
                                                <th class="text-center">CRC</th>
                                                <th class="text-center">Promo</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- SUB-TAB B: DETAILING -->
                            <div class="tab-pane" id="tab_sub_detailing">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover fixed-table" id="table_modal_detailing" style="font-size: 12px; width: 100%;">
                                        <thead style="background: #eef2f7;">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th>Tanggal</th>
                                                <th>Nama Dokter / Professional</th>
                                                <th>Spesialisasi</th>
                                                <th>Instansi / Outlet</th>
                                                <th>Channel</th>
                                                <th>Produk Didetailkan</th>
                                                <th class="text-center">Status Detailing</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- SUB-TAB C: PRESENSI -->
                            <div class="tab-pane" id="tab_sub_absensi">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover fixed-table" id="table_modal_absensi" style="font-size: 12px; width: 100%;">
                                        <thead style="background: #eef2f7;">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th>Tanggal</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Jam Presensi Masuk</th>
                                                <th class="text-center">Jam Presensi Pulang</th>
                                                <th>Keterangan</th>
                                                <th class="text-center">Foto Checkin</th>
                                                <th class="text-center">Foto Checkout</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Module JS -->
<script src="<?= base_url() . 'assets/modules/dashboard_konimex/dashboard-konimex.js' ?>?v=1.4"></script>
