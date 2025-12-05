<style>
    .chart-container {
        height: 330px;
    }
    .dashboard-card {
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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
        <div class="col-xs-6">
            <h4 class="section-title">Report Productivity</h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartProductivity"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <h4 class="section-title">Performa PAR</h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartPerformance"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <h4 class="section-title">Summary Man Power</h4>
            <div class="dashboard-card mb-4">
                <div class="chart-container">
                    <canvas id="chartSummary"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS content -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?php echo base_url() . 'assets/modules/dashboard_chart/dashboard-chart-content.js' ?>"></script>
