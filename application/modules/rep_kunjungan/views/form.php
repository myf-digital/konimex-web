<!-- Content Header (Page header) -->
<style>
.fixed-table{
  width: 100%;
  table-layout: fixed;
  border-collapse: collapse;
}

.fixed-table td {
  /* css-3 */
  white-space: -o-pre-wrap; 
  word-wrap: break-word;
  white-space: pre-wrap; 
  white-space: -moz-pre-wrap; 
  white-space: -pre-wrap; 
  font-family: sans-serif;
  font-size: 1.1em;
}

.fixed-table th {
  /* css-3 */
  font-family: sans-serif;
  font-size: 1.2em;
  background-color: #3d8cbc;
  color: white;
}

.container-table{
  overflow: auto; 
  max-width: 100%; 
  white-space: nowrap;
  border-top: 1px solid #d2d6de;
  border-left: 1px solid #d2d6de;
  border-right: 1px solid #d2d6de;
  height: 36px;
}

.container-table-content{
  overflow: auto;
  max-width: 100%; 
  white-space: nowrap;
  height: 500px;
  border-left: 1px solid #d2d6de;
  border-right: 1px solid #d2d6de;
  border-bottom: 1px solid #d2d6de;
}

.container-table::-webkit-scrollbar {
  display: none;
}

.container-table {
  -ms-overflow-style: none;
}
</style>
<section class="content-header">
    <h1>
        Kunjungan <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Kunjungan</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Kunjungan</h3>
                </div>

                <form id="fm-report-kunjungan" role="form" method="post">
                    <div class="box-body col-md-12">
                            <div class="form-group col-md-2">
								<label for="start_periode">Start Periode</label>
                                    <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="start_periode" placeholder="Periode" type="text" class="form-control datepicker" name="start_periode" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-2">
								<label for="end_periode">End Periode</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input id="end_periode" placeholder="End Periode" type="text" class="form-control datepicker" name="end_periode" readonly>
                                    </div>
							</div>
							<div class="form-group col-md-3">
								<label for="salesmanid">TPE User</label>
                        <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="Select TPE"></select>
							</div>

                    </div>

                    <div class="box-footer">
                        <button id="btn-preview-form" type="button" class="btn btn-success fa fa-book"> View</button>
                        <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="tbl-content" class="box-table box-success" style="margin:10px; overflow-y: auto; max-height: 100%; max-width: 99%; white-space: nowrap;">
                            
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/rep_kunjungan/rep-kunjungan-form.js' ?>"></script>
