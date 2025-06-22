<!-- Content Header (Page header) -->
<style type="text/css">

    .filter-date {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 1em;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .filter-date thead tr {
        background-color: #3d8cbc;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
    }

    .filter-date th,
    .filter-date td {
        padding: 12px 15px;
    }

    .filter-date tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .filter-date tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .filter-date tbody tr:last-of-type {
        border-bottom: 2px solid #3d8cbc;
    }

    .filter-date tbody tr.active-row {
        font-weight: bold;
        color: #009879;
    }

    .filter-date-pa {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        min-width: 100%;
        font-size: 1em;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .filter-date-pa thead tr {
        background-color: #f1934d;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
    }

    .filter-date-pa th,
    .filter-date-pa td {
        padding: 12px 15px;
    }

    .filter-date-pa tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .filter-date-pa tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .filter-date-pa tbody tr:last-of-type {
        border-bottom: 2px solid #3d8cbc;
    }

    .filter-date-pa tbody tr.active-row {
        font-weight: bold;
        color: #009879;
    }

    .filter-date-sos {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        min-width: 50%;
        font-size: 1em;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .filter-date-sos thead tr {
        background-color: #3d8cbc;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
    }

    .filter-date-sos th,
    .filter-date-sos td {
        padding: 12px 15px;
    }

    .filter-date-sos tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .filter-date-sos tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .filter-date-sos tbody tr:last-of-type {
        border-bottom: 2px solid #3d8cbc;
    }

    .filter-date-sos tbody tr.active-row {
        font-weight: bold;
        color: #009879;
    }
    .report-table {
        font-family: sans-serif;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 1em;
        min-width: 400px;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .report-table tbody th {
      background-color: #3d8cbc;
      color: #ffffff;
      text-align: left;
      font-weight: bold;
    }

    .report-table th,
    .report-table td {
      padding: 12px 15px;
    }

    .report-table tbody tr {
      border-bottom: 1px solid #dddddd;
    }

</style>
<section class="content-header">
    <h1>
        DRC Dashboard <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> DRC Dashboard</a></li>
        <li class="active"></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header">
                <nav class="nav nav-pills">
                <button id="btn-mp-form" type="button" class="btn btn-primary fa fa-user btn-lg"> Man Power</button>
                <button id="btn-pa-form" type="button" class="btn btn-warning fa fa-cube btn-lg"> Product Availability</button>
                <button id="btn-sos-form" type="button" class="btn btn-info fa fa-th-large btn-lg"> SOS</button>
                </nav>

                        <table class="filter-date" id="id-tbmp">
                            <thead>
                                <tr>
                                    <td>Year</td>
                                    <td>Month</td>
                                    <td>Date</td>
                                    <td colspan="2"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>
                            <?php
                            $now=date('Y');
                            echo "<select class='form-control' name='tahun' id='tahun-id'>";
                            for ($a=2020;$a<=$now;$a++)
                            {
                                echo "<option value='$a'>$a</option>";
                            }
                            echo "</select>";
                            ?>
                            </td><td>
                            <select class="form-control" name="bln" id="bln-id">
                            <option selected="selected" value="All">All</option>
                            <?php
                            $bulan=array("January","February","March","April","May","June","July","August","September","October","November","December");
                            $rtnbln=array("01","02","03","04","05","06","07","08","09","10","11","12");
                            $jlh_bln=count($bulan);
                            for($c=0; $c<$jlh_bln; $c+=1){
                                echo"<option value=$rtnbln[$c]> $bulan[$c] </option>";
                            }
                            ?>
                            </td><td>
                            </select>
                            <select class="form-control" name="tgl" id="tgl-id">
                            <option selected="selected" value="All">All</option>
                            <?php
                            for($a=1; $a<=31; $a+=1){
                                echo"<option value=$a> $a </option>";
                            }
                            ?>
                            </select>
                            </td><td>&nbsp;
                            </td><td>
                            <button id="btn-preview-form" type="button" class="btn btn-success fa fa-search btn-lg"> Search</button>
                            </td>
                            </tr>
                            </tbody>
                            </table>

                            <table class="filter-date-pa" id="id-tbpa">
                            <thead>
                                <tr>
                                    <td>Year</td>
                                    <td>Month</td>
                                    <td>Account</td>
                                    <td>Brand</td>
                                    <td>SKU</td>
                                    <td colspan="2"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>
                            <?php
                            $now=date('Y');
                            echo "<select class='form-control' name='tahun' id='tahun-id-pa'>";
                            for ($a=2020;$a<=$now;$a++)
                            {
                                echo "<option value='$a'>$a</option>";
                            }
                            echo "</select>";
                            ?>
                            </td><td>
                            <select class="form-control" name="bln" id="bln-id-pa">
                            <option selected="selected" value="All">All</option>
                            <?php
                            $bulan=array("January","February","March","April","May","June","July","August","September","October","November","December");
                            $rtnbln=array("01","02","03","04","05","06","07","08","09","10","11","12");
                            $jlh_bln=count($bulan);
                            for($c=0; $c<$jlh_bln; $c+=1){
                                echo"<option value=$rtnbln[$c]> $bulan[$c] </option>";
                            }
                            ?>
                            </select>
                            </td><td>
                            <select class="form-control" name="classid" id="classid-id-pa"></select>
                            </td><td>
                            <select class="form-control" name="brandid" id="brandid-id"></select>
                            </td><td>
                            <select class="form-control" name="productid" id="productid-id"></select>
                            </td><td>
                            <button id="btn-preview-form-pa" type="button" class="btn btn-success fa fa-search btn-lg"> Search</button>
                            </td></tr>
                            </tbody>
                            </table>

                            <table class="filter-date-sos" id="id-tbsos">
                            <thead>
                                <tr>
                                    <td>Year</td>
                                    <td>Month</td>
                                    <td>Account</td>
                                    <td colspan="2"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>
                            <?php
                            $now=date('Y');
                            echo "<select class='form-control' name='tahun' id='tahun-id-sos'>";
                            for ($a=2020;$a<=$now;$a++)
                            {
                                echo "<option value='$a'>$a</option>";
                            }
                            echo "</select>";
                            ?>
                            </td><td>
                            <select class="form-control" name="bln" id="bln-id-sos">
                            <option selected="selected" value="All">All</option>
                            <?php
                            $bulan=array("January","February","March","April","May","June","July","August","September","October","November","December");
                            $rtnbln=array("01","02","03","04","05","06","07","08","09","10","11","12");
                            $jlh_bln=count($bulan);
                            for($c=0; $c<$jlh_bln; $c+=1){
                                echo"<option value=$rtnbln[$c]> $bulan[$c] </option>";
                            }
                            ?>
                            </select>
                            </td><td>
                            <select class="form-control" name="classid" id="classid-id-sos"></select>
                            </td><td>
                            <button id="btn-preview-form-sos" type="button" class="btn btn-success fa fa-search btn-lg"> Search</button>
                            </td></tr>
                            </tbody>
                            </table>
                            <!--<button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>-->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div id="tbl-content" class="box-table"></div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/drc_dashboard/drc_dashboard-form.js' ?>"></script>
