<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup Pjp <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup Pjp</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup Pjp</h3>
                </div>

                <form id="fm-setup-pjp" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="siteid">Siteid</label>
								<input name="siteid" class="form-control" placeholder="Siteid">
							</div>
							<div class="form-group">
								<label for="salesmanid">Salesmanid</label>
								<input name="salesmanid" class="form-control" placeholder="Salesmanid">
							</div>
							<div class="form-group">
								<label for="nama_salesman">Nama Salesman</label>
								<input name="nama_salesman" class="form-control" placeholder="Nama Salesman">
							</div>
							<div class="form-group">
								<label for="position">Position</label>
								<input name="position" class="form-control" placeholder="Position">
							</div>
							<div class="form-group">
								<label for="ram_rsm">Ram Rsm</label>
								<input name="ram_rsm" class="form-control" placeholder="Ram Rsm">
							</div>
							<div class="form-group">
								<label for="aas_aam_tss_tsm">Aas Aam Tss Tsm</label>
								<input name="aas_aam_tss_tsm" class="form-control" placeholder="Aas Aam Tss Tsm">
							</div>
							<div class="form-group">
								<label for="customerid">Customerid</label>
								<input name="customerid" class="form-control" placeholder="Customerid">
							</div>
							<div class="form-group">
								<label for="nama_customer">Nama Customer</label>
								<input name="nama_customer" class="form-control" placeholder="Nama Customer">
							</div>
							<div class="form-group">
								<label for="alamat">Alamat</label>
								<input name="alamat" class="form-control" placeholder="Alamat">
							</div>
							<div class="form-group">
								<label for="group_account">Group Account</label>
								<input name="group_account" class="form-control" placeholder="Group Account">
							</div>
							<div class="form-group">
								<label for="outlet_type">Outlet Type</label>
								<input name="outlet_type" class="form-control" placeholder="Outlet Type">
							</div>
							<div class="form-group">
								<label for="dc">Dc</label>
								<input name="dc" class="form-control" placeholder="Dc">
							</div>
							<div class="form-group">
								<label for="channel_outlet">Channel Outlet</label>
								<input name="channel_outlet" class="form-control" placeholder="Channel Outlet">
							</div>
							<div class="form-group">
								<label for="tgl_proses">Tgl Proses</label>
								<input name="tgl_proses" class="form-control" placeholder="Tgl Proses">
							</div>
							<div class="form-group">
								<label for="keterangan">Keterangan</label>
								<input name="keterangan" class="form-control" placeholder="Keterangan">
							</div>
							<div class="form-group">
								<label for="minggu">Minggu</label>
								<input name="minggu" class="form-control" placeholder="Minggu">
							</div>
							<div class="form-group">
								<label for="hari">Hari</label>
								<input name="hari" class="form-control" placeholder="Hari">
							</div>
							<div class="form-group">
								<label for="status_send">Status Send</label>
								<input name="status_send" class="form-control" placeholder="Status Send">
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
<script src="<?php echo base_url() . 'assets/modules/sales_setup_pjp/setup-pjp-form.js' ?>"></script>
