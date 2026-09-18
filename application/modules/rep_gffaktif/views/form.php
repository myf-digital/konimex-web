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
    height: 90px;
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
    scrollbar-width: none;
    overflow-y: hidden;
  }

  .btn-preview-absensi i {
    margin-left: 2px;
    opacity: 0.85;
  }

  /* Modal Preview Styling */
  #modal-preview-absensi .modal-content {
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border: none;
  }

  #modal-preview-absensi .modal-header {
    background-color: #3c8dbc;
    color: #ffffff;
    padding: 12px 15px;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
  }

  #modal-preview-absensi .modal-header .close {
    color: #ffffff;
    opacity: 0.9;
    text-shadow: none;
  }

  #modal-preview-absensi .modal-header .close:hover {
    opacity: 1;
  }

  #modal-preview-absensi .modal-title {
    font-size: 15px;
    font-weight: 600;
  }

  #modal-preview-absensi .modal-body {
    padding: 15px;
  }

  #modal-absensi-info {
    font-size: 12px;
    font-weight: bold;
    text-align: left;
    background-color: #f7f7f7;
    border-left: 3px solid #3c8dbc;
    padding: 8px 12px;
    border-radius: 2px;
    margin-bottom: 12px;
    color: #495057;
  }

  .modal-absensi-img-wrapper {
    background-color: #f4f4f4;
    padding: 10px;
    border-radius: 4px;
    display: inline-block;
    max-width: 100%;
    border: 1px solid #e9ecef;
  }

  #modal-absensi-img {
    max-height: 400px;
    max-width: 100%;
    margin: 0 auto;
    object-fit: contain;
    cursor: pointer;
  }

  .modal-absensi-ket-box {
    margin-top: 15px;
    margin-bottom: 0;
    text-align: left;
    background-color: #fdfdfd;
    border: 1px solid #e3e3e3;
    padding: 9px 12px;
    border-radius: 3px;
  }

  .modal-absensi-ket-title {
    color: #333333;
    display: block;
    margin-bottom: 4px;
  }

  #modal-absensi-ket {
    margin: 0;
    color: #444444;
    word-break: break-word;
    font-size: 13px;
    font-style: normal;
    white-space: pre-wrap;
  }

  #modal-preview-absensi .modal-footer {
    padding: 10px 15px;
    background-color: #fafafa;
    border-top: 1px solid #eee;
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        TPE Aktif <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> TPE Aktif</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report TPE Aktif</h3>
                </div>

                <form id="fm-report_promo" role="form" method="post">
                    <div class="box-body col-md-12">
                        <div class="form-group col-md-2">
                            <label for="start_periode">Start Periode</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                                <input id="start_periode" placeholder="Start Periode" type="text" class="form-control datepicker" name="start_periode" readonly>
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
                        <div class="form-group col-md-2">
                            <label for="tipe_sales">Position</label>
                            <select id="tipe_sales-id" name="tipe_sales" class="form-control" placeholder="Position"></select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="regional">Regional</label>
                            <select id="regional-id" name="regional" class="form-control" placeholder="Select Regional"></select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="area">Area</label>
                            <select id="area-id" name="area" class="form-control" placeholder="Select Area"></select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="city">Sub Area</label>
                            <select id="city-id" name="city" class="form-control" placeholder="Select Sub Area"></select>
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

<!-- Modal Preview Image & Keterangan Absensi -->
<div class="modal fade" id="modal-preview-absensi" tabindex="-1" role="dialog" aria-labelledby="modalPreviewAbsensiLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalPreviewAbsensiLabel"><i class="fa fa-picture-o"></i> Detail Absensi</h4>
            </div>
            <div class="modal-body text-center">
                <div id="modal-absensi-info" class="text-muted"></div>
                <div class="modal-absensi-img-wrapper">
                    <a id="modal-absensi-link" href="#" target="_blank" title="Klik untuk memperbesar gambar">
                        <img id="modal-absensi-img" src="" alt="Foto Absensi" class="img-responsive img-thumbnail">
                    </a>
                </div>
                <div class="modal-absensi-ket-box">
                    <strong class="modal-absensi-ket-title"><i class="fa fa-info-circle text-primary"></i> Keterangan:</strong>
                    <p id="modal-absensi-ket">-</p>
                </div>
            </div>
            <div class="modal-footer">
                <a id="modal-absensi-btn-full" href="#" target="_blank" class="btn btn-default btn-sm pull-left"><i class="fa fa-external-link"></i> Buka Gambar Penuh</a>
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/modules/rep_gffaktif/rep-gffaktif-form.js' ?>?v=1.0"></script>
