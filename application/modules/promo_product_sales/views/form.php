<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Promo TPE <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Promo TPE</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Promo TPE</h3>
                </div>

                <form id="fm-promo-product" role="form" method="post">
                    <div class="box-body">
                            <div class="form-group col-md-2">
								<label for="no_promo">No Promo</label>
								<input name="no_promo" class="form-control" placeholder="No Promo">
							</div>
							<div class="form-group col-md-3">
								<label for="judul">Promo Name</label>
								<input name="judul" class="form-control" placeholder="judul">
							</div>
							<div class="form-group col-md-2">
								<label for="mulai_tanggal">Start Periode</label>
                                    <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="mulai_tanggal" placeholder="Start Periode" type="text" class="form-control datepicker" name="mulai_tanggal" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-2">
								<label for="selesai_tanggal">End Periode</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="selesai_tanggal" placeholder="End Periode" type="text" class="form-control datepicker" name="selesai_tanggal" readonly>
                                    </div>
							</div>
                            <div class="form-group col-md-3">
                                <label for="typeid">Cluster</label>
                                <select id="typeid-id" name="typeid" class="form-control" placeholder="Channel"></select>
                            </div>
                            <div class="form-group col-md-12">
								<label for="productid">Pilih Product</label>  
                                <select id="productid-id" name="productid[]" class="form-control" placeholder="Product"></select>
							</div>
							<div class="form-group col-md-12">
								<label for="desription_promo">Description</label>
								<input name="desription_promo" class="form-control" placeholder="Description Promo">
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
<script src="<?php echo base_url() . 'assets/modules/promo_product_sales/promo-product-form.js' ?>"></script>
