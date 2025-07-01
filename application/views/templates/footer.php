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

      if (!OneSignal.Notifications) {
        console.warn('OneSignal.Notifications belum tersedia');
        return;
      }
      
      // Setup event listeners
      OneSignal.Notifications.addEventListener('click', (event) => {
        // Ambil URL dari berbagai kemungkinan lokasi data
        const url = event.notification?.data?.url 
                  || event.notification?.launchURL 
                  || event.notification?.url;
        
        if (url) {
          window.location.href = url;
          if (event.notification.close) {
            event.notification.close();
          }
        } else {
          console.warn('No URL found in notification:', event);
        }
      });

      OneSignal.Notifications.addEventListener('display', (event) => {
        console.log('Notification will display:', event);
      });
    } catch (error) {
      console.error("OneSignal Error:", error);
    }
  });
</script>