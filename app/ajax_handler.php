<?php

/*
 * Ajax Handler
 * purpose: handle all NavEditor ajax requests
 * Use nav_tools.js call_php() function for the php handler
 */
try {
    require_once '../auth.php';

    global $g_UserMgmt, $ne_config_info;

    //returned data variable
    $data_to_return = '';

    //what file
    //path will be resolved, full path will be modified to NavEditor2/ relative path
    $file_to_call = str_replace($ne_config_info['app_path'], '', NavTools::simpleResolvePath(Input::get_post('file')));
    //what function
    $function_to_call = Input::get_post('function');
    //args, data to handle
    $data_to_pass = Input::get_post('args');



    //test if user has permission for the php file
    if (!$g_UserMgmt->isAllowAccesPHP($file_to_call)) {
        throw new Exception('Kein zugriff auf "' . $file_to_call . '"');
    }


    //test what file, then test what function
    switch ($file_to_call) {

        //AreasEditor------------------------------------------------------------
        case 'app/classes/AreasEditor.php':

            $bereichsname = $data_to_pass['bereich'];
            //falls bereich undefiniert, return error
            if (strlen($bereichsname) == 0) {
                throw new Exception('Bereich undefiniert');
            }

            require_once $ne_config_info['app_path'] . $file_to_call;

            $AreaEditor = new AreasEditor($bereichsname);
            //AreasEditor/functions----------------------------------------------
            switch ($function_to_call) {

                case 'get_content':
                    $data_to_return = $AreaEditor->get_content();
                    //falls feur tinyMCE comments ersetzen
                    if ($data_to_pass['tinymce'] == true) {
                        $data_to_return = str_replace(array('<!--#', '<!--', '-->'), array('<comment_ssi>', '<comment>', '</comment>'), $data_to_return);
                    }

                    break;


                case 'update_content':
                    $new_content = $data_to_pass['new_content'];
                    //falls feur tinyMCE comments ersetzen
                    if ($data_to_pass['tinymce'] == true) {
                        $new_content = str_replace(array('<comment_ssi>', '<comment>', '</comment>'), array('<!-' . '-#', '<!--', '-->'), $new_content);
                    }

                    $data_to_return = $AreaEditor->update_content($new_content);
                    break;


                default :
                    $data_to_return = 'Wrong function name';
                    break;
            }
            break;



        //AreasManager-----------------------------------------------------------
        case 'app/classes/AreasManager.php':

            require_once $ne_config_info['app_path'] . $file_to_call;
            $AreaManager = new AreasManager();
            //AreasManager/functions---------------------------------------------
            switch ($function_to_call) {
                //no need?
                case 'getAreaList':
                    $data_to_return = json_encode($AreaManager->getAreaList());
                    break;

                case 'getAllAreaSettings':

                    $data_to_return = json_encode($AreaManager->getAllAreaSettings());
                    break;

                case 'addAreaSettings':
                    $data_to_return = $AreaManager->addAreaSettings($data_to_pass['name'], $data_to_pass['settings']);
                    break;

                case 'deleteAreaSettings':
                    $data_to_return = $AreaManager->deleteAreaSettings($data_to_pass['name']);
                    break;

                case 'updateAreaSettings':
                    $data_to_return = $AreaManager->updateAreaSettings($data_to_pass['name'], $data_to_pass['settings']);
                    break;
            }
            break;


        //default-------------------------------------------------------------------
        default:
            $data_to_return = 'Wrong parameters.';
            break;
    }
} catch (Exception $e) {
    $data_to_return = 'Error: ' . $e->getMessage() . "\n";
}

echo($data_to_return);
?>
