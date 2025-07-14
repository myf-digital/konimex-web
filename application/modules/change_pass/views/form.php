<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Change Password <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Change Password</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Change Password</h3>
                </div>

                <form id="fm-jabatan" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="jabatan">Jabatan</label>
								<input name="jabatan" class="form-control" placeholder="Jabatan">
							</div>
							<div class="form-group">
								<label for="description">Description</label>
								<input name="description" class="form-control" placeholder="Description">
							</div>
							<div class="form-group">
								<label for="restrict_level">Restrict Level</label>
                                <select id="restrict_level-id" name="restrict_level" class="form-control">
                                </select>
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
<script src="<?php echo base_url() . 'assets/modules/change_pass/change-pass-form.js' ?>"></script>
