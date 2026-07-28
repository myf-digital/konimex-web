

<!-- Content Header (Page header) -->

<section class="content-header">
    <h1>
        Setup Rrk <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup Rrk</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup Rrk</h3>
                </div>

                <form id="fm-setup-rrk" role="form" method="post">
                    <div class="box-body">
							<div class="form-group col-md-4">
								<label for="siteid">Siteid</label>
                                <select id="siteid-id" name="siteid" class="form-control" placeholder="SiteID"></select>
							</div>
							<div class="form-group col-md-7">
								<label for="salesmanid">TPE ID</label>
                                <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="TPE"></select>
							</div>
							<div class="form-group col-md-6">
								<label for="areaid">Area</label>
                                <select id="areaid-id" name="areaid" class="form-control" placeholder="Area"></select>
							</div>
							<div class="form-group  col-md-6">
								<label for="areaid">Frequency of visits</label>
                                <select id="frvisit-id" name="frvisit" class="form-control" placeholder="Frequency"></select>
							</div>
							<!--<div class="form-group">
								<label for="subareaid">Sub Area</label>
                                <select id="subareaid-id" name="subareaid[]" class="form-control" placeholder="Sub Area"></select>
							</div>-->
							<div class="form-group col-md-6">
								<label for="minggu">Weeks</label>
                                <select id="minggu-id" name="minggu[]" class="form-control" placeholder="Weeks" ></select>
							</div>
							<div class="form-group col-md-6">
								<label for="day">Days</label>
                                <select id="day-id" name="day[]" class="form-control" placeholder="Days" ></select>
							</div>
							<div class="form-group">
								<label for="repeat_minggu">Repeat Minggu</label>
								<div class="row">
								<div class="col-sm-5">
									<select name="from[]" id="search" class="form-control" size="8" multiple="multiple">
										<option value="1" data-position="1">Item 1</option>
										<option value="2" data-position="2">Item 5</option>
										<option value="2" data-position="3">Item 2</option>
										<option value="2" data-position="4">Item 4</option>
										<option value="3" data-position="5">Item 3</option>
									</select>
								</div>
								
								<div class="col-sm-2">
									<button type="button" id="search_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
									<button type="button" id="search_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
									<button type="button" id="search_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
									<button type="button" id="search_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
								</div>
								
								<div class="col-sm-5">
									<select name="to[]" id="search_to" class="form-control" size="8" multiple="multiple"></select>
								</div>
							</div>
							</div>
							<!--<div class="form-group">
								<label for="user_create">User Create</label>
								<input name="user_create" class="form-control" placeholder="User Create">
							</div>
							<div class="form-group">
								<label for="date_create">Date Create</label>
								<input name="date_create" class="form-control" placeholder="Date Create">
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
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#search').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            },
            fireSearch: function(value) {
                return value.length > 3;
            }
        });
    });
</script>
<script src="<?php echo base_url() . 'assets/modules/conf_setup_rrk/setup-rrk-form.js' ?>"></script>


