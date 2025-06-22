<style type="text/css">
    div.input-group label.error {
        font-size: 11px;
        margin-top: -21px;
        margin-bottom: unset!important;
        color: #d50000;
        padding-left: 30px;
        font-weight: unset !important;
        position: absolute;
        left: 35px;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Absen <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Absen</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Absen</h3>
                </div>

                <form id="fm-absen" role="form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group col-md-7">
                            <label for="salesmanid">GFF (MD/SPG/SALESMAN)</label>
                            <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="GFF (MD/SPG/SALESMAN)"></select>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="periode">Periode</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input id="periode" placeholder="Periode" type="text" class="form-control datepicker" name="periode" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="checkin_date">Check In Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input id="checkin_date" placeholder="Check In Date" type="text" class="form-control datepicker" name="checkin_date" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="checkin_time">Check In Time</label>
                            <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <input type="text" class="form-control" placeholder="Check In Time" name="checkin_time" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="checkout_date">Check Out Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input id="checkout_date" placeholder="Check Out Date" type="text" class="form-control datepicker" name="checkout_date" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="checkout_time">Check Out Time</label>
                            <div class="input-group clockpicker">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                                <input type="text" class="form-control" placeholder="Check Out Time" name="checkout_time" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="status">Status - <input id="flag_adjust-id" type = "checkbox"> Adjust Target PJP<input type="hidden" id="flag-adjust" name="flag_adjust" readonly></label>
                            <select id="status-id" name="status" class="form-control" placeholder="Select Status">
                                <option value="H">Masuk</option>
                                <option value="HF">Libur</option>
                                <option value="C">Cuti</option>
                                <option value="S">Sakit</option>
                            </select>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="salesmanid">File</label>
                            <input type="file" name="fileupload" id="fileupload" class="form-control">
                            <small>*Max Size File Upload 2 Mb</small>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" class="form-control"></textarea>
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
<script src="<?php echo base_url() . 'assets/modules/ref_absen_salesman/ref-absen-form.js' ?>"></script>
