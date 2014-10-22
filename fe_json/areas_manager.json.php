<?php
$json_folder = $ne_config_info['ne_url'] . $ne_config_info['fe_json_folder_name'];

$areas_manager_form_json = file_get_contents(json_folder . '/' . 'areas_manager.json');

str_replace('\n', '', areas_manager);

echo $areas_manager_form_json

?>


var areas_manager.forms = JSON.parse(<?php ?>);