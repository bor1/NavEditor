<?php
require_once('auth.php');
global $g_Logger;

function removeLockFiles($dir, $cur_user) {
    global $ne_config_info;
    if(!$cur_user){return;}
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (FALSE !== ($file = readdir($dh))) {
                // escaped dirs
                if (!in_array($file, $ne_config_info['nologoupdate_dir'])) {
                    if (is_dir($dir . '/' . $file)) {
                        // recursively
                        removeLockFiles($dir . '/' . $file, $cur_user);
                    } else {
                        $lf = $dir . '/' . $file . '.lock';
                        if (file_exists($lf)) {
                            $lock_user = file_get_contents($lf);
                            if (strcmp($cur_user, $lock_user) == 0) {
                                @unlink($lf);
                            }
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}


$cur_user = '';
if (isset($_SESSION['ne_username'])) {
    $cur_user = $_SESSION['ne_username'];
}

// remoive self's .lock files
$root_path = $_SERVER['DOCUMENT_ROOT'];

removeLockFiles($root_path, $cur_user);

// clean cookies
NavTools::unsetAllCookies();


\sessions\unsetSession();

$g_Logger->log('Logged out');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Ausloggen - <?php echo($ne_config_info['app_titleplain']); ?></title>
        <?php echo NavTools::includeHtml("default"); ?>
    </head>

    <body>
        <div class="container page">
			<div class="page-header">
				 <h2 id="header" class="page-header clearfix">Sie haben sich abgemeldet!</h2>
			</div>
			<div class="row">
				<div class="col-md-12">
					<p>Klicken Sie bitte auf <a href="login.php">diesen Link</a> um sich wieder anzumelden.</p>
				</div>
			</div>
		</div>
	</body>
</html>