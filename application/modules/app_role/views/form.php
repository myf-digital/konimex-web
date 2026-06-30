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
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Role</h3>
                </div>

                <form id="fm-role" role="form" method="post">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
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
                            </div>
                            <div class="col-md-6" id="custom_role_group" style="display: none;">
                                <div class="form-group">
                                    <label for="role_name_desc">Custom Role Name</label>
                                    <input id="role_name_desc" name="role_name_desc" class="form-control" placeholder="Enter Custom Role Name">
                                </div>
                            </div>
                        </div>
                        
                        <div id="mapping_target_group" style="display: none;">
                            <div class="row" style="margin-left: 0; margin-right: 0; margin-bottom: 20px;">
                                <div class="col-md-6" style="border: 1px solid #eee; padding: 15px; border-radius: 4px;">
                                    <h5 class="font-weight-bold h5-title" style="margin-top: 0; margin-bottom: 15px;">Mapping Target DUB</h5>
                                    <input type="hidden" name="periode" id="periode" value="<?= date('Y-m') ?>">
                                    <div class="table-responsive table-detail">
                                        <table class="table table-striped table-hover" style="margin-bottom: 0;">
                                            <thead>
                                                <tr class="bg-f5">
                                                    <th class="th-detail">Target HK (Hari Kerja)</th>
                                                    <th class="th-detail">Target DUB</th>
                                                    <th class="th-detail">Target Call DUB</th>
                                                    <th class="th-detail">Target Call Visit</th>
                                                </tr>
                                            </thead>
                                            <tbody id="form_body">
                                                <tr>
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
                            
                            <div class="row" style="margin-left: 0; margin-right: 0;">
                                <!-- Mapping Spesialisasi -->
                                <div class="col-md-6" style="border: 1px solid #eee; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                                    <input type="hidden" name="periode_spesialisasi" id="periode_spesialisasi" value="<?= date('Y-m') ?>">
                                    <div class="form-group">
                                        <label for="spesialisasiid">Mapping Spesialisasi</label>
                                        <select id="spesialisasiid" name="spesialisasiid[]" class="form-control" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>
                                    <div class="table-responsive" style="margin-top: 1rem;">
                                        <table class="table table-bordered table-hover" id="tbl-spesialisasi-target" style="display: none; margin-bottom: 0;">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th>Spesialisasi</th>
                                                    <th style="width: 150px; text-align: center;">Target</th>
                                                    <th style="width: 50px; text-align: center;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Mapping Produk -->
                                <div class="col-md-6" style="border: 1px solid #eee; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                                    <input type="hidden" name="periode_product" id="periode_product" value="<?= date('Y-m') ?>">
                                    <div class="form-group">
                                        <label for="productid">Mapping Produk</label>
                                        <select id="productid" name="productid[]" class="form-control" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>
                                    <div class="table-responsive" style="margin-top: 1rem;">
                                        <table class="table table-bordered table-hover" id="tbl-product-target" style="display: none; margin-bottom: 0;">
                                            <thead>
                                                <tr class="bg-primary">
                                                    <th>Produk</th>
                                                    <th style="width: 120px; text-align: center;">Target Detailing</th>
                                                    <th style="width: 120px; text-align: center;">Target Qty</th>
                                                    <th style="width: 50px; text-align: center;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__rendered li.select2-selection__choice:not(.select2-selection__choice-counter) {
        display: none !important;
    }
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background: #3c8dbc !important;
		border: 1px solid #3c8dbc !important;
	}
	.datepicker table thead tr:first-child {
		display: table-row !important;
	}
	.datepicker table thead tr:first-child th {
		display: table-cell !important;
		color: #333 !important;
	}
	.badge-count-select2 {
		background: #3c8dbc !important;
		border: 1px solid #3c8dbc !important;
		padding: 0 8px; 
		margin-top: 5px; 
		margin-right: 5px; 
		float: left; 
		border-radius: 4px; 
		font-size: 12px; 
		font-weight: bold;
		color: #fff; 
		height: 24px; 
		line-height: 22px;
	}
</style>
