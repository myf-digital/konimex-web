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
							<select id="regionalid-id" name="regionalid" class="form-control" placeholder="Regional"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="areaid">Area</label>
							<select id="areaid-id" name="areaid" class="form-control" placeholder="Area"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="subareaid">Sub Area</label>
							<select id="subareaid-id" name="subareaid" class="form-control" placeholder="Sub Area"></select>
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
