<?php 

    include("../mysql.php");
    include("../functions.php");

    session_start();
    include("../autologout.php");
	


    // Prüfen, ob der User den Adminbereich betreten darf
    if(!isset($_SESSION['Rechte']) OR !in_array('Adminbereich', $_SESSION['Rechte']))
        die("Sie haben keine Berechtigung, diese Seite zu betreten!\n");

if(isset($_POST['CertType'])){
		$CertType = $_POST['CertType'];
		$RequestID = $_POST['RequestID'];
		$sql = "UPDATE
                    signing_requests
                    SET    CertType = '".$CertType."'
                    WHERE RequestID = '".$RequestID."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
		echo"Typ erfolgreich geaendert!";
	}
	else{
		echo"Falsche Seite";
	}
	echo"<br />";
	echo"<a href='approve.php' >Zurueck zur Uebersicht</a>";
?>