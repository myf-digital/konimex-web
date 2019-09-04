<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Event <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Event</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-primary">
                <div class="box-body">
                    <table id="tbl-event">
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="modal-content" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Publish Materi RDG</h3>
            </div>
            <div class="modal-body">
                <p>
					<div class="form-group">
						<label for="id_rdg_publish">Materi Rapat Dewan Gubernur</label>
						 <select id="id-rdg-publish" name="id_rdg_publish[]" class="form-control" multiple = "multiple" >
						 </select>
					</div>
                </p>
            </div>
            <div class="modal-footer"> 
                <a href="#" class="btn" data-dismiss="modal">Close</a>
                 <a href="#" class="btn btn-primary">Save changes</a>
            </div>
        </div>
    </div>
</div>

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/ref_event/event-content.js' ?>"></script>