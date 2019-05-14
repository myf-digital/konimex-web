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
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Karyawan</h3>
                </div>

                <form id="fm-karyawan" role="form" method="post">
                    <div class="box-body">
							<!--<div class="form-group">
								<label for="id_karyawan">Id Karyawan</label>
								<input name="id_karyawan" class="form-control" placeholder="Id Karyawan">
							</div>-->
							<div class="form-group">
								<label for="nama_karyawan">Nama Karyawan</label>
								<input name="nama_karyawan" class="form-control" placeholder="Nama Karyawan">
							</div>
							<div class="form-group">
								<label for="nip">Nip</label>
								<input name="nip" class="form-control" placeholder="Nip">
							</div>
							<div class="form-group">
								<label for="id_satker">SatKer</label>
								 <select id="id-satker" name="id_satker" class="form-control" placeholder="Id Satker"></select>
								<!--<input name="id_satker" class="form-control" placeholder="Id Satker">-->
							</div>
							<div class="form-group">
								<label for="email">Email</label>
								<input name="email" class="form-control" placeholder="Email">
							</div>
							<div class="form-group">
								<label for="telepon">Telepon</label>
								<input name="telepon" class="form-control" placeholder="Telepon">
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
<script src="<?php echo base_url() . 'assets/modules/ref_karyawan/karyawan-form.js' ?>"></script>
