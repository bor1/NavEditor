<?php

/**
 * Session functions
 *
 * @author Dmitry Gorelenkov
 * @internal note: learning PHP -> probably low quality code, sorry :/
 */

namespace sessions {

    /**
     * Start, resume or destroy session<br \>
     * @global array $ne2_config_info
     * @param int $timeout time session started/resumed for. If <= 0 then the current session will be destroyed
     */
    function setSession($timeout = NULL) {
        global $ne2_config_info;
        if (is_null($timeout) || !is_numeric($timeout)) {
            $timeout = $ne2_config_info['session_timeout'];
        }

        //if there is no session, create one
        if (!session_id() && $timeout > 0) {
            session_start();
            session_regenerate_id(); //get new id
        }

        //if timeout set to <= 0, or if LAST_ACTIVITY expiried, unset session
        if ($timeout <= 0 ||
                (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout))) {
            // last request was more than $timeout seconds ago
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in storage
        } else {
            $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
        }
    }

    function unsetSession() {
        setSession(0);
    }

    function refreshSession() {
        setSession();
    }

}
?>
