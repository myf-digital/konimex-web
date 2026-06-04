<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        User <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> User</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form User</h3>
                </div>

                <form id="form-set-outlet" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="professional">User <small class="text-danger">*</small></label>
                            <input type="text" id="professional" name="professional" class="form-control" placeholder="User" />
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="spesialisasi">Spesialisasi <small class="text-danger">*</small></label>
							<select id="spesialisasi" name="spesialisasi" class="form-control" placeholder="Spesialisasi"></select>
						</div>
						<div class="form-group col-md-12" style="margin-top: 1rem;">
							<div class="col-sm-5">
								<select id="customerid" class="form-control" size="8" multiple="multiple">
								</select>
							</div>
							
							<div class="col-sm-2">
								<button type="button" id="customerid_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
								<button type="button" id="customerid_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
								<button type="button" id="customerid_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
								<button type="button" id="customerid_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
							</div>
							
							<div class="col-sm-5">
								<select id="customerid_to" class="form-control" size="8" multiple="multiple"></select>
							</div>
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
<script src="<?php echo base_url() . 'assets/modules/professional/form.js' ?>"></script>
