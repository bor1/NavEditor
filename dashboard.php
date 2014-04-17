<?php
require_once('auth.php');

function formatDate($date) {
	$eng = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	$deu = array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

	return str_replace($eng, $deu, $date);
}

$feedUrl = $ne_config_info['dashboard_feed'];

// call simplepie... get feed info... error handling?
//set_time_limit(15);

$sp = new SimplePie();
$sp->strip_htmltags(array('base', 'blink', 'body', 'doctype', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'style'));
$sp->strip_attributes(array('bgsound', 'class', 'expr', 'id', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'));
$sp->set_cache_location('./data');
$sp->set_feed_url($feedUrl);
$sp->set_timeout(15);
$sp->init();
$sp->handle_content_type();

//if($sp->error()) {
//	echo('[ERR] ' . $sp->error() . '<br />');
//	$sp->__destruct();
//	unset($sp);
//	return FALSE;
//}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dashboard - <?php echo($ne_config_info['app_titleplain']); ?></title>

<?php
    echo NavTools::includeHtml("default");
?>

</head>

<body>
	<?php require('common_nav_menu.php'); ?>

	<div class="dashboard">

		<div class="container">

				<div class="page-header">
					<h1> <a href="<?php echo($sp->get_permalink()); ?>"><?php echo($sp->get_title()); ?></a> <small>Nachrichten und Artikel des WebTeams</small></h1>
				</div>


			<div class="row">
				<?php foreach($sp->get_items() as $item) { ?>
					<div class="card span12" >
						<div class="header clearfix">
							<h5 class="title"><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a></h5>
							<p class="time"><?php echo formatDate($item->get_date('j. F Y - G:i')); ?> Uhr</p>
						</div>
						<div class="content"><?php echo $item->get_description(); ?></div>
					</div>
				<?php } ?>
			</div>
		</div>

	</div>

	<script>
		$(".card").click(function() {
			var $this = $(this);

			$this.find(".content").slideDown();
			$this.siblings().find(".content").slideUp();
		});
	</script>

	<?php require('common_footer.php'); ?>
</body>

</html>
