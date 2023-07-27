# MyBB-Randomfacts_on_Index Extended
A MyBB Plugin which adds a Database for facts and shows them randomly on the index or anywhere else.


###################### Random Facts on the Index Extended #############################

See English below

Dieses Plugin erweitert die MyBB Software um eine separate Datenbank für Facts oder andere kurze Informationen, 
die zufällig auf dem Index oder an anderen Stellen im Forum (global) ausgegeben werden. 
Im ACP lässt sich einstellen, welche Gruppen Facts hinzufügen, bearbeiten und löschen dürfen und welche Gruppen die Übersichtsseite mit allen Facts sehen können.

### Installation ###

Lade die PHP-Dateien in die entsprechenden Verzeichnisse
Klicke im ACP/Konfiguration/Plugins auf installieren & aktivieren
Fertig!

### Einstellungen ###

Im ACP > Einstellungen > Random Facts kann eingestellt werden:

- Wer kann die Facts Übersicht unter {forenurl}/randomfacts.php?action=all sehen?
- Wer kann Facts unter {forenurl}/randomfacts.phprandomfacts.php?action=edit&id=X bearbeiten und löschen?
- Wer kann Facts unter {forenurl}/randomfacts.php?action=add hinzufügen?

### Variable auf dem Index ###

Die Variable ist global, wird aber standardmäßig in die index_boardstats hinter die Variable {$boardstats} eingefügt:

{$randomfacts}

### Neue PHP-Dateien ###

randomfacts.php
/inc/plugins/randomfacts.php

### Neue Templates ###

Folgende Templates werden hinzugefügt:

- index_randomfacts

- randomfacts	
- randomfacts_add	
- randomfacts_all	
- randomfacts_all_bit	
- randomfacts_edit	
- randomfacts_navigation

### Deaktivieren und & Deinstallieren ###

Die Templates werden nicht gelöscht, wenn das Plugin deaktiviert wird. Auch die Datenbankeinträge werden nicht gelöscht, wenn das Plugin deaktiviert wird. Sie werden erst gelöscht, wenn es deinstalliert wird!

----- English -----

This Plugin extends the MyBB board software by adding a new database (table) for facts or other short informationen, which radomly appears on the index or any other place of the board. 
In the ACP, you can decide which groups can add, editor or delete facts and which groups have access to the randomfacts main page including a list of all the facts.

