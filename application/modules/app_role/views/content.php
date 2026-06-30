<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Role <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Role</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="box-body">
                    <table id="tbl-role">
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title font-weight-bold" id="modalDetailLabel" style="display:inline-block;">Detail Role</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped" style="margin-bottom: 20px;">
					<tr>
						<th width="20%">Role Name</th>
						<td width="30%" id="detail-role-name">-</td>
						<th width="20%">Description</th>
						<td width="30%" id="detail-description">-</td>
					</tr>
				</table>
				
				<ul class="nav nav-tabs" id="mappingTabs" role="tablist" style="margin-bottom: 15px;">
					<li class="active">
						<a id="role-tab" data-toggle="tab" href="#role-target" role="tab" aria-controls="role-target" aria-selected="true">Mapping Target DUB</a>
					</li>
					<li>
						<a id="spesialis-tab" data-toggle="tab" href="#spesialis-target" role="tab" aria-controls="spesialis-target" aria-selected="false">Mapping Spesialis Target</a>
					</li>
					<li>
						<a id="produk-tab" data-toggle="tab" href="#produk-target" role="tab" aria-controls="produk-target" aria-selected="false">Mapping Produk Target</a>
					</li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane fade in active" id="role-target" role="tabpanel" aria-labelledby="role-tab">
						<div style="margin-bottom: 20px;">
							<h5 class="font-weight-bold h5-title">Mapping Target (Aktif)</h5>
							<div class="table-responsive table-detail">
								<table class="table table-striped table-hover" style="margin-bottom: 0;">
									<thead>
										<tr class="bg-f5">
											<th class="th-detail">Periode</th>
											<th class="th-detail">Target HK (Hari Kerja)</th>
											<th class="th-detail">Target DUB</th>
											<th class="th-detail">Target Call DUB</th>
											<th class="th-detail">Target Call Visit</th>
										</tr>
									</thead>
									<tbody id="detail-list-body">
									</tbody>
								</table>
							</div>
						</div>
						
						<div>
							<h5 class="font-weight-bold h5-title">Mapping Target (Histori)</h5>
							<div class="table-responsive table-history">
								<table class="table table-striped table-hover" style="margin-bottom: 0;">
									<thead>
										<tr class="bg-f5">
											<th class="th-detail">Periode</th>
											<th class="th-detail">Target HK (Hari Kerja)</th>
											<th class="th-detail">Target DUB</th>
											<th class="th-detail">Target Call DUB</th>
											<th class="th-detail">Target Call Visit</th>
										</tr>
									</thead>
									<tbody id="history-list-body">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<div class="tab-pane fade" id="spesialis-target" role="tabpanel" aria-labelledby="spesialis-tab">
						<div style="margin-bottom: 20px;">
							<h5 class="font-weight-bold h5-title">Mapping Target (Aktif)</h5>
							<div class="table-responsive table-detail">
								<table class="table table-striped table-hover" style="margin-bottom: 0;">
									<thead>
										<tr class="bg-f5">
											<th class="th-detail">Periode</th>
											<th class="th-detail">Spesialisasi</th>
											<th class="th-detail">Target</th>
										</tr>
									</thead>
									<tbody id="spesialis-active-body">
									</tbody>
								</table>
							</div>
						</div>
						
						<div>
							<h5 class="font-weight-bold h5-title">Mapping Target (Histori)</h5>
							<div class="table-responsive table-detail">
								<table class="table table-striped table-hover" style="margin-bottom: 0;">
									<thead>
										<tr class="bg-f5">
											<th class="th-detail">Periode</th>
											<th class="th-detail">Spesialisasi</th>
											<th class="th-detail">Target</th>
										</tr>
									</thead>
									<tbody id="spesialis-history-body">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<div class="tab-pane fade" id="produk-target" role="tabpanel" aria-labelledby="produk-tab">
						<div style="margin-bottom: 20px;">
							<h5 class="font-weight-bold h5-title">Mapping Target (Aktif)</h5>
							<div class="table-responsive table-detail">
								<table class="table table-striped table-hover" style="margin-bottom: 0;">
									<thead>
										<tr class="bg-f5">
											<th class="th-detail">Periode</th>
											<th class="th-detail">Produk</th>
											<th class="th-detail">Target</th>
											<th class="th-detail">Target Quantity</th>
										</tr>
									</thead>
									<tbody id="produk-active-body">
									</tbody>
								</table>
							</div>
						</div>
						
						<div>
							<h5 class="font-weight-bold h5-title">Mapping Target (Histori)</h5>
							<div class="table-responsive table-detail">
								<table class="table table-striped table-hover" style="margin-bottom: 0;">
									<thead>
										<tr class="bg-f5">
											<th class="th-detail">Periode</th>
											<th class="th-detail">Produk</th>
											<th class="th-detail">Target</th>
											<th class="th-detail">Target Quantity</th>
										</tr>
									</thead>
									<tbody id="produk-history-body">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/app_role/role-content.js' ?>"></script>