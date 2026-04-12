<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Product Competitor <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Product Competitor</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Product Competitor</h3>
                </div>

                <form id="fm-product-competitor" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="nama_invoice">Nama Produk</label>
								<input name="nama_invoice" class="form-control" placeholder="Nama Produk">
							</div>
							<div class="form-group">
								<label for="category">Kategori Produk</label>
                                <select id="category-id" name="category" class="form-control" placeholder="Kategori"></select>
							</div>
							<div class="form-group">
								<label for="status">Status</label>
                                <select id="status-id" name="status" class="form-control" placeholder="Status"></select>
							</div>
							<!--<div class="form-group">
								<label for="h_normal">Harga Normal</label>
								<input name="h_normal" class="form-control" placeholder="H Normal">
							</div>
							<div class="form-group">
								<label for="h_promo">H Promo</label>
								<input name="h_promo" class="form-control" placeholder="H Promo">
							</div>-->
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
<script src="<?php echo base_url() . 'assets/modules/ref_product_competitor/product-competitor-form.js' ?>"></script>
