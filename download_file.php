<?php
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
		$nickname = htmlentities($row['Nickname'], ENT_QUOTES);

// ---------------Nickname des Kunden geholt und in Variable nickname gespeichert

 

//SQL-Abfrage, um benötigte Parameter zu holen
$abfrage = "SELECT * FROM signing_requests WHERE RequestID ='".$_POST['download']."'";

$ergebnis = mysql_query($abfrage);
while($row = mysql_fetch_object($ergebnis)){

// Download-Script Funktionalität zum Downloaden vom fertigen Zertifikat als Zip (Zip muss zuvor erstellt werden)


// RequestID um Zertifikat zu identifizieren
$RequestID = $row->RequestID;
}
// Erstellen des Zip Archives
			$zip = new ZipArchive();


			if ($zip->open('C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/zert'.$RequestID.'.zip', ZIPARCHIVE::CREATE) === TRUE) {
				
			$zip->addFile('C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/zert'.$RequestID.'.crt', 'Zertifikat'.$RequestID.'.crt');
			$zip->addFile('C:/Users/Administrator/Documents/intermediate/intermediate.crt', 'Intermediate-Zertifikat.crt');
			$zip->addFile('C:/Users/Administrator/Documents/root/root.crt', 'Root-Zertifikat.crt');
			//$zip->addFile('C:/Users/Administrator/Documents/Projekt/Customers/Heiko2/Zertifikate/zert3.crt', 'zert.crt');
			//echo"C:/Users/Administrator/Documents/Projekt/Customers/".$nickname."/Zertifikate/zert".$RequestID.".crt";
			$zip->close();
			}
			else{
				exit("cannot open file\n");
			}


// http headers for zip downloads
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"zert".$RequestID.".zip\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize('C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/zert'.$RequestID.'.zip'));
ob_end_flush();
@readfile('C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/zert'.$RequestID.'.zip');
?>