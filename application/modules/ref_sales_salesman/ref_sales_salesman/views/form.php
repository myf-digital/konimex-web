<!-- Content Header (Page header) -->
<style>
.profile-pic-wrapper {
  height: 100%;
  width: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.pic-holder {
  text-align: center;
  position: relative;
  border-radius: 50%;
  width: 150px;
  height: 150px;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.pic-holder .pic {
  height: 100%;
  width: 100%;
  -o-object-fit: cover;
  object-fit: cover;
  -o-object-position: center;
  object-position: center;
}

.pic-holder .upload-file-block,
.pic-holder .upload-loader {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background-color: rgba(90, 92, 105, 0.7);
  color: #f8f9fc;
  font-size: 12px;
  font-weight: 600;
  opacity: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.pic-holder .upload-file-block {
  cursor: pointer;
}

.pic-holder:hover .upload-file-block,
.uploadProfileInput:focus ~ .upload-file-block {
  opacity: 1;
}

.pic-holder.uploadInProgress .upload-file-block {
  display: none;
}

.pic-holder.uploadInProgress .upload-loader {
  opacity: 1;
}

/* Snackbar css */
.snackbar {
  visibility: hidden;
  min-width: 250px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 1;
  left: 50%;
  bottom: 30px;
  font-size: 14px;
  transform: translateX(-50%);
}

.snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

@-webkit-keyframes fadein {
  from {
    bottom: 0;
    opacity: 0;
  }
  to {
    bottom: 30px;
    opacity: 1;
  }
}

@keyframes fadein {
  from {
    bottom: 0;
    opacity: 0;
  }
  to {
    bottom: 30px;
    opacity: 1;
  }
}

@-webkit-keyframes fadeout {
  from {
    bottom: 30px;
    opacity: 1;
  }
  to {
    bottom: 0;
    opacity: 0;
  }
}

@keyframes fadeout {
  from {
    bottom: 30px;
    opacity: 1;
  }
  to {
    bottom: 0;
    opacity: 0;
  }
}
</style>
<script language="javascript">
$(document).on("change", ".uploadProfileInput", function () {
  var triggerInput = this;
  var currentImg = $(this).closest(".pic-holder").find(".pic").attr("src");
  var holder = $(this).closest(".pic-holder");
  var wrapper = $(this).closest(".profile-pic-wrapper");
  $(wrapper).find('[role="alert"]').remove();
  triggerInput.blur();
  var files = !!this.files ? this.files : [];
  if (!files.length || !window.FileReader) {
    return;
  }
  if (/^image/.test(files[0].type)) {
    // only image file
    var reader = new FileReader(); // instance of the FileReader
    reader.readAsDataURL(files[0]); // read the local file

    reader.onloadend = function () {
      $(holder).addClass("uploadInProgress");
      $(holder).find(".pic").attr("src", this.result);
      $(holder).append(
        '<div class="upload-loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
      );

      // Dummy timeout; call API or AJAX below
      setTimeout(() => {
        $(holder).removeClass("uploadInProgress");
        $(holder).find(".upload-loader").remove();
        // If upload successful
        if (Math.random() < 0.9) {
          $(wrapper).append(
            '<div class="snackbar show" role="alert"><i class="fa fa-check-circle text-success"></i> Profile image updated successfully</div>'
          );

          // Clear input after upload
          $(triggerInput).val("");

          setTimeout(() => {
            $(wrapper).find('[role="alert"]').remove();
          }, 3000);
        } else {
          $(holder).find(".pic").attr("src", currentImg);
          $(wrapper).append(
            '<div class="snackbar show" role="alert"><i class="fa fa-times-circle text-danger"></i> There is an error while uploading! Please try again later.</div>'
          );

          // Clear input after upload
          $(triggerInput).val("");
          setTimeout(() => {
            $(wrapper).find('[role="alert"]').remove();
          }, 3000);
        }
      }, 1500);
    };
  } else {
    $(wrapper).append(
      '<div class="alert alert-danger d-inline-block p-2 small" role="alert">Please choose the valid image.</div>'
    );
    setTimeout(() => {
      $(wrapper).find('role="alert"').remove();
    }, 3000);
  }
});
</script>
<section class="content-header">
    <h1>
        Sales Salesman <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Sales Salesman</a></li>
        <li class="active">Form</li>
    </ol>
</section>

<!-- Main content -->
<section id="content-main" class="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Sales Salesman</h3>
                </div>

                <form id="fm-sales-salesman" role="form" method="post" enctype="multipart/form-data">

                    <div class="box-body">
							<div class="form-group col-md-2">
								<div class="profile-pic-wrapper">
								  <div class="pic-holder">
									<!-- uploaded pic shown here -->
									<img id="imageProfileimg" class="pic" name="image_profile" src="https://demo-gsk.sphere154.com/uploads/noimage.jpg">

									<Input class="uploadProfileInput" type="file" name="imageprofile" id="imageProfile" accept="image/*" style="opacity: 0;" />
									<label for="imageProfile" class="upload-file-block">
									  <div class="text-center">
										<div class="mb-2">
										  <i class="fa fa-camera fa-2x"></i>
										</div>
										<div class="text-uppercase">
										  Update <br /> Profile Photo
										</div>
									  </div>
									</label>
								  </div>

								  </hr>
								  <p class="text-info text-center small">Note: isi dengan foto profile GFF</p>
								</div>
							</div>
							<div class="form-group col-md-3">
								<label for="salesmanid">USER GFF</label>
								<input name="salesmanid" id="salesmanid-id" class="form-control" value="" placeholder="User GFF">
							</div>
							<div class="form-group col-md-3">
								<label for="password">Password</label>
								<input name="password" id="password-id" type="password" class="form-control" value="" placeholder="Password">
							</div>
							<div class="form-group col-md-3"></div>
							<div class="form-group col-md-3">
								<label for="nama_salesman">Nama GFF</label>
								<input name="nama_salesman" class="form-control" placeholder="Nama GFF">
							</div>
							<!--<div class="form-group col-md-3">
								<label for="supervisorid">Supervisorid</label>
								<input name="supervisorid" class="form-control" placeholder="Supervisorid">
							</div>
							<div class="form-group col-md-6">
								<label for="gudangid">Info Gudang</label>
								<select id="gudangid-id" name="gudangid" class="form-control" placeholder="Gudang" disabled></select>
							</div>-->
							<!--<div class="form-group">
								<label for="tipe_db">Tipe Db</label>
								<input name="tipe_db" class="form-control" placeholder="Tipe Db">
							</div>-->
							<div class="form-group  col-md-3">
								<label for="tipe_sales">Tipe GFF</label>
                                <select id="tipe_sales-id" name="tipe_sales" class="form-control" placeholder="Sales Type"></select>
							</div>
							<div class="form-group col-md-2">
								<label for="aktif">Status</label>
                                <select id="aktif-id" name="aktif" class="form-control" placeholder="Active/Not Active"></select>
							</div>
							
							<div class="form-group col-md-3">
								<label for="regionalid">Regional</label>
                                <select id="regionalid-id" name="regionalid" class="form-control" placeholder="Regional"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="areaid">Area</label>
                                <select id="areaid-id" name="areaid" class="form-control" placeholder="Area"></select>
							</div>
							<div class="form-group col-md-3">
								<label for="subareaid">Sub Area</label>
                                <select id="subareaid-id" name="subareaid" class="form-control" placeholder="Sub Area"></select>
							</div>

							<!--<div class="form-group  col-md-4">
								<label for="ram">RAM</label>
                                <select id="ram-id" name="ram" class="form-control" placeholder="RAM"></select>
							</div>
							<div class="form-group  col-md-4">
								<label for="rsm">RSM</label>
                                <select id="rsm-id" name="rsm" class="form-control" placeholder="RSM"></select>
							</div>
							<div class="form-group  col-md-4">
								<label for="aas_aam">AAS-AAM</label>
                                <select id="aas_aam-id" name="aas_aam" class="form-control" placeholder="AAS-AAM"></select>
							</div>
							<div class="form-group  col-md-4">
								<label for="tss_tsm">TSS-TSM</label>
                                <select id="tss_tsm-id" name="tss_tsm" class="form-control" placeholder="TSS-TSM"></select>
							</div>
							<div class="form-group  col-md-4">
								<label for="fc">FC</label>
                                <select id="fc-id" name="fc" class="form-control" placeholder="Field Coordinator"></select>
							</div>

							<div class="form-group">
								<label for="nilai_sales">Nilai Sales</label>
								<input name="nilai_sales" class="form-control" placeholder="Nilai Sales">
							</div>
							<div class="form-group">
								<label for="last_sync">Last Sync</label>
								<input name="last_sync" class="form-control" placeholder="Last Sync">
							</div>
							-->
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
<script src="<?php echo base_url() . 'assets/modules/ref_sales_salesman/sales-salesman-form.js' ?>"></script>
