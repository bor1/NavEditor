<?php
require_once('auth.php');

global $ne_config_info, $g_Logger, $g_UserMgmt, $g_current_user_permission, $g_current_user_name;

if (is_null($g_UserMgmt->GetUsers())) {
    header("Location: aktivierung.php");
}

$toWait = waitTimeForLogin();
if (isset($_POST['btnLogin']) && $toWait <= 0) {

    $username = $_POST['txtUserName'];
    $password = md5($_POST['txtPassword']);
    $login_result = $g_UserMgmt->Login($username, $password);

    if ($login_result == 1) {

        $_SESSION['ne_username'] = $username;
        $_SESSION['ne_password'] = $password;
        \auth\setGlobals();
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Einloggen - <?php echo($ne_config_info['app_titleplain']); ?></title>

		<?php
			echo NavTools::includeHtml("default", "json2.js");
		?>
	</head>

	<body>

		<div class="container page">
			<div class="page-header">
				<h3 class="">Bitte melden Sie sich an!</h3>
			</div>
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<form id="frmLogin" name="frmLogin" action="login.php" method="post" class="form-horizontal"><br>

						<?php
						if ($toWait <= 0) {
						?>
							<div class="form-group">
								<label for="txtUserName" class="col-sm-4 control-label">Benutzername:</label>
								<div class="col-sm-8">
									<input type="text" id="txtUserName" name="txtUserName" class="textBox form-control" />
								</div>
							</div>
							<div class="form-group">
								<label for="txtPassword" class="col-sm-4 control-label">Passwort:</label>
								<div class="col-sm-8">
									<input type="password" id="txtPassword" name="txtPassword" class="textBox form-control" />
								</div>
							</div>
							<br />
							<input type="submit" id="btnLogin" name="btnLogin" class="btn btn-primary btn-light center-block" value="Einloggen" />

						<?php
						}else{
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
                                                if(toWaitJs > 0 ){
                                                    startTime();
                                                }
                                            });

                                        </script>
						<p class="alert alert-danger" style="padding:15px;">
							Zu viele Versuche, bitte warten Sie noch: <span id="timeBlock" name="timeBlock" style="font-weight:bold;"></span> bis zum n&auml;chsten Login-Versuch.
						</p>
						<br />
						<?php
						}
						?>
						<br />
						<p style="text-align: center"><a href="pwrecovery.php">Passwort vergessen</a></p>
					</form>
				</div>
			</div>
		</div>

		<?php require('common_footer.php'); ?>

	</body>

</html>
