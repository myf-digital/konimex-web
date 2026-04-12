<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup Site <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup Site</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup Site</h3>
                </div>

                <form id="fm-setup-site" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="siteid">Siteid</label>
								<input name="siteid" class="form-control" placeholder="Siteid">
							</div>
							<div class="form-group">
								<label for="nama_site">Nama Site</label>
								<input name="nama_site" class="form-control" placeholder="Nama Site">
							</div>
							<div class="form-group">
								<label for="tanggal">Tanggal</label>
								<input name="tanggal" class="form-control" placeholder="Tanggal">
							</div>
							<div class="form-group">
								<label for="latitude">Latitude</label>
								<input name="latitude" class="form-control" placeholder="Latitude">
							</div>
							<div class="form-group">
								<label for="longitude">Longitude</label>
								<input name="longitude" class="form-control" placeholder="Longitude">
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
<script src="<?php echo base_url() . 'assets/modules/conf_setup_site/setup-site-form.js' ?>"></script>
