<!-- <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
    .row-login {height: 1500px}
    .sidenav {
      background-color: #EC6502;
      height: 100%;
    }
    .login-box-body {
      height: 1500px;
    }
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding-left: 0px;
      }
      .row-login {height: auto;} 
    }
  </style>
</head> -->
<!-- <body> -->

<div class="container-fluid">
  <div class="row login">
    <div class="col-lg-2 sidenav col-sm-2">
      <h2 class="label-side">Digital</h2>
      <h2 class="label-side">Record</h2>
      <h2 class="label-side">Card</h2>
    </div>
    <div class="col-lg-8 col-sm-8">
      <div class="login-box-body">
        <div class="row">
          <div class="col-xs-6"></div>
          <div class="col-xs-4">
            <img class="img login-banner" src="assets/images/new_dig_record.png" alt="Chania" width="140px" height="110px">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6"></div>
          <div class="col-xs-6">
            <label class="login-label" for="email">Gunakan Alamat Email Anda Untuk Masuk</label>
          </div>
        </div>
        <p class="login-box-msg"></p>
          <form id="login-form" method="post" action>
            <div class="form-group-login">
              <div class="row">
                <div class="col-xs-6"></div>
                <div class="col-xs-6">
                  <label class="login-label" for="email">Email</label>
                </div>
                <div class="col-xs-6"></div>
                <div class="col-xs-6">
                  <input type="username" name="username" class="form-control" placeholder="Masukan Email Disini">
                </div>
              </div>
            </div>
            <br>
            <div class="form-group-login">
              <div class="row">
                <div class="col-xs-6"></div>
                <div class="col-xs-6">
                  <label class="login-label" for="password">Password</label>
                </div>
                <div class="col-xs-6"></div>
                <div class="col-xs-6">
                  <input type="password" name="password" class="form-control" placeholder="Masukan Password Disini">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12">
                <label id="error-login" class="error-login"></label>
              </div>
              <div class="col-xs-6"></div>
              <div class="col-xs-6">
                <button type="submit" class="login-button btn btn-block">Sign In</button>
              </div>
            </div>
          </form>
        </div>
      </div>
  </div>
</div>

<!-- </body> -->
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