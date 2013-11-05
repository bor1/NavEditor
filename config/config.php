<?php
define ('NE_DIR_RELATIVE', '/vkdaten/tools/NavEditor3/');
define ('NE_DIR_ROOT', $_SERVER['DOCUMENT_ROOT'].NE_DIR_RELATIVE);
define ('NE_DIR_CONFIG', NE_DIR_ROOT.'config/');
define ('NE_DIR_CLASSES', NE_DIR_ROOT.'app/classes/');

require_once(NE_DIR_ROOT . 'autoload.php');

require_once(NE_DIR_CONFIG  . 'config_users.php');
require_once(NE_DIR_CONFIG  . 'config_areaeditor.php');

// error_reporting(E_ALL & ~E_STRICT);
// ini_set('display_errors', 'on');

global $ne_config_info;
global $ne_menu;
global $ne_user_php_persmissions;
global $ne_user_public_php;
global $g_areas_settings;

//===========FROM OTHER CONFIGS, ALIASES========================================
//This php files are public for access, no need to login before.
$ne_config_info['public_php_files'] = $ne_user_public_php;
//array wih permissions for php files
$ne_config_info['php_file_permissions'] = $ne_user_php_persmissions;
//settings and information about areas
$ne_config_info['areas_settings'] = $g_areas_settings;


//========================USUAL SETTINGS========================================
// please include trailing slash!
$ne_config_info['app_path_without_host'] = NE_DIR_RELATIVE;
$ne_config_info['app_path']     = NE_DIR_ROOT;
$ne_config_info['log_path']     = NE_DIR_ROOT . "log/";
$ne_config_info['cgi-bin_path'] = NavTools::simpleResolvePath($_SERVER['DOCUMENT_ROOT'] . "/../cgi-bin/");

// the filename of user-data
$ne_config_info['user_data_file_name'] = '.hteditoruser';
//full path to user_data file
$ne_config_info['user_data_file_path'] = $ne_config_info['app_path'] . 'data/' . $ne_config_info['user_data_file_name'];

// the filename of debug times
$ne_config_info['debug_execution_file_name'] = 'debug-execution-time.log';
$ne_config_info['debug_execution_file']      = $ne_config_info['log_path'].$ne_config_info['debug_execution_file_name'];
$ne_config_info['debug_time']                = 1;

// where to save uploaded data (without trailing slash!)
$ne_config_info['upload_dir'] = $_SERVER['DOCUMENT_ROOT'] . '';



// current public title with html
$ne_config_info['app_title']        = 'NavEditor 3 <sup>Alpha</sup>';

// current public title with html
$ne_config_info['app_titleplain']   = 'NavEditor 3 Delta';

// current version
$ne_config_info['version']          = '3.13.0814';

// update host
$ne_config_info['update_url']       = 'http://www.vorlagen.uni-erlangen.de/downloads/naveditor/';



// path to naveditor config files
$ne_config_info['config_path']      = $ne_config_info['app_path'] . 'data/';

// path for template files
$ne_config_info['template_path']    = $ne_config_info['app_path'] . 'data/templates/';

// template files for default  pages
$ne_config_info['template_default'] = 'seitenvorlage.html';

// JS folder name in vkdaten folder
$ne_config_info['js_folder_name']   = 'js';

// CSS folder name in vkdaten folder
$ne_config_info['css_folder_name']  = 'css';

//URL to naveditor
$ne_config_info['ne_url']           = "http://".$_SERVER['HTTP_HOST'].$ne_config_info['app_path_without_host'];

//ssi folder path
$ne_config_info['ssi_folder_path']  = $_SERVER['DOCUMENT_ROOT']. "/ssi/";


// new in editor editable conf-items

$default_conf_file = $ne_config_info['config_path'] . 'ne2_config.conf';
if(!file_exists($default_conf_file)) {
	$fallback_file = $ne_config_info['config_path'] . '_ne2_config.conf';
	@copy($fallback_file, $default_conf_file);
}
$config_manager = new ConfigManager($default_conf_file);

