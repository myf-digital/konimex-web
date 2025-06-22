<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Outlet <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Outlet</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Master Outlet</h3>
                </div>

                <form id="fm-customer" role="form" method="post">
                    <div class="box-body">
                                <input name="siteid" type=hidden>
								<input name="customerid_m" type=hidden>
								<input name="customerid" type=hidden>
							<div class="form-group col-md-3">
								<label for="kode_outlet">Kode Outlet</label>
								<input name="kode_outlet" class="form-control" placeholder="Kode Outlet">
							</div>
							<div class="form-group col-md-9">
								<label for="nama_customer">Nama Outlet</label>
								<input name="nama_customer" class="form-control" placeholder="Nama Customer">
							</div>
							<div class="form-group col-md-12">
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
							<div class="form-group col-md-2">
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
							<div class="form-group col-md-2">
								<label for="outlet-type">Outlet Type</label>
                                <select id="outlet-type-id" name="spot_id" class="form-control" placeholder="Outlet Type"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="propinsiid">Propinsi</label>
                                <select id="propinsiid-id" name="propinsiid" class="form-control" placeholder="Propinsi"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="kotaid">Kota/Kabupaten</label>
                                <select id="kotaid-id" name="kotaid" class="form-control" placeholder="Kota/Kabupaten"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="kecamatanid">Kecamatan</label>
                                <select id="kecamatanid-id" name="kecamatanid" class="form-control" placeholder="Kecamatan"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="kelurahanid">Kelurahan</label>
                                <select id="kelurahanid-id" name="kelurahanid" class="form-control" placeholder="Kelurahan"></select>
							</div>
							<!--<div class="form-group">
								<label for="createdate">Createdate</label>
								<input name="createdate" class="form-control" placeholder="Createdate">
							</div>
							<div class="form-group">
								<label for="latitude">Latitude</label>
								<input name="latitude" class="form-control" placeholder="Latitude">
							</div>
							<div class="form-group">
								<label for="longitude">Longitude</label>
								<input name="longitude" class="form-control" placeholder="Longitude">
							</div>-->
							<!--<div class="form-group">
								<label for="nilai_sales">Nilai Sales</label>
								<input name="nilai_sales" class="form-control" placeholder="Nilai Sales">
							</div>-->
							<div class="form-group col-md-4">
								<label for="mcc">DC</label>
								<input name="mcc" class="form-control" placeholder="DC">
							</div>
							<div class="form-group col-md-6">
								<label for="salesmanid">Kode GFF - Double Cover <input id="doublecover-id" type = "checkbox" name="doublecover"></label>  
                                <select id="salesmanid-id" name="salesmanid[]" class="form-control" placeholder="Salesman"></select>
							</div>
							<!--<div class="form-group">
								<label for="tanggal">Tanggal</label>
								<input name="tanggal" class="form-control" placeholder="Tanggal">
							</div>
							<div class="form-group">
								<label for="jenis_saran">Jenis Saran</label>
								<input name="jenis_saran" class="form-control" placeholder="Jenis Saran">
							</div>
							<div class="form-group">
								<label for="deskripsi_saran">Deskripsi Saran</label>
								<input name="deskripsi_saran" class="form-control" placeholder="Deskripsi Saran">
							</div>
							<div class="form-group">
								<label for="mcc">Mcc</label>
								<input name="mcc" class="form-control" placeholder="Mcc">
							</div>
							<div class="form-group">
								<label for="mnc">Mnc</label>
								<input name="mnc" class="form-control" placeholder="Mnc">
							</div>
							<div class="form-group">
								<label for="lac">Lac</label>
								<input name="lac" class="form-control" placeholder="Lac">
							</div>
							<div class="form-group">
								<label for="cid">Cid</label>
								<input name="cid" class="form-control" placeholder="Cid">
							</div>-->
							<div class="form-group">
								<label for="googleMap">Peta</label>
								<div id="googleMap" style="width:100%;height:380px;"></div>
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
<script src="<?php echo base_url() . 'assets/modules/ref_customer/customer-form.js' ?>"></script>
