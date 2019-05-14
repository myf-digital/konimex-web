<div class="login-box">
  <div class="login-logo">
    <a href="https://adminlte.io/themes/AdminLTE/index2.html"><b>LIPI</b> IP Performance</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
	<!--
    <form id="loginForm" action=" < ?php echo $form_auth; ? >" method="post">
      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <div class="icheckbox_square-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> 
			  <!-- Remember Me -- >
            </label>
          </div>
        </div>
        <!-- /.col -- >
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -- >
      </div>
	  -->
	<form id="signupForm" class="form-horizontal" autocomplete="off">
		<div class="form-group">
			<label class="col-xs-3 control-label">Username</label>
			<div class="col-xs-5">
				<input type="text" class="form-control" name="username" />
			</div>
		</div>
	
		<div class="form-group">
			<label class="col-xs-3 control-label">Password</label>
			<div class="col-xs-5">
				<input type="password" class="form-control" name="password" />
			</div>
		</div>
	
		<div class="form-group">
			<div class="col-xs-9 col-xs-offset-3">
				<button type="submit" class="btn btn-primary">Sign up</button>
			</div>
		</div>
	  
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script>
$(document).ready(function() {
	$('#loginForm').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        row: {
            valid: 'field-success',
            invalid: 'field-error'
        },
        fields: {
            username: {
                validators: {
                    notEmpty: {
                        message: 'The username is required'
                    },
                    stringLength: {
                        min: 6,
                        max: 30,
                        message: 'The username must be more than 6 and less than 30 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: 'The username can only consist of alphabetical, number, dot and underscore'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The password is required'
                    }
                }
            }
        }
    });
})
</script>