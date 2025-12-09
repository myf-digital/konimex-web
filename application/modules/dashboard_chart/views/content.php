<style>
    .chart-container {
        position: relative;
        width: 100%;
        height: 350px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #chartParmaCoverage {
        max-width: 100% !important;
        max-height: 100% !important;
    }
    .dashboard-card {
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .mt-3r {
        margin-top: 3rem;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Dashboard Chart
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard Chart</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="form-group col-md-3">
                        <label for="periode">Periode</label>
                        <select id="periode_id" name="periode" class="form-control" placeholder="Select Periode">
                            <option value="today" selected>Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                            <option value="custom">Pilih Tanggal</option>
                        </select>
                    </div>
                    <div id="date_range" class="hidden">
                        <div class="form-group col-md-2">
                            <label for="start_periode">Start Periode</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                                <input id="start_periode" placeholder="Periode" type="text" class="form-control datepicker" name="start_periode" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="end_periode">End Periode</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                                <input id="end_periode" placeholder="End Periode" type="text" class="form-control datepicker" name="end_periode" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <button
                            id="btn_load_data"
                            type="button"
                            class="btn btn-warning fa fa-refresh"
                            style="margin-top: 2.7rem;"
                        > Load Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <h4 class="section-title">
                Report Productivity
                <span id="labelProductivity"></span>
            </h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartProductivity"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <h4 class="section-title">
                Performance PAR-MA
                <span id="labelPerformance"></span>
            </h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartPerformance"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3r">
        <div class="col-md-6 col-sm-12">
            <h4 class="section-title">
                Preseni PAR-MA
                <span id="labelPresensi"></span>
            </h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartPresensi"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <h4 class="section-title">
                Man Power PAR-MA
                <span id="labelParmaCoverage"></span>
            </h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartParmaCoverage"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS content -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?php echo base_url() . 'assets/modules/dashboard_chart/dashboard-chart-content.js' ?>"></script>
