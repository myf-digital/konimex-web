<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Set Quota <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Set Quota</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Set Quota</h3>
                </div>

                <form id="fm-set_quota" role="form" method="post">
                    <div class="box-body col-md-12">
							<!--<div class="form-group col-md-4">
								<label for="start_periode">Start Periode</label>
                                    <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="start_periode" placeholder="Start Periode" type="text" class="form-control datepicker" name="start_periode" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-4">
								<label for="end_periode">End Periode</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="end_periode" placeholder="End Periode" type="text" class="form-control datepicker" name="end_periode" readonly>
                                    </div>
							</div>-->
                    </div>

                    <div class="box-footer">
                        <button id="btn-preview-form" type="button" class="btn btn-success fa fa-book"> Set Quota Budget</button>
                        <!--<button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>-->
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="tbl-content" class="box-table box-success" style="margin:10px; overflow-y: auto; max-height: 100%; max-width: 99%; white-space: nowrap;">
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/set_quota_budget/set-quota-budget-form.js' ?>"></script>
