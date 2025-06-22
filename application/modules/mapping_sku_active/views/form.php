<!-- Content Header (Page header) -->
<!-- <style type="text/css">
    div.checkboxes div{
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        float: left;
        width: 23%;
        margin-left:18px;
    }
    div.checkboxes input[type='checkbox']{
        margin-left:-18px;
    }
</style> -->
<section class="content-header">
    <h1>
        Sku Active <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Sku Active</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Sku Active</h3>
                </div>

                <form id="fm-sku-active" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-md-6">
							<label for="idaccount">Account</label>
                            <select id="idaccount-id" name="idaccount" class="form-control" placeholder="Account" ></select>
						</div><!-- 
						<div class="form-group col-md-12">
							<label for="productid">Product</label>
                            <select id="productid-id" name="productid[]" class="form-control" placeholder="Product" ></select>
						</div> -->
<!--                         <div class="form-group col-md-6">
                            <label>Product</label>
                            <input type="text" name="search" class="form-control" id="search" placeholder="Search Product">
                        </div>
                        <div id="checkboxes">
                        </div> -->
                        <div class="form-group col-md-12">
                            <div class="col-sm-5">
                                <select id="search" class="form-control" size="8" multiple="multiple">
                                </select>
                            </div>
                            
                            <div class="col-sm-2">
                                <button type="button" id="search_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                <button type="button" id="search_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                <button type="button" id="search_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                <button type="button" id="search_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                            </div>
                            
                            <div class="col-sm-5">
                                <select id="search_to" class="form-control" size="8" multiple="multiple"></select>
                            </div>
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
<script src="<?php echo base_url() . 'assets/modules/mapping_sku_active/sku-active-form.js' ?>"></script>
