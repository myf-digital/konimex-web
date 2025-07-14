<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Gl Coa <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Gl Coa</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Gl Coa</h3>
                </div>

                <form id="fm-gl-coa" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="coa_id">Coa Id</label>
								<input name="coa_id" class="form-control" placeholder="Coa Id">
							</div>
							<div class="form-group">
								<label for="decr_coa">Decr Coa</label>
								<input name="decr_coa" class="form-control" placeholder="Decr Coa">
							</div>
							<div class="form-group">
								<label for="create_user">Create User</label>
								<input name="create_user" class="form-control" placeholder="Create User">
							</div>
							<div class="form-group">
								<label for="create_date">Create Date</label>
								<input name="create_date" class="form-control" placeholder="Create Date">
							</div>
							<div class="form-group">
								<label for="update_user">Update User</label>
								<input name="update_user" class="form-control" placeholder="Update User">
							</div>
							<div class="form-group">
								<label for="update_date">Update Date</label>
								<input name="update_date" class="form-control" placeholder="Update Date">
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
<script src="<?php echo base_url() . 'assets/modules/ref_gl_coa/gl-coa-form.js' ?>"></script>
