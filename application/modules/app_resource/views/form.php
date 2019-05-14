<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Resource
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Resource</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Resource</h3>
                </div>

                <form id="fm-resource" role="form" method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input name="nip" class="form-control" placeholder="Nip">
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <input name="type" class="form-control" placeholder="Type">
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input name="name" class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input name="telepon" class="form-control" placeholder="Telepon">
                        </div>
                        <div class="form-group">
                            <label for="id_satker">Satuan Kerja</label>
                            <select id="id-satker" name="id_satker" class="form-control" placeholder="Satker"></select>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select id="role-id" name="role_id" class="form-control" placeholder="Role"></select>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input name="username" class="form-control" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input name="password" class="form-control" placeholder="Password">
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
<script src="<?php echo base_url() . 'assets/modules/app_resource/resource-form.js' ?>"></script>
