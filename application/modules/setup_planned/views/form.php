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

						<div class="form-group col-xs-12 col-sm-12 col-md-4" id="daterange-container" style="display: none; margin-top: 20px;">
							<label>Tanggal Kunjungan</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
								<input id="planned-daterange" placeholder="Pilih Rentang Tanggal" type="text" class="form-control" readonly style="background-color: #fff; cursor: pointer;">
							</div>
						</div>

						<div class="form-group col-xs-12 col-sm-12 col-md-12" id="planned-dates-container" style="display: none; margin-top: 20px;">
							<label>
								List Tanggal Kunjungan Planned
								<span id="main-total-planned-badge" class="label label-success" style="margin-left: 5px;">0 terpilih</span>
							</label>
							<div id="date-buttons-container" style="max-height: 400px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #fdfdfd; margin-top: 10px;">
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

<!-- Modal Accordion DUB -->
<div class="modal fade" id="modalPlannedAccordion" role="dialog" aria-labelledby="modalPlannedAccordionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalPlannedAccordionLabel">Pilih User Binaan untuk Tanggal: <span id="modal-planned-date-display" style="font-weight: bold; color: #3c8dbc;"></span></h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <input type="text" id="modal-search-outlet" class="form-control" placeholder="Cari Outlet atau User..." style="margin-bottom: 15px;">
                <div id="modal-outlet-accordion-container">
                </div>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>Total Terpilih pada Tanggal ini: </strong>
                    <span id="modal-date-total-badge" class="label label-success" style="font-size: 13px;">0 terpilih</span>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Simpan & Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/modules/setup_planned/form.js' ?>"></script>
