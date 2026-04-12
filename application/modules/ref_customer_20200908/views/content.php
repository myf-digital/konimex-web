<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Outlet <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Outlet</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="box-body">
                    <table id="tbl-customer">
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="ModalLabel">List MEDREP</h4>
                </div>
                <div class="modal-body">

                        <div id="tbl-listgff" class="box-table box-success">
                        </div>

 
                        <div class="modal-footer">
                            <a  class="btn btn-secondary" data-dismiss="modal">Close</a>
                        </div>
                </div>
            </div>
        </div>
    </div>
<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/ref_customer/customer-content.js' ?>"></script>