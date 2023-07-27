de<?php
define('IN_MYBB', 1);
require_once './global.php';

add_breadcrumb("Random Facts", "randomfacts-extended.php");
eval("\$navigation = \"".$templates->get("randomfacts-extended_navigation")."\";");


//Hauptseite mit Erklärung
if(!$mybb->input['action']) {
    eval("\$page = \"".$templates->get("randomfacts-extended")."\";");
    output_page($page);
}
 

// Liste aller Random Facts

if($mybb->input['action'] == "all") {
    add_breadcrumb("Random Facts Übersicht");

    if(is_member($mybb->settings['randomfacts-extended_group-access']) == 1) {
        error_no_permission();
     } 

    // Datenbankabfrage der Facts
    $sql = "SELECT * FROM ".TABLE_PREFIX."randomfacts-extended"; // Infos, die aus der DB aufgerufen werden
    $query = $db->query($sql); // Funktion führt SQL Befehl aus


    // Alle Fakten auf einer Seite ausgeben
    while($randomfacts = $db->fetch_array($query)) {
        eval("\$randomfacts_bit .= \"".$templates->get("randomfacts-extended_all_bit")."\";");
      }

    eval("\$page = \"".$templates->get("randomfacts-extended_all")."\";");
    output_page($page);
}

// Random Facts Eintragen

if($mybb->input['action'] == "add") {
    add_breadcrumb("Random Facts Eintragen");
    // Nur für ausgewählte Gruppen zugänglich
    if(is_member($mybb->settings['randomfacts-extended_group-add']) == 1) {
        error_no_permission();
     } 

    eval("\$page = \"".$templates->get("randomfacts-extended_add")."\";");
    output_page($page);
}

// Datenbankeintrag von Facts

if($mybb->input['action'] == "do_add") {

    $titel = $mybb->get_input('titel');
    $text = $mybb->get_input('text');

    $new_array = [
        "titel" => $db->escape_string($titel), // escape_string macht Sonderzeichen lesbar
        "text" => $db->escape_string($text)
    ];

    $db->insert_query("randomfacts-extended", $new_array); // Speichert Inhalt in DB, randomfacts = Tabellenname ohne Präfix

    redirect("randomfacts-extended.php?action=all");
}


// Bearbeiten von Facts

if($mybb->input['action'] == "edit") {
    // set permission
    if(is_member($mybb->settings['randomfacts-extended_group-edit']) == 1) {
       error_no_permission();
    }
 
     $id = $mybb->input['id'];
 
    // lese Datenbankeintrag aus, der zu der ID passt
    $sql = "SELECT * FROM ".TABLE_PREFIX."randomfacts-extended WHERE id = '$id'";
    $query = $db->query($sql);
    $randomfacts = $db->fetch_array($query);


 
     add_breadcrumb("randomfacts bearbeiten");
     // lade Template
     eval("\$page = \"".$templates->get("randomfacts-extended_edit")."\";");
     output_page($page);
 } 
 
 if($mybb->input['action'] == "do_edit") {
    $id      = $mybb->input['id'];
    $titel   = $mybb->get_input('titel');
    $text    = $mybb->get_input('text');
 
    $new_array = [
       "titel"  => $db->escape_string($titel),
       "text"   => $db->escape_string($text)
    ];
 
    $db->update_query("randomfacts-extended", $new_array, "id = '$id'");

     redirect("randomfacts-extended.php?action=all");
 }
 
 if($mybb->input['action'] == "delete") {
    $id = $mybb->input['id'];
    $db->delete_query("randomfacts", "id = '$id'");

    redirect("randomfacts-extended.php?action=all");
 } 



 
 


?>
