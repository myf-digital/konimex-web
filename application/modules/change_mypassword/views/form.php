<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ganti Password <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Ganti Password</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Ganti Password</h3>
                </div>

                <form id="fm-ganti-password" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="old_password">Password Lama</label>
								<input type="password" name="old_password" class="form-control" placeholder="Password Lama">
							</div>
							<div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <input type="password" name="new_password" id="new-password" class="form-control" placeholder="Password Baru">
                            </div>
                            <div class="form-group">
                                <label for="confir_password">Password Konfirmasi</label>
                                <input type="password" name="confir_password" id="confir-password" class="form-control" placeholder="Password Konfirmasi">
                            </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/change_mypassword/change-pass-form.js' ?>"></script>
