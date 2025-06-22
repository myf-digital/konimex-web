<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Gudang <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Gudang</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Gudang</h3>
                </div>

                <form id="fm-gudang" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-3">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="SiteID"></select>
							</div>
							<!--<div class="form-group">
								<label for="gudangid">Gudangid</label>
								<input name="gudangid" class="form-control" placeholder="Gudangid">
							</div>-->
							<div class="form-group col-md-6">
								<label for="gudang_name">Gudang Name</label>
								<input name="gudang_name" class="form-control" placeholder="Gudang Name">
							</div>
							<div class="form-group col-md-3">
								<label for="status_aktif">Status Aktif</label>
                                <select id="status_aktif-id" name="status_aktif" class="form-control" placeholder="Status"></select>
							</div>
							<!--<div class="form-group">
								<label for="user_create">User Create</label>
								<input name="user_create" class="form-control" placeholder="User Create">
							</div>
							<div class="form-group">
								<label for="date_create">Date Create</label>
								<input name="date_create" class="form-control" placeholder="Date Create">
							</div>
							<div class="form-group">
								<label for="user_update">User Update</label>
								<input name="user_update" class="form-control" placeholder="User Update">
							</div>
							<div class="form-group">
								<label for="date_update">Date Update</label>
								<input name="date_update" class="form-control" placeholder="Date Update">
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
<script src="<?php echo base_url() . 'assets/modules/ref_gudang/gudang-form.js' ?>"></script>
