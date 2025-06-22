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

                <form id="fm-area-city" role="form" method="post">
                    <div class="box-body">
					        <!--<div class="form-group">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control">
                                </select>
							</div>-->
							<div class="form-group">
								<label for="regionalid">Regional</label>
                                <select id="regionalid-id" name="regionalid" class="form-control">
                                </select>
							</div>
							<div class="form-group">
								<label for="areaid">Area Name</label>
                                <select id="areaid-id" name="areaid" class="form-control">
                                </select>
							</div>
							<div class="form-group">
								<label for="nama_subarea">SubArea Name</label>
								<input name="nama_area" class="form-control" placeholder="SubArea Name">
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
<script src="<?php echo base_url() . 'assets/modules/ref_area_city/area-city-form.js' ?>"></script>
