<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Matrix Aspek <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Matrix Aspek</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Matrix Aspek</h3>
                </div>

                <form id="fm-matrix-aspek" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="id_matrix">Tipe Pertanyaan</label>
								 <select id="id-matrix" name="id_matrix" class="form-control" placeholder="Pilih Matrix Table">
								 <option value="">Pilih Matrix Table</option>
								 </select>
							</div>
							<div class="form-group">
								<label for="id_aspek">Aspek</label>
								 <select id="id-aspek" name="id_aspek[]" class="form-control" multiple = "multiple" ></select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_matrix_aspek/matrix-aspek-form.js' ?>"></script>
