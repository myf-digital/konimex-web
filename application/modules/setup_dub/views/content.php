<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Setup DUB <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Setup DUB</a></li>
        <li class="active">Content</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div id="tbl-content" class="box-table box-success">
                <div class="box-body">
                    <table id="tbl-setup-dub" data-options="filterDelay:1500">
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
				<h4 class="modal-title font-weight-bold" id="modalDetailLabel" style="display:inline-block;">Detail Request Setup DUB</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float:right;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped" style="margin-bottom: 20px;">
					<tr>
						<th width="20%">REQ NO</th>
						<td width="30%" id="detail-req-no">-</td>
						<th width="20%">STATUS</th>
						<td width="30%" id="detail-status">-</td>
					</tr>
					<tr>
						<th>PERIODE</th>
						<td id="detail-periode">-</td>
						<th>MEDREP</th>
						<td id="detail-salesman">-</td>
					</tr>
					<tr>
						<th>KETERANGAN</th>
						<td colspan="3" id="detail-keterangan">-</td>
					</tr>
				</table>
				
				<h5 class="font-weight-bold" style="margin-top: 15px; margin-bottom: 10px; font-weight: bold; font-size: 15px; color: #333;">Daftar Outlet & User Binaan (DUB)</h5>
				<div style="max-height: 350px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 0;">
					<table class="table table-striped table-hover" style="margin-bottom: 0;">
						<thead>
							<tr style="background-color: #f5f5f5;">
								<th style="position: sticky; top: 0; background-color: #f5f5f5; z-index: 10; padding: 10px; border-bottom: 2px solid #ddd;">Outlet (Customer)</th>
								<th style="position: sticky; top: 0; background-color: #f5f5f5; z-index: 10; padding: 10px; border-bottom: 2px solid #ddd;">Daftar User Binaan (DUB)</th>
							</tr>
						</thead>
						<tbody id="detail-list-body">
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

<!-- JS content -->
<script src="<?php echo base_url() . 'assets/modules/setup_dub/content.js' ?>"></script>