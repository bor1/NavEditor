<?php
require_once ('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Nutzungslizenz - <?php echo($ne_config_info['app_titleplain']); ?></title>

    <?php echo NavTools::includeHtml("default"); ?>


</head>


<body>
			<?php
			require ('common_nav_menu.php');
			?>
    <div class="container">


            <div class="row page">
                <h2>Forum</h2>
                <p>Bevor Sie eine E-Mail an das Webmasterteam senden wenden Sie sich bitte zuerst an das Forum. Handelt es sich um Probleme, die allgemeiner Natur sind, empfehlen wir, dass Sie das Problem im <a class="extern" href="http://www.portal.uni-erlangen.de/forums/topics/user/22/93">Forum</a> beschreiben. <br />
                    M&ouml;glicherweise haben auch andere Anwender dieselben Probleme gehabt und auch dazu bereits eine L&ouml;sung gefunden. Daher kann eine Diskussion im Forum schneller eine Antwort geben, als eine E-Mail an das Webteam. Sollten Sie Vorschl&auml;nge f&uuml;r Verbesserungen oder neue Funktionen haben, ist das Forum ebenfalls der richtige Platz. <a class="extern" href="https://www.portal.uni-erlangen.de/auth/login">Foren Webdienste Web-Baukasten</a></p>
                <p>Sollte das Forum nicht hilfreich sein oder Sie eine pers&ouml;nliche Hilfe ben&ouml;tigen, senden Sie eine E-Mail an die Adresse <a href="mailto:webmaster@rrze.uni-erlangen.de">webmaster@rrze.uni-erlangen.de</a></p>
                <p><b>Seien Sie bei einer Fehlermeldung bitte m&ouml;glichst genau.</b><br /></p>
                Beschreiben Sie, wie der Fehler zustande kam und was genau getan werden muss um den Fehler erneut hervorzurufen.
                <p><b>Geben Sie immer die NavEditor Versionsnummer an,</b> unter der Sie arbeiten. Diese finden Sie im Dashboard in der untersten Zeile. Beispiel: NavEditor 2 Delta - WYSIWYG-Editor des Webbaukastens der FAU, Version: <b>2.12.0718</b>
                </p>

                <h2>Blogs</h2>
                <p>Webworking - Nachrichten und Artikel des WebTeams<br />
                    Aktuelle Informationen, geplante Ã„nderungen, Wartungsmeldungen oder St&ouml;rungen aus dem Bereich der Webdienste sind &uuml;ber dem Blog des Webteams abrufbar. Bitte abbonnieren Sie das Blog um als Webmaster informiert zu bleiben.</p>
                <p>Blog aufrufen unter <a class="extern" href="http://blogs.fau.de/webworking/">http://blogs.fau.de/webworking/</a></p>
            </div>

	</div>

	<?php require('common_footer.php'); ?>

</body>

</html>

