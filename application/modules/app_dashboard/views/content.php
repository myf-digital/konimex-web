<!-- Content Header (Page header) -->
<style>
  .customBox {
    background: yellow;
    position: absolute;
    padding:2px;
    min-width:10px;
    position-y:+30px;
    color:white;
  }

  .customBox2 {
    background: blue;      
    position: absolute;
    padding:2px;
    min-width:10px;
    position-y:+15px;
    color:white;
  }

  .controlUI {
    background-color: #fff;
    border: 2px solid #fff;
    border-radius: 3px;
    box-shadow: 0 2px 6px rgba(0,0,0,.3);
    margin-top: 22px;
    margin-right: 22px;
    text-align: center;
    min-width:200px;
    height:450px;
  }
  .controlText {
    color: rgb(25,25,25);
    font-family: Roboto,Arial,sans-serif;
    font-size: 12px;
    line-height: 18px;
    padding-left: 5px;
    padding-right: 5px;
  }
</style>

<section class="content-header">
</section>

<!-- Main content -->
<section id="content-main" class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <section class="content-header">
          <h1>
            Dashboard Maps
          </h1>
        </section>

        <!-- /.box-header -->
        <div class="box-body">
          <div class="col-md-12">
            <!-- /.col -->

            <div id="maps" style="height:500px;">
              <!-- /.progress-group -->
            </div>
            <div id="tbl-content" class="box-table box-success">
              <div class="box-body">
                <table id="tbl">
                </table>
              </div>
            </div>
            <!-- /.col -->
          </div>
            <!-- /.row -->
        </div>
        <!-- ./box-body -->
        <!-- /.box-footer -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

</section>
<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content" style="overflow-y: auto; width:9x`00px; max-height: 500px;" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">List Data</h4>
      </div>
		<div class="modal-body" style=" max-height: 75%; width: 100%;">
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>      
      </div>
    </div>
  </div>
</div>

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/app_dashboard/dashboard-content.js' ?>"></script>
