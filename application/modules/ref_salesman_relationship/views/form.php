<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        TPE Relationship <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> TPE Relationship</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form TPE Relationship</h3>
                </div>
                <form id="fm-salesman-relationship" role="form" method="post">
                    <div class="box-body">
						<input type="hidden" id="siteid" name="siteid">
						<input type="hidden" id="nama_salesman" name="nama_salesman">
						<div class="form-group col-md-4">
							<label for="salesmanid">TPE</label>
							<select id="salesmanid" name="salesmanid" class="form-control" placeholder="TPE"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="relationship">Relationship</label>
							<select id="relationship" name="relationship" class="form-control" placeholder="Relationship"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="relationship">Nama Relationship</label>
							<input type="text" id="nama_relationship" name="nama_relationship" class="form-control" placeholder="Nama Relationship">
						</div>
						<div class="form-group col-md-4">
							<label for="jenis_kelamin">Jenis Kelamin</label>
							<select id="jenis_kelamin" name="jenis_kelamin" class="form-control" placeholder="Jenis Kelamin"></select>
						</div>
						<div class="form-group col-md-4">
							<label for="tanggal_lahir">Tanggal Lahir</label>
							<input type="text" id="tanggal_lahir" name="tanggal_lahir" class="form-control datepicker" placeholder="Tanggal Lahir" readonly>
						</div>
						<div class="form-group col-md-4">
							<label for="keterangan">Keterangan</label>
							<textarea name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan"></textarea>
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
<script src="<?php echo base_url() . 'assets/modules/ref_salesman_relationship/salesman-relationship-form.js' ?>"></script>
