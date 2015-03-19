<?php
include("mysql.php");
include("functions.php");
session_start();
include("autologout.php");
include("header.inc");

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
	//echo $customer
// ---------------Nickname des Kunden geholt und in Variable customer gespeichert

?> 


<form action="zertifikat_beantragen.php" method="post">
  <p>Bitte w&aumlhlen Sie die CSR:</p>
  <p>
    <select name="csrPath">
	
		<?php
		foreach(glob("C:/Users/Administrator/Documents/Projekt/Customers/" . $customer . "/CSRs/*.csr") as $pathToCsr) {
			echo "<option value=\"" . $pathToCsr . "\">";
			$pathToCsr = basename($pathToCsr);
			echo $pathToCsr . "</option>";
			}
			//--------------------------Alle CSR Dateien des Kunden in Dropdown in aufbereiteter Form angezeigt. Der Pfad der ausgewählten CSR wir mit POST weitergegeben
		?>
	
    </select>
  </p>
  <p><strong> Gew&uumlnschte G&uumlltigkeitsdauer ausw&aumlhlen: </strong></p>
  <input type="radio" name="Validity" value="365" checked> 1 Jahr<br>
    <input type="radio" name="Validity" value="730"> 2 Jahre<br>
    <input type="radio" name="Validity" value="1825"> 5 Jahre

  
    <p><strong> Gew&uumlnschte Zertifikatart: </strong></p>
  <input type="radio" name="CertType" value="Normal" checked> Normales Zertifikat<br>
    <input type="radio" name="CertType" value="Wildcard">Wildcard-Zertifikat<br>
    

	<input type="radio" name="CertType" value="SAN">SAN-Zertifikat<p> 
  <p>Domains hinzuf&uumlgen (nur bei SAN, Komma als Trennzeichen):
	<input name="SANs" type="text" size="50" maxlength="120">

  <div id="dynamisch"></div>

	<input type="submit"> </p>
</form>


<?php
include("footer.inc");
?>