//einige .conf dateinamen.
$ne_config_info['website_conf_filename']    = $config_manager->get_conf_item('website', 'website.conf');
$ne_config_info['variables_conf_filename']  = $config_manager->get_conf_item('variables', 'variables.conf');
//config file path fuer bereiche
$ne_config_info['area_conf_filepath']       = $config_manager->get_conf_item('area_conf_filepath', $ne_config_info['config_path'] . 'bereiche.conf');

//maximum user lock time after wrong pw etc. (in Seconds)
$ne_config_info['login_max_lock_time']          = $config_manager->get_conf_item('timeout_loghistory', 3600);

//tiny_mce theme_advanced_styles
$ne_config_info['custom_content_css_classes']   = $config_manager->get_conf_item('custom_content_css_classes', 'clear|unsichtbar|marker|hinweis|links|rechts|marker|bildrechts|bildlinks|vollbox|klein_box_links|klein_box_rechts|box_rechts|box_links');
//zeigt in nav_editor neben jedem menue eigene ID
$ne_config_info['show_navtree_numbers']         = $config_manager->get_conf_item('show_navtree_numbers', 0); // 1 or 0
//beim laden wird das Tree geoffnen sein
$ne_config_info['navtree_start_open'] 			= $config_manager->get_conf_item('navtree_start_open', 0);

//==========================LOGGER==============================================
//bestimmt ob irgendwas geloggt werden muss
$ne_config_info['log_activated']        = $config_manager->get_conf_item('log_activated',1);
//maximale dateiegroesse des logs in bytes
$ne_config_info['log_max_file_size']    = $config_manager->get_conf_item('log_max_file_size',1024*1024); //1mb
//maximum log history time (in Seconds)
$ne_config_info['log_max_history']      = $config_manager->get_conf_item('log_max_history', 4*24*3600);//4 days
//default log file path
$ne_config_info['log_file']             = $config_manager->get_conf_item('log_file', $ne_config_info['log_path'].'log.csv');
//separator for CSV log file
$ne_config_info['log_csv_separator']    = $config_manager->get_conf_item('log_csv_separator', ';');
//format for CSV log file
$ne_config_info['log_csv_format']       = $config_manager->get_conf_item('log_format', 'timestamp|date-time|errorlevel|username|ip|host|referrer|file|line|message');
//mask for errorlevels of Logger
use Logger\LoggerCSV as L;
$ne_config_info['log_errormask']        = $config_manager->get_conf_item('log_errormask', L::MASK_DEBUG | L::MASK_INFO | L::MASK_WARNING | L::MASK_ERROR);
//==========================LOGGER END==========================================


//==========================SESSIONS============================================
$ne_config_info['session_timeout'] 			= $config_manager->get_conf_item('session_timeout', 7200 );

//gibt an, wie oft bei editierung in nav_editor.php session verlaengert wird.
//js_keep_session_time sollte kleiner sein als session_timeout:
$ne_config_info['js_keep_session_time'] 	= $config_manager->get_conf_item('js_keep_session_time', 1200 );

// Sicherung, dass die Session-Times nicht zu klein ausfallen:
if ($ne_config_info['session_timeout'] < 1800) {
	$ne_config_info['session_timeout'] = 1800;
}
if ($ne_config_info['js_keep_session_time'] > $ne_config_info['session_timeout']) {
	$ne_config_info['js_keep_session_time'] = 600;
}
//==========================SESSIONS END========================================

