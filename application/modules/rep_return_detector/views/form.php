<!-- Content Header (Page header) -->
<style type="text/css">

    .report-table {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 1em;
        border-radius: 5px 5px 0 0;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        white-space: nowrap;
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
        Report SLOB <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Report SLOB</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report SLOB</h3>
                </div>

                <form id="fm-report-return" role="form" method="post">
                    <div class="box-body col-md-12">
                        <div class="form-group col-md-3">
                            <label for="brand">Brand</label>
                            <select id="brand-id" name="brand" class="form-control" placeholder="Select Brand"></select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="brand">Product</label>
                            <select id="product-id" name="product" class="form-control" placeholder="Select Product"></select>
                        </div>
                    </div>
                </form>
                <div class="box-footer">
                    <button id="btn-preview-form" type="button" class="btn btn-success fa fa-eye"> View</button>
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
<script src="<?php echo base_url() . 'assets/modules/rep_return_detector/rep-return-detector-form.js' ?>"></script>
