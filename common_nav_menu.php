<?php
require_once('auth.php');
\auth\no_direct_call(__FILE__);

function createSubMenu($num)   {
    global $ne_menu;
    global $g_current_user_name;
    global $g_UserMgmt;

    $actualPage = $_SERVER['PHP_SELF'];
//    $actualPath = $_SERVER['REQUEST_URI'];
    $actualPageName = basename($actualPage);

    $link = '';
	foreach ($ne_menu as $i => $v) {
        $class = '';
        $attribute = '';
        $desc = '';
        $key = $v['id'];

        if ($num == $v['up']) {
            if ($g_UserMgmt->isAllowAccessMenu($v['id'], $g_current_user_name)) {
                if ($actualPageName == $v['link']) {
                    $class .= 'active';
                }
                if (isset($v["sub"]) && $v['sub'] == 1) {
                    $class .= ' dropdown';
                }
                if (isset($v['addclass'])) {
                    $class .= ' ' . $v['addclass'];

                    if ($v['addclass'] == "logout") {
                        break;
                    }
                }



                if (isset($v['attribut'])) {
                    $attribute = $v['attribut'];
                }
                if (isset($v['desc']) && ($v['desc'])) {
                    $desc = 'title="' . $v['desc'] . '"';
                }

                $link .= '<li';
                if ($class) {
                    $link .= " class=\"$class\"";
                }
                $link .= ">";

                if ($v['sub'] == 1) {
                    $link .= '<a class="dropdown-toggle" data-toggle="dropdown" ' . $desc . ' ' . $attribute . '  href="' . $v['link'] . '">';
                } else {
                    $link .= '<a ' . $desc . ' ' . $attribute . '  href="' . $v['link'] . '">';
                }

                $link .= $v['title'];

                if ($v['sub'] == 1) {
                    $link .= '<b class="caret"></b>';
                }

                $link .= '</a>';


                if ($v['sub'] == 1) {
                    $link .= "<ul class=\"dropdown-menu\">\n";
                    $link .= createSubMenu($key);
                    $link .= "</ul>\n";
                }

                $link .= "</li>\n";
            }
        }
    }
    return $link;

}

?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="./index.php"><?php echo($ne_config_info['app_title']); ?></a>
		</div>
		<div class="collapse navbar-collapse" id="navbar-collapse-1">
			<ul class="nav navbar-nav">
				<?php echo createSubMenu(0);?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="logout.php"><i class="glyphicon glyphicon-off"></i>&nbsp;Abmelden</a></li>
			</ul>
		</div>
	</div>
</nav>
