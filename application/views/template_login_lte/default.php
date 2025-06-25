<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('X-Powered-By: Prod-domProjects.com');
header('X-XSS-Protection: 1');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Vary: Accept-Encoding');
?>
<!DOCTYPE html>
<html>
    <head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-W6HTBNN');</script>
	<!-- End Google Tag Manager -->

	<?php if (isset($header)) echo $header; ?>
    </head>
	<body class="hold-transition login-page">		
		<?php if (isset($contents)) echo $contents; ?>
    </body>
	<script>
	</script>
</html>