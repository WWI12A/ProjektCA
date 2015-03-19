<?php

include("mysql.php");
include("functions.php");
session_start();
include("autologout.php");

//Übergabe der Variablen 
$csrdata = $_POST['csrPath'];
$validity = $_POST['Validity'];
$CertType = $_POST['CertType'];


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

	
//Antrag in Datenbank schreiben
//Status des Antrages (0=offen; 1=genehmigt; 2=abgelehnt)
 $sql = "INSERT INTO
                           signing_requests
                            (Nickname,
                             Status,
                             CSR_Path,
							 Validity,	
							 CertType
                            )
                    VALUES
                            ('".$customer."',
                             '0', 
                             '".$csrdata."',
							 '".$validity."',
							 '".$CertType."'
                            )
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            echo "Vielen Dank!\n<br>".
                 "Ihre Anfrage ist eingeangen\n<br>".
                 "Sie wird schnellstm&oumlglich bearbeitet.\n<br>".
                 "<a href=\"myprofil.php\">Zur &Uumlbersichtsseite</a>\n";
				 

				 
				 
				 
//Prüfen ob SAN-Zertifikat, wenn ja dann die gewünschten SANs in richtiges Format bringen und zum DB-Eintrag hinzufügen	 
	
if($CertType == "SAN"){
	$SANs = $_POST['SANs'];
	
	$formatiert = "";
	$delimiter = ", ";
	$gesamt = explode($delimiter , $SANs);
	foreach ($gesamt as $teil){
		
		$formatiert = $formatiert."DNS:".$teil.",";
		
	}
	$charlist = ', ';
	$formatiert = rtrim($formatiert, $charlist);
	
	$sql = "SELECT
                 RequestID
             FROM
                 signing_requests
             ORDER BY 
				RequestID desc limit 1
		";
    $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
	$row = mysql_fetch_assoc($result);
		$CurrentId = htmlentities($row['RequestID'], ENT_QUOTES);
	
	 $sql = "UPDATE
                           signing_requests
                    SET    SAN_Value = '".$formatiert."'
                    WHERE RequestID = '".$CurrentId."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
}
	




?> 