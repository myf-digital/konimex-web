<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Customer Spot <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Customer Spot</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Customer Spot</h3>
                </div>

                <form id="fm-customer-spot" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="cust_id">Cust Id</label>
								<input name="cust_id" class="form-control" placeholder="Cust Id">
							</div>
							<div class="form-group">
								<label for="nama_spot">Nama Spot</label>
								<input name="nama_spot" class="form-control" placeholder="Nama Spot">
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
<script src="<?php echo base_url() . 'assets/modules/ref_customer_spot/customer-spot-form.js' ?>"></script>
