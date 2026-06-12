<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Role
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Role</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Role</h3>
                </div>

                <form id="fm-role" role="form" method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="role_name">Role Name</label>
                            <select id="role_name" name="role_name" class="form-control">
                                <option value="">Select Role Name</option>
                                <option value="other">Lainnya (Input Manual)</option>
                                <option value="MEDREP">MEDREP - MEDICAL REP</option>
                                <option value="MRC">MRC - MEDICAL REP COORDINATOR</option>
                                <option value="ASS">ASS - AREA SALES SUPERVISOR</option>
                                <option value="ASM">ASM - AREA SALES MANAGER</option>
                                <option value="SM">SM - SALES MANAGER</option>
                                <option value="GME">GME - GENERAL MANAGER EB</option>
                                <option value="ESO">ESO - ETHICAL SUPPORT OFFICER</option>
                                <option value="PA">PA - PENATA ADM</option>
                                <option value="PM">PM - PRODUCT MANAGER</option>
                                <option value="PE">PE - PRODUCT EXECUTIVE</option>
                            </select>
                        </div>
                        <div class="form-group" id="custom_role_group" style="display: none;">
                            <label for="role_name_desc">Custom Role Name</label>
                            <input id="role_name_desc" name="role_name_desc" class="form-control" placeholder="Enter Custom Role Name">
                        </div>
                        <div class="form-group">
                            <label for="target_dub">Target DUB</label>
                            <input type="number" min="1" name="target_dub" class="form-control" placeholder="Target DUB">
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
<script src="<?php echo base_url() . 'assets/modules/app_role/role-form.js' ?>"></script>
