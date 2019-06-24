<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Event <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Event</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Event</h3>
                </div>

                <form id="fm-event" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="event">Event Name</label>
								<input name="event" class="form-control" placeholder="Event">
							</div>
							<div class="form-group">
								<label for="tanggal">Start Periode</label>
								<div class="input-group date">
									  <div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									  </div>
									  <input name="tanggal" type="text" class="form-control pull-right" id="id-tanggal" placeholder="Mulai pelaksanaan">
								</div>
							</div>
							<div class="form-group">
								<label for="end_periode">End Periode</label>
								<div class="input-group date">
									  <div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									  </div>
									  <input name="end_periode" type="text" class="form-control pull-right" id="id-end_periode" placeholder="Selsai pelaksanaan">
								</div>
							</div>
							<div class="form-group">
								<label for="id_rdg">Materi Rapat Dewan Gubernur</label>
								 <select id="id-rdg" name="id_rdg[]" class="form-control" multiple = "multiple" >
								 </select>
							</div>
							<div class="form-group">
								<label for="id_karyawan">Peserta</label>
								 <select id="id-karyawan" name="id_karyawan[]" class="form-control" multiple = "multiple" >
								 </select>
							</div>
							<div class="form-group">
								<label for="password">Default Password</label>
								<input name="password" class="form-control" placeholder="Default Password">
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
<script src="<?php echo base_url() . 'assets/modules/ref_event/event-form.js' ?>"></script>
