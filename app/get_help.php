<?php
require_once('../auth.php');

$fpath = $ne_config_info['help_path'] ;
$helpFile = preg_replace('/[^A-Za-z0-9_\-]/','',strtolower($_REQUEST['page_name']));
$fpath .= $helpFile .$ne_config_info['help_filesuffix'];

if(file_exists($fpath)) {
    echo(nl2br(file_get_contents($fpath)));
} else {
    echo('Keine Hilfe-Texte vorhanden.');
}
?>