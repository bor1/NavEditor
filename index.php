<?php
require_once ('auth.php');

// check if first run
$fpath =  $ne_config_info['user_data_file_path'];
if(! file_exists($fpath)) {
        header('Location: aktivierung.php');
} else {
	header('Location: dashboard.php');
}
?>
