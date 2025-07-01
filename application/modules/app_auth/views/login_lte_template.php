<div class="container-fluid">
  <div class="row login">
    <div class="col-lg-2 col-sm-2 sidenav text-center">
      <img class="img img-left" src="assets/images/par.png" alt="PAR" width="50%">
    </div>
    <div class="col-lg-8 col-sm-8">
      <div class="login-box-body">
        <div class="row">
          <div class="col-xs-6"></div>
          <div class="col-xs-4">
            <img class="img login-banner" src="assets/images/par-dark.png" alt="PAR" width="50%">
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
            <div class="col-xs-6"></div>
            <div class="col-xs-6">
              <label id="error-login" class="error-login"></label>
            </div>
            <div class="col-xs-6"></div>
            <div class="col-xs-6">
              <input type="hidden" id="player_id" name="player_id">
              <button type="submit" class="login-button btn btn-block">Sign In</button>
            </div>
          </div>
        </form>
      </div>
  </div>
</div>

<script>
  (function () {
    const common = new Common();
    let errorLabel = $("#error-login");
    let formLogin = $("#login-form");

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
          common.setCookie("session", result.session);
          location.replace(common.baseURL("app_dashboard"));
        } else {
          errorLabel.text(result.message);
        }
      }
    });
  })()
</script>

<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script>
  window.OneSignalDeferred = window.OneSignalDeferred || [];
  // 1. Configure Service Worker Paths
  OneSignalDeferred.push(function(OneSignal) {
    OneSignal.SERVICE_WORKER_PATH = '/OneSignalSDKWorker.js';
    OneSignal.SERVICE_WORKER_UPDATER_PATH = '/OneSignalSDKUpdaterWorker.js';
  });

  // 2. Initialize OneSignal with proper settings
  OneSignalDeferred.push(async function(OneSignal) {
    try {
      await OneSignal.init({
        appId: '<?php echo $onesignal_app_id; ?>',
        autoRegister: true,
        promptOptions: {
          slidedown: {
            enabled: false,
            autoPrompt: false // Disable auto prompt since we have permission
          }
        },
        notifyButton: {
          enable: true,
          size: 'small',
          position: 'bottom-right',
          showCredit: false,
        },
        persistNotification: false,
      });

      // Check current permission state
      const permission = await OneSignal.Notifications.permissionNative;
      if (permission === 'granted') {
        const userId = await OneSignal.User.PushSubscription.id;
        document.getElementById('player_id').value = userId; // Set player ID in the form
      } else {
        console.log('User has not granted permission');
      }
    } catch (error) {
      console.error('OneSignal initialization error: ', error);
    }
  });
</script>