<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sales TPE <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Sales TPE</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Sales TPE</h3>
                </div>

                <form id="fm-sales-salesman" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-3">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="SiteID"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="salesmanid">TPE ID</label>
								<input name="salesmanid" class="form-control" value="" placeholder="TPE ID">
							</div>
							<div class="form-group col-md-6">
								<label for="password">Password</label>
								<input name="password" type="password" class="form-control" value="" placeholder="Password">
							</div>
							<div class="form-group col-md-9">
								<label for="nama_salesman">Nama TPE</label>
								<input name="nama_salesman" class="form-control" placeholder="Nama TPE">
							</div>
							<!--<div class="form-group col-md-3">
								<label for="supervisorid">Supervisorid</label>
								<input name="supervisorid" class="form-control" placeholder="Supervisorid">
							</div>-->
							<div class="form-group col-md-6">
								<label for="gudangid">Info Gudang</label>
								<select id="gudangid-id" name="gudangid" class="form-control" placeholder="Gudang" disabled></select>
							</div>
							<!--<div class="form-group">
								<label for="tipe_db">Tipe Db</label>
								<input name="tipe_db" class="form-control" placeholder="Tipe Db">
							</div>-->
							<div class="form-group col-md-3">
								<label for="aktif">Status TPE</label>
                                <select id="aktif-id" name="aktif" class="form-control" placeholder="Active/Not Active"></select>
							</div>
							<div class="form-group  col-md-3">
								<label for="tipe_sales">Tipe TPE</label>
                                <select id="tipe_sales-id" name="tipe_sales" class="form-control" placeholder="TPE Type"></select>
							</div>
							<!--<div class="form-group">
								<label for="nilai_sales">Nilai Sales</label>
								<input name="nilai_sales" class="form-control" placeholder="Nilai Sales">
							</div>
							<div class="form-group">
								<label for="last_sync">Last Sync</label>
								<input name="last_sync" class="form-control" placeholder="Last Sync">
							</div>
							-->
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
