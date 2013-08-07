<?php

/**
 * Session functions
 *
 * @author Dmitry Gorelenkov
 * @internal note: learning PHP -> probably low quality code, sorry :/
 */

namespace sessions {

    /**
     * Start, resume or destroy session<br />
     * if session already exists and not expiried - refresh.<br />
     * if session exists and expiried - destroy.<br />
     * if no session exists - create one.
     * @global array $ne_config_info
     * @param int $timeout time session started/resumed for. If <= 0 then the current session will be destroyed
     */
    function setSession($timeout = NULL) {
        global $ne_config_info;
        if (is_null($timeout) || !is_numeric($timeout)) {
            $timeout = $ne_config_info['session_timeout'];
        }

        //if $timeout <= 0, unset session.
        if($timeout<=0){
            unsetSession();
            return;
        }

        //if there is no session, create/resume
        if (!\session_id()) {
            startSession();
        }

        //if there is a session //no need?
        if(\session_id()){
            //if LAST_ACTIVITY expiried, unset session
            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)){
                unsetSession();
            //else refresh session
            }else{
                refreshSession();
            }
        }
    }

    /**
     * destroy session, if exists
     */
    function unsetSession() {
        if(\session_id()){
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in storage
        }
    }

    /**
     * refresh session, if exists
     */
    function refreshSession() {
        if(session_id()){
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }

    /**
     * start session and generates new id<br />
     * if session exists, just resume.<br />
     */
    function startSession() {
        if(!session_id()){
            \session_start();
        }

        //in case there was no session set before
        if(!isset($_SESSION['LAST_ACTIVITY'])){
            \session_regenerate_id(); //get new id
            $_SESSION['LAST_ACTIVITY'] = time();
        }


    }

}
?>
