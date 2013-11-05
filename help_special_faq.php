<?php
require_once ('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Nutzungslizenz - <?php echo($ne2_config_info['app_titleplain']); ?></title>

    <?php echo NavTools::includeHtml("default"); ?>


</head>

<body>
			<?php
			require ('common_nav_menu.php');
			?>
    <div class="container">


            <div class="row page">
                <h2>RRZE-Helpdesk</h2>
                <p>Fragen & Antworten zum Webbaukasten und dessen Werkzeuge <br />
                    &Uuml;ber die Reiter FAQ - Webmaster - Webbaukasten haben Sie die M&ouml;glichkeit, nach schon bekannten Probleml&ouml;sungen zu suchen. </p>
                <p><br />
                    Fragen und Antworten finden Sie unter:<br />
                    <a class="extern" href="https://www.helpdesk.rrze.uni-erlangen.de/otrs/public.pl?Action=PublicFAQExplorer;CategoryID=44">RRZE-Helpdesk speziell zum Webbaukasten</a><u><br />
                    </u><a class="extern" href="https://www.helpdesk.rrze.uni-erlangen.de/otrs/public.pl?Action=PublicFAQExplorer;CategoryID=101">RRZE-Helpdesk speziell zum NavEditor</a> <br />
                    &Uuml;ber das Helpdesk-System k&ouml;nnen Sie bereits gemeldete Probleme suchen oder den Status laufender Vorf&auml;lle abfragen. </p>
                <p>Sollten die FAQs nicht hilfreich sein oder Sie eine pers&ouml;nliche Hilfe ben&ouml;tigen, senden Sie eine E-Mail an die Adresse<br />
                    <a href="mailto:webmaster@rrze.uni-erlangen.de">webmaster@rrze.uni-erlangen.de</a></p>
                <p>&nbsp;</p>
                <p><b>Seien Sie bei einer Fehlermeldung bitte m&ouml;glichst genau.</b><br />
                    Beschreiben Sie, wie der Fehler zustande kam und was genau getan werden muss um den Fehler erneut hervorzurufen. </p>
                <p><b>Geben Sie immer die NavEditor Versionsnummer an,</b> unter der Sie arbeiten. Diese finden Sie im Dashboard in der untersten Zeile. Beispiel: NavEditor 2 Delta - WYSIWYG-Editor des Webbaukastens der FAU, Version: 2.12.0718</p>
            </div>

	</div>

	<?php require('common_footer.php'); ?>

</body>

</html>

