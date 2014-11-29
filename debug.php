<?php require_once("auth.php");

error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 'on');

require_once 'app/classes/AreasManager.php';

?>

<h2>Debug und Fehlersuche sowie -behebung</h2>

<h3>Areas Manager:</h3>

<?php

function isShtml($filename){
    if (strpos($filename, ".shtml") === FALSE) {
        return false;
    } else {
        return true;
    }
}

function printArray(array $toPrint){
    foreach ($toPrint as $element){
        echo $element . '<br>';
    }
}

function removeEnding(array $filenames){
    $i = 0;
    $retArray = Array(sizeof($filenames));
    foreach ($filenames as $name){
        
        $retArray[$i] = str_replace(".shtml", "" , $name);
        
        $i++;
    }
    
    return $retArray;
}

function putCheckboxInFront(array $areaNames){
    $i = 0;
    $retArray = Array(sizeof($areaNames));
    foreach ($areaNames as $name){
        $retArray[$i] = '<input type="checkbox" id="' . $name . '">' . $name;
        $i++;
    }
    return $retArray;
}

$a_m = new AreasManager();

$allLists = $a_m->getAreaList();
$fileList = scandir($ne_config_info['ssi_folder_path']);
$shtmlfileList = array_filter($fileList, "isShtml");

echo '<table><tr><td><h4>All Areas in config file:</h4>';
printArray($allLists);
echo '</td><td><h4>All .shtml config files:</h4>';
printArray($shtmlfileList);
echo '</td><td><h4>Import .shtml files / areas:</h4>';

echo '<form>';

$possibleAreas = removeEnding($shtmlfileList);
printArray(putCheckboxInFront(array_diff($possibleAreas, $allLists)));
?><button onclick="ne_debug.importAreas()">Import Area</button><button onclick="ne_debug.deleteAreaFile()">delete Area file</button>(only use this if you know what you do!!!)<?php
echo '</form></td>';

echo '</tr></table>';


?>

<hr>
