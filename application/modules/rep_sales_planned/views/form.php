<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Report Planned <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Report Planned</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Planned</h3>
                </div>

                <form id="fm-report-planned" role="form" method="post">
                    <div class="box-body">
                        <div class="form-group col-md-4">
                            <label for="salesmanid">TPE</label>
                            <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="TPE"></select>
                        </div>
                    </div>
                </form>
                <div class="box-footer">
                    <button id="btn-preview-form" type="button" class="btn btn-success fa fa-eye"> View</button>
                    <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div id="salesman-info-container">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="calendar-wrapper">
                    <div class="calendar-header">
                        <h3 id="calendar-month-year"></h3>
                        <div>
                            <button type="button" class="calendar-nav-btn" id="btn-prev-month"><i class="fa fa-chevron-left"></i></button>
                            <button type="button" class="calendar-nav-btn" id="btn-today">Today</button>
                            <button type="button" class="calendar-nav-btn" id="btn-next-month"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="calendar-grid" id="calendar-grid-container">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="planned-detail-modal" class="planned-detail-modal" style="display: none;">
        <div class="planned-modal-content">
            <div class="planned-modal-header">
                <h4 id="planned-modal-title">Planned Details</h4>
                <button type="button" class="planned-modal-close" id="btn-close-modal">&times;</button>
            </div>
            <div class="planned-modal-body" id="planned-modal-body">
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/rep_sales_planned/form.js' ?>"></script>