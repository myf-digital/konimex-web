<!-- Content Header (Page header) -->
<style type="text/css">
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
    height: 1000px;
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

  html {
    scroll-behavior: smooth;
  }

  .select2 {
    width: 160px!important;
  }

  #btn-search {
    height: 33px!important;
    padding: 4px 15px;
    font-size: 12px;
  }

  #btn-download {
    height: 33px!important;
    padding: 4px 15px;
    font-size: 12px;
    margin-left: 0px;
  }
</style>

<section class="content-header">
    <h1>
      Review Outlet<small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Review Outlet</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="box-table box-success">
            <div class="box-body col-md-12">
                <table id="tbl"></table>
            </div>
            <div class="row">
              <div class="col-xs-12">
                <a href="#tbl-content" id="click-scroll" style="display: none;"></a>
                <div id="tbl-content" class="box-table box-success" style="margin:10px; overflow: auto; max-height: 100%; max-width: 99%; white-space: nowrap;">
                </div>
              </div>
            </div>
        </div>
    </div>
  </div>
</section>


<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content" style="overflow-y: auto; width:9x`00px; max-height: 750px; max-width: 1024px;" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">List Data</h4>
      </div>
		<div class="modal-body" style=" max-height: 100%; width: 100%;">
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>      
      </div>
    </div>
  </div>
</div>

<script src="<?php echo base_url() . 'assets/modules/rep_join_visit_outlet/rep-join-visit-outlet-content.js' ?>"></script>
