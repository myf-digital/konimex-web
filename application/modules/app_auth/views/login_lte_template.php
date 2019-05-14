<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>&nbsp;</b> </a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <img class="img-responsive login-banner" src="assets/images/login_riset_background.png" alt="Chania">
        <p class="login-box-msg">Welcome</p>
        <form id="login-form" method="post" action>
            <div class="form-group-login">
                <input type="username" name="username" class="form-control" placeholder="Username">

            </div>
            <div class="form-group-login">
                <input type="password" name="password" class="form-control" placeholder="Password">

            </div>
            <div class="row">
                <div class="col-xs-12">
                    <label id="error-login" class="error-login"></label>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>

        </form>
    </div>
</div>
<script>
    (function () {

        const common = new Common();
        let errorLabel = $("#error-login");
        let formLogin = $("#login-form");

        //errorLabel.hide();
        formLogin.initForm({
            url: common.baseURL("app_auth/verify"),
            initEasyui: false,
            beforeSubmit: function(form, opt){
                errorLabel.text(null);
                return true;
            },
            afterSuccess: function (response) {
                let result = response.result;
                if (200 === result.status_login) {
                    console.log(result);
                    common.setCookie("session", result.session);
                    location.replace(common.baseURL("app_dashboard"));
                } else {
                    errorLabel.text(result.message);
                    //errorLabel.show();
                }
            }
        });

    })()
</script>