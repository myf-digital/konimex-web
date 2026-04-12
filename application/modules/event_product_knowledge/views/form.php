<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Event Product Knowledge <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Event Product Knowledge</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Event Product Knowledge</h3>
                </div>

                <form id="fm-product-knowledge" role="form" method="post">
                    <div class="box-body">
					<div class="form-group col-md-6">
							<div class="form-group col-md-12">
								<label for="event">Event Name</label>
								<input name="event" class="form-control" placeholder="Event Name">
							</div>
							<div class="form-group col-md-12">
								<label for="desc">Description</label>
                                <textarea name="desc" class="form-control" placeholder="Description"></textarea>
							</div>
							<div class="form-group col-md-3">
									<label for="start_periode">Start Date</label>
                                    <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="start_period" placeholder="Start Period" type="text" class="form-control datepicker" name="start_period" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-3">
								<label for="start_time">Start Time</label>
								<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-time"></span>
									</span>
									<input type="text" class="form-control" placeholder="Start Time" name="start_time" readonly>
								</div>
							</div>
							<div class="form-group col-md-6"></div>
							<div class="form-group col-md-3">
								<label for="end_periode">End Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="end_period" placeholder="End Period" type="text" class="form-control datepicker" name="end_period" readonly>
                                </div>
							</div>
							<div class="form-group col-md-3">
								<label for="end_time">End Time</label>
								<div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-time"></span>
									</span>
									<input type="text" class="form-control" placeholder="End Time" name="end_time" readonly>
								</div>
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
<script src="<?php echo base_url() . 'assets/modules/event_product_knowledge/event-product-knowledge-form.js' ?>"></script>
