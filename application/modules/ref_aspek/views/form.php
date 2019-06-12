<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Aspek <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Aspek</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Aspek</h3>
                </div>

                <form id="fm-aspek" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="aspek">Aspek</label>
								<input name="id_aspek" type="hidden" class="form-control" placeholder="Id Aspek">
								<input name="aspek" class="form-control" placeholder="Aspek">
							</div>
							<div class="form-group">
								<label for="id_tipe">Tipe Pertanyaan</label>
								 <select id="id-tipe_pertanyaan" name="id_tipe" class="form-control" placeholder="Pilih Tipe Pertanyaan">
								 <option value="">Pilih Tipe Pertanyaan</option>
								 </select>
							</div>
							<div class="form-group">
								<label for="nourut">Nourut</label>
								<input name="nourut" class="form-control" placeholder="Nourut">
							</div>
							<div class="form-group">
								<label for="keterangan">Keterangan</label>
								<input name="keterangan" class="form-control" placeholder="Keterangan">
							</div>
							<div id="show_type_soal_mc" class="input_fields_wrap">
								<label for="listjawaban_add" >
									<a id ="add_button" class="btn btn-primary" href="javascript:void(0);" style="height:30px; width:150px; margin:5px 0 0 0; padding-top:5px; ">
										<i class="fa fa-plus">Tambah Jawaban</i>
									</a>
								</label>
								<!--<input id="listjawaban" type="text" name="listjawaban[]" class="form-control" style="height:30px; width:350px; margin:5px 0 0 0; padding-top:5px;" value="" autocomplete="off" /> -->
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
<script src="<?php echo base_url() . 'assets/modules/ref_aspek/aspek-form.js' ?>"></script>
