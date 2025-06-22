<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Business Unit <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Business Unit</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Business Unit</h3>
                </div>

                <form id="fm-customer-segment" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-3">
								<label for="segmentid">BU ID</label>
								<input name="segmentid" class="form-control" placeholder="BU ID">
							</div>
							<div class="form-group col-md-6">
								<label for="nama_segment">BUSINESS UNIT</label>
								<input name="nama_segment" class="form-control" placeholder="Business Unit">
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
<script src="<?php echo base_url() . 'assets/modules/ref_customer_segment/customer-segment-form.js' ?>"></script>
