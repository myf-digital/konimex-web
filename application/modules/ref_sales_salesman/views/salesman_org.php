<style>
    .node {
        width: 200px !important;
        padding: 10px;
        text-align: center;
    }
    .node div {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .node div:first-of-type {
        white-space: normal;
        word-break: break-word;
        line-height: 1.2;
        max-height: 38px;
        overflow: hidden;
    }
    .node img {
        display: block;
        margin: 0 auto 5px;
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Struktur Organisasi <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Struktur Organisasi</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="salesman-org" class="box-table box-success">
                <div class="box-body">
                    <div id="tree"></div>
                </div>
                <div class="box-footer">
                    <a id="btn-back" href="javascript:void(0)" class="btn btn-warning fa fa-backward"> Back</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS content -->
<script src="https://balkan.app/js/OrgChart.js"></script>
<script src="<?php echo base_url() . 'assets/modules/ref_sales_salesman/sales-salesman-org.js' ?>"></script>