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
      Report Progress Listing <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Progress Listing</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="box box-success">
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

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/rep_progress_listing/rep-progress-listing-content.js'?>"></script>
