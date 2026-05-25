<style>
    .dashboard-card {
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    #col-barat .dashboard-card,
    #col-timur .dashboard-card {
        margin-bottom: 0;
    }
    #col-barat > div + div,
    #col-timur > div + div {
        margin-top: 20px;
    }
    .chart-container {
        position: relative;
        width: 100%;
        height: 300px;
    }
    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin: 0 0 16px;
        padding: 10px 14px;
        background: #f4f4f4;
        border-left: 4px solid #00a65a;
        border-radius: 4px;
    }
    #charts-row {
        margin-bottom: 8px;
    }
    #charts-row .col-md-4 {
        padding-left: 10px;
        padding-right: 10px;
    }
    #col-barat, #col-timur {
        padding-top: 4px;
    }
    .clickable-card:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    }
</style>

<section class="content-header">
    <h1>Dashboard PJP Daily</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">PJP Daily</li>
    </ol>
</section>

<section id="content-main" class="content">
    <!-- Filter -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="form-group col-md-2">
                        <label>Start Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                            <input id="start_date" type="text" class="form-control datepicker" readonly>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label>End Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                            <input id="end_date" type="text" class="form-control datepicker" readonly>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <button id="btn_load" type="button" class="btn btn-warning" style="margin-top:2.7rem;">
                            <i class="fa fa-refresh"></i> Load Data
                        </button>
                        <button id="btn_export" type="button" class="btn btn-success" style="margin-top:2.7rem; margin-left:6px;" disabled>
                            <i class="fa fa-file-excel-o"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nasional + per-region summary -->
    <div id="charts-row" class="row"></div>

    <!-- Area detail -->
    <div id="row-detail" class="row" style="margin-top: 16px; display:none;">
        <div class="col-xs-12">
            <div class="section-title"><i class="fa fa-map-marker"></i> <span id="title-detail"></span></div>
        </div>
        <div id="col-detail" class="col-xs-12"></div>
    </div>

    <!-- Subarea detail -->
    <div id="row-detail-area" class="row" style="margin-top: 16px; display:none;">
        <div class="col-xs-12">
            <div class="section-title"><i class="fa fa-map-pin"></i> <span id="title-detail-area"></span></div>
        </div>
        <div id="col-detail-area" class="col-xs-12"></div>
    </div>

    <!-- Final detail -->
    <div id="row-detail-subarea" class="row" style="margin-top: 16px; display:none;">
        <div class="col-xs-12">
            <div class="section-title"><i class="fa fa-circle"></i> <span id="title-detail-subarea"></span></div>
        </div>
        <div id="col-detail-subarea" class="col-xs-12"></div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script src="<?php echo base_url() . 'assets/modules/dashboard_pjp_daily/dashboard-pjp-daily-content.js' ?>"></script>
