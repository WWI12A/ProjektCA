<?php

 error_reporting(E_ALL);
    include("../mysql.php");
    include("../functions.php");
	include("../header.inc");
    session_start();
    include("../autologout.php");
	


    // Prüfen, ob der User den Adminbereich betreten darf
    if(!isset($_SESSION['Rechte']) OR !in_array('Adminbereich', $_SESSION['Rechte']))
        die("Sie haben keine Berechtigung, diese Seite zu betreten!\n");
	
// Veraltete!!!! Wir brauchen unser CA Zertifikat und dessen privaten Schlüssel 
//$cacert = "file://C:/Users/Administrator/Documents/intermediate/intermediate.crt";
// $privkey = "file://C:/Users/Administrator/Documents/intermediate/private/intermediate.key";


$abfrage = "SELECT * FROM signing_requests WHERE RequestID ='".$_POST['genehmigen']."'";

$ergebnis = mysql_query($abfrage);
while($row = mysql_fetch_object($ergebnis)){

//Übergabe von Pfad zur CSR und der Gültigkeitsdauer aus der Datenbank an Variablen
$pfad = $row->CSR_Path;
$validity = $row->Validity;
$nickname = $row->Nickname;	
$RequestID = $row->RequestID;
	
}

//damit leere Variablen keine Fehler ausgeben:
$CertType ="";
$SANs = "";

//Variablen übergeben von Formular

$Land = $_POST['Land'];
$Bundesland = $_POST['Bundesland'];
$Stadt = $_POST['Stadt'];
$Organisation = $_POST['Organisation'];
$Einheit = $_POST['Einheit'];
$CommonName = $_POST['CommonName'];
$Email = $_POST['email'];

if(isset($_POST['CertType'])){
$CertType = $_POST['CertType'];
}
if(isset($_POST['SANs'])){
$SANs = $_POST['SANs'];
}

if($Einheit == "''"){
	$Einheit ="";
	
}


if($CertType == 'SAN'){
	$extfiletext = "subjectAltName=".$SANs;
	$extfile = fopen("C:/Users/Administrator/Documents/Projekt/Customers/".$nickname."/Zertifikate/extfile".$RequestID.".cfg", "w+");
	fwrite($extfile, $extfiletext);
	fclose($extfile);	
	
}
//Wenn SAN-Zertifikat, dann zur Generierung des Zertifikats das entsprechende extfile nutzen, wenn nicht dann ohne
if($CertType == 'SAN'){
	
	system('openssl ca -in '.$pfad.' -subj "/C='.$Land.'/ST='.$Bundesland.'/L='.$Stadt.'/O='.$Organisation.'/OU='.$Einheit.'/CN='.$CommonName.'/emailAddress='.$Email.'" -batch -config C:/Users/Administrator/Documents/intermediate/intermediate.cnf -out C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/zert'.$RequestID.'.crt -days '.$validity.' -extfile C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/extfile'.$RequestID.'.cfg');
	
} else{

system('openssl ca -in '.$pfad.' -subj "/C='.$Land.'/ST='.$Bundesland.'/L='.$Stadt.'/O='.$Organisation.'/OU='.$Einheit.'/CN='.$CommonName.'/emailAddress='.$Email.'" -batch -config C:/Users/Administrator/Documents/intermediate/intermediate.cnf -out C:/Users/Administrator/Documents/Projekt/Customers/'.$nickname.'/Zertifikate/zert'.$RequestID.'.crt -days '.$validity.' ');
//echo $Land, $Bundesland, $Stadt, $Organisation, $Einheit, $CommonName, $Email;

}
//Löschen der Einträge Index.txt
fopen("C:/Users/Administrator/Documents/intermediate/index.txt", "w");
/*----------------------------------------------------------------------------------ALTES PHP --------------------------------------------------------------------------
$csrdata = "file://".$pfad."";


//echo ($csrdata);
$userscert = openssl_csr_sign($csrdata, $cacert, $privkey, $validity);

//Zur Kontrolle, ob es geklappt hat! Anzeige für Admins
openssl_x509_export($userscert, $test);
echo $test;


openssl_x509_export_to_file($userscert, "C:/Users/Administrator/Documents/Projekt/Customers/".$nickname."/Zertifikate/zert".$RequestID.".crt");

// Anzeigen der möglichen aufgetretenen Fehler
while (($e = openssl_error_string()) !== false) {
    echo $e . "<br/>";
}
-------------------------------------------------------------------------------ALTES PHP-----------------------------------------------------------------------------------*/


//Den Status im Antrag ändern! Von 0 auf 1
$sql = "UPDATE signing_requests SET Status = '1' WHERE RequestID = ".$RequestID."";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            echo "Status ge&aumlndernt\n<br>";
			echo"<a href='approve.php'>Zur&uumlck zur &Uumlbersicht</a>";
                 
	include("../footer.inc");
?> 