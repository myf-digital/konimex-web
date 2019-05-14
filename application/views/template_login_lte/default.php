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
		<?php if (isset($header)) echo $header; ?>
    </head>
	<body class="hold-transition login-page">
		<?php if (isset($contents)) echo $contents; ?>
    </body>
	<script>
	
	</script>
     
</html>