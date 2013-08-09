<?php
require_once('../auth.php');

global $ne_config_info;

$root = $ne_config_info['upload_dir'];
$dir = urldecode($_REQUEST['dir']); //TODO sicherer daten bekommen? input::?
$path = NavTools::root_filter($root . $dir);

//TODO fehlerbehandlung?
if(empty($path)){return;}


if (file_exists($path)) {
    if(!is_dir($path)){return;}
    $returnLinksArray = array();

    $files = scandir($path);

    if (count($files) > 2) {
        natcasesort($files);


        foreach ($files as $file) {
            if (file_exists($path . $file) && $file != '.' && $file != '..' && !is_dir($path . $file)) {
                $ext = preg_replace('/^.*\./', '', $file);
                //TODO pics ext. arrary in config ?
                if (in_array($ext, Array('jpg', 'gif', 'png', 'jpeg'))) {
                    array_push($returnLinksArray, $dir . $file);
                }
            }
        }
    }

    echo json_encode($returnLinksArray);
}

?>
