<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup FJP <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup FJP</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup FJP</h3>
                </div>

                <form id="fm-setup-pjp" role="form" method="post">
                    <div class="box-body">
						<input name="siteid" type=hidden>
						<input name="customerid_m" type=hidden>
						<input name="customerid" type=hidden>
						<input name="salesmanid" type=hidden>
						<div class="form-group  col-xs-12 col-sm-12 col-md-1">
							<label for="weekaktif">Week Aktif</label>
							<input name="week_aktif" class="form-control" placeholder="Week Aktif" disabled>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-5">
							<label for="salesmanid">TPE (MD/SPG/MEDREP)</label>
							<select id="salesmanid-id" name="salesmanid_new" class="form-control" placeholder="TPE (MD/SPG/MEDREP)"></select>
						</div>
						<div class="form-group  col-xs-12 col-sm-12 col-md-6">
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-1">
							<label for="customerid">OutletID</label>
							<input name="customerid" class="form-control" placeholder="OutletID" disabled>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-2">
							<label for="kode_outlet">Kode Outlet</label>
							<input name="kode_outlet" class="form-control" placeholder="Kode Outlet" disabled>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-5">
							<label for="nama_customer">Outler Name</label>
							<input name="nama_customer" class="form-control" placeholder="Outler Name" disabled>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-2">
							<label for="nama_class">Sub Channel / Account</label>
							<input name="nama_class" class="form-control" placeholder="Sub Channel / Account" disabled>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-2">
							<label for="mcc">DC</label>
							<input name="mcc" class="form-control" placeholder="DC" disabled>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week1">Week 1</label>
							<select id="week1-id" name="week1[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week2">Week 2</label>
							<select id="week2-id" name="week2[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week3">Week 3</label>
							<select id="week3-id" name="week3[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week4">Week 4</label>
							<select id="week4-id" name="week4[]" class="form-control" placeholder="Days" ></select>
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
<script src="<?php echo base_url() . 'assets/modules/conf_setup_pjp/setup-pjp-form.js' ?>"></script>
