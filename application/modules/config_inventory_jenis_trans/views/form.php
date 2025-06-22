<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Inventory Jenis Trans <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inventory Jenis Trans</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Inventory Jenis Trans</h3>
                </div>

                <form id="fm-inventory-jenis-trans" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="jenis_trans">Jenis Trans</label>
								<input name="jenis_trans" class="form-control" placeholder="Jenis Trans">
							</div>
							<div class="form-group">
								<label for="nama_trans">Nama Trans</label>
								<input name="nama_trans" class="form-control" placeholder="Nama Trans">
							</div>
							<div class="form-group">
								<label for="flag_trans">Flag Trans</label>
								<input name="flag_trans" class="form-control" placeholder="Flag Trans">
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
<script src="<?php echo base_url() . 'assets/modules/config_inventory_jenis_trans/inventory-jenis-trans-form.js' ?>"></script>
