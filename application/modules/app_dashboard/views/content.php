<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Dashboard
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">


    <div class="row">
        <div class="col-md-12">
            <div class="box">

                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <!-- /.col -->

                        <div class="col-md-4">
                            <select id="satker" name="satker" class="form-control"></select>
                        </div>
                        <div class="col-md-8">
                            <select id="riset" name="riset" class="form-control"></select>

                        </div>
                        <div id="labelriset" class="col-md-12" style="padding-top: 20px">
                            <p class="text-center">
                                <strong></strong>
                            </p>
                        </div>

                        <div class="col-md-12">
                            <canvas id="chart-1" height="75"></canvas>
                            <!-- /.progress-group -->
                        </div>

                        <div id="label-rgd" class="col-md-12" style="padding-top: 20px">
                            <p class="text-center">
                                <strong></strong>
                            </p>
                        </div>

                        <div class="col-md-12">
                            <canvas id="chart-2" height="75"></canvas>
                            <!-- /.progress-group -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./box-body -->
                <!-- /.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->


</section>

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/app_dashboard/dashboard-content.js' ?>"></script>