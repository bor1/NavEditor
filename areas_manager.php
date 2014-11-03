<?php
require_once('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Bereich Management - <?php echo($ne_config_info['app_titleplain']); ?></title>

        <script type="text/javascript">
            var _json_area_data = '<?php echo(json_encode($g_areas_settings['area_settings'])); ?>';
            <?php
                $json_php_filename = $ne_config_info['fe_json_folder_name'] . "/areas_manager.json.php";
                require($json_php_filename);
            ?>
        </script>

        <?php
        echo NavTools::includeHtml('default',
                'jquery-ui-1.8.2.custom.min.js',
                'jqueryui/ne2-theme/jquery-ui-1.8.17.custom.css',
                'naveditor2.js',
                'jquery.md5.js',
                'livevalidation_standalone.compressed.js',
                'live_validation.css',
                'nav_tools.js'
        );
        
        echo NavTools::includeFE("areas_manager");
        ?>

        <script type="text/javascript">
            $(document).ready(fe_areas_manager.loadContent);
        </script>
    </head>

    <body id="areas_manager">
        <div id="wrapper">
            <h1 id="header"><?php echo($ne_config_info['app_title']); ?></h1>
            <div id="navBar">
                <?php require('common_nav_menu.php'); ?>
            </div>

            <div id="content_areas_manager">
                <div id="areas_manager" >
                    <div id="areasList"></div>
                    <div id="areasSettings"></div>
                </div>
            </div>

        </div>
        
        <?php require('common_footer.php'); ?>
    
    </body>

</html>
