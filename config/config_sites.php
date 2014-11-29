<?php

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
		'link'	=> 'index.php?p=dashboard',
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
		'desc'	=> 'Seiten, Navigation und Dateien bearbeiten',
	),
	11 => array(
		'id'		=> 11,
		'title'	=> 'Seite und Navigation',
		'link'	=> 'index.php?p=nav_editor',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 10,
		'desc'	=> '',
	),
	12 => array(
		'id'		=> 12,
		'title'	=> 'Bilder und Dateien',
		'link'	=> 'index.php?p=file_editor',
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
		'desc'	=> 'Allgemeine Bereiche bearbeiten',
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
		'link'	=> 'index.php?p=ma_editor',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 40,
		'desc'	=> 'Mitarbeiter-Zusatzinformationen hochladen',
	),
    42 => array(
		'id'		=> 41,
		'title'	=> 'Caches',
		'link'	=> 'index.php?p=remove_caches',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 40,
		'desc'	=> 'Caches leeren',
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
		'link'	=> 'index.php?p=website_editor',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),
	52 => array(
		'id'		=> 52,
		'title'	=> 'Konfiguration',
		'link'	=> 'index.php?p=conf_editor',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),

	53 => array(
		'id'		=> 53,
		'title'	=> 'Design',
		'link'	=> 'index.php?p=design_editor',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),
	54 => array(
		'id'		=> 54,
		'title'	=> 'Benutzerverwaltung',
		'link'	=> 'index.php?p=user_manager',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'  => '',
        ),
        55 => array(
            'id'        => 55,
            'title' => 'Bereiche verwalten',
            'link'  => 'index.php?p=areas_manager',
            'role'  => 'admin',
            'sub'   => 0,
            'up'    => 50,
            'desc'  => 'Bereiche Verwalten',
        ),
	56 => array(
		'id'		=> 56,
		'title'	=> 'Update',
		'link'	=> 'index.php?p=update',
		'role'	=> 'admin',
		'sub'	=> 0,
		'up'	=> 50,
		'desc'	=> '',
	),
        57 => array(
		'id'		=> 56,
		'title'	=> 'Debug',
		'link'	=> 'index.php?p=debug',
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
		'link'	=> 'index.php?p=help_using',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	62 => array(
		'id'		=> 62,
		'title'	=> 'Detaillierte Hilfe',
		'link'	=> 'index.php?p=help_details',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	63 => array(
        'id'		=> 63,
		'title'	=> 'Spezielle Fragen &amp; Antworten',
		'link'	=> 'index.php?p=help_special_faq',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	64 => array(
        'id'		=> 64,
		'title'	=> 'Forum &amp; Blog',
		'link'	=> 'index.php?p=help_forum_blog',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	65 => array(
        'id'		=> 65,
		'title'	=> 'Nutzungslizenz',
		'link'	=> 'index.php?p=licence',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	66 => array(
        'id'		=> 66,
		'title'	=> 'Entwickler',
		'link'	=> 'index.php?p=credits',
		'role'	=> 'public',
		'sub'	=> 0,
		'up'	=> 60,
		'desc'	=> '',
	),
	100 => array(
		'id'		=> 100,
		'title'	=> 'Abmelden',
		'link'	=> 'index.php?p=logout',
		'role'	=> 'user',
		'sub'	=> 0,
		'up'	=> 0,
		'desc'	=> '',
		'addclass'	=> 'logout',
		'attribut' => 'onclick="javascript:return confirm(\'Wollen Sie sich wirklich abmelden?\');"',
	)
 );
//fill $ne_menu permissions depend on $ne_config_info['php_file_permissions']
$tmp_files_with_permissions = array_keys($ne_config_info['php_file_permissions']);//for performance
foreach ($ne_menu as $key=>$params) {
    if(in_array($params['link'],  $tmp_files_with_permissions)){
        $ne_menu[$key]['role'] = $ne_config_info['php_file_permissions'][$params['link']];
    }
}
//=======================END MENU===============================================


$ne_site_name = array(
    "not_found"       => "Seite nicht gefunden!",
    "areas_manager"   => "Bereich Management",
    "nav_editor"      => "Seite und Navigation",
    "user_manager"    => "User management",
    "file_editor"     => "Manage Images/Files",
    "dashboard"       => "Dashboard",
    "credits"         => "Credits",
    "licence"         => "Nutzungslizens",
    "help_forum_blog" => "Forum & Blog",
    "help_special_faq"=> "Spezielle Fragen und Antworten",
    "help_details"    => "Detaillierte Hilfe",
    "help_using"      => "Nutzungshinweis",
    "website_editor"  => "Daten bearbeiten",
    "conf_editor"     => "Konfigurationen bearbeiten",
    "ma_editor"       => "Mitarbeiter bearbeiten",
    "design_editor"   => "Design wechseln",
    "debug"           => "Status und Fehlersuche"
);



?>
