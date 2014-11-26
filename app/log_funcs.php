<?php
/**
 * Logger + login wartezeit, bei brutforce.
 * @TODO neu als Klasse schreiben. +param max file size. +Klasse fuer sperrzeit extra
 */

function getOpName($operation) {
    switch ($operation) {
        case "mailSent":
            $op = "Mail versendet";
            break;
        case "nomail":
            $op = "Mailadresse bei dem User ist falsch/nicht gefunden";
            break;
        case "linkUsed":
            $op = "Link verwendet";
            break;
        case "pwChanged":
            $op = "Passwort geaendert";
            break;
        case "wrongKey":
            $op = "Key ist falsch";
            break;
        case "noKey":
            $op = "Key File existiert nicht";
            break;
        case "loginOk":
            $op = "Login erfolgreich";
            break;
        case "loginFail":
            $op = "Login gescheitert";
            break;
        case "sessionup":
            $op = "Sitzung refreshed";
            break;
        case "aktLinkUsed":
            $op = "aktivierungslink benutzt oder key eingegeben";
            break;
        default:
            $op = $operation;
    }
    return $op;
}

//log hinzufügen
function logadd($operation) {
    global $g_Logger;
    $op = getOpName($operation);
    $g_Logger->log($op);
}

//anzahl von bestimmten Erreignissen/operation
function countLog($operation, $minTimestamp = 0) {
    global $g_Logger;

    $op = getOpName($operation);
    //nun nur die benoetige log eintraege ($op) auslesen
    $ipAdresse = NavTools::ifsetor($_SERVER["REMOTE_ADDR"]);
    $dataArray = $g_Logger->getLogArray();

    $usefulDataArray = array_filter($dataArray, function($row) use($ipAdresse,$op, $minTimestamp) {
        if(!isset($row['ip']) || !isset($row['message']) || !isset($row['timestamp'])){
            return FALSE;
        }

        return $row['ip'] == $ipAdresse
            && $row['message'] == $op
            && $row['timestamp'] >= $minTimestamp;
    });

    $counter = count($usefulDataArray);
    $lastEntry = end($usefulDataArray);
    $arr = array('counter' => $counter, 'lasttry' =>  NavTools::ifsetor($lastEntry['timestamp'],0));
    return $arr;
}

function calcRestZeit($anzahlVersuche, $lastTry) {
    global $ne_config_info;
    $toWaitFunc = ($anzahlVersuche * pow(1.5, $anzahlVersuche)) + $lastTry - time();
    $maxTime = $ne_config_info['login_max_lock_time'];

    if ($toWaitFunc >= ($maxTime)) {
        $toWaitFunc = $maxTime + $lastTry - time();
    } elseif ($toWaitFunc < 0) {
        $toWaitFunc = 0;
    }
    
    return $toWaitFunc;
}

//wartezeit fuer login, nach x fehlvesuche.
function waitTimeForLogin() {
    $pwChanged = countLog('pwChanged');
    $loginOk = countLog('loginOk');
    $lastLoginOk = max(Array($loginOk['lasttry'],$pwChanged['lasttry']));
    $logFail = countLog('loginFail',  $lastLoginOk);
    if (($pwChanged['lasttry'] > $logFail['lasttry']) || ($loginOk['lasttry'] > $logFail['lasttry'])) {
        $toWait = 0;
    } else {
        $toWait = calcRestZeit($logFail['counter'] - 3, $logFail['lasttry']);
    }
    return $toWait;
}

?>