$ne_config_info['dashboard_feed']               = $config_manager->get_conf_item('dashboard_feed', 'http://blogs.fau.de/webworking/feed');
//source editor bei file_editor.php
$ne_config_info['hide_sourceeditor'] 			= $config_manager->get_conf_item('hide_sourceeditor', 1);
$ne_config_info['max_upload_filesize']          = $config_manager->get_conf_item('max_upload_filesize', 50); // megabytes
$ne_config_info['show_logoupdate_allwebpages']  = $config_manager->get_conf_item('show_logoupdate_allwebpages', 1);
$ne_config_info['defaulthtml_filesuffix']   	= $config_manager->get_conf_item('defaulthtml_filesuffix', 'shtml');
// helpfilesuffix:
$ne_config_info['help_filesuffix']  			= $config_manager->get_conf_item('help_filesuffix', '.html');
// path for help files
$ne_config_info['help_path']                    = $config_manager->get_conf_item('help_path', $ne_config_info['app_path'] .'data/helps/');   ;
// path for temp files
$ne_config_info['temp_path']                    = $config_manager->get_conf_item('temp_path',$ne_config_info['app_path'] . '_tmp/');

// indexdatei:
$ne_config_info['directoryindex_file']  		= $config_manager->get_conf_item('directoryindex_file', 'index.shtml');
// where to store backup files
$ne_config_info['backup_root']                  = $config_manager->get_conf_item('backup_root', $ne_config_info['app_path'] . '.htbackup/');
 // backup type: 1-Only one (with .bak-suffix); 2-Many (with Timestamp-suffix)
$ne_config_info['backup_type']                  = $config_manager->get_conf_item('backup_type', 2);
// where to backup navgationsindex.txt
$ne_config_info['navindex_backup_dir']  		= $config_manager->get_conf_item('navindex_backup_dir', $_SERVER['DOCUMENT_ROOT'] . '/vkdaten/navindex_backup/');

 //webauftritt configfiles ordner
$ne_config_info['default_configs_path']         = $config_manager->get_conf_item('default_configs_path', $_SERVER['DOCUMENT_ROOT'] . "/vkdaten/");



$ne_config_info['page_content_marker_start']           = $config_manager->get_conf_item('page_content_marker_start',  '<!-- TEXT AB HIER -->');
$ne_config_info['page_content_marker_end']             = $config_manager->get_conf_item('page_content_marker_end', '<!-- AB HIER KEIN TEXT MEHR -->');
$ne_config_info['page_content_marker_start_fallback']  = $config_manager->get_conf_item('page_content_marker_start_fallback', '<a name="contentmarke" id="contentmarke"></a>');
$ne_config_info['page_content_marker_end_fallback']    = $config_manager->get_conf_item('page_content_marker_end_fallback', '<hr id="vorfooter" />');
$ne_config_info['page_content_marker_preinhaltsinfo']  = $config_manager->get_conf_item('page_content_marker_preinhaltsinfo',  '<!--#include virtual="/ssi/inhaltsinfo.shtml" -->');

// Definition der Seitenbereiche
$ne_config_info['content_marker_start_setting']        = 'content_marker_start';
$ne_config_info['content_marker_end_setting']          = 'content_marker_end';

$ne_config_info['current_site_title_file'] = $config_manager->get_conf_item('current_site_title_file',  '');

//========================= LOESCHEN SPAETER BEGIN =============================
// activate_univis_mitarbeitereditor:  Wird der Editor fuer UnivIS-Extra Personendaten angezeigt? (Alter Editor war er default an, ab 2012 besser default aus)
$ne_config_info['tool_univis_mitarbeitereditor'] 	= $config_manager->get_conf_item('tool_univis_mitarbeitereditor', 0); // 1 or 0
if ($ne_config_info['tool_univis_mitarbeitereditor'] ) {
	$ne_config_info['activate_toolmenu']  = 1;
}


