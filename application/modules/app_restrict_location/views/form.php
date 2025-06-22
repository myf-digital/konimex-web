<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Restrict Location <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Restrict Location</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Restrict Location</h3>
                </div>

                <form id="fm-restrict-location" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="id">Id</label>
								<input name="id" class="form-control" placeholder="Id">
							</div>
							<div class="form-group">
								<label for="resource_id">Resource Id</label>
								<input name="resource_id" class="form-control" placeholder="Resource Id">
							</div>
							<div class="form-group">
								<label for="idregion">Idregion</label>
								<input name="idregion" class="form-control" placeholder="Idregion">
							</div>
							<div class="form-group">
								<label for="idarea">Idarea</label>
								<input name="idarea" class="form-control" placeholder="Idarea">
							</div>
							<div class="form-group">
								<label for="idcity">Idcity</label>
								<input name="idcity" class="form-control" placeholder="Idcity">
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
<script src="<?php echo base_url() . 'assets/modules/app_restrict_location/restrict-location-form.js' ?>"></script>
