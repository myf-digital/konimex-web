<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sales Medrep Category <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Sales Medrep Category</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Sales Medrep Category</h3>
                </div>

                <form id="fm-sales-salesman-category" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="siteid">Siteid</label>
								<input name="siteid" class="form-control" placeholder="Siteid">
							</div>
							<div class="form-group">
								<label for="salesmanid">TPE ID</label>
								<input name="salesmanid" class="form-control" placeholder="TPE ID">
							</div>
							<div class="form-group">
								<label for="categoryid">Categoryid</label>
								<input name="categoryid" class="form-control" placeholder="Categoryid">
							</div>
							<div class="form-group">
								<label for="nama_category">Nama Category</label>
								<input name="nama_category" class="form-control" placeholder="Nama Category">
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
<script src="<?php echo base_url() . 'assets/modules/ref_sales_salesman_category/sales-salesman-category-form.js' ?>"></script>
