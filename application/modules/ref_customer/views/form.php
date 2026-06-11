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
						<div class="row" style="display: flex; flex-wrap: wrap;">
							<div class="form-group col-md-4">
								<label for="cust_id_map">ID Outlet Distributor</label>
								<input name="cust_id_map" class="form-control" placeholder="ID Outlet Distributor">
							</div>
							<div class="form-group col-md-4">
								<label for="typeid">Channel <small class="text-danger">*</small></label>
								<select id="typeid-id" name="typeid" class="form-control" placeholder="Channel"></select>
							</div>
							<div class="form-group col-md-4">
								<label for="classid">Sub Channel <small class="text-danger">*</small></label>
								<select id="classid-id" name="classid" class="form-control" placeholder="Sub Channel"></select>
							</div>
						</div>
						<div class="row" style="display: flex; flex-wrap: wrap;">
							<div class="form-group col-md-4">
								<label for="nama_customer">Nama Outlet <small class="text-danger">*</small></label>
								<input name="nama_customer" class="form-control" placeholder="Nama Outlet">
							</div>
							<div class="form-group col-md-4">
								<label for="telp">Telpon</label>
								<input name="telp" class="form-control" placeholder="Telpon">
							</div>
							<div class="form-group col-md-4">
								<label for="email">Email</label>
								<input name="email" class="form-control" placeholder="Email">
							</div>
						</div>
						<div class="row" style="display: flex; flex-wrap: wrap;">
							<div class="form-group col-md-4">
								<label for="regionalid">Regional <small class="text-danger">*</small></label>
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
						<div class="row" style="display: flex; flex-wrap: wrap;">
							<div class="form-group col-md-6">
								<label for="alamat">Alamat</label>
								<textarea name="alamat" class="form-control" placeholder="Alamat"></textarea>
							</div>
						</div>
						<div class="form-group col-md-12" style="margin-top: 1rem;">
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 1rem;">
									<label for="professional">User (Professional)</label>
									<button type="button" class="btn btn-sm btn-success" id="btn-add-professional">
										<i class="fa fa-plus"></i> Tambah User
									</button>
								</div>
								<div class="col-sm-5">
									<select id="professional" class="form-control" size="8" multiple="multiple">
									</select>
								</div>
								
								<div class="col-sm-2">
									<button type="button" id="professional_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
									<button type="button" id="professional_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
									<button type="button" id="professional_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
									<button type="button" id="professional_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
								</div>
								
								<div class="col-sm-5">
									<select id="professional_to" class="form-control" size="8" multiple="multiple"></select>
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

<!-- Modal Quick Add Professional -->
<div class="modal fade" id="modal-add-professional" tabindex="-1" role="dialog" aria-labelledby="modal-add-professional-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-add-professional-label">Tambah User (Professional)</h4>
            </div>
            <form id="form-add-professional" role="form">
                <div class="modal-body">
                    <div class="row" style="display: flex; flex-wrap: wrap; margin: 0 -10px;">
                        <div class="form-group col-md-6" style="padding: 0 10px;">
                            <label for="modal-professional">Nama <small class="text-danger">*</small></label>
                            <input type="text" id="modal-professional" name="professional" class="form-control" placeholder="Nama" required />
                        </div>
                        <div class="form-group col-md-6" style="padding: 0 10px;">
                            <label for="modal-spesialisasi">Spesialisasi <small class="text-danger">*</small></label>
                            <select id="modal-spesialisasi" name="spesialisasi" class="form-control" placeholder="Spesialisasi" style="width: 100%;" required></select>
                        </div>
                    </div>
                    <div class="row" style="display: flex; flex-wrap: wrap; margin: 0 -10px;">
                        <div class="form-group col-md-4" style="padding: 0 10px;">
                            <label for="modal-type">Tipe</label>
                            <select id="modal-type" name="type" class="form-control" placeholder="Tipe" style="width: 100%;"></select>
                        </div>
                        <div class="form-group col-md-4" style="padding: 0 10px;">
                            <label for="modal-tanggal_lahir">Tanggal Lahir</label>
                            <input type="text" id="modal-tanggal_lahir" name="tanggal_lahir" class="form-control datepicker" placeholder="Tanggal Lahir" />
                        </div>
                        <div class="form-group col-md-4" style="padding: 0 10px;">
                            <label for="modal-tanggal_aniv_pernikahan">Tanggal Aniv Pernikahan</label>
                            <input type="text" id="modal-tanggal_aniv_pernikahan" name="tanggal_aniv_pernikahan" class="form-control datepicker" placeholder="Tanggal Aniv Pernikahan" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
  .datepicker table thead tr:first-child {
    display: table-row !important;
  }
  .datepicker table thead tr:first-child th {
    display: table-cell !important;
    color: #333 !important;
  }
</style>

<script src="<?php echo base_url() . 'assets/modules/ref_customer/customer-form.js' ?>"></script>
