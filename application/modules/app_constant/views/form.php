<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Constant <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Constant</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Constant</h3>
                </div>

                <form id="fm-constant" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="app_variable_id">App Variable Id</label>
								<input name="app_variable_id" class="form-control" placeholder="App Variable Id">
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
<script src="<?php echo base_url() . 'assets/modules/app_constant/constant-form.js' ?>"></script>
