<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Karyawan <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Karyawan</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="box-body">
                    <table id="tbl-sales-salesman">
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalMappingDetail" tabindex="-1" role="dialog" aria-labelledby="modalMappingDetailLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title font-weight-bold" id="modalMappingDetailLabel" style="display:inline-block;">Detail Mapping Target</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Salesman Details Table -->
				<table class="table table-striped table-striped" style="margin-bottom: 20px;">
					<tr>
						<th width="15%">Salesman ID</th>
						<td width="35%" id="detail-salesman-id">-</td>
						<th>Tipe Sales</th>
						<td id="detail-salesman-tipe">-</td>
					</tr>
					<tr>
						<th width="15%">Nama Salesman</th>
						<td width="35%" id="detail-salesman-name">-</td>
						<th>Jabatan</th>
						<td id="detail-salesman-jabatan">-</td>
					</tr>
				</table>
				
				<ul class="nav nav-tabs" id="mappingTabs" role="tablist" style="margin-bottom: 15px;">
					<li class="nav-item">
						<a class="nav-link active" id="role-tab" data-toggle="tab" href="#role-target" role="tab" aria-controls="role-target" aria-selected="true">Mapping Role Target</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="spesialis-tab" data-toggle="tab" href="#spesialis-target" role="tab" aria-controls="spesialis-target" aria-selected="false">Mapping Spesialis Target</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="produk-tab" data-toggle="tab" href="#produk-target" role="tab" aria-controls="produk-target" aria-selected="false">Mapping Produk Target</a>
					</li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane fade" id="role-target" role="tabpanel" aria-labelledby="role-tab">
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
									<tbody id="role-active-body">
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
											<th class="th-detail">Target HK (Hari Kerja)</th>
											<th class="th-detail">Target DUB</th>
											<th class="th-detail">Target Call DUB</th>
											<th class="th-detail">Target Call Visit</th>
										</tr>
									</thead>
									<tbody id="role-history-body">
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
<script src="<?php echo base_url() . 'assets/modules/ref_sales_salesman/sales-salesman-content.js' ?>"></script>