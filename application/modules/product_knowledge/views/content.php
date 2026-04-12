<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Produck Knowledge <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Product Knowledge</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="box-body">
                    <table id="tbl-product-knowledge">
                    </table>
                </div>
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

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/product_knowledge/product-knowledge-content.js' ?>"></script>