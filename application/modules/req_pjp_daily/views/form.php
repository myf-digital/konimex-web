<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Request PJP Daily <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Request PJP Daily</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Request PJP Daily</h3>
                </div>

                <form id="fm-req-pjp-daily" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="salesmanid">SALESMAN</label>
							<select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="SALESMAN" disabled></select>
						</div>
						<div class="form-group col-md-12">
							<div class="col-sm-5">
								<select id="customerid" class="form-control" size="8" multiple="multiple">
								</select>
							</div>
							<div class="col-sm-2">
								<button type="button" id="customerid_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
								<button type="button" id="customerid_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
								<button type="button" id="customerid_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
								<button type="button" id="customerid_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
							</div>
							
							<div class="col-sm-5">
								<select id="customerid_to" class="form-control" size="8" multiple="multiple"></select>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week1">Week 1</label>
							<select id="week1-id" name="week1[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week2">Week 2</label>
							<select id="week2-id" name="week2[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week3">Week 3</label>
							<select id="week3-id" name="week3[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week4">Week 4</label>
							<select id="week4-id" name="week4[]" class="form-control" placeholder="Days" ></select>
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
<script src="<?php echo base_url() . 'assets/modules/req_pjp_daily/req-pjp-daily-form.js' ?>"></script>
