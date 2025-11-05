<style>	
	#maps {
		flex: 1;
		height: 75vh;
		width: 100%;
	}
	#sidebar {
		width: 300px;
		border-right: 1px solid #ccc;
		padding: 10px 0 0 10px;
	}
	#outletList {
		overflow: hidden auto;
		height: 72vh;
		margin-top: 10px;
	}
	.outlet-item {
		padding: 5px;
		border-bottom: 1px solid #eee;
		cursor: pointer;
		transition: background 0.2s;
	}
	.outlet-item:hover {
		background: #f1f1f1;
	}
	.outlet-item.selected {
		background: #ebf6f4;
	}
	#btnSearchArea {
		position: absolute;
		top: 10px;
		left: 60%;
		transform: translateX(-50%);
		background: white;
		border: 1px solid #ccc;
		padding: 6px 12px;
		cursor: pointer;
		z-index: 1;
		width: 200px;
		border-radius: 10px;
	}
	#modalMaps .modal-dialog.modal-fullscreen {
		width: 90%;
		height: 90vh;
	}
	#modalMaps .modal-body {
		display: flex;
		height: 75vh;
		padding: 0;
	}
	#modalMaps select,
	#modalMaps input {
		width: 97%;
	}
	.pac-container {
		position: absolute !important;
		z-index: 9999 !important;
	}
	.mt-5px {
		margin-top: 5px;
	}
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup FJP <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup FJP</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Setup FJP</h3>
                </div>

                <form id="fm-add-setup-pjp" role="form" method="post">
                    <div class="box-body">
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="salesmanid">User (PAR-MA)</label>
							<select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="User (PAR-MA)"></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="salesmanid">Pilih Outlet dari:</label> <br>
                        	<a id="btn-maps" href="javascript:void(0)" class="btn btn-success mt-4" disabled>
								<i class="fa fa-map-marker" aria-hidden="true"></i> Maps
							</a>
						</div>
						<div class="form-group col-md-12">
							<div class="col-sm-5">
								<select id="customerid" class="form-control" size="8" multiple="multiple">
								</select>
							</div>
							
							<div class="col-sm-2">
								<button type="button" id="customerid_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
								<button type="button" id="customerid_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
								<button type="button" id="customerid_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
								<button type="button" id="customerid_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
							</div>
							
							<div class="col-sm-5">
								<select id="customerid_to" class="form-control" size="8" multiple="multiple"></select>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week1">Week 1</label>
							<select id="week1-id" name="week1[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week2">Week 2</label>
							<select id="week2-id" name="week2[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week3">Week 3</label>
							<select id="week3-id" name="week3[]" class="form-control" placeholder="Days" ></select>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<label for="week4">Week 4</label>
							<select id="week4-id" name="week4[]" class="form-control" placeholder="Days" ></select>
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
<div class="modal fade" id="modalMaps" tabindex="-1" role="dialog" aria-labelledby="myModalMapsLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered modal-fullscreen">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title font-weight-bold" id="myModalMapsLabel">Peta Lokasi</h4>
				<button type="button" class="close btn-close" data-dismiss="modal" aria-hidden="true"></button>
			</div>
			<div class="modal-body p-0">
				<div id="sidebar">
					<input id="search-input" type="text" class="form-control" placeholder="Cari lokasi...">
					<select id="radiusSelect" class="form-control mt-5px">
						<option value="5" selected>Radius 5 km</option>
						<option value="10">Radius 10 km</option>
						<option value="20">Radius 20 km</option>
						<option value="50">Radius 50 km</option>
					</select>
					<button id="btnSearchArea">Telusuri area ini</button>
					<div id="outletList"></div>
				</div>

				<div id="maps"></div>
			</div>
			<div class="modal-footer">
				<button id="btnSave" class="btn btn-primary">Simpan Outlet</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url() . 'assets/modules/conf_setup_pjp/setup-pjp-form-add.js' ?>"></script>
