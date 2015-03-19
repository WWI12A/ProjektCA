
<?php
// Gewünschte Verzeichnisstruktur angeben und der Nickname als Ordnername
$structure = '../../Testordner/'+$nicknames+'/';

// Zur Erstellung der verschachtelten Struktur muss der $recursive-Parameter 
// von mkdir() angegeben werden

if (!mkdir($structure, 0, true)) {
    die('Erstellung der Verzeichnisse schlug fehl...');
}

?>
