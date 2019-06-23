<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Matrix Table <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Matrix Table</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Matrix Table</h3>
                </div>

                <form id="fm-matrix-table" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="matrix_table">Matrix Table</label>
								<input name="matrix_table" class="form-control" placeholder="Matrix Table">
							</div>
							<div class="form-group">
								<label for="id_aspek">Aspek</label>
								 <select id="id-aspek" name="id_aspek[]" class="form-control" multiple = "multiple" ></select>
							</div>
							<div class="form-group">
								<label for="id_aspek_grafik">Header Report Grafik</label>
								 <select id="id-aspek_grafik" name="id_aspek_grafik[]" class="form-control" multiple = "multiple" ></select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_matrix_table/matrix-table-form.js' ?>"></script>
