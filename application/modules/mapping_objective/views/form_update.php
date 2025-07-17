<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Mapping Objective <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Mapping Objective</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Mapping Objective</h3>
                </div>

                <form id="fm-mapping-objective" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-md-6">
							<label for="classid">SubChannel / Account</label>
							<select id="classid-id" name="classid[]" class="form-control" placeholder="SubChannel / Account"></select>
						</div>
						<div class="form-group col-md-3">
							<label for="start_periode">Start Periode</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<span class="fa fa-calendar"></span>
								</div>
								<input id="start_periode" placeholder="Start Periode" type="text" class="form-control datepicker" name="start_periode" readonly>
							</div>
						</div>
						<div class="form-group col-md-3">
							<label for="end_periode">End Periode</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<span class="fa fa-calendar"></span>
								</div>
								<input id="end_periode" placeholder="End Periode" type="text" class="form-control datepicker" name="end_periode" readonly>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="products">Produk</label>
							<select id="products" name="products" class="form-control" placeholder="Objective"></select>
						</div>
						<div class="form-group col-md-6">
							<label for="keterangan">Keterangan</label>
							<input name="keterangan" class="form-control" placeholder="Keterangan">
						</div>
						<div class="form-group col-md-6">
							<label for="objective">Objective</label>
							<select id="objective-id" name="objective" class="form-control" placeholder="Objective"></select>
						</div>
						<div id="div_min_order" class="form-group col-md-6" style="display: none;">
							<label for="min_order">Minimal Order</label>
							<input id="min_order" name="min_order" class="form-control" placeholder="Minimal Order">
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
<script src="<?php echo base_url() . 'assets/modules/mapping_objective/mapping-objective-form-update.js' ?>"></script>
