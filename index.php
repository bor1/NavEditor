<?php
require_once ('auth.php');



// check if first run
$fpath =  $ne_config_info['user_data_file_path'];
if(! file_exists($fpath)) {
    header('Location: aktivierung.php');
} else {
    switch($_GET["p"]){
        case "areas_manager":
        break;
        
        case "":
            header('Location: dashboard.php');
        break;
    
        default:
            header('Location: ' . $_GET["p"] . ".php");
    }
    
}
///TODO: sanitize this input!!!
$site_class = $_GET["p"];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Bereich Management - <?php echo($ne_config_info['app_titleplain']); ?></title>

        <?php
        echo NavTools::includeFE($site_class);
        ?>

        <script type="text/javascript">
            <?php
                $json_php_filename = $ne_config_info['fe_json_folder_name'] . "/" . $site_class . ".json.php";
                require($json_php_filename);
            ?>
        </script>

    </head>

    <body id="areas_manager">
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

?>
