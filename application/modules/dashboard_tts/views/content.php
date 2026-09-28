<style type="text/css">
    #section_kpi_cards {
        display: flex;
        flex-wrap: wrap;
        margin-left: -10px;
        margin-right: -10px;
    }
    #section_kpi_cards > .col-lg-3 {
        padding-left: 10px;
        padding-right: 10px;
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }
    #section_kpi_cards .small-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 125px;
        margin-bottom: 0;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    #section_kpi_cards .small-box .inner {
        padding: 12px 15px 8px 15px;
        flex: 1;
    }
    #section_kpi_cards .small-box .inner h3 {
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 4px 0;
        white-space: nowrap;
    }
    #section_kpi_cards .small-box .inner p {
        font-size: 13px;
        margin-bottom: 3px;
        font-weight: 500;
    }
    #section_kpi_cards .small-box .icon {
        top: 10px;
        right: 12px;
        font-size: 55px;
        opacity: 0.25;
        transition: all .3s linear;
    }
    #section_kpi_cards .small-box:hover .icon {
        font-size: 60px;
        opacity: 0.35;
    }
    #section_kpi_cards .small-box .small-box-footer {
        padding: 5px 0;
        font-size: 12px;
        background: rgba(0,0,0,0.12);
    }
    #section_kpi_cards .small-box .small-box-footer:hover {
        background: rgba(0,0,0,0.22);
    }
    #kpi_kategori_rincian::-webkit-scrollbar,
    #kpi_user_rincian::-webkit-scrollbar {
        width: 3px;
    }
    #kpi_kategori_rincian::-webkit-scrollbar-thumb,
    #kpi_user_rincian::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.45);
        border-radius: 3px;
    }
</style>

<section class="content-header">
    <h1>
        Dashboard TTS
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">TTS</li>
    </ol>
</section>

