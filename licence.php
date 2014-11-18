<?php require_once ('auth.php'); ?>

   <div class="container page" id="wrapper">
		<div class="page-header">
			<h2 class="page-header">Lizenz</h2>
		</div>

		<p>Der NavEditor <?php echo($ne_config_info['version']); ?>, sowie dessen Vorg&auml;ngerversionen, werden &uuml;ber der <a class="extern" href="http://www.gnu.org/licenses/gpl.html">GPL Lizenz</a> angeboten und k&ouml;nnen unter Einhaltung der Lizenz weitergegeben und genutzt werden.<br />
			<a class="extern" href="http://de.wikipedia.org/wiki/GNU_General_Public_License">Weiterf&uuml;hrende Informationen in deutscher Sprache</a>	zur Lizenz finden sich in der Wikipedia.</p>

		<pre>
			<?php require('licence.txt'); ?>
		</pre>
	</div>

