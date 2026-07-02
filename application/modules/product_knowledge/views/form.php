<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Product Knowledge <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Product Knowledge</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Product Knowledge</h3>
                </div>

                <form id="fm-product-knowledge" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="judul">Judul</label>
                            <input name="judul" class="form-control" placeholder="Judul" required>
                        </div>
                        <div class="form-group">
                            <label for="product">Produk</label>
                            <select id="product" name="product" class="form-control" placeholder="Produk"></select>
                        </div>
                        <div class="form-group">
                            <label for="product">
                                Files
                                <i><small class="form-text text-danger">*Hanya PDF dan gambar (JPG, PNG, GIF) yang diperbolehkan.</small></i>
                            </label>
                            <input class="form-control" type="file" id="fileInput" name="files[]" multiple accept="application/pdf,image/*">
                            <ul id="fileList" class="list-group" style="margin-top: 10px;"></ul>
                            <ul id="fileListData" class="list-group" style="margin-top: 10px;"></ul>
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
    
    <div class="modal fade" id="viewFileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div id="fileModalLabel" class="modal-title"></div>
                </div>
                <div id="bodyFileModal" class="modal-body"></div>
                <div class="modal-footer">
                    <a class="btn btn-secondary" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/product_knowledge/product-knowledge-form.js' ?>"></script>
