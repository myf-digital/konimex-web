<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Group Responden <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Group Responden</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Group Responden</h3>
                </div>

                <form id="fm-group-peserta" role="form" method="post">
                    <div class="box-body">
							<div class="form-group">
								<label for="nama_group">Group Responden</label>
								<input type="hidden" name="id_group" class="form-control" >
								<input name="nama_group" class="form-control" placeholder="Group Responden">
							</div>
							<div class="form-group">
								<label for="id_karyawan">Responden</label>
								 <select id="id-karyawan" name="id_karyawan[]" class="form-control" multiple = "multiple" ></select>
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
<script src="<?php echo base_url() . 'assets/modules/ref_group_peserta/group-peserta-form.js' ?>"></script>
