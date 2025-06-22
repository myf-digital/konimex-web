<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Bank <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Bank</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Bank</h3>
                </div>

                <form id="fm-bank" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="bankid">Bankid</label>
								<input name="bankid" class="form-control" placeholder="Bankid">
							</div>
							<div class="form-group">
								<label for="nama_bank">Nama Bank</label>
								<input name="nama_bank" class="form-control" placeholder="Nama Bank">
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
<script src="<?php echo base_url() . 'assets/modules/ref_bank/bank-form.js' ?>"></script>
