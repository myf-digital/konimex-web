<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Product <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Product</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Product</h3>
                </div>

                <form id="fm-product" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="productid">Kode Produk</label>
								<input name="productid" class="form-control" placeholder="Productid">
							</div>
							<div class="form-group">
								<label for="barcode">Barcode</label>
								<input name="barcode" class="form-control" placeholder="Barcode">
							</div>
							<div class="form-group">
								<label for="nama_invoice">Nama Produk</label>
								<input name="nama_invoice" class="form-control" placeholder="Nama Invoice">
							</div>
							<div class="form-group">
								<label for="group_product">Group Produk</label>
								<select id="group_product-id" name="group_product" class="form-control" placeholder="Group Produk"></select>
							</div>
							<div class="form-group">
								<label for="category_product">Kategori Produk</label>
								<select id="category_product-id" name="category_product" class="form-control" placeholder="Kategori Produk"></select>
							</div>
							<div class="form-group">
								<label for="brandid">Brand</label>
								<select id="brandid-id" name="brandid" class="form-control" placeholder="Brand"></select>
							</div>
							<div class="form-group">
								<label for="h_grosir">HJP</label>
								<input name="h_grosir" class="form-control" placeholder="H Grosir">
							</div>
							<div class="form-group">
								<label for="h_ritel">HNA</label>
								<input name="h_ritel" class="form-control" placeholder="H Ritel">
							</div>
							<div class="form-group">
                                <label for="status">Status</label>
                                <select id="status-id" name="status" class="form-control" placeholder="Status"></select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_product/product-form.js' ?>"></script>
