<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Karyawan <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Karyawan</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Karyawan</h3>
                </div>

                <form id="fm-sales-salesman" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-md-3">
							<label for="salesmanid">NIK</label>
							<input name="salesmanid" id="salesmanid-id" class="form-control" value="" placeholder="NIK">
						</div>
						<div class="form-group col-md-5">
							<label for="nama_salesman">Nama Karyawan</label>
							<input name="nama_salesman" class="form-control" placeholder="Nama Karyawan">
						</div>
						<div class="form-group col-md-3">
							<label for="password">Password</label>
							<input name="password" id="password-id" type="password" class="form-control" value="" placeholder="Password">
						</div>
						<div class="form-group  col-md-3">
							<label for="tipe_sales">Tipe Karyawan</label>
							<select id="tipe_sales-id" name="tipe_sales" class="form-control" placeholder="Tipe Karyawan"></select>
						</div>
						<div class="form-group col-md-3">
							<label for="supervisorid">Leader</label>
							<select id="supervisorid" name="supervisorid" class="form-control" placeholder="Leader"></select>
						</div>
						<div class="form-group col-md-2">
							<label for="aktif">Status</label>
							<select id="aktif-id" name="aktif" class="form-control" placeholder="Active/Not Active"></select>
						</div>
						<div class="form-group col-md-2">
							<label for="join_date">Join Date</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-th"></span>
								</div>
								<input id="join_date" placeholder="Join Date" type="text" class="form-control datepicker" name="join_date" readonly>
							</div>
						</div>
						<div class="form-group col-md-2">
							<label for="resign_date">Resign Date</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-th"></span>
								</div>
								<input id="resign_date" placeholder="Resign Date" type="text" class="form-control datepicker" name="resign_date" readonly>
							</div>
						</div>
						
						<div class="form-group col-md-4">
							<label for="regionalid">Regional</label>
							<select id="regionalid-id" name="regionalid[]" class="form-control" multiple="multiple" style="width: 100%;" placeholder="Regional"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="areaid">Area</label>
							<select id="areaid-id" name="areaid[]" class="form-control" multiple="multiple" style="width: 100%;" placeholder="Area"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="subareaid">Sub Area</label>
							<select id="subareaid-id" name="subareaid[]" class="form-control" multiple="multiple" style="width: 100%;" placeholder="Sub Area"></select>
						</div>
						<div class="clearfix"></div>
						<hr style="border-top: 2px solid #eee; margin-top: 2rem; margin-bottom: 2rem;">
						
						<div class="row" style="margin-left: 0; margin-right: 0;margin-bottom: 2rem;">
							<!-- Mapping Sales -->
							<div class="col-md-6" style="border: 1px solid #eee;">
								<div class="row" style="padding-top: 1rem;">
									<div class="form-group col-xs-12 col-sm-12 col-md-6">
										<label for="periode_sales">Periode Target Sales</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</div>
											<input id="periode_sales" placeholder="Periode Target Spesialisasi" type="text" class="form-control yearpicker" name="periode_sales" readonly>
										</div>
									</div>
									<div class="form-group col-xs-12 col-sm-12 col-md-6">
										<label for="total_target">Total Target</label>
										<input id="total_target" name="total_target" class="form-control" placeholder="Total Target" />
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
<script src="<?php echo base_url() . 'assets/modules/ref_sales_salesman/sales-salesman-form.js' ?>"></script>
<style>
	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
		color: #fff;
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
