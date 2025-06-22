<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sales Salesman Area <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Sales Salesman Area</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Sales Salesman Area</h3>
                </div>

                <form id="fm-sales-salesman-area" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="SiteID"></select>
							</div>
							<div class="form-group">
								<label for="salesmanid">Salesmanid</label>
                                <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="Salesman"></select>
							</div>
							<div class="form-group">
								<label for="areaid">Areaid</label>
                                <select id="areaid-id" name="areaid[]" class="form-control" multiple = "multiple" placeholder="Area"></select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_sales_salesman_area/sales-salesman-area-form.js' ?>"></script>
