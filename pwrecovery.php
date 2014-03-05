<?php
require_once('auth.php');

$oper = "pwvergessen"; //mit der operation anfangen
$keyfilepath = $ne_config_info['temp_path'] . '.key_tmp';
$logCounter = array();
$toWait = 0;

//code eingeben -> mail send.
if (isset($_POST['btnReset'])) {
    $logCounter = countLog('mailSent');
    $toWait = calcRestZeit($logCounter['counter'], $logCounter['lasttry']);
    if ($logCounter['counter'] > 0 && $toWait > 5) {
        $oper = "wait";
    } else {
        $current_user_name = $_POST['txtUserName'];
        $current_zahl = $_POST['txtCheck'];
        $userArray = $g_UserMgmt->GetUser($current_user_name, 'array');
        $_SESSION['userName'] = $current_user_name;

        if (!isset($_SESSION['rndZahl'])) {
            echo "Ein Fehler aufgetreten, bitte pruefen Sie ob Cookies aktiviert sind!";
        } elseif (!is_null($userArray) && strcmp($current_zahl, $_SESSION['rndZahl']) == 0) {
            $oper = "mailsend";
            //bei dem Server-Admin nur auf server mail versenden
            $user_mail = ($current_user_name == NavTools::getServerAdmin()) ? $_SERVER['SERVER_ADMIN'] : $userArray['email'];
            if ($user_mail == "") {
                $oper = "nomail";
                logadd("nomail");
            } else {
                $key = md5(uniqid(rand(), true));
                $md5key = md5($key);

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/plain; charset=utf-8\r\n";
                $text = "Hallo $current_user_name,\r\n";
                $text .= "Sie erhalten diese E-Mail, weil Sie ein neues Passwort angefordert haben.\n\n";
                $text .= "Um das neue Passwort zu setzen, benutzen Sie bitte innerhalb der n\xC3\xA4chsten 24 Stunden den folgenden Link: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?key=$key&user=$current_user_name\n\n";
                $text .= "Mit freundlichen Gr\xC3\xBC\xC3\x9Fen\n\n";
                $text .= 'Das RRZE Team.';

                if (!mail($user_mail, 'Passwort wiederherstellen', $text, $headers)) {
                    $oper = "nomail";
                    logadd("nomail");
                } else {
                    $fh = fopen($keyfilepath . "-" . $current_user_name, 'w') or die('Cannot create file!');
                    fwrite($fh, $md5key);
                    fclose($fh);

                    logadd("mailSent");
                }
            }
        } else {
            $oper = "wrongnum";
        }
    }
// link von dem Mail benutzt
} elseif (isset($_GET['key']) && isset($_GET['user'])) {
    //$_SESSION['key'] = $_GET['key'];
    $keyfilepath .= "-" . $_GET['user'];
    $logCounter = countLog('linkUsed');
    //20 Versuche werden toleriert.
    $toWait = calcRestZeit($logCounter['counter'] - 20, $logCounter['lasttry']);
    if ($logCounter['counter'] > 0 && $toWait > 5) {
        $oper = "wait";
    } elseif (file_exists($keyfilepath)) {
        if ((time() - filectime($keyfilepath)) < (24 * 60 * 60)) {
            $key = file_get_contents($keyfilepath);
            if (strcmp($key, md5($_GET['key'])) == 0) {
                $oper = "resetpw";
                logadd("linkUsed");
            } else {
                $oper = "wrongkey";
            }
        } else {
            $oper = "wrongkey";
        }
    } else {
        $oper = "wrongkey";
    }
    //form fuer pw aendern benutzt
} elseif (isset($_POST['btnPwSet'])) {
    $logCounter = countLog('wrongkey');
    //20 Versuche werden toleriert.
    $toWait = calcRestZeit($logCounter['counter'] - 20, $logCounter['lasttry']);
    //keine fehlermeldung, da sowas nur bei hackversuchen vorkommen kann
    sleep((int) $toWait);
    $pw1 = $_POST['txtPw'];
    $pw2 = $_POST['txtPwRe'];
    $key = $_POST['key'];
    $user = $_POST['user'];
    $keyfilepath .= "-" . $user;

    if (strcmp($pw1, $pw2) == 0 && $pw1 != "") {
        if (file_exists($keyfilepath)) {
            $key_from_file = file_get_contents($keyfilepath);
            if (strcmp(md5($key), $key_from_file) == 0) {
                $g_UserMgmt->UpdateUser($user, Array('password_hash' => md5($pw1)));
                unlink($keyfilepath);
                //session beenden
                \sessions\unsetSession();
                $oper = "changed";
                logadd("pwChanged");
            } else {
                echo 'Key ist falsch? Oo Bitte nicht hacken!';
                logadd('wrongkey');
            }
        } else {
            echo 'Key File existiert nicht mehr';
            logadd('nokey');
        }
    } else {
        echo 'Passwort stimmt nicht';
    }
}
$_SESSION['rndZahl'] = (string) (rand(10000, 99999));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Passwort vergessen - <?php echo($ne_config_info['app_titleplain']); ?></title>
        <?php echo NavTools::includeHtml("default"); ?>
    </head>
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
            $("#frmPw").submit(function(event) {
                if ($("#txtPw").val() != $("#txtPwRe").val()){
                    event.preventDefault();
                    alert("Passwort stimmt nicht, bitte noch mal eingeben");
                    $('#frmPw')[0].reset();
                    //$("#txtPw").val("");
                    //$("#txtPwRe").val("");
                }
            });
        });

    </script>
    <body>
        <div class="container page">
			<div class="page-header">
				<h3 id="header">Passwort vergessen</h3>
			</div>

            <?php
            if (strcmp($oper, "mailsend") == 0) {
                echo "<p class='alert alert-success'>Eine Mail mit dem Aktivierungslink wurde versendet.</p>";
            } elseif (strcmp($oper, "wrongnum") == 0 || strcmp($oper, "pwvergessen") == 0) {
                ?>
                <div id="contentPanel1">
                    <form id="frmLogin" name="frmLogin" action="pwrecovery.php" method="post" class="col-md-6 col-md-offset-3 form-horizontal">
						<div class="form-group">
							<label for="txtUserName" class="control-label col-md-6">Benutzerkennung:</label>
							<div class="col-md-4">
								<input type="text" id="txtUserName" name="txtUserName" class="form-control textBox" />
							</div>
						</div>
						<div class="form-group">
							<label for="txtCheck" class="control-label col-md-6">Bitte die Zahl eingeben: <b><?php echo $_SESSION['rndZahl'] ?></b></label>
							<div class="col-md-4">
								<input type="text" id="txtCheck" name="txtCheck" class="form-control textBox" />
							</div>
						</div>
						<br />
                        <input type="submit" id="btnReset" name="btnReset" class="btn btn-primary btn-light center-block" value="Passwort zur&uuml;cksetzen" />
						<br/>
                    </form>
                </div>
                <?php
            }

            switch ($oper) {
                case 'wrongnum':
                    echo "<script type='text/javascript'>alert('Die Zahl oder der Loginname ist falsch. Bitte versuchen Sie es noch ein mal.');</script>";
                    break;

                case 'nomail':
                    echo "<script type='text/javascript'>alert('Dieser Benutzer hat keine E-Mail-Adresse angegeben. Bitte wenden Sie sich an Ihren Webmaster.');</script>";
                    break;

                case 'resetpw':
                    ?>
                    <div id="contentPanel1">
                        <form id="frmPw" name="frmPw" action="pwrecovery.php" method="post" class="col-md-6 col-md-offset-3 form-horizontal">
							<div class="form-group">
								<label for="txtPw" class="control-label col-md-6">Neues Passwort:</label>
								<div class="col-md-4">
									<input type="password" id="txtPw" name="txtPw" class="form-control textBox" />
								</div>
							</div>
							<div class="form-group">
								<label for="txtPwRe" class="control-label col-md-6">Best&auml;tigung des Passworts:</label>
								<div class="col-md-4">
									<input type="password" id="txtPwRe" name="txtPwRe" class="form-control textBox" />
								</div>
							</div>
							<br />
                            <input type="submit" id="btnPwSet" name="btnPwSet" class="btn btn-primary btn-light center-block" value="Passwort speichern" />
							<br />
                            <input type="hidden" id="key" name="key" value="<?php echo $_GET['key'] ?>" />
                            <input type="hidden" id="user" name="user" value="<?php echo $_GET['user'] ?>" />
                        </form>
                    </div>
                    <?php
                    break;

                case 'wrongkey':
                    echo "<p class='alert alert-danger' style='padding:15px;'>Der Link ist veraltet oder existiert nicht.</p>";
                    echo "<p style='text-align: center'><a href='login.php'>Zur&uuml;ck zum Login</a><p>";
                    break;

                case 'wait':
                    echo "<p class='alert alert-danger' style='padding:15px;'>Zu viele Versuche, bitte warten Sie noch <span id='timeBlock' name='timeBlock' style='font-weight:bold;'></span> bis zum n&auml;chsten Login-Versuch.</p>\n";
                    break;

                case 'changed':
                    echo "<script type='text/javascript'>alert(unescape('Passwort erfolgreich ge%E4ndert%21'));";
                    echo "window.location.href = 'login.php';</script>";
                    break;

                default:
                    break;
            }
            ?>
		</div>

		<?php require('common_footer.php'); ?>

    </body>
</html>
