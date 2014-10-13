<?php
require_once('auth.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Bereich Management - <?php echo($ne_config_info['app_titleplain']); ?></title>

        <script type="text/javascript">
                var _json_area_data = '<?php echo(json_encode($g_areas_settings['area_settings'])); ?>';
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

            /*-------- after document loaded do code: --------*/
            $(document).ready(function() {
                //make beautiful buttons
                $("button").button();

                //loading all info about areas
                NavTools.call_php('app/classes/AreasManager.php', 'getAllAreaSettings',
                {},
                loadContentCallback);

                //all buttons to pick
                $("#bereichList .bereich_button").on('click', function(){
                    //ask by changes, prevent if needed
                    if(checkInputChange()){$(this).blur();return;}

                    var thisBereichName = $(this).attr("id");
                    var bereichArray = _area_data_array[thisBereichName]
                    fillFieldsWithData(bereichArray);
                    _currentValues['area'] = thisBereichName;
                    //ui bug ? ..
                    addContentToElement($('#bereichSettings'), createButtonHtml('updateBereich', 'Speichern') + createButtonHtml('removeBereich', 'L&ouml;schen'));
                    //addContentToElement($('#bereichSettings'), createButtonHtml('removeBereich', 'Delete Bereich'));
                    checkInputChange(false);

                    selectMenu($(this));
                });


                //btn create new
                $("#bereichList #addNewBereich").on('click',function() {
                    //ask by changes, prevent if needed
                    if(checkInputChange()){$(this).blur();return;}

                    fillFieldsWithData(_empty_area_data_array);
                    clearTempButtons();
                    addContentToElement($('#bereichSettings'), createButtonHtml('createBereich', 'Erstellen'));
                    _currentValues['area'] = "";
                    checkInputChange(false);
                    selectMenu($(this));
                });

                //btn save bei new
                $("#bereichmanager #createBereich").on('click',function() {
                    if(!checkForm(true)){
                        return;
                    }
                    var areaName = $("#bereichmanager input[name='name']").val();
                    var params = readInput();
                    //                    params = JSON.stringify(params);

                    NavTools.call_php('app/classes/AreasManager.php', 'addAreaSettings',
                    {
                        name: areaName,
                        settings: params
                    },
                    createBereichCallback);
                });

                //btn save
                $("#bereichmanager #updateBereich").on('click',function() {
                    if(!checkForm()){
                        return;
                    }
                    var areaName = _currentValues['area'];

                    if(confirm(unescape('Den Bereich: \"'+ areaName + '" aktualisieren?'))){
                        var params = readInput()
                        //                        params = JSON.stringify(params);
                        NavTools.call_php('app/classes/AreasManager.php', 'updateAreaSettings',
                        {
                            name: areaName,
                            settings: params
                        },
                        updateBereichCallback);
                    }


                });

                //button delete bereich
                $("#bereichmanager #removeBereich").on('click',function() {
                    var areaName = _currentValues['area'];
                    if(confirm(unescape('Den Bereich: \"'+ areaName + '" l%F6schen?'))){
                        NavTools.call_php('app/classes/AreasManager.php', 'deleteAreaSettings',
                        {
                            name: areaName
                        },
                        removeBereichCallback);
                    }
                });

                //bind event 'change' to every input, to catch any changes, for checkInputChange() function
                $('#bereichmanager').find(':input').on('change', function(){
                    if(_currentValues['area'] != null){
                        if( _currentValues['area'].length > 0){
                            _somethingChanged = true;
                        }
                    }
                });

                $(window).resize(function() {
                    setPanelScroll();
                });
                setPanelScroll();
            });
        </script>
    </head>

    <body id="bereich_manager">
        <div id="wrapper">
            <h1 id="header"><?php echo($ne_config_info['app_title']); ?></h1>
            <div id="navBar">
                <?php require('common_nav_menu.php'); ?>
            </div>

            <div id="content_bereich_manager">
                <div id="bereichmanager" >
                    <div id="bereichList"></div>
                    <div id="bereichSettings"></div>
                </div>
            </div>

            <?php require('common_footer.php'); ?>
        </div>
    </body>

</html>
