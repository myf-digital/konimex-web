<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup Planned <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup Planned</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup Planned</h3>
                </div>

                <form id="fm-add-setup-planned" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-xs-12 col-sm-12 col-md-4">
							<label for="salesmanid">MEDREP</label>
							<select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="MEDREP"></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-4">
							<label for="periode">Periode</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
								<input id="periode" placeholder="Periode" type="text" class="form-control datepicker" name="periode" readonly>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-4">
							<label for="keterangan">Keterangan</label>
							<textarea id="keterangan" name="keterangan" class="form-control" placeholder="Keterangan" rows="1"></textarea>
						</div>
						<div id="professional-container" class="form-group col-md-12" style="display: none; margin-top: 20px;">
							<label id="list-professional">List Planned</label>
							<div class="table-detail" style="min-height: 600px;">
								<div id="professional-list"></div>
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

<script src="<?php echo base_url() . 'assets/modules/setup_planned/form.js' ?>"></script>
