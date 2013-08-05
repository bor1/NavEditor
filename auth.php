<?php

/**
 * Authentification
 * requires config.php, UserMgmt_Class.php, log_funcs.php, NavTools.php
 *
 * @TODO +Performance: check user right only every 30 minuts for example...
 */

namespace auth {
    require_once('app/config.php');
    require_once('app/classes/UserMgmt_Class.php');
    require_once('app/log_funcs.php');
    require_once ('app/sessions.php');

    /**
     * Call if login failed
     */
    function login_failed() {
        goToLogin();
        logadd('loginFail');
        exit;
    }

    /**
     * Go to login page.
     * @global array $ne2_config_info
     */
    function goToLogin() {
        global $ne2_config_info;
        $host = $_SERVER['HTTP_HOST'] . $ne2_config_info['app_path_without_host'];
        header('Location: http://' . $host . 'login.php');
    }

    /**
     * Check if file is for public access
     * @global array $ne2_config_info
     * @return boolean TRUE if public file
     */
    function checkPublic() {
        global $ne2_config_info;
        $uri = $_SERVER['REQUEST_URI'];
        $appuri = $ne2_config_info['app_path_without_host'];
        $allowthis = FALSE;
        foreach ($ne2_config_info['nologin_file'] as $allowedFile) {
            $thisuri = $appuri . $allowedFile;
            if (strcmp($uri, $thisuri) == 0) {
                $allowthis = TRUE;
                break;
            }
        }
        return $allowthis;
    }

    //global variables
    global $ne2_config_info;
    global $g_current_user_permission;
    global $g_current_user_name;
    global $is_admin;

    //start or refresh session
    \sessions\setSession();

    $um = new \UserMgmt();

    //in case there are no users, have to activate, go to aktivierung.php
    if (is_null($um->GetUsers())) {
        header("Location: http://" . $_SERVER['HTTP_HOST'] . $ne2_config_info['app_path_without_host'] . "aktivierung.php");

        //otherwise login process
    } else {

        $is_admin = FALSE;
        $current_user_name = NULL;
        $current_user_pwd = NULL;

        $loginResult = 'FAIL'; //values: FAIL, OK, WAIT
        //waitTimeForLogin ist eine aufwaendige function, dafuer aber mehr sicherheit,
        //da man theoretisch die session/cookie gesp. passwoerter immer ersetzen kann.
        //Ob es sich lohnt..
        $toWait = waitTimeForLogin();
        if ($toWait > 5) {
            //goToLogin();
            //need to wait, too many login tries
            $loginResult = 'WAIT';

            //dont need to wait, can try to login
        } else {

            //get username and password
            $current_user_name = \NavTools::ifsetor($_SESSION['ne2_username'],'');
            $current_user_pwd = \NavTools::ifsetor($_SESSION['ne2_password'],'');


            //try to login
            if ($um->Login($current_user_name, $current_user_pwd) != 1) { // login failed
                //login_failed();
                $loginResult = 'FAIL';
            } else { // login ok
                $loginResult = 'OK';

                //routine for logged in user

                $g_current_user_permission = $um->GetPermission($current_user_name);
                $g_current_user_name = $current_user_name;

                //set $is_admin, fallback. TODO remove. replace with permissions check
                if (strcmp($current_user_name, \NavTools::getServerAdmin()) == 0) {
                    $is_admin = TRUE;
                }
            }
        }





        //test file access
        //if not public... otherwise OK, nothing to do
        if (!checkPublic()) {

            //switch loginResult
            switch (strtoupper($loginResult)) {
                case 'OK'://if logged in
                    //test access for the requested file
                    $requested_file_path = str_replace($ne2_config_info['app_path_without_host'], '', $_SERVER["SCRIPT_NAME"]);
                    if (!$um->isAllowAccesPHP($requested_file_path, $current_user_name)) {
                        access_denied('You dont have permission for this file');
                    }
                    break;
                case 'WAIT': //if have to wait
                    goToLogin();
                    break;

                case 'FAIL': //if failed to login
                    login_failed();
                    break;
                default:
                    throw new \Exception('Cannot login...');
                    break;
            }
        }
    }
}


//global usefull

namespace {

    function access_denied($msg = 'Contact SERVER_ADMIN for your account information.') {
        $backlink = '<A HREF="javascript:javascript:history.go(-1)">Click here to go back to previous page</A>';
        echo $msg, '<br />', $backlink;
        exit;
    }

}
?>
