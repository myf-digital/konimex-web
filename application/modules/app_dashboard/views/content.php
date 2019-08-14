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

                        <div id="chartriset" class="col-md-12">
                            <canvas id="chart-1" height="75"></canvas>
                            <!-- /.progress-group -->
                        </div>

                        <div id="label-rgd" class="col-md-12" style="padding-top: 20px">

                            <p class="text-center">
                                <i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;Total
                                <strong>(0)</strong>&nbsp;&nbsp;Responden <strong>(0)</strong>
                            </p>
                        </div>

                        <div class="col-md-12">
                            <canvas id="chart-2" height="75"></canvas>
                            <!-- /.progress-group -->
                        </div>
                        <div class="col-md-12">
                            <table id="tbl-saran" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Responden</th>
                                    <th>Satker</th>
                                    <th>Waktu</th>
                                    <th>Saran</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!--
                                <tr>
                                    <td>John</td>
                                    <td>Doe</td>
                                    <td>john@example.com</td>
                                    <td>john@example.com</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td>Mary</td>
                                    <td>Moe</td>
                                    <td>mary@example.com</td>
                                    <td>mary@example.com</td>
                                    <td>mary@example.com</td>
                                </tr>
                                <tr>
                                    <td>July</td>
                                    <td>Dooley</td>
                                    <td>july@example.com</td>
                                    <td>july@example.com</td>
                                    <td>july@example.com</td>
                                </tr>
                                -->
                                </tbody>
                            </table>
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