<?php
echo "<pre>";
echo "FILES:<br />";
print_r ($_FILES );
echo "</pre>";

//Zeitstempel für Dateiname
$timestamp = date('Y-m-d');

//Variable um Upload zu checken
$uploadOk =1;

//Neuer Dateiname (Hier fehlt noch der Username als Variable)
$file_name = 'C:/Users/Administrator/Documents/Projekt/Testordner/' . $timestamp . '-' . $_FILES['uploaddatei']['name'];

// Prüfung ob Datei bereits existiert
if (file_exists($file_name)) {
    echo "Sorry, Datei existiert bereits. Bitte umbenennen!";
    $uploadOk = 0;
} 
// Prüfen ob $uploadOk 0 aufgrund eines Fehlers ist
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
	
// Alles ok, Datei uploaden
} else {
if ( $_FILES['uploaddatei']['name']  <> "" )
{
    // Datei wurde durch HTML-Formular hochgeladen
    // und kann nun weiterverarbeitet werden (Hier fehlt noch der Username als Variable)
    move_uploaded_file (
         $_FILES['uploaddatei']['tmp_name'] ,
         'C:/Users/Administrator/Documents/Projekt/Testordner/' . $timestamp . '-' . $_FILES['uploaddatei']['name']);
 
 //Es hat geklappt!
    echo "<p>Hochladen war erfolgreich";
    
	
//Anzeigen der Dateien im Verzeichnis

$alledateien = scandir('C:/Users/Administrator/Documents/Projekt/Testordner'); //Ordner "files" auslesen
 
 echo "<table>";


foreach ($alledateien as $datei) { // Ausgabeschleife
	echo "<tr>";
    echo "<th>$datei</th> ";
  echo "</tr>";
   
   };
echo"</table>";


}
}

/*
// Gibt das gesamte Ergebnis des Shellkommandos "ls" aus und hält als
// Rückgabewert die letzte Zeile dieser Ausgabe in $last_line. Der
// Rückgabewert des Shellkommandos wird in $retval gespeichert.
system('dir', $retval);

echo $retval;



$dir    = "../../Testordner/Heiko1";
$files1 = scandir($dir);
$files2 = scandir($dir, 1);

print_r($files1);
print_r($files2);

*/

?>
 
<form name="uploadformular" enctype="multipart/form-data" action="funktionen_ausfuehren.php" method="post" >
Datei: <input type="file" name="uploaddatei" size="60" maxlength="255" >
<input type="Submit" name="submit" value="Datei hochladen">
</form>
