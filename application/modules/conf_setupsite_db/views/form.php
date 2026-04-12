<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setupsite Db <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setupsite Db</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setupsite Db</h3>
                </div>

                <form id="fm-setupsite-db" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="Id">Id</label>
								<input name="Id" class="form-control" placeholder="Id">
							</div>
							<div class="form-group">
								<label for="siteid">Siteid</label>
								<input name="siteid" class="form-control" placeholder="Siteid">
							</div>
							<div class="form-group">
								<label for="hostname">Hostname</label>
								<input name="hostname" class="form-control" placeholder="Hostname">
							</div>
							<div class="form-group">
								<label for="dbname">Dbname</label>
								<input name="dbname" class="form-control" placeholder="Dbname">
							</div>
							<div class="form-group">
								<label for="user">User</label>
								<input name="user" class="form-control" placeholder="User">
							</div>
							<div class="form-group">
								<label for="pwd">Pwd</label>
								<input name="pwd" class="form-control" placeholder="Pwd">
							</div>
							<div class="form-group">
								<label for="jeniskoneksi">Jeniskoneksi</label>
								<input name="jeniskoneksi" class="form-control" placeholder="Jeniskoneksi">
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
<script src="<?php echo base_url() . 'assets/modules/conf_setupsite_db/setupsite-db-form.js' ?>"></script>
