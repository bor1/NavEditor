<?php
require_once ('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Credits - <?php echo($ne_config_info['app_titleplain']); ?></title>

    <?php echo NavTools::includeHtml("default"); ?>


</head>


<body>
	<?php require ('common_nav_menu.php'); ?>

    <div class="container page" id="wrapper">
		<div class="page-header">
			<h2 class="page-header">Entwickler</h2>
		</div>

		<h3>Entwickler</h3>
		<p>Der <?php echo($ne_config_info['app_title']); ?> wurde im Rahmen des <a class="extern" href="http://www.vorlagen.uni-erlangen.de">Webbaukastens der Friedrich-Alexander-Universit&auml;t</a> entwickelt. Folgende Personen sind und waren beteiligt:</p>
		<ul>
			<li><b>Wolfgang Wiese</b>, Leiter Webmanagement am <a class="extern" href="http://www.rrze.uni-erlangen.de">RRZE</a>, <a class="extern"  href="http://blogs.fau.de/webworking">Webworking-Blog</a><br />
				Diverse Programmierarbeiten.</li>
			<li><b>Dmitry Gorelenkov</b>,  Student der Informatik  (Master) an der FAU<br />
				Programmierung ab M&auml;rz 2011</li>
			<li><b>Ke Chang</b>, Student der Informatik  (Diplom) an der FAU, <a class="extern" href="http://changke.net">Homepage</a><br />
				Programmierung des NavEditor von Oktober 2007 bis M&auml;rz 2011</li>
		</ul>

		<h3>Projektleitung</h3>
		<ul>
			<li><b>Wolfgang Wiese</b>, Leiter Webmanagement am <a class="extern" href="http://www.rrze.uni-erlangen.de">RRZE</a>,  <a  class="extern"  href="http://blogs.fau.de/webworking">Webworking-Blog</a><br />
				Projektleitung Webbaukasten seit 2006.</li>
			<li><b>Natalia Khamatgalimova</b>, <a class="extern" href="http://www.rrze.uni-erlangen.de">RRZE</a><br />
						Administration und Betreuung Webbaukasten von August 2009 bis September 2010</li>
		</ul>

		<h3>Unterst&uuml;tzer</h3>
		<p>Folgende Personen waren oder sind zwar nicht in der stetigen Entwicklung t&auml;tig, leisteten jedoch
			wertvolle Arbeiten, die Einflu&szlig; auf den Editor haben.</p>
		<ul>
			<li>Aydin &Uuml;lfer</li>
			<li>Rolf von der Forst</li>
			<li>Max Wankerl</li>
		</ul>

		<h3>Verwendete Plugins und Ressourcen</h3>
		<p>Der <?php echo($ne_config_info['app_title']); ?> verwendet einige &ouml;ffentliche Ressourcen und Plugins:</p>
		<ul>
			<li><a class="extern" href="http://www.jquery.com">JavaScript Bibliothek jQuery</a></li>
			<li><a class="extern" href="http://docs.jquery.com/UI/Accordion">jQuery UI Accordion 1.8.2</a></li>
			<li><a class="extern" href="http://users.tpg.com.au/j_birch/plugins/superfish//">jQuery Plugin
					uperfish</a></li>
			<li><a class="extern" href="http://www.tinymce.com/">TinyMCE Editor</a></li>
		</ul>
	</div>

    <?php require('common_footer.php'); ?>

</body>

</html>
