<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        City <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> City</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form City</h3>
                </div>

                <form id="fm-city" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="id">Id</label>
								<input name="id" class="form-control" placeholder="Id">
							</div>
							<div class="form-group">
								<label for="idregional">Idregional</label>
								<input name="idregional" class="form-control" placeholder="Idregional">
							</div>
							<div class="form-group">
								<label for="idarea">Idarea</label>
								<input name="idarea" class="form-control" placeholder="Idarea">
							</div>
							<div class="form-group">
								<label for="city">City</label>
								<input name="city" class="form-control" placeholder="City">
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
<script src="<?php echo base_url() . 'assets/modules/ref_city/city-form.js' ?>"></script>
