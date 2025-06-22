<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Outlet Type <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Outlet Type</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Outlet Type</h3>
                </div>

                <form id="fm-customer-type" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-3">
								<label for="typeid">TYPEID</label>
								<input name="typeid" class="form-control" placeholder="Typeid">
							</div>
							<div class="form-group col-md-6">
								<label for="nama_type">OUTLET TYPE</label>
								<input name="nama_type" class="form-control" placeholder="Type Name">
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
<script src="<?php echo base_url() . 'assets/modules/ref_customer_type/customer-type-form.js' ?>"></script>
