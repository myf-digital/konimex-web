<!-- Content Header (Page header) -->
<style type="text/css">

    .filter-date {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 1em;
        min-width: 400px;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .filter-date thead tr {
        background-color: #3d8cbc;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
    }

    .filter-date th,
    .filter-date td {
        padding: 12px 15px;
    }

    .filter-date tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .filter-date tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .filter-date tbody tr:last-of-type {
        border-bottom: 2px solid #3d8cbc;
    }

    .filter-date tbody tr.active-row {
        font-weight: bold;
        color: #009879;
    }

    .report-table {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 1em;
        min-width: 400px;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .report-table tbody th {
        color: #ffffff;
        text-align: left;
        font-weight: bold;
        background: linear-gradient(90deg,rgba(22, 33, 51, 1) 0%, rgba(31, 48, 58, 1) 50%, rgba(45, 68, 73, 1) 100%);
    }

    .report-table th,
    .report-table td {
      padding: 12px 15px;
    }

    .report-table tbody tr {
      border-bottom: 1px solid #dddddd;
    }

    .margin-top-less {
        margin-bottom: -35px
    }
</style>
<section class="content-header">
    <h1>
        Report Sales <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Report Sales</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Sales</h3>
                </div>

                <form id="fm-report-sales-pjp" role="form" method="post">
                    <div class="box-body">
                        <div class="form-group col-md-4">
                            <label for="salesid">Sales ID</label>
                            <select id="salesid-id" name="salesid" class="form-control" placeholder="Sales ID"></select>
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
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div id="tbl-content" class="box-table box-success">

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/rep_sales_pjp/rep-salespjp-form.js' ?>"></script>
