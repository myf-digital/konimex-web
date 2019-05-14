<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Satker <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Satker</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Satker</h3>
                </div>

                <form id="fm-satker" role="form" method="post">
                    <div class="box-body">
							<!--<div class="form-group">
								<label for="id_satker">Id Satker</label>
								<input name="id_satker" class="form-control" placeholder="Id Satker">
							</div> -->
							<div class="form-group">
								<label for="kode_satker">Kode Satker</label>
								<input name="kode_satker" class="form-control" placeholder="Kode Satker">
							</div>
							<div class="form-group">
								<label for="satker">Satker</label>
								<input name="satker" class="form-control" placeholder="Satker">
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
<script src="<?php echo base_url() . 'assets/modules/ref_satker/satker-form.js' ?>"></script>
