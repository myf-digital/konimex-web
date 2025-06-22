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
    height: 70px;
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
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Tracking Product<small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Tracking Product</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Tracking Product</h3>
                </div>

                <form id="fm-report-doi" role="form" method="post">
                    <div class="box-body col-md-12">
                            <div class="form-group col-md-2">
								<label for="year">Year</label>
                                <select id="year-id" name="year" class="form-control" placeholder="Select Year Period">
                                    <?php
                                        for ($i=0; $i < 5; $i++) { 
                                            $year = date('Y');
                                            $opt = $year-$i;
                                            echo '<option>'.$opt.'</option>';
                                        }
                                    ?>
                                </select>
							</div>
							<div class="form-group col-md-2">
								<label for="month">Month</label>
                                <select id="month-id" name="month" class="form-control" placeholder="Select Month Period">
                                    <?php
                                        $monthNow = date('m');
                                        $bulan=array("January","February","March","April","May","June","July","August","September","October","November","December");
                                        $rtnbln=array("01","02","03","04","05","06","07","08","09","10","11","12");
                                        $jlh_bln=count($bulan);
                                        for($c=0; $c<$jlh_bln; $c+=1){
                                            $select = $rtnbln[$c] == $monthNow ? 'selected' : '';
                                            echo"<option value=$rtnbln[$c] $select> $bulan[$c] </option>";
                                        }
                                    ?>
                                </select>
							</div>
                            <div class="form-group col-md-3">
                                <label for="regional">Regional</label>
                                <select id="regional-id" name="regional" class="form-control" placeholder="Select Regional"></select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="city">City</label>
                                <select id="city-id" name="city" class="form-control" placeholder="Select City"></select>
                            </div>
							<div class="form-group col-md-2">
                            </div>
							<div class="form-group col-md-2">
								<label for="brand">Brand</label>
                                <select id="brand-id" name="brand" class="form-control" placeholder="Select Brand"></select>
							</div>
                            <div class="form-group col-md-10">
                                <label for="sku">SKU</label>
                                <select id="sku-id" name="sku" class="form-control" placeholder="Select SKU"></select>
                            </div>
                    </div>

                    <div class="box-footer">
                        <button id="btn-preview-form" type="button" class="btn btn-success fa fa-book"> View</button>
                        <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="tbl-content" class="box-table box-success" style="margin:10px; overflow: auto; max-height: 100%; max-width: 99%; white-space: nowrap;">
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/tracking_product/tracking-product-form.js' ?>"></script>
