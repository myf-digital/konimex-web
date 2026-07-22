<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kendaraan <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Kendaraan</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Kendaraan</h3>
                </div>

                <form id="fm-kendaraan" role="form" method="post">
                    <div class="box-body">
					<div class="form-group col-md-3">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="SiteID"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="kendaraanid">Kendaraanid</label>
								<input name="kendaraanid" class="form-control" placeholder="Kendaraanid">
							</div>
							<div class="form-group col-md-12">
								<label for="keterangan">Keterangan</label>
								<input name="keterangan" class="form-control" placeholder="Keterangan">
							</div>
							<div class="form-group col-md-6">
								<label for="salesmanid">TPE ID</label>
                                <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="TPE"></select>
							</div>
							<div class="form-group col-md-6">
							</div>
							<div class="form-group col-md-3">
								<label for="no_polisi">No Polisi</label>
								<input name="no_polisi" class="form-control" placeholder="No Polisi">
							</div>
							<div class="form-group col-md-6">
								<label for="nama_pemilik">Nama Pemilik</label>
								<input name="nama_pemilik" class="form-control" placeholder="Nama Pemilik">
							</div>
							<div class="form-group col-md-12">
								<label for="alamat">Alamat</label>
								<input name="alamat" class="form-control" placeholder="Alamat">
							</div>
							<div class="form-group col-md-3">
								<label for="merk">Merk</label>
								<input name="merk" class="form-control" placeholder="Merk">
							</div>
							<div class="form-group col-md-3">
								<label for="no_rangka">No Rangka</label>
								<input name="no_rangka" class="form-control" placeholder="No Rangka">
							</div>
							<div class="form-group col-md-3">
								<label for="tahun">Tahun</label>
								<input name="tahun" class="form-control" placeholder="Tahun">
							</div>
							<div class="form-group col-md-3">
								<label for="stnk_akhir">Stnk Akhir</label>
								<input name="stnk_akhir" class="form-control" placeholder="Stnk Akhir">
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
<script src="<?php echo base_url() . 'assets/modules/ref_kendaraan/kendaraan-form.js' ?>"></script>