$ne_config_info['zusatzinfo_file']                      = $config_manager->get_conf_item('zusatzinfo_file', '/ssi/zusatzinfo.shtml');
// Marker fuer Zusatzinfo:
$ne_config_info['zusatzinfo_content_marker_start']      = $config_manager->get_conf_item('zusatzinfo_content_marker_start',  '<div id="zusatzinfo" class="noprint">  <!-- begin: zusatzinfo -->');
$ne_config_info['zusatzinfo_content_marker_startdiv']   = $config_manager->get_conf_item('zusatzinfo_content_marker_startdiv', '<a id="zusatzinfomarke" name="zusatzinfomarke"></a>');
$ne_config_info['zusatzinfo_content_marker_end']        = $config_manager->get_conf_item('zusatzinfo_content_marker_end', '</div>  <!-- end: zusatzinfo -->');
// Kurzinfo Datei
$ne_config_info['kurzinfo_file']                        = $config_manager->get_conf_item('kurzinfo_file', '/ssi/kurzinfo.shtml');
// Marker fuer Kurzinfo:
$ne_config_info['kurzinfo_content_marker_start']        = $config_manager->get_conf_item('kurzinfo_content_marker_start',  '<div id="kurzinfo">  <!-- begin: kurzinfo -->');
$ne_config_info['kurzinfo_content_marker_end']          = $config_manager->get_conf_item('kurzinfo_content_marker_end', '</div>  <!-- end: kurzinfo -->');
//TMP sidebar
$ne_config_info['sidebar_content_marker_start']         = $config_manager->get_conf_item('sidebar_content_marker_start',  '<aside><div id="sidebar" class="noprint">  <!-- begin: sidebar -->');
$ne_config_info['sidebar_content_marker_startdiv']      = $config_manager->get_conf_item('sidebar_content_marker_startdiv', '<aside><div id="sidebar" class="noprint">  <!-- begin: sidebar -->');
$ne_config_info['sidebar_content_marker_end']           = $config_manager->get_conf_item('sidebar_content_marker_end', '</div></aside>  <!-- end: sidebar -->');//verkehrt?
$ne_config_info['sidebar_file']                         = $config_manager->get_conf_item('sidebar_file', '/ssi/sidebar.shtml');

// Inhaltsinfo Datei
$ne_config_info['inhaltsinfo_file']                     = $config_manager->get_conf_item('inhaltsinfo_file', '/ssi/inhaltsinfo.shtml');
// Marker fuer Inhaltsinfo:
$ne_config_info['inhaltsinfo_content_marker_start']  	= $config_manager->get_conf_item('inhaltsinfo_content_marker_start',  '<div id="inhaltsinfo">  <!-- begin: inhaltsinfo -->');
$ne_config_info['inhaltsinfo_content_marker_end']       = $config_manager->get_conf_item('inhaltsinfo_content_marker_end', '</div>  <!-- end: inhaltsinfo -->');


// Footerinfo Datei
$ne_config_info['footerinfo_file']                      = $config_manager->get_conf_item('footerinfo_file', '/ssi/footerinfos.shtml');
// Marker fuer Footerinfo sind nicht vorhanden, da diese ausserhalb der Datei definiert wurden

// Zielgruppenmenu Datei
$ne_config_info['zielgruppenmenu_file']                 = $config_manager->get_conf_item('zielgruppenmenu_file', '/ssi/zielgruppenmenu.shtml');
// Marker fuer das Zielgruppenmenu sind nicht vorhanden, da diese ausserhalb der Datei definiert wurden


 //========================= LOESCHEN SPAETER END ==============================




$ne_config_info['live_update_backupfiles'] = array(
	".hteditoruser",
	"ne2_config.conf",
	"htacc_template_auth",
	"htacc_template_host",
	"current_design.txt",
	"templates/seitenvorlage.html",
	"heads/kopf-z0s0.shtml",
	"heads/kopf-z1s0.shtml",
	"heads/kopf-z0s1.shtml",
	"heads/kopf-z1s1.shtml",
);


//wichtige verzeichnisse, die in manchen Faellen nicht bearbeitet werden duerfen
$ne_config_info['important_folders'] = Array('css','grafiken','img','ssi','js','vkdaten','univis','vkapp');
// Verzeichnisse in denen bei einer Aktualisierung aller Dateien fuer den Kopfteil nicht geschaut wird
$ne_config_info['nologoupdate_dir'] = Array('.', '..', 'css','grafiken','img','ssi','js','vkdaten','univis','vkapp','Smarty','xampp');


