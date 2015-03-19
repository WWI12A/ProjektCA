<?php

error_reporting(E_ALL);
    include("../mysql.php");
    include("../functions.php");

    session_start();
    include("../autologout.php");
	


    // Prüfen, ob der User den Adminbereich betreten darf
    if(!isset($_SESSION['Rechte']) OR !in_array('Adminbereich', $_SESSION['Rechte']))
        die("Sie haben keine Berechtigung, diese Seite zu betreten!\n");
	
	
//SQL-Abfrage, um benötigte Parameter zu holen
$abfrage = "SELECT * FROM signing_requests WHERE RequestID ='".$_POST['csr']."'";

$ergebnis = mysql_query($abfrage);
while($row = mysql_fetch_object($ergebnis)){

//Übergabe von Pfad zur CSR 7Nickname / RequestID aus der Datenbank an Variablen + anderes Angaben (SANs...)
$pfad = $row->CSR_Path;
$nickname = $row->Nickname;	
$RequestID = $row->RequestID;
$CertType = $row->CertType;
$SANs = $row->SAN_Value;
	
}


	
//Parsen des CSR-Strings, um benötigte Subjects zu erhalten und Übergabe an Variablen
$out=array();
exec('openssl req -in '.$pfad.' -text -noout',$out);


$Land = preg_split("/,/", explode('C=', $out[3])[1]);

$Bundesland = preg_split("/, /", explode('ST=', $out[3])[1]);

$Stadt = preg_split("/, /", explode('L=', $out[3])[1]);

$Organisation = preg_split("/, /", explode('O=', $out[3])[1]);

//Prüfen ob OU leer istLa
if(strpos ( $out[3] , 'OU=') != FALSE){
$Einheit = preg_split("/, /", explode('OU=', $out[3])[1]);
} else {
	$Einheit[0] = "' '";
}
$CommonName = preg_split("#/#", explode('CN=', $out[3])[1]);

$Email = preg_split("/ /", explode('emailAddress=', $out[3])[1]);
	

//Form um die Werte des CSR anzuzeigen und gegebenenfalls zu ändern

echo "<form action='zertifikat_erstellen.php' method='post' >" ; //Hier fehlt noch der passende Aufruf von zertifikat_erstellen.php    
echo" <table>";
echo "<tr style=\"color:red\">";
echo "<td>Zertifikatart:</td>";
echo "<td>$CertType<td/>";
echo "</tr>";
echo "<tr>";
echo "<td>Land:</td>";
echo "<td><input name='Land' value='".$Land[0]."' type='text' size='30' maxlength='50'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Bundesland:</td>";
echo "<td><input name='Bundesland' value='".$Bundesland[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Stadt:</td>";
echo "<td><input name='Stadt' value='".$Stadt[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Organisation:</td>";
echo "<td><input name='Organisation' value='".$Organisation[0]."'  size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Organisationseinheit:</td>";
echo "<td><input name='Einheit' value='".$Einheit[0]."'  type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>CommonName:</td>";
echo "<td><input name='CommonName' value='".$CommonName[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Email:</td>";
echo "<td><input name='email' value='".$Email[0]."' type='text' size='30' maxlength='30'></td>";
echo "</tr>";
if ($CertType == "SAN") {
	
	echo "<tr>";
	echo "<td><input type='hidden' name='CertType' value='SAN'>SANs:</td>";
	echo "<td><input name='SANs' value='".$SANs."' type='text' size='30' maxlength='120'></td>";
	echo "</tr>";
	
}
echo "<tr>";
echo "<td>Genehmigen:</td>";
echo "<td><button type='submit' name='genehmigen' value = ".$RequestID." >Genehmigen </button><td/>";
echo "</tr>";
echo "</table>";
echo"</form>";	
	

	
echo '<strong>Pfad zur CSR:</strong>';
echo "<br/>";
echo $pfad; 
echo "<br/>";
echo "<br/>";

echo "<strong>Ausgabe der kompletten CSR:</strong>";
echo "<br/>";
passthru('openssl req -in '.$pfad.' -text -noout');

	

 echo"<form action='typ_aendern.php' method='post'>";
 echo"<p><strong> Zertifikatart &aumlndern </strong></p>";
  echo"<input type='radio' name='CertType' value='Normal' >Normales Zertifikat<br>";
    echo"<input type='radio' name='CertType' value='Wildcard'>Wildcard-Zertifikat<br>";
	echo"<input type='radio' name='CertType' value='SAN'>SAN-Zertifikat<p>";
	echo"<input type='submit' value='Typ aendern'>";
	echo"<input type='hidden' name='RequestID' value= ".$RequestID." >";
echo"</form>";


?>