<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Area Regional <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Area Regional</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Area Regional</h3>
                </div>

                <form id="fm-area-regional" role="form" method="post">
                    <div class="box-body">
							<!--<div class="form-group">
								<label for="siteid">Site</label>
                                <select id="siteid-id" name="siteid" class="form-control">
                                </select>
							</div>-->
							<div class="form-group">
								<label for="nama_regional">Regional</label>
								<input name="nama_regional" class="form-control" placeholder="Regional Name">
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
<script src="<?php echo base_url() . 'assets/modules/ref_regional/regional-form.js' ?>"></script>
