<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Area Kirim <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Area Kirim</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Area Kirim</h3>
                </div>

                <form id="fm-area-kirim" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-3">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="SiteID"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="areakirimid">Areakirimid</label>
								<input name="areakirimid" class="form-control" placeholder="Areakirimid">
							</div>
							<div class="form-group col-md-6">
								<label for="nama_areakirim">Nama Areakirim</label>
								<input name="nama_areakirim" class="form-control" placeholder="Nama Areakirim">
							</div>
							<!--
							<div class="form-group">
								<label for="branchid">Branchid</label>
								<input name="branchid" class="form-control" placeholder="Branchid">
							</div>
							<div class="form-group">
								<label for="companyid">Companyid</label>
								<input name="companyid" class="form-control" placeholder="Companyid">
							</div>
							<div class="form-group">
								<label for="headofficeid">Headofficeid</label>
								<input name="headofficeid" class="form-control" placeholder="Headofficeid">
							</div>
							<div class="form-group">
								<label for="flag_kirim">Flag Kirim</label>
								<input name="flag_kirim" class="form-control" placeholder="Flag Kirim">
							</div>
							-->
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
<script src="<?php echo base_url() . 'assets/modules/ref_area_kirim/area-kirim-form.js' ?>"></script>
