<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Customer Image <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Customer Image</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Customer Image</h3>
                </div>

                <form id="fm-customer-image" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="id">Id</label>
								<input name="id" class="form-control" placeholder="Id">
							</div>
							<div class="form-group">
								<label for="siteid">Siteid</label>
								<input name="siteid" class="form-control" placeholder="Siteid">
							</div>
							<div class="form-group">
								<label for="salesmanid">TPE ID</label>
								<input name="salesmanid" class="form-control" placeholder="TPE ID">
							</div>
							<div class="form-group">
								<label for="customerid">Customerid</label>
								<input name="customerid" class="form-control" placeholder="Customerid">
							</div>
							<div class="form-group">
								<label for="image">Image</label>
								<input name="image" class="form-control" placeholder="Image">
							</div>
							<div class="form-group">
								<label for="datecreate">Datecreate</label>
								<input name="datecreate" class="form-control" placeholder="Datecreate">
							</div>
							<div class="form-group">
								<label for="size">Size</label>
								<input name="size" class="form-control" placeholder="Size">
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
<script src="<?php echo base_url() . 'assets/modules/ref_customer_image/customer-image-form.js' ?>"></script>
