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
						<div class="form-group col-md-4">
							<label for="cust_id_map">ID Outlet Distributor</label>
							<input name="cust_id_map" class="form-control" placeholder="ID Outlet Distributor">
						</div>
						<div class="form-group col-md-4">
							<label for="typeid">Channel</label>
							<select id="typeid-id" name="typeid" class="form-control" placeholder="Channel"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="classid">Sub Channel</label>
							<select id="classid-id" name="classid" class="form-control" placeholder="Sub Channel"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="nama_customer">Nama Outlet</label>
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
						<div class="form-group col-md-6">
							<label for="alamat">Alamat</label>
							<textarea name="alamat" class="form-control" placeholder="Alamat"></textarea>
						</div>
						<div class="form-group col-md-6">
							<label for="professional">User - Double Cover <input id="doublecover-id" type = "checkbox" name="doublecover"></label>  
							<select id="professional-id" name="professional[]" class="form-control" placeholder="User"></select>
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
