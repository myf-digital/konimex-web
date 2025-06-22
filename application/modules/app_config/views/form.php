<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Config <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Config</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Config</h3>
                </div>

                <form id="fm-config" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="configuration">Configuration</label>
								<input name="configuration" class="form-control" placeholder="Configuration">
							</div>
							<div class="form-group">
								<label for="value">Value</label>
								<input name="value" class="form-control" placeholder="Value">
							</div>
							<div class="form-group">
								<label for="description">Description</label>
								<input name="description" class="form-control" placeholder="Description">
							</div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/app_config/config-form.js' ?>"></script>
