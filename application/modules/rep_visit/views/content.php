<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Report Visit <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Visit FC</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="box-body">
                    <table id="tbl">
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" >
    <div class="modal-content" style="overflow-y: auto; width:900px; max-height: 700px;" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
		<div class="modal-body" style=" max-height: 75%; width: 100%;">
			<div id="maps" style="height:500px;">
				<!-- /.progress-group -->
			</div>
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>      
      </div>
    </div>
  </div>
</div>


<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/rep_visit/rep-visit-content.js' ?>"></script>