// jqueryFileTree Icons Pfad
$ne_config_info['jquery_file_tree'] = Array (
    'icons' => Array(
        'newfolder_icon'    => $ne_config_info['ne_url'] . $ne_config_info['css_folder_name'].'/images/newfolder.png',
        'rename_icon'       => $ne_config_info['ne_url'] . $ne_config_info['css_folder_name'].'/images/rename.png',
        'delete_icon'       => $ne_config_info['ne_url'] . $ne_config_info['css_folder_name'].'/images/delete.png',
        'create_new_icon'   => $ne_config_info['ne_url'] . $ne_config_info['css_folder_name'].'/images/newdocument.png',
    ),
    'colors' => Array(
        'color_notallow'    => '#999',
        'color_someallow'   => '#666'
    )
);

//for word filters
$ne_config_info['symbols_being_replaced']   = array ('ä', 'ö', 'ü', 'ß', "Ä", "Ö", "Ü","ẞ");
$ne_config_info['symbols_replacement']      = array ('ae', 'oe', 'ue', 'ss', "AE", "OE", "UE", "SS");
$ne_config_info['regex_removed_symbols']    = '/[^a-zA-Z0-9\-_\s]/';


//default html includes for every file
$default_include_date = date('Ymdis');
$ne_config_info['default_includes_js_css'] = Array(
    "style.css?, $default_include_date",
    "bootstrap.css?, $default_include_date",
    "bootstrap-responsive.css?, $default_include_date",
    "jquery-1.10.1.min.js",
    "loading.js",
    "bootstrap.js"
);





