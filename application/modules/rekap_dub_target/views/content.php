<section class="content-header">
    <h1>Rekap DUB Target</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Rekap DUB Target</li>
    </ol>
</section>

<section id="content-main" class="content">
    <!-- Filter -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <div class="form-group col-md-2">
                        <label>Tanggal Mulai</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                            <input id="start_date" type="text" class="form-control datepicker" readonly>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Tanggal Selesai</label>
                        <div class="input-group date">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
                            <input id="end_date" type="text" class="form-control datepicker" readonly>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Salesman</label>
                        <select id="salesman_ids" class="form-control select2" multiple="multiple" data-placeholder="Salesman (Semua)">
                        </select>
                    </div>
                    <div class="form-group col-md-3" style="white-space: nowrap;">
                        <button id="btn_load" type="button" class="btn btn-warning" style="margin-top:2.7rem;">
                            <i class="fa fa-refresh"></i> Load Data
                        </button>
                        <button id="btn_export" type="button" class="btn btn-success" style="margin-top:2.7rem; margin-left:6px;">
                            <i class="fa fa-file-excel-o"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body table-responsive">
                    <table id="tbl-summary" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Salesman ID</th>
                                <th>Nama Salesman</th>
                                <th>Tipe Sales</th>
                                <th>Actual DUB</th>
                                <th>Target DUB</th>
                                <th>Pencapaian DUB</th>
                                <th>Actual Visit</th>
                                <th>Target Visit</th>
                                <th>Pencapaian Visit</th>
                                <th style="width: 10%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="11" class="text-center">Silakan klik Load Data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detail Visit Modal -->
<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header" style="background:#0073b7;color:#fff;border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
                <h4 class="modal-title" id="modal-title-detail">Detail Kunjungan</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tbl-detail" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Customer ID</th>
                                <th>Nama Customer</th>
                                <th>Tipe</th>
                                <th>Nama Dokter</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Durasi</th>
                                <th>Jenis Visit</th>
                                <th>Detailing Produk</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="11" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/modules/rekap_dub_target/rekap-dub-target.js' ?>"></script>
