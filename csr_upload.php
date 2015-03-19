<?php
//--------------------Kundennickname holen
include("mysql.php");
include("functions.php");
session_start();
include("autologout.php");

$sql = "SELECT
                 Nickname
             FROM
                 User
             WHERE
                 ID = '".mysql_real_escape_string($_SESSION['UserID'])."'
		";
    $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
	$row = mysql_fetch_assoc($result);
		$customer = htmlentities($row['Nickname'], ENT_QUOTES);
//-------------------Nickname geholt in variable "customer"
		
		//Zeitstempel für Dateiname
$timestamp = date('Y-m-d');

//Variable um Upload zu checken
$uploadOk =1;

//Neuer Dateiname (Hier fehlt noch der Username als Variable)
$file_name = "C:/Users/Administrator/Documents/Projekt/Customers/" . $customer . "/CSRs/" . $timestamp . '-' . $_FILES['uploaddatei']['name'];
$file_name = strtr($file_name, " ", "_");
// Prüfung ob Datei bereits existiert
if (file_exists($file_name)) {
    echo "<a href=\"myprofil.php\">Hier gehts zur&uumlck</a> <br> Sorry, Datei existiert bereits. Bitte umbenennen und nochmal versuchen!";
    $uploadOk = 0;
} 
// Prüfen ob $uploadOk 0 aufgrund eines Fehlers ist
if ($uploadOk == 0) {
    echo "<br>Datei wurde nicht hochgeladen.";
	
// Alles ok, Datei uploaden
} else {
		if ( $_FILES['uploaddatei']['name']  <> "" )
		{
		// Datei wurde durch HTML-Formular hochgeladen
		// und kann nun weiterverarbeitet werden 
		move_uploaded_file (
         $_FILES['uploaddatei']['tmp_name'] , $file_name);
 
		//Es hat geklappt!
		//echo "<p>Hochladen war erfolgreich";
		header("Location:myprofil.php");
		
		// Zurück zur übersicht wechseln
		
		exit;
		}
		}
?>