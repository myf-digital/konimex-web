<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup DUB <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup DUB</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup DUB</h3>
                </div>

                <form id="fm-add-setup-dub" role="form" method="post">
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
						<div id="professional-container" class="form-group col-md-12" style="display: none; margin-top: 20px;">
							<label id="list-professional">Daftar User Binaan (DUB)</label>
							<div class="well well-sm" style="background-color: #fdfdfd; border: 1px solid #ddd; border-radius: 4px; padding: 15px; max-height: 350px; overflow-y: auto;">
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

<script src="<?php echo base_url() . 'assets/modules/setup_dub/form.js' ?>"></script>
