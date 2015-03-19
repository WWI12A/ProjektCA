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
	echo "<a href='../logout.php'>Ausloggen</a>";
	echo "<br />";
	echo "<br />";
	echo "Legende:<br/>";
	echo "<span style=\"color:red\">zu bearbeiten</span>\n <br/>";
	echo "<span style=\"color:green\">erledigt</span>\n <br/>"; 
	echo "<span style=\"color:orange\">abgelehnt</span>\n <br/><br/>";
	echo "<table>";
    echo " <tr>\n";
    echo "  <td>\n";
    echo "RequestID\n";
    echo "  </td>\n";
    echo "  <td>\n";
    echo "Nickname\n";
    echo "  </td>\n";
    echo "  <td>\n";
    echo "Status\n";
    echo "  </td>\n";
    echo "  <td>\n";
    echo " Validity \n";
    echo "  </td>\n";
	echo "  <td>\n";
    echo " Antragszeitpunkt \n";
    echo "  </td>\n";
	echo "  <td>\n";
    echo " Zertifikatart \n";
    echo "  </td>\n";
	echo "  <td>\n";
    echo " CSR anzeigen und genehmigen \n";
    echo "  </td>\n";
    echo "  <td>\n";
/*-------------------ALT und muss raus-----------------------
    echo " Antrag genehmigen \n";
    echo "  </td>\n";
	 echo "  <td>\n";
------------------------------------------------------------	 */
    echo " Antrag ablehnen \n";
    echo "  </td>\n";
    echo " </tr>\n";

$abfrage = "SELECT * FROM signing_requests ORDER BY Status";
$ergebnis = mysql_query($abfrage);

while($row = mysql_fetch_object($ergebnis))
   {
   
   
    if($row->Status == "0")
            $status = "red";
        elseif ($row->Status == "1")
            $status = "green";
			else
			$status = "orange";
   
   echo " <tr style=\"color:".$status."\">\n";
        echo "  <td>\n";
        echo "$row->RequestID";
        echo "  </td>\n";
        echo "  <td>\n";
        echo "$row->Nickname";
        echo "  </td>\n";
        echo "  <td>\n";
        echo "$row->Status" ;
        echo "  </td>\n";
        echo "  <td>\n";
        echo "$row->Validity";
        echo "  </td>\n";
		echo "  <td>\n";
        echo "$row->RequestDate";
        echo "  </td>\n";
		echo "  <td>\n";
        echo "$row->CertType";
        echo "  </td>\n";
		echo "  <td>\n";
		//$pfad = $row->CSR_Path
		echo " <form action='show_csr.php' method='post'>";
		echo "<button type='submit' name='csr' value= ".$row->RequestID." >CSR Anzeigen und genehmigen </button>";
		echo"</form>";
        echo "  </td>\n";
		echo "  <td>\n";
		echo " <form action='deny_csr.php' method='post'>";
		echo "<button type='submit' name='ablehnen' value = ".$row->RequestID.">Ablehnen </button>";
		echo"</form>";
        echo "  </td>\n";
        echo " </tr>\n";	
   }
    echo"</table>";
?>
<?php 
include("../footer.inc");
?>