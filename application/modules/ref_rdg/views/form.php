<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Rdg <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Rdg</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Materi Rapat Dewan Gubernur</h3>
                </div>

                <form id="fm-rdg" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="nama_rdg">Materi</label>
								<input name="nama_rdg" class="form-control" placeholder="Materi Rapat Dewan Gubernur">
							</div>
							<div class="form-group">
								<label for="tanggal">Tanggal</label>
								<div class="input-group date">
									  <div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									  </div>
									  <input name="tanggal" type="text" class="form-control pull-right" id="id-tanggal" placeholder="Tanggal pelaksanaan">
								</div>
							</div>
							<div class="form-group">
								<label for="id_satker">Satuan Kerja</label>
								 <!--<select id="id-satker" name="id_satker" class="form-control" placeholder="Pilih Satker">-->
								 <select id="id-satker" name="id_satker[]" class="form-control" multiple = "multiple" ></select>
								 <option value="">Pilih Satker</option>
								 </select>
							</div>
							<div class="form-group">
								<label for="aspek">Aspek</label>
								 <select id="id-aspek" name="id_aspek[]" class="form-control" multiple = "multiple" ></select>
							</div>
							<div class="form-group">
								<label for="keterangan">Keterangan</label>
								<input name="keterangan" class="form-control" placeholder="Keterangan">
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
<script src="<?php echo base_url() . 'assets/modules/ref_rdg/rdg-form.js' ?>"></script>
