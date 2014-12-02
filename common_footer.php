<?php
require_once('auth.php');
\auth\no_direct_call();
?>

<div class="footer">
	<div class="container">
		<h4>
			<?php echo($ne_config_info['app_title']); ?>
			<small>
				<abbr lang="en" xml:lang="en" title="What you see, is what you get">WYSIWYG</abbr> - Editor des Webbaukastens der FAU,

			Version: <b><?php echo($ne_config_info['version']); ?></b>, <a href="help_details.php">Hilfe</a>, <a href="credits.php">Credits</a>;

			lizenziert unter der <a href="licence.php">GPL</a>

			</small>
		</h4>
	</div>
</div>

