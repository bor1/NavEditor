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
                <h2>Allgemeine Dokumentation zum Webbaukasten</h2>
                <p>Unter folgenden Links finden Sie Informationen &uuml;ber den NavEditor und den Webbaukasten:<br />
                    <a class="extern" href="http://www.vorlagen.uni-erlangen.de/">Allgemeine Dokumentation zum Webbaukasten</a> <br />
                    Dies ist die Startseite zum offiziellen <em>Web-Baukasten</em> zur Erstellung von eigenen Webauftritten. Hier finden Sie <a class="extern" href="http://www.vorlagen.uni-erlangen.de/vorlagen">Vorlagen</a> (Inhalte, semantische Strukturen und grafische Elemente) und m&ouml;gliche <a class="extern" href="http://www.vorlagen.uni-erlangen.de/downloads/designs.php">Designs</a> (Stylesheets und grafische Zusammenstellungen), mit deren Hilfe Webauftritte gestaltet werden k&ouml;nnen.</p>


                <h2>Speziell zum NavEditor</h2>
                <p>Mit Hilfe des Vorlagenkatalogs k&ouml;nnen Webmaster schon jetzt mit wenigen Schritten die Grundlage f&uuml;r ihren neuen Webauftritt schaffen.<br />
                    <a class="extern" href="http://www.vorlagen.uni-erlangen.de/anwendungen/naveditor/">Speziell zum NavEditor</a> <br />
                    <br />
                    <br />
                    Im Navigationspunkt <strong>GUI </strong>erhalten Sie detaillierte Informationen und Hilfestellungen direkt zum Navigationsmen&uuml; des NavEditors. <br />
                    <a class="extern"  href="http://www.vorlagen.uni-erlangen.de/anwendungen/naveditor/gui/">Erkl&auml;rung zur Navigation im NavEditor</a></p>

            </div>

	</div>

	<?php require('common_footer.php'); ?>

</body>

</html>

