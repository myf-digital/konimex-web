<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Area Kota <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Area Kota</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Area Kota</h3>
                </div>

                <form id="fm-area-kota" role="form" method="post">
                    <div class="box-body">
                            <!--<div class="form-group">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="Site Id">
                                </select>
							</div>-->
							<div class="form-group">
								<label for="propinsiid">Propinsi</label>
                                <select id="propinsiid-id" name="propinsiid" class="form-control" placeholder="Propinsi">
                                </select>
							</div>
							<div class="form-group">
								<label for="nama_kota">Nama Kota</label>
								<input name="nama_kota" class="form-control" placeholder="Nama Kota">
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
<script src="<?php echo base_url() . 'assets/modules/ref_area_kota/area-kota-form.js' ?>"></script>
