<?php

 error_reporting(E_ALL);
    include("../mysql.php");
    include("../functions.php");

    session_start();
    include("../autologout.php");
	


    // Prüfen, ob der User den Adminbereich betreten darf
    if(!isset($_SESSION['Rechte']) OR !in_array('Adminbereich', $_SESSION['Rechte']))
        die("Sie haben keine Berechtigung, diese Seite zu betreten!\n");
	

$abfrage = "SELECT * FROM signing_requests WHERE RequestID ='".$_POST['ablehnen']."'";

$ergebnis = mysql_query($abfrage);
while($row = mysql_fetch_object($ergebnis)){

//Übergabe von Pfad zur CSR und der Gültigkeitsdauer aus der Datenbank an Variablen
$RequestID = $row->RequestID;
	
}


//Den Status im Antrag ändern! Von 0 auf 2 (abgelehnt)
$sql = "UPDATE signing_requests SET Status = '2' WHERE RequestID = ".$RequestID."";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            echo "Antrag abgelehnt, Status ge&aumlndert und Nutzer wird informiert\n<br>";
			echo "Zur&uumlck zur <a href=\"approve.php\">&Uumlbersicht</a> "
                 

?> 