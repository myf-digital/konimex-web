<!-- Content Header (Page header) -->
<style>
/*$baseColor: #398B93;
$borderRadius: 10px;
$imageBig: 100px;
$imageSmall: 60px;
$padding: 10px;

body {
   background-color: lighten($baseColor, 30%);
   * { box-sizing: border-box; }
}

.header {
   background-color: darken($baseColor, 5%);
   color: white;
   font-size: 1.5em;
   padding: 1rem;
   text-align: center;
   text-transform: uppercase;
}

img {
   border-radius: 50%;
   height: $imageSmall;
   width: $imageSmall;
}

.table-users {
   border: 1px solid darken($baseColor, 5%);
   border-radius: $borderRadius;
   box-shadow: 3px 3px 0 rgba(0,0,0,0.1);
   max-width: calc(100% - 2em);
   margin: 1em auto;
   overflow: hidden;
   width: 800px;
}

table {
   width: 100%;
   
   td, th { 
      color: darken($baseColor, 10%);
      padding: $padding; 
   }
   
   td {
      text-align: center;
      vertical-align: middle;
      
      &:last-child {
         font-size: 0.95em;
         line-height: 1.4;
         text-align: left;
      }
   }
   
   th { 
      background-color: lighten($baseColor, 50%);
      font-weight: 300;
   }
   
   tr {     
      &:nth-child(2n) { background-color: white; }
      &:nth-child(2n+1) { background-color: lighten($baseColor, 55%) }
   }
}

@media screen and (max-width: 700px) {   
   table, tr, td { display: block; }
   
   td {
      &:first-child {
         position: absolute;
         top: 50%;
         transform: translateY(-50%);
         width: $imageBig;
      }

      &:not(:first-child) {
         clear: both;
         margin-left: $imageBig;
         padding: 4px 20px 4px 90px;
         position: relative;
         text-align: left;

         &:before {
            color: lighten($baseColor, 30%);
            content: '';
            display: block;
            left: 0;
            position: absolute;
         }
      }

      &:nth-child(2):before { content: 'Name:'; }
      &:nth-child(3):before { content: 'Email:'; }
      &:nth-child(4):before { content: 'Phone:'; }
      &:nth-child(5):before { content: 'Comments:'; }
   }
   
   tr {
      padding: $padding 0;
      position: relative;
      &:first-child { display: none; }
   }
}

@media screen and (max-width: 500px) {
   .header {
      background-color: transparent;
      color: lighten($baseColor, 60%);
      font-size: 2em;
      font-weight: 700;
      padding: 0;
      text-shadow: 2px 2px 0 rgba(0,0,0,0.1);
   }
   
   img {
      border: 3px solid;
      border-color: lighten($baseColor, 50%);
      height: $imageBig;
      margin: 0.5rem 0;
      width: $imageBig;
   }
   
   td {
      &:first-child { 
         background-color: lighten($baseColor, 45%); 
         border-bottom: 1px solid lighten($baseColor, 30%);
         border-radius: $borderRadius $borderRadius 0 0;
         position: relative;   
         top: 0;
         transform: translateY(0);
         width: 100%;
      }
      
      &:not(:first-child) {
         margin: 0;
         padding: 5px 1em;
         width: 100%;
         
         &:before {
            font-size: .8em;
            padding-top: 0.3em;
            position: relative;
         }
      }
      
      &:last-child  { padding-bottom: 1rem !important; }
   }
   
   tr {
      background-color: white !important;
      border: 1px solid lighten($baseColor, 20%);
      border-radius: $borderRadius;
      box-shadow: 2px 2px 0 rgba(0,0,0,0.1);
      margin: 0.5rem 0;
      padding: 0;
   }
   
   .table-users { 
      border: none; 
      box-shadow: none;
      overflow: visible;
   }
}*/

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
        Absensi <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Absensi</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Absensi</h3>
                </div>

                <form id="fm-report-absensi" role="form" method="post">
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
<script src="<?php echo base_url() . 'assets/modules/rep_absensi/rep-absensi-form.js' ?>"></script>
