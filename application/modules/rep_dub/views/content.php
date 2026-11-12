<style type="text/css">
  .dub-progress {
    position: relative;
    height: 22px;
    background-color: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    min-width: 115px;
  }
  .dub-progress-fill {
    height: 100%;
    transition: width 0.25s ease;
  }
  .dub-progress-text {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 22px;
    line-height: 22px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #1f2937;
    text-shadow: 0 0 2px rgba(255, 255, 255, 0.85);
  }
  .dub-period-badge {
    display: inline-block;
    padding: 5px 12px;
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }
  .dub-source-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    background-color: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
  }
  #tbl-dub-summary thead th,
  #tbl-dub-detail thead th {
    background-color: #0073b7;
    color: #ffffff;
    vertical-align: middle;
    white-space: nowrap;
  }
</style>

<section class="content-header">
  <h1>
    Report DUB <small>Daftar User Binaan</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Reporting</a></li>
    <li class="active">Report DUB</li>
  </ol>
</section>

<section id="content-main" class="content">
  <!-- Filter Card -->
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Report DUB</h3>
          <div class="box-tools pull-right">
            <span id="active-date-badge" class="dub-period-badge" style="display: none;"></span>
          </div>
        </div>
        <form id="fm-report-dub" role="form" method="post">
          <div class="box-body">
            <div class="row">
              <div class="form-group col-md-2">
                <label for="periode">Bulan &amp; Tahun</label>
                <div class="input-group date">
                  <div class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </div>
                  <input id="periode" name="periode" type="text" class="form-control" placeholder="YYYY-MM" readonly>
                </div>
              </div>

              <div class="form-group col-md-2">
                <label for="week">Minggu Ke-</label>
                <select id="week" name="week" class="form-control">
                  <option value="all">Semua Minggu (1 Bulan Penuh)</option>
                  <option value="1">Minggu Ke-1 (Tgl 01 - 07)</option>
                  <option value="2">Minggu Ke-2 (Tgl 08 - 14)</option>
                  <option value="3">Minggu Ke-3 (Tgl 15 - 21)</option>
                  <option value="4">Minggu Ke-4 (Tgl 22 - Akhir Bulan)</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label for="salesmanid">Salesman / TPE <span class="text-danger">*</span></label>
                <select id="salesmanid" name="salesmanid" class="form-control">
                  <option value=""></option>
                </select>
              </div>

              <div class="form-group col-md-3" style="white-space: nowrap;">
                <button id="btn-preview-form" type="button" class="btn btn-success" style="margin-top: 25px;">
                  <i class="fa fa-book"></i> View
                </button>
                <button id="btn-download-form" type="button" class="btn btn-primary" style="margin-top: 25px; margin-left: 6px;">
                  <i class="fa fa-download"></i> Download Excel
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Summary Per Salesman -->
  <div class="row" id="row-summary">
    <div class="col-xs-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">
            <i class="fa fa-users"></i> Ringkasan Pencapaian DUB Salesman (<span id="summary-count">0</span>)
          </h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body table-responsive">
          <table id="tbl-dub-summary" class="table table-bordered table-striped table-hover" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">No</th>
                <th>Salesman / TPE</th>
                <th style="text-align: center;">Tipe</th>
                <th>Wilayah</th>
                <th style="text-align: center;">Target DUB</th>
                <th style="text-align: center;">Terdaftar DUB</th>
                <th style="text-align: center;">Target Call / Dokter</th>
                <th style="text-align: center;">Total Actual Call</th>
                <th style="text-align: center; min-width: 140px;">Pencapaian DUB</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="10" class="text-center text-muted">Silakan pilih Salesman / TPE terlebih dahulu lalu klik View.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail List DUB -->
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">
            <i class="fa fa-user-md"></i> List DUB Salesman (<span id="detail-count">0</span>)
          </h3>
        </div>
        <div class="box-body table-responsive">
          <table id="tbl-dub-detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">No</th>
                <th>Salesman / TPE</th>
                <th>Outlet / Customer</th>
                <th style="text-align: center;">ID User</th>
                <th>Nama Professional</th>
                <th>Spesialisasi / Tipe</th>
                <th style="text-align: center;">Target Call</th>
                <th style="text-align: center;">Actual Call</th>
                <th style="text-align: center; min-width: 140px;">Pencapaian Dokter</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="9" class="text-center text-muted">Silakan pilih Salesman / TPE terlebih dahulu lalu klik View.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>

<script src="<?php echo base_url() . 'assets/modules/rep_dub/rep-dub-content.js' ?>?v=1.3"></script>

