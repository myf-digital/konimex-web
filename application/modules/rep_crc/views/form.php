<!-- Content Header (Page header) -->
<style type="text/css">

    .report-table {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 1em;
        border-radius: 5px 5px 0 0;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        overflow: auto;
        display: block;
        white-space: nowrap;
    }

    .report-table tbody th {
      background-color: #8BB581;
      color: #ffffff;
      text-align: left;
      font-weight: bold;
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
        Report CRC <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Report CRC</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report CRC</h3>
                </div>

                <form id="fm-report-crc" role="form" method="post">
                    <div class="box-body col-md-12">
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
                        <div class="form-group col-md-3">
                            <label for="account">SubChannel / Account</label>
                            <select id="account-id" name="account" class="form-control" placeholder="Select Account"></select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="account">Outlet</label>
                            <select id="outlet-id" name="outlet" class="form-control" placeholder="Select Outlet"></select>
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
<script src="<?php echo base_url() . 'assets/modules/rep_crc/rep-crc-form.js' ?>"></script>
