<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Regional <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Regional</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Regional</h3>
                </div>

                <form id="fm-regional" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="regional">Regional</label>
								<input name="regional" class="form-control" placeholder="Regional">
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
<script src="<?php echo base_url() . 'assets/modules/ref_regional/regional-form.js' ?>"></script>
