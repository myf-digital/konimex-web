<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Request Register Outlet <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Request Register Outlet</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Request Register Outlet</h3>
                </div>

                <form id="fm-request_new_outlet" role="form" method="post">
                    <div class="box-body">
                                <input name="siteid" type=hidden>
								<input name="customerid_m" type=hidden>
								<input name="customerid" type=hidden>
							<div class="form-group  col-xs-12 col-sm-12 col-md-2">
								<label for="Aktif_Week">Week Aktif</label>
								<input name="aktif_week" class="form-control" placeholder="Week Aktif" disabled>
							</div>
							<div class="form-group col-md-3">
								<label for="kode_outlet">Kode Outlet</label>
								<input name="kode_outlet" class="form-control" placeholder="Kode Outlet">
							</div>
							<div class="form-group col-md-3">
								<label for="nama_customer">Nama Outlet</label>
								<input name="nama_customer" class="form-control" placeholder="Nama Customer">
							</div>
							<div class="form-group col-md-6">
								<label for="alamat">Alamat</label>
								<input name="alamat" class="form-control" placeholder="Alamat">
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
							<div class="form-group col-md-4">
								<label for="segmentid">Business Unit</label>
                                <select id="segmentid-id" name="segmentid" class="form-control" placeholder="Business Unit"></select>
							</div>
							<div class="form-group col-md-4">
								<label for="typeid">Channel</label>
                                <select id="typeid-id" name="typeid" class="form-control" placeholder="Channel"></select>
							</div>
							<div class="form-group col-md-4">
								<label for="classid">SubChannel</label>
                                <select id="classid-id" name="classid" class="form-control" placeholder="SubChannel"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="propinsiid">Propinsi</label>
                                <select id="propinsiid-id" name="propinsiid" class="form-control" placeholder="Propinsi"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="kotaid">Kota/Kabupaten</label>
                                <select id="kotaid-id" name="kotaid" class="form-control" placeholder="Kota/Kabupaten"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="kecamatanid">Kecamatan</label>
                                <select id="kecamatanid-id" name="kecamatanid" class="form-control" placeholder="Kecamatan"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="kelurahanid">Kelurahan</label>
                                <select id="kelurahanid-id" name="kelurahanid" class="form-control" placeholder="Kelurahan"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="salesmanid">TPE ID</label>
                                <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="TPE"></select>
							</div>
							<div class="form-group col-md-4">
								<label for="mcc">DC</label>
								<input name="mcc" class="form-control" placeholder="DC">
							</div>
                    </div>
					<div class="box-header with-border">
						<h3 class="box-title">PJP</h3>
					</div>
                    <div class="box-body">
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
                        <button type="submit" class="btn btn-primary">Approve</button>
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/ref_request_new_outlet/request_new_outlet-form.js' ?>"></script>