//=====================Menu=====================================================
//rollen in dem fall sind nur bei '#' links wichtig.
//andere rollen werden von $ne_config_info['php_file_permissions'] ersetzt
//zu dem array kommen danach noch extra werte fuer bereiche.
//die Bereich-daten werden von bereich-klasse geladen,
//weil die "rollen" von der Bereich-config-datei abhaengig sind.
$ne_menu = array(
	1 => array(
		'id'        => 1,
		'title' => 'Dashboard',
		'link'	=> 'dashboard.php',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 0,
		'desc'	=> '',
	),
	10 => array(
		'id'		=> 10,
		'title'	=> 'Bearbeiten',
		'link'	=> '#',
		'role'	=> 'user',
		'sub'	=> 1,
		'up'	=> 0,
		'desc'	=> 'Seiten und Struktur erstellen und &auml;ndern',
	),
	11 => array(
		'id'		=> 11,
		'title'	=> 'Seite und Navigation',
		'link'	=> 'nav_editor.php',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 10,
		'desc'	=> '',
	),
	12 => array(
		'id'		=> 12,
		'title'	=> 'Bilder und Dateien',
		'link'	=> 'file_editor.php',
		'role'	=> 'redaktor',
		'sub'	=> 0,
		'up'	=> 10,
		'desc'	=> '',
	),
	20 => array(
		'id'		=> 20,
		'title'	=> 'Allgemeine Bereiche',
		'link'	=> '#',
		'role'	=> 'redaktor',
		'sub'	=> 1,
		'up'	=> 0,
		'desc'	=> 'Allgemeine Information von jeder Seiten bearbeiten',
	),
//	21...3x loaded from config_areaeditor

	40 => array(
		'id'		=> 40,
		'title'	=> 'Tools',
		'link'	=> '#',
		'role'	=> 'redaktor',
		'sub'	=> 1,
		'up'	=> 0,
		'desc'	=> 'Funktionen die modulare Werkzeuge des Webbaukastens betreffen',
	),
	41 => array(
		'id'		=> 41,
		'title'	=> 'UnivIS-Integration: Mitarbeiter',
		'link'	=> 'ma_editor.php',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 40,
		'desc'	=> 'Zielgruppemmenu und Umgebung (Kopfteil der Seite)',
	),
    42 => array(
		'id'		=> 41,
		'title'	=> 'Caches',
		'link'	=> 'remove_caches.php',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 40,
		'desc'	=> 'Caches l&ouml;eschen',
	),

	50 => array(
		'id'		=> 50,
		'title'	=> 'Erweitert',
		'link'	=> '#',
		'role'	=> 'admin',
		'sub'	=> 1,
		'up'	=> 0,
		'desc'	=> 'Administratorfunktionen',
		'addclass'	=> 'role_admin',
	),
	51 => array(
		'id'		=> 51,
		'title'	=> 'Daten zur Website',
		'link'	=> 'website_editor.php',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),
	52 => array(
		'id'		=> 52,
		'title'	=> 'Konfiguration',
		'link'	=> 'conf_editor.php',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),

	53 => array(
		'id'		=> 53,
		'title'	=> 'Design',
		'link'	=> 'design_editor.php',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),
	54 => array(
		'id'		=> 54,
		'title'	=> 'Benutzerverwaltung',
		'link'	=> 'user_manager.php',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'  => '',
    ),
    55 => array(
        'id'        => 55,
        'title' => 'Bereiche verwalten',
        'link'  => 'areas_manager.php',
        'role'  => 'admin',
        'sub'   => 0,
        'up'    => 50,
        'desc'  => 'Bereiche Verwalten',
    ),
	56 => array(
		'id'		=> 56,
		'title'	=> 'Update',
		'link'	=> 'update.php',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),
	60 => array(
		'id'		=> 60,
		'title'	=> 'Hilfe',
		'link'	=> '#',
		'role'	=> 'public',
		'sub'	=> 1,
		'up'	=> 0,
		'desc'	=> '',
	),
	61 => array(
		'id'		=> 61,
		'title'	=> 'Nutzung der Hilfe',
		'link'	=> 'help_using.php',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	62 => array(
		'id'		=> 62,
		'title'	=> 'Detaillierte Hilfe',
		'link'	=> 'help_details.php',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	63 => array(
        'id'		=> 63,
		'title'	=> 'Spezielle Fragen &amp; Antworten',
		'link'	=> 'help_special_faq.php',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	64 => array(
        'id'		=> 64,
		'title'	=> 'Forum &amp; Blog',
		'link'	=> 'help_forum_blog.php',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	65 => array(
        'id'		=> 65,
		'title'	=> 'Nutzungslizenz',
		'link'	=> 'licence.php',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	66 => array(
        'id'		=> 66,
		'title'	=> 'Entwickler',
		'link'	=> 'credits.php',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	100 => array(
		'id'		=> 100,
		'title'	=> 'Abmelden',
		'link'	=> 'logout.php',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 0,
		'desc'	=> '',
		'addclass'	=> 'logout',
		'attribut' => 'onclick="javascript:return confirm(\'Wollen Sie sich wirklich abmelden?\');"',
	)
 );

//dynamisch AreasEditors binden
//MUST BE SET BEFORE:
//          $ne_config_info['area_conf_filepath'],
//          $ne_config_info[{bereich}.'_content_marker_start']
//          $ne_config_info[{bereich}.'_content_marker_end']

$AreaManager = new AreasManager();
$alleBereiche = $AreaManager->getAllAreaSettings();

foreach ($alleBereiche as $aBereich) {
    static $i = 21;

    $ne_menu[$i] = array(
        'id'    => $i,
        'title' => $aBereich['title'],
        'link'  => 'default_editor.php?' . $aBereich['name'],
        'role'  => $aBereich['user_role_required'],
        'sub'   => 0,
        'up'    => 20,
        'desc'  => $aBereich['description']
    );

    $i++;
}


//fill $ne_menu permissions depend on $ne_config_info['php_file_permissions']
$tmp_files_with_permissions = array_keys($ne_config_info['php_file_permissions']);//for performance
foreach ($ne_menu as $key=>$params) {
    if(in_array($params['link'],  $tmp_files_with_permissions)){
        $ne_menu[$key]['role'] = $ne_config_info['php_file_permissions'][$params['link']];
    }
}
//=======================END MENU===============================================

?>
