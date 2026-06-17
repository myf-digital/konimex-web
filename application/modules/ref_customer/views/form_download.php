<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
	Download MEDREP Outlet <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Download MEDREP Outlet</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Download MEDREP Outlet</h3>
                </div>

                <form id="fm-customer" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-6">
								<label for="classid">Channel</label>
                                <select id="classid-id" name="classid" class="form-control" placeholder="Channel">
								<option value="All">All Channel</option>
								</select>
							</div>
                    </div>

                    <div class="box-footer">
                        <a id="btn-cancel-form" href="javascript:void(0)" class="btn btn-warning fa fa-backward">  Back</a>
                        <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/ref_customer/customer-form-download.js' ?>"></script>
