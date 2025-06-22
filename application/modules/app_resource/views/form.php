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
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Resource</h3>
                </div>

                <form id="fm-resource" role="form" method="post">
                    <div class="box-body">
                        <div class="form-group col-md-2">
                            <label for="nip">NIP</label>
                            <input name="nip" class="form-control" placeholder="Nip">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Name</label>
                            <input name="name" class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="email">Email</label>
                            <input name="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="telepon">Telepon</label>
                            <input name="telepon" class="form-control" placeholder="Telepon">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="idjabatan">Jabatan</label>
                            <select id="idjabatan-id" name="idjabatan" class="form-control" placeholder="Jabatan"></select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="role_id">Role</label>
                            <select id="role-id" name="role_id" class="form-control" placeholder="Role"></select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="username">Username</label>
                            <input name="username" id="username-id" class="form-control" placeholder="Username">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="password">Password</label>
                            <input name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                <div class="box-header with-border">
                    <h3 class="box-title">Restrict</h3>
                </div>
                <div class="box-body">
                        <!--<div class="form-group col-md-2">
                            <label for="siteid">Site</label>
                            <select id="siteid-id" name="siteid" class="form-control" placeholder="Site"></select>
                        </div>-->
                        <div class="form-group col-md-3">
                            <label for="regionalid">Regional</label>
                            <select id="regionalid-id" name="regionalid[]" class="form-control" multiple = "multiple" placeholder="Regional"></select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="areaid">Area</label>
                            <select id="areaid-id" name="areaid[]" class="form-control" multiple = "multiple" placeholder="Area"></select>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="subareaid">City</label>
                            <select id="subareaid-id" name="subareaid[]" class="form-control" multiple = "multiple" placeholder="City"></select>
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
