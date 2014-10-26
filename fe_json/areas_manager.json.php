<?php
require_once("auth.php");

$areas_manager_form_json = file_get_contents($ne_config_info['fe_json_folder_name'] . '/' . 'areas_manager.json');


///TODO string replace is not working!

$areas_manager_form_json = str_replace('\n\r', '', $areas_manager_form_json);
$areas_manager_form_json = str_replace('\n', '', $areas_manager_form_json);

?>


var areas_manager_form = <?php echo $areas_manager_form_json ?>;
