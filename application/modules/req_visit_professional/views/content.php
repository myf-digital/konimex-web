<style type="text/css">
  .modal-detail-info-table th {
    width: 30%;
    background-color: #f9fafb;
    color: #4b5563;
    font-weight: 600;
  }
  .img-gallery-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
  }
  .img-gallery-card {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    width: calc(33.333% - 10px);
    min-width: 180px;
    display: flex;
    flex-direction: column;
  }
  .img-gallery-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.15);
  }
  .img-gallery-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    cursor: pointer;
    background-color: #f3f4f6;
  }
  .img-gallery-card .card-footer-info {
    padding: 8px 10px;
    font-size: 11px;
    color: #6b7280;
    background: #f9fafb;
    border-top: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  @media (max-width: 768px) {
    .img-gallery-card {
      width: 100%;
    }
  }
</style>

<section class="content-header">
    <h1>
      Join Visit <small>Reporting</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Report</a></li>
        <li class="active">Join Visit</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">List Join Visit</h3>
                </div>

                <form id="fm-report-req-visit" role="form" method="post">
                    <div class="box-body col-md-12">
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
                        <div class="form-group col-md-3">
                            <label for="salesmanid">MEDREP User</label>
                            <select id="salesmanid-id" name="salesmanid" class="form-control" placeholder="Select MEDREP"></select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="status">Status</label>
                            <select id="status-id" name="status" class="form-control" placeholder="Select Status">
                                <option value="">Semua Status</option>
                                <option value="1">Pending</option>
                                <option value="3">Disetujui</option>
                                <option value="2">Reject</option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button id="btn-preview-form" type="button" class="btn btn-success fa fa-book"> View</button>
                        <button id="btn-download-form" type="button" class="btn btn-primary fa fa-download">  Download</button>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="tbl-content" class="box-table box-success" style="margin:10px; overflow-y: auto; max-height: 100%; max-width: 99%; white-space: nowrap;">
                                <table id="tbl-req-visit-professional"></table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modal_detail_visit" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width: 900px; width: 95%;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
      <div class="modal-header bg-primary" style="color: #fff; padding: 15px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.9;">&times;</button>
        <h4 class="modal-title" id="modalDetailLabel" style="font-weight: 600;">
          <i class="fa fa-user-md"></i> Detail Join Visit
        </h4>
      </div>
      <div class="modal-body" style="padding: 20px; max-height: calc(85vh - 120px); overflow-y: auto;">
        
        <div class="box box-solid box-default" style="margin-bottom: 20px; border: 1px solid #e5e7eb; border-radius: 6px;">
          <div class="box-header with-border" style="background: #f8fafc;">
            <h3 class="box-title" style="font-size: 14px; font-weight: 700; color: #374151;">
              <i class="fa fa-info-circle text-primary"></i> Informasi
            </h3>
          </div>
          <div class="box-body no-padding">
            <table class="table table-bordered modal-detail-info-table" style="margin: 0;">
              <tbody>
                <tr>
                  <th>Periode / Tanggal</th>
                  <td id="detail-periode">-</td>
                </tr>
                <tr>
                  <th>Salesman</th>
                  <td><span id="detail-salesman-name" style="font-weight: 600;">-</span> <span id="detail-salesman-id" class="text-muted">(-)</span></td>
                </tr>
                <tr>
                  <th>Outlet</th>
                  <td><span id="detail-customer-name" style="font-weight: 600;">-</span> <span id="detail-customer-id" class="text-muted">(-)</span></td>
                </tr>
                <tr>
                  <th>Professional</th>
                  <td><span id="detail-prof-name" style="font-weight: 600; color: #0073b7;">-</span> <span id="detail-prof-id" class="text-muted">(-)</span></td>
                </tr>
                <tr>
                  <th>Status</th>
                  <td id="detail-status">-</td>
                </tr>
                <tr>
                  <th>Keterangan</th>
                  <td id="detail-keterangan" style="white-space: pre-wrap;">-</td>
                </tr>
                <tr id="row-detail-reason" style="display: none;">
                  <th style="color: #dc2626;">Alasan Reject</th>
                  <td id="detail-reason" style="color: #dc2626; white-space: pre-wrap;">-</td>
                </tr>
                <tr id="row-detail-approval" style="display: none;">
                  <th>Disetujui Oleh</th>
                  <td><span id="detail-approved-by">-</span> (<span id="detail-approved-date">-</span>)</td>
                </tr>
                <tr>
                  <th style="background-color: #ecfdf5; color: #065f46;">Keterangan Visit</th>
                  <td id="detail-visit-keterangan" style="font-weight: 500; white-space: pre-wrap;">-</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="box box-solid box-default" style="margin-bottom: 0; border: 1px solid #e5e7eb; border-radius: 6px;">
          <div class="box-header with-border" style="background: #f8fafc;">
            <h3 class="box-title" style="font-size: 14px; font-weight: 700; color: #374151;">
              <i class="fa fa-camera text-primary"></i> Foto Dokumentasi (<span id="doc-count">0</span>)
            </h3>
          </div>
          <div class="box-body" style="padding: 15px;">
            <div id="gallery-container" class="img-gallery-container"></div>
            <div id="gallery-empty" class="alert alert-info" style="display: none; margin: 0;">
              <i class="fa fa-info-circle"></i> Tidak ada foto dokumentasi.
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer" style="background: #f9fafb; border-top: 1px solid #e5e7eb;">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_preview_image" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
  <div class="modal-dialog modal-lg" style="max-width: 800px; width: 90%;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; background: #000;">
      <div class="modal-header" style="border: none; padding: 10px 15px; color: #fff;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #fff; opacity: 0.8; font-size: 24px;">&times;</button>
        <h5 class="modal-title" id="preview-image-title" style="color: #fff; font-weight: 500;">Preview Foto</h5>
      </div>
      <div class="modal-body" style="padding: 0; text-align: center; background: #111;">
        <img id="preview-img-element" src="" style="max-width: 100%; max-height: 80vh; object-fit: contain; margin: 0 auto; display: block;" alt="Foto Dokumentasi">
      </div>
      <div class="modal-footer" style="border: none; padding: 10px 15px; background: #000; text-align: right;">
        <a id="preview-img-download-link" href="#" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Buka Gambar Asli</a>
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo base_url() . 'assets/modules/req_visit_professional/req-visit-professional-content.js' ?>?v=1.0"></script>
