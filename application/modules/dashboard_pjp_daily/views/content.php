<section class="content-header">
    <h1>Dashboard Visit</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Visit</li>
    </ol>
</section>

<section id="content-main" class="content dashboard">
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
                    <div class="form-group col-md-8" style="white-space: nowrap;">
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

    <!-- TPE visit detail -->
    <div id="row-detail-salesman" class="row" style="margin-top: 16px; display:none;">
        <div class="col-xs-12">
            <div class="section-title"><i class="fa fa-user"></i> <span id="title-detail-salesman"></span></div>
        </div>
        <div id="col-detail-salesman" class="col-xs-12"></div>
    </div>
</section>

<!-- Visit Detail Modal -->
<div class="modal fade" id="modal-visit-detail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modal-visit-header" style="background:#0073b7;color:#fff;border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
                <h4 class="modal-title" id="modal-visit-title"></h4>
            </div>
            <div class="modal-body" id="modal-visit-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/modules/dashboard_pjp_daily/dashboard-pjp-daily-content.js' ?>"></script>
