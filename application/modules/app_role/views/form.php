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
                            <h5 class="font-weight-bold h5-title">Mapping Target</h5>
                            <div class="table-detail">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr class="bg-f5">
                                            <th class="th-detail">Periode</th>
                                            <th class="th-detail">Target HK (Hari Kerja)</th>
                                            <th class="th-detail">Target DUB</th>
                                            <th class="th-detail">Target Call DUB</th>
                                            <th class="th-detail">Target Call Visit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="form_body">
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="periode" id="periode" placeholder="Periode">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="target_hk" id="target_hk" placeholder="Target HK">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="target_dub" id="target_dub" placeholder="Target DUB">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="target_call_dub" id="target_call_dub" placeholder="Target Call DUB">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="target_call_visit" id="target_call_visit" placeholder="Target Call Visit">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
