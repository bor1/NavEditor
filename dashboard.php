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
?>
		<div class="container">

				<div class="page-header">
					<h1> <a href="<?php echo($sp->get_permalink()); ?>"><?php echo($sp->get_title()); ?></a> <small>Nachrichten und Artikel des WebTeams</small></h1>
				</div>


			<div class="panel-group" id="accordion">
				<?php foreach($sp->get_items() as $item) { ?>
					<div class="panel panel-primary">
						<div class="panel-heading">
							<p class="pull-right"><?php echo formatDate($item->get_date('j. F Y - G:i')); ?> Uhr</p>
							<h3 class="panel-title">
							<a href="#"><?php echo $item->get_title(); ?> </a>
								<a href="<?php echo $item->get_permalink(); ?>" target="_blank" title="Artikel im Webworking-Blog &ouml;ffnen"> <span class="glyphicon glyphicon-new-window" style="margin-left: 10px; font-weight: normal;"></span></a>
							</h3>

						</div>
						<div class="panel-body"><?php echo $item->get_description(); ?></div>
					</div>
				<?php } ?>
			</div>
		</div>

	</div>
