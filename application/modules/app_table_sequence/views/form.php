<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Table Sequence <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Table Sequence</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Table Sequence</h3>
                </div>

                <form id="fm-table-sequence" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="id">Id</label>
								<input name="id" class="form-control" placeholder="Id">
							</div>
							<div class="form-group">
								<label for="name">Name</label>
								<input name="name" class="form-control" placeholder="Name">
							</div>
							<div class="form-group">
								<label for="prefix">Prefix</label>
								<input name="prefix" class="form-control" placeholder="Prefix">
							</div>
							<div class="form-group">
								<label for="increment">Increment</label>
								<input name="increment" class="form-control" placeholder="Increment">
							</div>
							<div class="form-group">
								<label for="pad">Pad</label>
								<input name="pad" class="form-control" placeholder="Pad">
							</div>
							<div class="form-group">
								<label for="row">Row</label>
								<input name="row" class="form-control" placeholder="Row">
							</div>
							<div class="form-group">
								<label for="used">Used</label>
								<input name="used" class="form-control" placeholder="Used">
							</div>
							<div class="form-group">
								<label for="last_insert_id">Last Insert Id</label>
								<input name="last_insert_id" class="form-control" placeholder="Last Insert Id">
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
<script src="<?php echo base_url() . 'assets/modules/app_table_sequence/table-sequence-form.js' ?>"></script>
