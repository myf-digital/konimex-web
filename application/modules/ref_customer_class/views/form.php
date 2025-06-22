<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Account Outlet <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Account Outlet</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Account Outlet</h3>
                </div>

                <form id="fm-customer-class" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-2">
								<label for="classid">ACCOUNTID</label>
								<input name="classid" class="form-control" placeholder="Classid">
							</div>
							<div class="form-group col-md-5">
								<label for="nama_class">ACCOUNT</label>
								<input name="nama_class" class="form-control" placeholder="Nama Class">
							</div>
                            <div class="form-group col-md-5">
                                <label for="subchannel">SUBCHANNEL</label>
                                <select id="subchannel-id" name="typeid" class="form-control" placeholder="Select Sub Channel"></select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_customer_class/customer-class-form.js' ?>"></script>
