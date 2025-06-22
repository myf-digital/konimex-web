<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Promo Active <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Promo GSK</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Promo GSK</h3>
                </div>

                <form id="fm-promo-active" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-12">
								<label for="promo">Promo</label>
								<input name="promo" class="form-control" placeholder="Promo">
							</div>
							<div class="form-group col-md-2">
								<label for="classid">Type Promo</label>
                                <select id="tipepromo-id" name="tipepromo" class="form-control" placeholder="Select Type Promo"></select>
							</div>
							<div class="form-group col-md-2">
								<label for="classid">SubChannel / Account</label>
                                <select id="classid-id" name="classid" class="form-control" placeholder="SubChannel / Account"></select>
							</div>
							<div class="form-group col-md-2">
								<label for="start_periode">Start Periode</label>
                                    <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="start_periode" placeholder="Start Periode" type="text" class="form-control datepicker" name="start_periode" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-2">
								<label for="end_periode">End Periode</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="end_periode" placeholder="End Periode" type="text" class="form-control datepicker" name="end_periode" readonly>
                                    </div>
							</div>
                            <div class="form-group col-md-12">
								<label for="productid">Pilih Product</label>  
                                <select id="productid-id" name="productid[]" class="form-control" placeholder="Product"></select>
							</div>
							<div class="form-group col-md-12">
								<label for="description">Description</label>
								<input name="description" class="form-control" placeholder="Description">
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
<script src="<?php echo base_url() . 'assets/modules/mapping_promo_active/promo-active-form.js' ?>"></script>