<section id="content-main" class="content dashboard">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Filter</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form id="form_filter_dashboard">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label>Periode</label>
                                <div class="input-group date">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                                    <input id="periode" type="text" class="form-control datepicker" readonly>
                                </div>
                            </div>
                            <div class="form-group col-md-2 col-sm-6">
                                <label>Regional</label>
                                <select id="filter_regionalid" name="regionalid" class="form-control select2">
                                    <option value="">Semua Regional</option>
                                    <?php if (!empty($regional_list)): ?>
                                        <?php foreach ($regional_list as $reg): ?>
                                            <option value="<?= $reg['regionalid'] ?>"><?= $reg['nama_regional'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-6">
                                <label>Area</label>
                                <select id="filter_areaid" name="areaid" class="form-control select2">
                                    <option value="">Semua Area</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-6">
                                <label>Sub Area</label>
                                <select id="filter_subareaid" name="subareaid" class="form-control select2">
                                    <option value="">Semua Sub Area</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2 col-sm-6">
                                <label>TPE</label>
                                <select id="filter_salesmanid" name="salesmanid" class="form-control select2">
                                    <option value="">Semua TPE</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <button type="button" id="btn_reset_filter" class="btn btn-warning">
                        <i class="fa fa-undo"></i> Reset Filter
                    </button>
                    <button type="button" id="btn_apply_filter" class="btn btn-success" style="margin-left: 6px;">
                        <i class="fa fa-refresh"></i> Load Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="section_kpi_cards" style="position: relative;">
        <div id="loading_kpi" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
            <i class="fa fa-refresh fa-spin" style="font-size: 30px; color: #3c8dbc;"></i>
            <p style="font-weight: 600; color: #333; margin-top: 10px;">Memuat Data KPI...</p>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_absensi_pct">0%</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">Kehadiran TPE: <span id="kpi_absensi_act">0</span> / <span id="kpi_absensi_tgt">0</span> HK</p>
                    </div>
                    <div id="kpi_absensi_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="absensi">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_call_pct">0%</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">Performa TPE: <span id="kpi_call_act">0</span> / <span id="kpi_call_tgt">0</span></p>
                    </div>
                    <div id="kpi_call_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-phone"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="call">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_kategori_total">0</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">Ketercapaian Kategori</p>
                    </div>
                    <div id="kpi_kategori_rincian" style="flex: 1; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 2px 6px; font-size: 11px; line-height: 1.25; text-align: right; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-building-o"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="kategori">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_user_pct">0%</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">User Wajib: <span id="kpi_user_act">0</span> / <span id="kpi_user_tgt">0</span></p>
                    </div>
                    <div id="kpi_user_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-user-md"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="user_wajib">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-teal">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_detailing_pct">0%</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">Detailing Produk: <span id="kpi_detailing_act">0</span> / <span id="kpi_detailing_tgt">0</span></p>
                    </div>
                    <div id="kpi_detailing_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-medkit"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="detailing_produk">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_selling_pct">0%</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">Selling Produk: <span id="kpi_selling_act">0</span> / <span id="kpi_selling_tgt">0</span></p>
                    </div>
                    <div id="kpi_selling_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="selling_produk">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-maroon">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_join_pct">0%</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">Join Visit: <span id="kpi_join_act">0</span> / <span id="kpi_join_tgt">0</span></p>
                    </div>
                    <div id="kpi_join_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="join_visit">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-navy">
                <div class="inner" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="min-width: 80px; margin-right: 6px;">
                        <h3 id="kpi_tpe_act">0</h3>
                        <p style="margin-bottom: 0; font-size: 12px; white-space: nowrap;">TPE Aktif</p>
                    </div>
                    <div id="kpi_tpe_rincian" style="flex: 1; font-size: 11px; line-height: 1.3; text-align: right; max-height: 62px; overflow: hidden; position: relative; z-index: 2;">
                        -
                    </div>
                </div>
                <div class="icon" style="opacity: 0.12;">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer kpi-card-trigger" data-kpi="tpe_aktif">
                    Lihat Detail <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-line-chart text-primary"></i> Tren Performa Bulanan (Januari - Desember) <span id="label_trend_year"></span>
                    </h3>
                    <div class="box-tools pull-right">
                        <div class="btn-group btn-group-sm" id="btn_toggle_metrics">
                            <button type="button" class="btn btn-default active" data-metric="all">Semua Metrik (%)</button>
                            <button type="button" class="btn btn-default" data-metric="absensi">Kehadiran TPE</button>
                            <button type="button" class="btn btn-default" data-metric="call">Performa TPE</button>
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

    <div class="row" id="section_breakdown">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" style="font-size: 13px; font-weight: 600;">
                    <li class="active">
                        <a href="#tab_breakdown_absensi" data-toggle="tab" id="tab_link_absensi">
                            <i class="fa fa-calendar-check-o text-green"></i> 1. Kehadiran TPE
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_call" data-toggle="tab" id="tab_link_call">
                            <i class="fa fa-phone text-aqua"></i> 2. Performa TPE
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_kategori" data-toggle="tab" id="tab_link_kategori">
                            <i class="fa fa-th-large text-purple"></i> 3. Ketercapaian Kategori
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_user_wajib" data-toggle="tab" id="tab_link_user_wajib">
                            <i class="fa fa-user-md text-yellow"></i> 4. User Wajib
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_detailing" data-toggle="tab" id="tab_link_detailing">
                            <i class="fa fa-medkit text-teal"></i> 5. Detailing Produk
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_selling" data-toggle="tab" id="tab_link_selling">
                            <i class="fa fa-shopping-cart text-red"></i> 6. Selling Produk
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_join_visit" data-toggle="tab" id="tab_link_join_visit">
                            <i class="fa fa-users text-maroon"></i> 7. Join Visit
                        </a>
                    </li>
                    <li>
                        <a href="#tab_breakdown_tpe_aktif" data-toggle="tab" id="tab_link_tpe_aktif">
                            <i class="fa fa-id-badge text-navy"></i> 8. TPE Aktif
                        </a>
                    </li>
                </ul>

                <div class="tab-content" style="padding: 15px;">
                    <div class="tab-pane active" id="tab_breakdown_absensi" style="position: relative;">
                        <div id="loading_breakdown_absensi" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #00a65a;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-md-6 col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-calendar-check-o text-success"></i> Rincian Kehadiran TPE
                                </h4>
                                <small class="text-muted">Data hadir, izin, sakit, dan cuti</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_absensi" style="font-size: 12px; width: 100%;">
                                <thead style="background: #1b7e5a; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Salesman ID</th>
                                        <th>Nama Salesman</th>
                                        <th>Area</th>
                                        <th class="text-center">Hari Kerja</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-center">Izin</th>
                                        <th class="text-center">Sakit</th>
                                        <th class="text-center">Cuti</th>
                                        <th class="text-center">Persentase (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="10" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_call" style="position: relative;">
                        <div id="loading_breakdown_call" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #00c0ef;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-md-6 col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-phone text-aqua"></i> Rincian Performa TPE
                                </h4>
                                <small class="text-muted">Target, Call, dan Extra Call</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_call" style="font-size: 12px; width: 100%;">
                                <thead style="background: #008fae; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Salesman ID</th>
                                        <th>Nama Salesman</th>
                                        <th>Area</th>
                                        <th class="text-center">Target Visit</th>
                                        <th class="text-center">Call (PJP)</th>
                                        <th class="text-center">Extra Call</th>
                                        <th class="text-center">Persentase (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="8" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_kategori" style="position: relative;">
                        <div id="loading_breakdown_kategori" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #605ca8;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-th-large text-purple"></i> Rincian Ketercapaian Kategori
                                </h4>
                                <small class="text-muted">Jumlah visit per jenis kategori</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_kategori" style="font-size: 12px; width: 100%;">
                                <thead style="background: #605ca8; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Type ID</th>
                                        <th>Nama Kategori Outlet</th>
                                        <th class="text-center">Jumlah Outlet</th>
                                        <th class="text-center">Jumlah Visit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="5" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_user_wajib" style="position: relative;">
                        <div id="loading_breakdown_user_wajib" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #f39c12;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-user-md text-warning"></i> Rincian User Wajib
                                </h4>
                                <small class="text-muted">Target dan realisasi user</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_user_wajib" style="font-size: 12px; width: 100%;">
                                <thead style="background: #d58512; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Nama Spesialisasi</th>
                                        <th>Tipe Spesialis</th>
                                        <th class="text-center">Target</th>
                                        <th class="text-center">Aktual</th>
                                        <th class="text-center">Persentase (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="6" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_detailing" style="position: relative;">
                        <div id="loading_breakdown_detailing" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #39cccc;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-medkit text-teal"></i> Rincian Detailing Produk
                                </h4>
                                <small class="text-muted">Target dan realisasi detailing produk</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_detailing" style="font-size: 12px; width: 100%;">
                                <thead style="background: #259d9d; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Product ID</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Target</th>
                                        <th class="text-center">Aktual</th>
                                        <th class="text-center">Persentase (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="6" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_selling" style="position: relative;">
                        <div id="loading_breakdown_selling" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #dd4b39;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-shopping-cart text-red"></i> Rincian Selling Produk
                                </h4>
                                <small class="text-muted">Target dan realisasi selling produk</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_selling" style="font-size: 12px; width: 100%;">
                                <thead style="background: #c23321; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Product ID</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Target</th>
                                        <th class="text-center">Aktual</th>
                                        <th class="text-center">Persentase (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="6" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_join_visit" style="position: relative;">
                        <div id="loading_breakdown_join_visit" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #d81b60;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-users text-maroon"></i> Rincian Join Visit
                                </h4>
                                <small class="text-muted">Daftar join visit ke profesional</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_join_visit" style="font-size: 12px; width: 100%;">
                                <thead style="background: #b5154f; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th class="text-center">Periode</th>
                                        <th>Salesman</th>
                                        <th>Outlet</th>
                                        <th>Nama Professional</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="7" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_breakdown_tpe_aktif" style="position: relative;">
                        <div id="loading_breakdown_tpe_aktif" class="overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:10; display:none; text-align:center; padding-top:40px;">
                            <i class="fa fa-refresh fa-spin" style="font-size: 28px; color: #001f3f;"></i>
                        </div>
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-xs-12">
                                <h4 style="margin: 5px 0 0; font-weight: 700;">
                                    <i class="fa fa-id-badge text-navy"></i> Rincian TPE Aktif
                                </h4>
                                <small class="text-muted">Daftar TPE aktif</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover fixed-table" id="table_breakdown_tpe_aktif" style="font-size: 12px; width: 100%;">
                                <thead style="background: #001f3f; color: #fff;">
                                    <tr>
                                        <th class="text-center" style="width: 40px;">No</th>
                                        <th>Salesman ID</th>
                                        <th>Nama Salesman</th>
                                        <th>Tipe Sales</th>
                                        <th>Area</th>
                                        <th>Leader</th>
                                        <th class="text-center" style="width: 70px;">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="7" class="text-center text-muted">Klik 'Load Data' untuk memuat data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_detail_tpe" tabindex="-1" role="dialog" aria-labelledby="modal_detail_tpe_label">
        <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header" style="background:#0073b7; color:#fff; border-radius:4px 4px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal_detail_tpe_label">
                        Detail Log TPE: <span id="modal_tpe_name" style="font-weight: 700;">-</span>
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
                            <li class="active"><a href="#tab_sub_kunjungan" data-toggle="tab">Kunjungan (<span id="count_log_kunjungan">0</span>)</a></li>
                            <li><a href="#tab_sub_detailing" data-toggle="tab">Detailing (<span id="count_log_detailing">0</span>)</a></li>
                            <li><a href="#tab_sub_absensi" data-toggle="tab">Presensi (<span id="count_log_absensi">0</span>)</a></li>
                        </ul>
                        <div class="tab-content" style="padding: 15px 0;">
                            <div class="tab-pane active" id="tab_sub_kunjungan">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover fixed-table" id="table_modal_kunjungan" style="font-size: 12px; width: 100%;">
                                        <thead style="background: #eef2f7;">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th>Tanggal</th>
                                                <th>Outlet ID</th>
                                                <th>Nama Outlet</th>
                                                <th>Tipe</th>
                                                <th class="text-center">Check-In</th>
                                                <th class="text-center">Check-Out</th>
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

                            <div class="tab-pane" id="tab_sub_detailing">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover fixed-table" id="table_modal_detailing" style="font-size: 12px; width: 100%;">
                                        <thead style="background: #eef2f7;">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th>Tanggal</th>
                                                <th>Nama Professional</th>
                                                <th>Spesialisasi</th>
                                                <th>Outlet</th>
                                                <th>Channel</th>
                                                <th>Produk Didetailkan</th>
                                                <th class="text-center">Status Detailing</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_sub_absensi">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover fixed-table" id="table_modal_absensi" style="font-size: 12px; width: 100%;">
                                        <thead style="background: #eef2f7;">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th>Tanggal</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Absen Awal</th>
                                                <th class="text-center">Absen Akhir</th>
                                                <th>Keterangan</th>
                                                <th class="text-center">Foto Awal</th>
                                                <th class="text-center">Foto Akhir</th>
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

    <div class="modal fade" id="modal_detail_visit" tabindex="-1" role="dialog" aria-labelledby="modalDetailVisitLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px; width: 95%;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
                <div class="modal-header bg-primary" style="background: #b5154f; color: #fff; padding: 15px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.9;">&times;</button>
                    <h4 class="modal-title" id="modalDetailVisitLabel" style="font-weight: 600;">
                        <i class="fa fa-users"></i> Detail Supervisi Join Visit
                    </h4>
                </div>
                <div class="modal-body" style="padding: 20px; max-height: calc(85vh - 120px); overflow-y: auto;">
                    <div class="box box-solid box-default" style="margin-bottom: 20px; border: 1px solid #e5e7eb; border-radius: 6px;">
                        <div class="box-header with-border" style="background: #f8fafc;">
                            <h3 class="box-title" style="font-size: 14px; font-weight: 700; color: #374151;">
                                <i class="fa fa-info-circle text-primary"></i> Informasi Join Visit
                            </h3>
                        </div>
                        <div class="box-body no-padding">
                            <table class="table table-bordered modal-detail-info-table" style="margin: 0; font-size: 13px;">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%; background: #f9fafb;">Periode / Tanggal</th>
                                        <td id="detail_visit_periode">-</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f9fafb;">Salesman</th>
                                        <td><span id="detail_visit_salesman_name" style="font-weight: 600;">-</span> <span id="detail_visit_salesman_id" class="text-muted">(-)</span></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f9fafb;">Outlet</th>
                                        <td><span id="detail_visit_customer_name" style="font-weight: 600;">-</span> <span id="detail_visit_customer_id" class="text-muted">(-)</span></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f9fafb;">Professional / User</th>
                                        <td><span id="detail_visit_prof_name" style="font-weight: 600; color: #b5154f;">-</span> <span id="detail_visit_prof_id" class="text-muted">(-)</span></td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f9fafb;">Status</th>
                                        <td id="detail_visit_status">-</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #f9fafb;">Keterangan Awal</th>
                                        <td id="detail_visit_keterangan" style="white-space: pre-wrap;">-</td>
                                    </tr>
                                    <tr id="row_detail_visit_reason" style="display: none;">
                                        <th style="background: #fef2f2; color: #dc2626;">Alasan Reject</th>
                                        <td id="detail_visit_reason" style="color: #dc2626; white-space: pre-wrap;">-</td>
                                    </tr>
                                    <tr id="row_detail_visit_approval" style="display: none;">
                                        <th style="background: #f9fafb;">Disetujui Oleh</th>
                                        <td><span id="detail_visit_approved_by">-</span> (<span id="detail_visit_approved_date">-</span>)</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #ecfdf5; color: #065f46;">Keterangan Visit</th>
                                        <td id="detail_visit_keterangan_visit" style="font-weight: 500; white-space: pre-wrap;">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="box box-solid box-default" style="margin-bottom: 0; border: 1px solid #e5e7eb; border-radius: 6px;">
                        <div class="box-header with-border" style="background: #f8fafc;">
                            <h3 class="box-title" style="font-size: 14px; font-weight: 700; color: #374151;">
                                <i class="fa fa-camera text-primary"></i> Foto Dokumentasi (<span id="detail_visit_doc_count">0</span>)
                            </h3>
                        </div>
                        <div class="box-body" style="padding: 15px;">
                            <div id="detail_visit_gallery" class="img-gallery-container" style="display: flex; flex-wrap: wrap; gap: 15px;"></div>
                            <div id="detail_visit_gallery_empty" class="alert alert-info" style="display: none; margin: 0;">
                                <i class="fa fa-info-circle"></i> Tidak ada foto dokumentasi.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f9fafb; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_preview_image" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg" style="max-width: 800px; width: 90%;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden; background: #000;">
                <div class="modal-header" style="border: none; padding: 10px 15px; color: #fff;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.8; font-size: 24px;">&times;</button>
                    <h5 class="modal-title" id="preview_image_title" style="color: #fff; font-weight: 500;">Preview Foto</h5>
                </div>
                <div class="modal-body" style="padding: 0; text-align: center; background: #111;">
                    <img id="preview_img_element" src="" style="max-width: 100%; max-height: 80vh; object-fit: contain; margin: 0 auto; display: block;" alt="Foto Dokumentasi">
                </div>
                <div class="modal-footer" style="border: none; padding: 10px 15px; background: #000; text-align: right;">
                    <a id="preview_img_download_link" href="#" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Buka Gambar Asli</a>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</section>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= base_url() . 'assets/modules/dashboard_tts/dashboard-tts.js' ?>?v=1.1"></script>
