<?php
require_once ('auth.php');

if ($loginResult == 'FAIL'){
    Header('Location: login.php');
    exit;
}

// check if first run
$fpath =  $ne_config_info['user_data_file_path'];
if(! file_exists($fpath)) {
    header('Location: aktivierung.php');
} else {
    switch($_GET["p"]){
        case "areas_manager":
        case "nav_editor":
        case "user_manager":
        case "file_editor":
            $site_class = $_GET["p"];
            break;
        
        case "dashboard":
        case "ma_editor":
        case "remove_caches":
        case "website_editor":
        case "conf_editor":
        case "design_editor":
        case "areas_manager":
        case "update":
        case "help_using":
        case "help_details":
        case "help_special_faq":
        case "help_forum_blog":
        case "licence":
        case "credits":
        case "logout":
            header('Location: ' . $_GET["p"] . ".php");
            exit;
            break;
    
        case "":
            header('Location: dashboard.php');
            exit;
            break;
    
        default:
            $site_class = "not_found";
            break;
    }
    
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo($ne_site_name[$site_class]);?> - <?php echo($ne_config_info['app_titleplain']); ?></title>

        <?php
        echo NavTools::includeFE($site_class);
        ?>

        <script type="text/javascript">
            <?php
                $json_php_filename = $ne_config_info['fe_json_folder_name'] . "/" . $site_class . ".json.php";
                //This file may not exist, so we won't force including:
                include($json_php_filename);
            ?>
        </script>

    </head>

    <body class="container" id="areas_manager nav_editor bd_User">
        <div id="wrapper">
            <h1 id="header"><?php echo($ne_config_info['app_title']); ?></h1>
            <div id="navBar">
                <?php require('common_nav_menu.php'); ?>
            </div>


    <?php require($site_class . ".php"); ?>

        </div>
        
        <?php require('common_footer.php'); ?>
    
    </body>

</html>
