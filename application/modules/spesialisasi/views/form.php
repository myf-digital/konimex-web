<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Spesialisasi <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Spesialisasi</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Spesialisasi</h3>
                </div>

                <form id="form-spesialisasi" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="spesialisasi">Spesialisasi <small class="text-danger">*</small></label>
                            <input type="text" id="spesialisasi" name="spesialisasi" class="form-control" placeholder="Spesialisasi" />
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
<script src="<?php echo base_url() . 'assets/modules/spesialisasi/form.js' ?>"></script>
