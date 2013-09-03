<?php


function createSubMenu($num)   {
    global $ne2_menu;
    global $g_current_user_name;
//    global $is_admin;

    $actualPage = $_SERVER['PHP_SELF'];
//    $actualPath = $_SERVER['REQUEST_URI'];
    $actualPageName = basename($actualPage);

    $link = '';
    $um = new UserMgmt();

	foreach ($ne2_menu as $i => $v) {
		$class = '';
		$attribute = '';
		$desc = '';
		$key = $v['id'];

		if ($num == $v['up']) {
            if($um ->isAllowAccess($v['id'], $g_current_user_name)){
				if ($actualPageName == $v['link']) {
					$class .= 'active';
				}
				if (isset($v["sub"]) && $v['sub'] == 1) {
					$class .= ' dropdown';
				}
				if (isset($v['addclass'])) {
					$class .= ' '.$v['addclass'];
				
					if($v['addclass'] == "logout")
						break;
				}


				
				if (isset($v['attribut'])) {
					$attribute = $v['attribut'];
				}
				if (isset($v['desc']) && ($v['desc'])) {
					$desc = 'title="'.$v['desc'].'"';
				}

				$link .= '<li';
				if ($class) {
					$link .= " class=\"$class\"";
				}
				$link .= ">";
				
				if($v['sub'] == 1) {
					$link .= '<a class="dropdown-toggle" data-toggle="dropdown" '.$desc.' '.$attribute.'  href="'.$v['link'].'">';
				}else{
					$link .= '<a '.$desc.' '.$attribute.'  href="'.$v['link'].'">';
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

<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="./index.php"><?php echo($ne2_config_info['app_title']); ?></a>
			<ul class="nav">
				<?php echo createSubMenu(0);    ?>
			</ul>

			<ul class="nav pull-right">
				<li><a href="logout.php">Abmelden</a></li>
			</ul>
		</div>
	</div>
</nav>
