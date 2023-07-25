<?php
// Direktzugriff auf die Datei aus Sicherheitsgründen sperren
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

 
// Die Informationen, die im Pluginmanager angezeigt werden
function randomfacts_info()
{
	return array(
		"name"		        => "Randomfacts auf dem Index",
		"description"	    => "Erstellt eine Datenbank, in die Random Fakts oder Informationen eingetragen und dann zufällig auf dem Index ausgegeben werden können.",
		"website"	        => "Webseite des Plugins (Herstellerseite)",
		"author"	        => "White_Rabbit (Tom)",
		"authorsite"	    => "Webseite des Erstellers",
		"version"	        => "1.0",
		"compatibility"     => "18*"
    );
}

// Installationsfunktionen

function randomfacts_install()
{
    global $db, $cache, $mybb;

    // Neue Datenbank Tabelle hinzufügen
    $db->query("CREATE TABLE ".TABLE_PREFIX."randomfacts (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `titel` varchar(140) NOT NULL,
        `text` longtext NOT NULL,
        PRIMARY KEY (`id`),
        KEY `id` (`id`)
        )
        ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1"
        );


    // Einstellungen im ACP

    $setting_group = array(
        'name'          => 'randomfacts',
        'title'         => 'Random Facts',
        'description'   => 'Einstellungen für die Random Facts',
        'disporder'     => 1,
        'isdefault'     => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        'randomfacts_group-access' => array(
            'title' => 'Facts Übersicht anzeigen',
            'description' => 'Wähle aus, welche Gruppen die Factsübersicht sehen darf.',
            'optionscode' => 'groupselect',
            'value' => '1', // Default
            'disporder' => 1
        ),

        'randomfacts_group-add' => array(
            'title' => 'Facts hinzugügen',
            'description' => 'Wähle aus, welche Gruppen Facts hinzufügen darf.',
            'optionscode' => 'groupselect',
            'value' => '1', // Default
            'disporder' => 2
        ),

        'randomfacts_group-edit' => array(
            'title' => 'Facts bearbeiten',
            'description' => 'Wähle aus, welche Gruppen Facts bearbeiten und löschen dürfen:',
            'optionscode' => 'groupselect',
            'value' => '1', // Default
            'disporder' => 3
        ),
    );

    foreach($setting_array as $name => $setting)
            {
                $setting['name'] = $name;
                $setting['gid']  = $gid;
                $db->insert_query('settings', $setting);
            }

    rebuild_settings();


    // Templates erstellen

    $templategroup = array(
        "prefix"    => "randomfacts",
        "title"     => $db->escape_string("Random Facts"),
    );

    $db->insert_query("templategroups", $templategroup);


    // Random Facts Übersicht

    $insert_array = array(
        'title'         => 'randomfacts',
        'template'      => $db->escape_string('
        <html>

        <head>
        <title>{$settings[\'bbname\']} - Random Facts</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
            <tr>
            <td class="thead" colspan="2"><strong>Random Facts</strong></td>
            </tr>
            <td class="trow1" width="25%">
            {$navigation}
            </td>
            <td class="trow1" align="center">
                Hallo, das ist ein Test!
            </td>
            </tr>
        </table>
        {$footer}
        </body>
        </html>
        '),
        'sid'           => '-2', // -1 Global, -2 Design
        'version'       => '*',
        'dateline'      => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // Random Fakts alle Fakten

    $insert_array = array(
        'title'         => 'randomfacts_all',
        'template'      => $db->escape_string('
        <html>
        <head>
        <title>{$settings[\'bbname\']} - Random Facts</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
            <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
                <tr>
                    <td class="thead" colspan="2"><strong>Random Facts</strong></td>
                </tr>
                <tr>
                    <td class="trow1" width="25%" valign="top">
                        {$navigation}
                    </td>
                    <td class="trow1" align="center">
						<div class="randomfacts">
                        {$randomfacts_bit}
						</div>
                    </td>
                </tr>
            </table>
        {$footer}
        </body>
        </html>
        '),
        'sid'           => '-2', // -1 Global, -2 Design
        'version'       => '*',
        'dateline'      => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // Random Fakts alle Fakten Bit

    $insert_array = array(
        'title'         => 'randomfacts_all_bit',
        'template'      => $db->escape_string('
        <div class="randomfacts">
        <h1>{$randomfacts[\'titel\']}</h1>
        <div class="randomfacts-text">{$randomfacts[\'text\']}</div>   
        <div class="randomfacts-do">
            <div class="randomfacts-edit"><a href="randomfacts.php?action=edit&id={$randomfacts[\'id\']}">[E]</a></div>
            <div class="randomfacts-edit"><a href="randomfacts.php?action=delete&id={$randomfacts[\'id\']}">[X]</a></div>
        </div>
        </div>
        '),
        'sid'            => '-2', // -1 Global, -2 Design
        'version'        => '*',
        'dateline'       => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // Random Fakts Fakt bearbeiten

    $insert_array = array(
        'title'         => 'randomfacts_edit',
        'template'      => $db->escape_string('
        <html>
        <head>
        <title>{$settings[\'bbname\']} - Hintergrundinformationen</title>
        {$headerinclude}
        </head>
        <body>
            {$header}
            <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
                <tr>
                    <td class="thead" colspan="2"><strong>Hintergrundinformationen</strong></td>
                </tr>
                <tr>
                    <td class="trow1" width="25%" valign="top">
                    {$navigation}
                    </td>
                    <td class="trow1" align="center">
                    <form action="randomfacts.php?action=do_edit&id={$randomfacts[\'id\']}" method="post">
                        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
                            <tr>
                                <td class="thead" colspan="2">Fakt ändern</td>
                            </tr>
                            <tr>
                                <td class="trow1">Überschrift</td>
                                <td class="trow1"><input type="text" name="titel" value="{$randomfacts[\'titel\']}"/></td>
                            </tr>
                            <tr>
                                <td class="trow1">Inhalt</td>
                                <td class="trow1"><textarea name="text" rows="6" cols="100">{$randomfacts[\'text\']}</textarea></td>
                            </tr>
                            <tr>
                                <td class="trow1" colspan="2"><input type="submit" value="Absenden" /></td>
                            </tr>
                        </table>
                    </form>
                    
                    </td>
                </tr>
            </table>
            {$footer}
        </body>
        </html>
        '),
        'sid'           => '-2', // -1 Global, -2 Design
        'version'       => '*',
        'dateline'      => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // Random Fakts Fakt hinzufügen

    $insert_array = array(
        'title'         => 'randomfacts_add',
        'template'      => $db->escape_string('
        <html>
        <head>
            <title>{$settings[\'bbname\']} - Hintergrundinformationen</title>
            {$headerinclude}
        </head>
        <body>
            {$header}
            <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
                <tr>
                    <td class="thead" colspan="2"><strong>Hintergrundinformationen</strong></td>
                </tr>
                <tr>
                    <td class="trow1" width="25%" valign="top">
                        {$navigation}
                    </td>
                    <td class="trow1" align="center">
                        <form action="randomfacts.php?action=do_add" method="post">
							<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
                            <tr>
                                <td class="thead" colspan="2">Fakt ändern</td>
                            </tr>
                            <tr>
                                <td class="trow1">Überschrift</td>
                                <td class="trow1"><input type="text" name="titel" width="80%"></td>
                            </tr>
                            <tr>
                                <td class="trow1">Inhalt</td>
                                <td class="trow1"><textarea name="text" rows="6" cols="100"></textarea></td>
                            </tr>
                            <tr>
                                <td class="trow1" colspan="2"><center><input type="submit" value="Absenden" /></center></td>
                            </tr>
                        </table>
                        </form>
                    </td>
                </tr>
            </table>
        {$footer}
        </body>
        </html>
        '),
        'sid'           => '-2', // -1 Global, -2 Design
        'version'       => '*',
        'dateline'      => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // Random Fakts Fakt Navigation

    $insert_array = array(
        'title'         => 'randomfacts_navigation',
        'template'      => $db->escape_string('
        <div class="randomfacts_navigation">
            <div class="thead">Navigation</div>
            <div class="trow2"><a href="randomfacts.php">Random Facts</a></div>
            <div class="trow1"><a href="randomfacts.php?action=all">Alle Facts</a></div>
            <div class="trow2"><a href="randomfacts.php?action=add">Facts hinzufügen</a></div>
        </div>
        '),
        'sid'           => '-2', // -1 Global, -2 Design
        'version'       => '*',
        'dateline'      => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // Random Fakts Fakt auf dem Index

    $insert_array = array(
        'title'         => 'index_randomfacts',
        'template'      => $db->escape_string('
        <tr><td class="tcat"><span class="smalltext"><strong>Random Facts</strong></span></td></tr>
        <tr>
        <td class="trow1">
            <div class="index_randomfacts">
                    <div class="index_randomfacts-title">{$randomfact[\'titel\']}</div>
                    <div class="index_randomfacts-content">
                        {$randomfact[\'text\']}	
                    </div>
             </div>
        </td>
        </tr>
        '),
        'sid'           => '-2', // -1 Global, -2 Design
        'version'       => '*',
        'dateline'      => TIME_NOW
    );

    $db->insert_query("templates", $insert_array); // Aktualisierungsbefehl

    // CSS Einfügen

    global $db, $cache;
        
        require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
        require MYBB_ROOT."/inc/adminfunctions_templates.php";
        
        // VARIABLEN EINFÜGEN
        find_replace_templatesets('index_boardstats', '#'.preg_quote('{$forumstats}').'#', '{$boardstats}{$randomfacts}');
        
        // STYLESHEET HINZUFÜGEN
        $css = array(
            'name'          => 'randomfacts.css',
            'tid'           => 1,
            'attachedto'    => '',
            "stylesheet"    =>	'
.randomfacts {
    display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	gap: 20px;
}

.randomfact {
      width: 43%;
      padding: 20px;
      text-align: justify;
      background: #efefef;
}
            
.randomfacts-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 20px;
}

.randomfacts-do {
	margin-top: 20px;
	display: flex;
	gap: 10px;
}
            
.randomfacts-action {
      margin-top: 20px;
}

.index_randomfacts {

}
		
.index_randomfacts-title {
	font-size: 20px;
	text-transform: uppercase;
	margin-bottom: 10px;
}

.index_randomfacts-content {
	font-size: 12px;
}
		
		',
            'cachefile' => $db->escape_string(str_replace('/', '', 'randomfacts.css')),
            'lastmodified' => time(),
        );

        require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";
        
            $sid = $db->insert_query("themestylesheets", $css);
            $db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=" . $sid), "sid = '" . $sid . "'", 1);
        
            $tids = $db->simple_select("themes", "tid");
            while ($theme = $db->fetch_array($tids)) {
                update_theme_stylesheet_list($theme['tid']);
            }

}

function randomfacts_is_installed()
{
	global $db;
        if($db->table_exists('randomfacts'))
        {
            return true;
        }
        return false;
}


 
// Diese Funktion wird aufgerufen, wenn das Plugin deinstalliert wird (optional).
function randomfacts_uninstall()
{
    global $db;
      // Einstellungen Löschen

      $db->delete_query('settings', "name LIKE 'randomfacts_%'");  
      $db->delete_query('settinggroups', "name = 'randomfacts'");

  
      // Eigene Tabelle erstellt löschen
  
      if($db->table_exists('randomfacts'))
      {
          $db->drop_table("randomfacts");
      }

      rebuild_settings();


      // CSS löschen
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    $db->delete_query("themestylesheets", "name = 'randomfacts.css'");
    $query = $db->simple_select("themes", "tid");
    while($theme = $db->fetch_array($query)) {
        update_theme_stylesheet_list($theme['tid']);
    }

    // Templates löschen
    $db->delete_query("templates", "title LIKE 'randomfacts%'");
}

// Funktion zur Überprüfung des Installationsstatus; liefert true zurürck, wenn Plugin installiert, sonst false (optional).
function pluginname_is_installed()
{
       // Installationsüberprüfung Datenbank

       global $db, $cache, $mybb;
       if($db->table_exists("randomfacts"))
       {
           return true;
       }  
       return false;
   
   
       // Installationsüberprüfung Settings
   
       global $db, $cache, $mybb;
       if(isset($mybb->settings['randomfacts']))
       {
           return true;
       }
       return false;
   
} 
 
 
// Diese Funktion wird aufgerufen, wenn das Plugin deaktiviert wird.
function randomfacts_deactivate()
{
    global $db, $cache;

    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    require MYBB_ROOT."/inc/adminfunctions_templates.php";

    // VARIABLEN ENTFERNEN
    find_replace_templatesets("index_boardstats", "#".preg_quote('{$randomfacts}')."#i", '', 0);

}

$plugins->add_hook("global_start", "randomfacts_global");
 // Random Fact auf dem Index ausgeben
 function randomfacts_global() {

    global $db, $randomfacts, $templates;

    $randomfacts_query = $db->query("SELECT * FROM ".TABLE_PREFIX."randomfacts ORDER BY rand() LIMIT 1");

    $randomfact = $db->fetch_array($randomfacts_query);
  
    eval("\$randomfacts = \"".$templates->get("index_randomfacts")."\";");
 }

