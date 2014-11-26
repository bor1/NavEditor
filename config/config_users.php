<?php
/**
 * Config for users..
 */

global $ne_user_php_persmissions;
global $ne_user_modus;
global $ne_user_roles;
global $ne_user_params;
global $ne_user_public_php;
//possible User settings/fields
$ne_user_params = Array(
    'user_name'     => Array(
        'name'      => 'Benutzername',
        'editable'  => true
    ),
    'password_hash' => Array(
        'name'      => 'Passwort',
        'editable'  => true
    ),
    'vorname'       => Array(
        'name'      => 'Vorname',
        'editable'  => true
    ),
    'nachname'      => Array(
        'name'      => 'Nachname',
        'editable'  => true
    ),
    'email'         => Array(
        'name'      => 'E-Mail',
        'editable'  => true
    ),
    'rolle'         => Array(
        'name'      => 'Rolle',
        'editable'  => true
    ),
    'permission'    => Array(
        'name'      => 'Rechte',
        'editable'  => true
    ),
    'ablaufdatum'   => Array(
        'name'      => 'Ablaufdatum',
        'editable'  => true
    ),
    'erstellungsdatum' => Array(
        'name'      => 'Erstellungsdatum',
        'editable'  => false
    ),
    'letzter_login' => Array(
        'name'      => 'Letzter Login',
        'editable'  => false
    ),
    'bedienermodus' => Array(
        'name'      => 'Bedienermodus',
        'editable'  => true
    ),
    'zusatzrechte'  => Array(
        'name'      => 'Zusatzrechte',
        'editable'  => true,
        'allow'         => Array(
            'name'      => 'Erlaubte Seiten',
            'editable'  => true
        ),
        'deny'          => Array(
            'name'      => 'Nicht Erlaubte Seiten',
            'editable'  => true
        ),
        'specialrights' => Array(
            'name'      => 'Spezielle Rechte',
            'editable'  => true
        )
    )
);

//user params simple.
function get_ne_user_params_simple() {
    static $ne_user_params_simple;
    if (!is_array($ne_user_params_simple)){
        $ne_user_params_simple = Array();
        foreach ($GLOBALS['ne_user_params'] as $key => $val) {
            $ne_user_params_simple[$key] = '';
        }
    }
    return $ne_user_params_simple;
}

//get not editable user params
function get_ne_user_params_not_editable() {
    static $ne_user_params_not_editable;
    if (!is_array($ne_user_params_not_editable)){
        $ne_user_params_not_editable = Array();
        foreach ($GLOBALS['ne_user_params'] as $key => $val) {
            if ($val['editable'] === false) {
                $ne_user_params_not_editable[$key] = '';
            }
        }
    }
    return $ne_user_params_not_editable;
}

$ne_user_roles = Array(
  'user' => Array(
    'value' => 100,
    'name'  => 'Benutzer'
  ),
  'redaktor' => Array(
    'value' => 200,
    'name'  => 'Redakteur'
  ),
  'admin' => Array(
    'value' => 1000,
    'name'  => 'Administrator'
  )
);

//no need?
$ne_user_modus= Array(
  'normal' => Array(
    'value' => 0,
    'name'  => 'Normalmodus'
  ),
  'expert' => Array(
    'value' => 1,
    'name'  => 'Expertenmodus'
  )
);




//default permissions to access files (path relative to NavEditor folder). If not listed => access declined
//this works only by direct call of file. So includes will work indepentent of permissions!
$ne_user_php_persmissions = array(
    //Public
    'aktivierung.php'           => '0',
    'credits.php'               => '0',
    'help_details.php'          => '0',
    'help_forum_blog.php'       => '0',
    'help_special_faq.php'      => '0',
    'help_using.php'            => '0',
    'index.php'                 => '0',
    'licence.php'               => '0',
    'login.php'                 => '0',
    'pwrecovery.php'            => '0',
    'app/get_help.php'          => '0',

    //User
    'dashboard.php'             => 'user',
    'logout.php'                => 'user',
    'nav_editor.php'            => 'user',
    'app/ajax_handler.php'      => 'user',
    'app/do_upload.php'         => 'user',
    'app/keep_session.php'      => 'user',
    'app/load_tree_data.php'    => 'user',
    'app/save_tree_data.php'    => 'user',
    'app/update_contents.php'   => 'user',


    //Redaktor
    'default_editor.php'        => 'redaktor',
    'file_editor.php'           => 'redaktor',
    'ma_editor.php'             => 'redaktor',
    'remove_caches.php'         => 'redaktor',
    'app/edit_ma.php'           => 'redaktor',
    'app/file_manager.php'      => 'redaktor',
    'app/folderImgPreview.php'  => 'redaktor',
    'app/jqueryFileTree.php'    => 'redaktor',
    'app/ma_photo_upload.php'   => 'redaktor',
    'app/upload.php'            => 'redaktor',
    'app/classes/AreasEditor.php' => 'redaktor',//for ajax_handler


    //Admin
    'areas_manager.php'      => 'admin',
    'conf_editor.php'           => 'admin',
    'design_editor.php'         => 'admin',
    'update.php'                => 'admin',
    'user_manager.php'          => 'admin',
    'website_editor.php'        => 'admin',
    'app/create_conf.php'       => 'admin',
    'app/edit_conf.php'         => 'admin',
    'app/edit_design.php'       => 'admin',
    'app/edit_logo.php'         => 'admin',
    'app/edit_up_img.php'       => 'admin',
    'app/edit_user.php'         => 'admin',
    'app/live_update.php'       => 'admin',
    'app/load_osm.php'          => 'admin',
    'app/save_osm.php'          => 'admin',
    'app/classes/AreasManager.php'       => 'admin',//for ajax_handler

    //no need yet, but listed..
    'app/classes/BackupManager.php'     => 'admin',
    'app/classes/ConfigFileManagerAbstract.php' => 'admin',
    'app/classes/ConfigFileManagerJSON.php' => 'admin',
    'app/classes/ConfigManager.php'         => 'admin',
    'app/classes/ContentHandler.php'        => 'admin',
    'app/classes/FileHandler.php'       => 'admin',
    'app/classes/FileManager.php'       => 'admin',
    'app/classes/Input.php'             => 'admin',
    'app/classes/NavEditorAbstractClass.php' => 'admin',
    'app/classes/NavTools.php'          => 'admin',
    'app/classes/SimplePie.php'         => 'admin',
    'app/classes/Snoopy.php'            => 'admin',
    'app/classes/UserMgmt.php'          => 'admin',
    'app/classes/Logger/LoggerCSV.php'  => 'admin',
    'app/classes/Logger/NE_Logger.php'  => 'admin',
    'app/classes/pattern/Singleton.php' => 'admin',

    //include only => need to be admin if called directly
    'auth.php'                  => 'admin',//include only
    'common_footer.php'         => 'admin',//include only
    'common_nav_menu.php'       => 'admin',//include only
    'app/log_funcs.php'         => 'admin',//include only
    'app/sessions.php'          => 'admin',//include only

    'file_editor_old.php'          => 'admin',//testing
    'design_editor_old.php'          => 'admin',//testing
    'nav_editor_old.php'          => 'admin',//testing
    'osm_popover.php'          => 'admin',//testing
    'file_editor_popover.php'          => 'admin',//testing

);

//public paths
$ne_user_public_php = array();
foreach ($ne_user_php_persmissions as $path => $permission) {
    if(strcmp($permission, '0')===0){
        $ne_user_public_php[] = $path;
    }
}

?>
