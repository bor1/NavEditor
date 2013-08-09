<?php
require_once('auth.php');

if (is_null($g_UserMgmt->GetUsers())) {
    header("Location: aktivierung.php");
}

$toWait = 0;
if (isset($_POST['btnLogin'])) {
    $toWait = waitTimeForLogin();
    if ($toWait <= 0) {

        $username = $_POST['txtUserName'];
        $password = md5($_POST['txtPassword']);
        $login_result = $g_UserMgmt->Login($username, $password);

        if ($login_result == 1) {

            $_SESSION['ne_username'] = $username;
            $_SESSION['ne_password'] = $password;
            $g_UserMgmt->saveLoginTime($username);
            logadd('loginOk');

            //TODO NavTools::testFallBack?
            if (!file_exists($ne_config_info['default_configs_path'] . $ne_config_info['website_conf_filename'])
                    || !file_exists($ne_config_info['default_configs_path'] . $ne_config_info['variables_conf_filename'])) {
                header('Location: website_editor.php');
            } else {
                header('Location: dashboard.php');
            }
        } else {

            logadd('loginFail');
            NavTools::unsetAllCookies();
            \sessions\unsetSession();
            //falls der account abgelaufen ist, extra Fehlermeldung anzeigen
            if ($login_result == -1) {
                echo '<script type="text/javascript">';
                echo 'alert("Account is abgelaufen!")';
                echo '</script>';
            } else {
                header('Location: login.php');
            }
        }
    }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Einloggen - <?php echo($ne_config_info['app_titleplain']); ?></title>
        <link rel="stylesheet" type="text/css" href="css/styles.css?<?php echo date('Ymdis'); ?>" />
    </head>

    <body>
        <div id="wrapper">
            <h1 id="header">Bitte melden Sie sich an!</h1>

            <div id="contentPanel1">
                <form id="frmLogin" name="frmLogin" action="login.php" method="post" style="width:100%;text-align:center;"><br>

<?php
if ($toWait <= 0) {
    ?>
                            <label for="txtUserName" style="width:8em;display:inline-block;">Username:</label>
                            <input type="text" id="txtUserName" name="txtUserName" size="16" class="textBox" /><br>
                                <label for="txtPassword" style="width:8em;display:inline-block;">Passwort:</label>
                                <input type="password" id="txtPassword" name="txtPassword" size="16" class="textBox" /><br><br>
                                        <input type="submit" id="btnLogin" name="btnLogin" class="button" value="Einloggen" />
    <?php
} else {
    ?>
                                        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
                                        <script type="text/javascript">
                                            var toWaitJs = <?php echo $toWait ?>;
                                            var restZeit = new Date(toWaitJs*1000);


                                            function startTime(){
                                                var h=Math.floor(restZeit.getTime()/(60*60*1000));
                                                var m=restZeit.getUTCMinutes();
                                                var s=restZeit.getUTCSeconds();
                                                // add a zero in front of numbers<10
                                                h=checkTime(h);
                                                m=checkTime(m);
                                                s=checkTime(s);
                                                if(restZeit > 0 ){
                                                    $('#timeBlock').html(h+':'+m+':'+s);
                                                    restZeit.setTime(restZeit.getTime()-1000);
                                                    t=setTimeout('startTime()',999);
                                                } else {
                                                    window.location.href = 'login.php';
                                                }
                                            }

                                            function checkTime(i){
                                                if(i==0){
                                                    i = "00";
                                                }else if (i<10){
                                                    i="0" + i;
                                                }
                                                return i;
                                            }



                                            $(document).ready(function() {
                                                if(toWaitJs > 5 ){
                                                    startTime();
                                                }
                                            });

                                        </script>
                                        <p style='text-align:center;font-size:large;'>Zu viele Versuche, bitte warten Sie noch <div id='timeBlock' name='timeBlock' style='color: red;text-align:center;font-size:x-large;'></div><p><br>
    <?php
}
?>
                                                <br><br><a href="pwrecovery.php">Passwort vergessen</a>
                                                        </form>
                                                        </div>

<?php require('common_footer.php'); ?>
                                                        </div>
                                                        </body>

                                                        </html>