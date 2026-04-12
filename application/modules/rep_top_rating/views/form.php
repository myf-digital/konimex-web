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
  scrollbar-width: none;
  overflow-y: hidden;
}

.clickable-row {
    cursor: pointer;
}

</style>
<section class="content-header">
    <h1>
        Report Top Rating <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Report Top Rating</a></li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Top Rating</h3>
                </div>
                <form id="fm-rekap_rating" role="form" method="post">
                    <div class="box-body col-md-12">
                        <div class="form-group col-md-1">
                            <label for="top_rating">Top Rating</label>
                            <select id="top_rating-id" name="top_rating" class="form-control" placeholder="Top Rating"></select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="search">Search</label>
                            <input id="search-id" type="text" name="search" class="form-control" placeholder="Outlet Nama / Outlet ID / Kode Outlet">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="salesmanid">User MEDREP</label>
                            <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="Select User MEDREP"></select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button id="btn-preview-form" type="button" class="btn btn-success fa fa-search">Search</button>
                        <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">Download</button>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="tbl-content" class="box-table box-success" style="margin: 10px; overflow-y: auto; max-height: 100%; max-width: 99%; white-space: nowrap;"></div>
                        </div>
                        <div class="col-xs-12">
                            <div id="tbl-content" style="padding: 0px 10px 0px 10px;">
                                <div id="detail-info">
                                    
                                </div>
                                <div id="detail-content">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url() . 'assets/modules/rep_top_rating/rep-top-rating-form.js' ?>"></script>
