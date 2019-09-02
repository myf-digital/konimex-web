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
                    <h3 class="box-title">Form Event Publish</h3>
                </div>

                <form id="fm-event-publish" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="event">Event Name</label>
								<input name="event" class="form-control" placeholder="Event" disabled = "true" >
							</div>
							<div class="form-group">
								<label for="id_rdg_publish">Materi Rapat Dewan Gubernur</label>
								 <select id="id-rdg-publish" name="id_rdg_publish[]" class="form-control" multiple = "multiple" >
								 </select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_event/event-form-publish.js' ?>"></script>
