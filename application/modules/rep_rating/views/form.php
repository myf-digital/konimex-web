<!-- Content Header (Page header) -->
<style type="text/css">
.main-rating-box {
    display: flex;
    flex-wrap: wrap;
}

.rating-box p {
    text-align: center;
    position:relative;
}

.rating-box {
    width:245px;
    background:#ffffff;
    margin-right: 20px;
    margin-bottom: 20px;
    border: 1px solid #cccccc6e;
    cursor: pointer;
}

.effect-rating-box {
    -webkit-box-shadow: 0 10px 6px -6px #777;
    -moz-box-shadow: 0 10px 6px -6px #777;
    box-shadow: 0 10px 6px -6px #777;
}

.checked {
  color: orange!important;
}

.fa-star {
    color: #80808057;
}

.rating-detail-container {
    background-color: #fffbf8;
    min-height: 5rem;
    border: 1px solid #f9ede5;
    margin-bottom: 1rem;
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -moz-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    border-radius: 2px;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    padding: 1.875rem;
}

.rating-detail-info {
    text-align: center;
    margin-right: 1.875rem;
}

.retaing-detail-filter {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -moz-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
    margin-left: .9375rem;
}

.retaing-detail-filter .btn {
    margin-right: 5px;
}

.rating-detail-content {
    border-bottom: 1px solid rgba(0,0,0,.09);
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: -ms-flexbox;
    display: flex;
    padding: 1rem 0 1rem 1.25rem;
}

.rating-detail-avatar {
    width: 2.5rem;
    margin-right: .625rem;
    text-align: center;
}

.rating-detail-content-info {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -moz-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
}

.img-outlet {
    width:100px; 
    height:100px;
    margin-top: 10px;
}

.img-rating {
    width:100px; 
    height:100px;
    padding-right: 2px;
}

.outlet-checked {
    border: 1px solid #3d8cbc;
}

.fa-star {
    margin-right: 2px;
}

.title {
    font-weight: bold;
    color: #121212;
}

.subtitle {
    color: #7a7a7a;
}

.button-preview-group {
    margin-top: 27px;
    margin-left: -5px;
}

.pagination-detail {
    margin-top: 10px;
    margin-bottom: 10px;
}

#detail-prev {
    margin-right: 2px;
}

</style>
<section class="content-header">
    <h1>
        Report Rating <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Report Rating</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Rating</h3>
                </div>
                <div class="box-body">
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
                    <div class="button-preview-group">
                        <button id="btn-preview-form" type="button" class="btn btn-success fa fa-search"> Search</button>
                    </div>
                </div>
                <div class="box-body main-rating-box" id="outlet-list">
                        
                </div>
                <div class="box-footer" style="display: none;">
                    <input type="hidden" id="offset" value="0">
                    <input type="hidden" id="result-count" value="0">
                    <input type="hidden" id="start-periode" value="0">
                    <input type="hidden" id="end-periode" value="0">
                    <button type="button" class="btn btn-default" id="outlet-prev"><i class="fa fa-arrow-left"></i></button>
                    <button type="button" class="btn btn-default" id="outlet-next"><i class="fa fa-arrow-right"></i></button>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div id="tbl-content" style="padding: 0px 10px 0px 10px;">
                            <div id="detail-info">
                                
                            </div>
                            <div id="detail-content">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/rep_rating/rep-rating-form.js' ?>"></script>
