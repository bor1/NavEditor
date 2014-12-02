<?php

/**
 * Authentification, config loading.
 * Schould be included in every php file of NavEditor! (except for classes)
 *
 * requires config.php, UserMgmt_Class.php, log_funcs.php, NavTools.php
 * @TODO +Performance: make user login only every 30 minuts for example...
 */

namespace auth;

use Exception;
use Logger\LoggerCSV;
use NavTools;
use UserMgmt;
use sessions;

//if called directly, exit.
no_direct_call();

require_once 'config/config.php';

require NE_DIR_ROOT.'app/log_funcs.php';
require NE_DIR_ROOT.'app/sessions.php';

//global variables
global $ne_config_info;
global $g_current_user_permission;
global $g_current_user_name;
global $is_admin;
global $g_UserMgmt;
global $g_Logger;

//start or refresh session
sessions\setSession();

$g_UserMgmt = new UserMgmt();
$g_Logger = new LoggerCSV();

$is_admin = false;

$loginResult = 'FAIL'; //values: FAIL, OK
//get username and password
$current_user_name = NavTools::ifsetor($_SESSION['ne_username'], '');
$current_user_pwd = NavTools::ifsetor($_SESSION['ne_password'], '');


//try to login
if (empty($current_user_name)
        || $g_UserMgmt->Login($current_user_name, $current_user_pwd) != 1) {
    // login failed or no user logged in
    $loginResult = 'FAIL';
} else { // login ok
    $loginResult = 'OK';
    setGlobals();
}

//log current called page
$g_Logger->log('Called page:'. NavTools::ifsetor($_SERVER['HTTP_REFERER'],'unknown'));

//test file access
//if not public... otherwise OK, nothing to do
if (!checkPublic()) {

    //switch loginResult
    switch (strtoupper($loginResult)) {
        case 'OK'://if logged in
            //test access for the requested file
            $requested_file_path = str_replace($ne_config_info['app_path_without_host'], '', $_SERVER['SCRIPT_NAME']);
            if (!$g_UserMgmt->isAllowAccessPHP($requested_file_path, $current_user_name)) {
                access_denied('You don\'t have permission for this file');
            }
            break;
        case 'FAIL': //if failed to login
            login_failed();
            break;
        default:
            throw new Exception('Cannot login...');
            break;
    }
}



// no cache!
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past



/**
 * Stop script if this file called directly.<br />
 * @todo implement with traits. Make sure php_5.4 supported
 */
function no_direct_call() {
    $backtrace = debug_backtrace();
    if(!isset($backtrace[0]) || !isset($backtrace[0]['file'])){return;}
    $caller_file_path = $backtrace[0]['file'];
    if (strcmp(realpath($caller_file_path), realpath($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) === 0) {
        die("Direct access forbidden");
    }
}

/**
 * Call if login failed
 */
function login_failed() {
    goToLogin();
    exit();
}

/**
 * Go to login page.
 * @global array $ne_config_info
 */
function goToLogin() {
    global $ne_config_info;
    $host = $_SERVER['HTTP_HOST'] . $ne_config_info['app_path_without_host'];
    header('Location: http://' . $host . 'login.php');
}

/**
 * Check if file has public access
 * @global array $ne_config_info
 * @return boolean TRUE if public file
 */
function checkPublic() {
    global $ne_config_info;
    $uri = $_SERVER['SCRIPT_NAME'];
    $appuri = $ne_config_info['app_path_without_host'];
    $requested_file = str_replace($appuri, '', $uri);

    return in_array($requested_file, $ne_config_info['public_php_files']);
}

/**
 * generates and shows "access denied" page
 * @param string $msg message to show
 */
function access_denied($msg = 'Contact SERVER_ADMIN for your account information.') {
    $backlink = '<A HREF="javascript:history.go(-1)">Click here to go back to previous page</A>';
    echo $msg, '<br />', $backlink;
    exit();
}

/**
 * sets global variables from set $_SESSION
 */
function setGlobals() {
    global $g_current_user_permission, $g_current_user_name, $g_Logger, $is_admin, $g_UserMgmt;

    if(!isset($_SESSION['ne_username'])){
        return false;
    }

    $current_user_name = $_SESSION['ne_username'];
    $g_current_user_permission = $g_UserMgmt->GetPermission($current_user_name);
    $g_current_user_name = $current_user_name;
    $g_Logger->setCurrentUserName($current_user_name); //set username for logger
    //set $is_admin, fallback. TODO remove. replace with permissions check
    if (strcmp($current_user_name, NavTools::getServerAdmin()) == 0) {
        $is_admin = true;
    }

    return true;
}
?>
