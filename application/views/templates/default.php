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
    <?php exist_session(); ?>
    <?php if (isset($header)) echo $header; ?>
</head>

<body class="<?php if (isset($navmode)) echo $navmode; ?>">
<div class="wrapper" style="height: auto; min-height: 100%;">
    <?php if (isset($toolbar)) echo $toolbar; ?>
    <?php if (isset($sidebar)) echo $sidebar; ?>
    <?php if (isset($contents)) echo $contents; ?>
    <?php if (isset($content_footer)) echo $content_footer; ?>
</div>
<?php if (isset($footer)) echo $footer; ?>

</body>

</html>