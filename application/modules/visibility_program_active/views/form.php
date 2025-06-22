<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Visibility Program <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Visibility Program</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Visibility Program</h3>
                </div>

                <form id="fm-visibility-program-active" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-6">
								<label for="program_name">Program name</label>
								<input name="program_name" class="form-control" placeholder="Program name">
							</div>
							<div class="form-group col-md-6">
							</div>
							<div class="form-group col-md-6">
								<label for="description">Description</label>
								<input name="description" class="form-control" placeholder="Description">
							</div>
							<div class="form-group col-md-6">
							</div>
							<div class="form-group col-md-2">
								<label for="start_period">Start Period</label>
                                    <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="start_period" placeholder="Start Period" type="text" class="form-control datepicker" name="start_period" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-2">
								<label for="end_period">End Period</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="end_period" placeholder="End Period" type="text" class="form-control datepicker" name="end_period" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-2">
								<label for="qty_submit_photo">Qty Submit Photo</label>
								<input name="qty_submit_photo" class="form-control" placeholder="Qty Submit Photo" type="number">
							</div>
							<div class="form-group col-md-6">
							</div>

							<div class="form-group col-md-6">
								<label for="classid">Select Account List</label>
                                <select id="classid-id" name="classid[]" class="form-control" placeholder="Select Account List"></select>
							</div>
							<div class="form-group col-md-6">
							</div>
                            <div class="form-group col-md-3">
                                <label for="regionalid">Regional</label>
                                <select id="regionalid-id" name="regionalid" class="form-control" placeholder="Regional"></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="areaid">Area</label>
                                <select id="areaid-id" name="areaid" class="form-control" placeholder="Area"></select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="subareaid">City</label>
                                <select id="subareaid-id" name="subareaid[]" class="form-control" placeholder="City"></select>
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
<script src="<?php echo base_url() . 'assets/modules/visibility_program_active/visibility-program-form.js' ?>"></script>
