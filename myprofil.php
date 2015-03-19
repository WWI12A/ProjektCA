<?php
	include("header.inc");
    error_reporting(E_ALL);
    include("mysql.php");
    include("functions.php");

    session_start();
    include("autologout.php");

    if(!isset($_SESSION['UserID'])) {
         echo "Sie sind nicht eingeloggt.<br>\n".
              "Bitte <a href=\"index.php\">loggen</a> Sie sich zuerst ein.\n";
    }
    else{
       
        // Daten ändern
        if(isset($_POST['submit']) AND $_POST['submit']=='E-mail ändern'){
            // Fehlerarray anlegen
            $errors = array();
            // Prüfen, ob alle Formularfelder vorhanden sind
            if(!isset($_POST['Email']))
                // Ein Element im Fehlerarray hinzufügen
                $errors = "Bitte benutzen Sie das Formular aus Ihrem Profil";
            else{
                $emails = array();
                $sql = "SELECT
                               Email
                        FROM
                               User
                       ";
                $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                while($row = mysql_fetch_assoc($result))
                    $emails[] = $row['Email'];
                // momentane Email-Adresse ausfiltern
                $sql = "SELECT
                               Email
                        FROM
                               User
                        WHERE
                               ID = '".mysql_real_escape_string($_SESSION['UserID'])."'
                       ";
                $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                $row = mysql_fetch_assoc($result);

                if(trim($_POST['Email'])=='')
                    $errors[]= "Bitte geben Sie Ihre Email-Adresse ein.";
                elseif(!preg_match('§^[\w\.-]+@[\w\.-]+\.[\w]{2,4}$§', trim($_POST['Email'])))
                    $errors[]= "Ihre Email Adresse hat eine falsche Syntax.";
                elseif(in_array(trim($_POST['Email']), $emails) AND trim($_POST['Email'])!= $row['Email'])
                    $errors[]= "Diese Email-Adresse ist bereits vergeben.";
                }
                if(count($errors)){
                    echo "Ihre Daten konnten nicht bearbeitet werden.<br>\n".
                         "<br>\n";
                    foreach($errors as $error)
                        echo $error."<br>\n";
                    echo "<br>\n".
                         "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Profil</a>\n";
                }
                else{
                $sql = "UPDATE
                                User
                        SET
                                Email =  '".mysql_real_escape_string(trim($_POST['Email']))."'
                        WHERE
                                ID = '".mysql_real_escape_string($_SESSION['UserID'])."'
                       ";
                mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                echo "Ihre Daten wurden erfolgreich gespeichert.<br>\n".
                     "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Profil</a>\n";
            }
        }
        // Passwort ändern
        elseif(isset($_POST['submit']) AND $_POST['submit'] == 'Passwort ändern') {
            $errors=array();
            // Altes Passwort zum Vergleich aus der Datenbank holen
            $sql = "SELECT
                        Passwort
                    FROM
                        User
                    WHERE
                        ID = '".$_SESSION['UserID']."'
                   ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            $row = mysql_fetch_assoc($result);
            if(!isset($_POST['Passwort'],
                      $_POST['Passwortwiederholung'],
                      $_POST['Altes_Passwort']))
                $errors[]= "Bitte benutzen Sie das Formular aus Ihrem Profil.";
            else {
                if(trim($_POST['Passwort'])=="")
                    $errors[]= "Bitte geben Sie Ihr Passwort ein.";
                elseif(strlen(trim($_POST['Passwort'])) < 6)
                    $errors[]= "Ihr Passwort muss mindestens 6 Zeichen lang sein.";
                if(trim($_POST['Passwortwiederholung'])=="")
                    $errors[]= "Bitte wiederholen Sie Ihr Passwort.";
                elseif(trim($_POST['Passwort']) != trim($_POST['Passwortwiederholung']))
                    $errors[]= "Ihre Passwortwiederholung war nicht korrekt.";
                // Kontrolle des alten Passworts
                if(trim($row['Passwort']) != md5(trim($_POST['Altes_Passwort'])))
                    $errors[]= "Ihr altes Passwort ist nicht korrekt.";
            }
            if(count($errors)){
                echo "Ihr Passwort konnte nicht gespeichert werden.<br>\n".
                     "<br>\n";
                 foreach($errors as $error)
                     echo $error."<br>\n";
                 echo "<br>\n".
                      "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Profil</a>\n";
            }
            else{
                $sql = "UPDATE
                                User
                        SET
                                Passwort ='".md5(trim($_POST['Passwort']))."'
                        WHERE
                                ID = '".$_SESSION['UserID']."'
                       ";
                mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                echo "Ihr Passwort wurde erfolgreich gespeichert.<br>\n".
                     "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Profil</a>\n";
            }
        }
        else {
            $sql = "SELECT
                         Nickname,
                         Email
                     FROM
                         User
                     WHERE
                         ID = '".mysql_real_escape_string($_SESSION['UserID'])."'
                    ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            $row = mysql_fetch_assoc($result);
			$customer = htmlentities($row['Nickname'], ENT_QUOTES); //Diese Variable enthält den Nickname des Kunden
			

            echo "<h2>Hallo ".$customer." </h2>\n";
            echo "<a href='logout.php'>Ausloggen</a>";
            echo "<br>\n";
			echo "<br>\n";
			echo "<br>\n";
			
			
			echo"<h3>&Uumlbersicht &uumlber ihre CSRs</h3>";
			echo "<br>\n";
			echo "Neue CSR hinzuf&uumlgen:";
			echo "<form name=\"uploadformular\" enctype=\"multipart/form-data\" action=\"csr_upload.php\" method=\"post\" >";
			echo "<input type=\"file\" name=\"uploaddatei\" size=\"60\" maxlength=\"255\" >";
			echo "<input type=\"Submit\" name=\"submit\" value=\"Datei hochladen\">";
			echo "</form><br>";
			//---------------Ausgabe der CSRs des Kunden ------------------
			foreach(glob("C:/Users/Administrator/Documents/Projekt/Customers/" . $customer . "/CSRs/*.csr") as $pathToCsr) {
				$pathToCsr = basename($pathToCsr); //nur letzten teil des Dateipfades nehmen, also Dateinamen
				echo $pathToCsr . "<br>";
				}
				
			echo "<br>\n";
			echo"<h3>Ihre Zertifkate</h3>";
			//echo"<a href=\"antrag.php\">Zertifikat beantragen</a>";
			echo"<a href=\"csr_auswahl.php\">Neues Zertifikat beantragen</a>";
			echo "<br>\n";
			////////////////////////////// ANZEIGE ZERTIFIKATE //////////////////////////////////////////////////////////
		 echo "<table>";
    echo " <tr>\n";
    echo "  <td>\n";
    echo "CSR Name\n";
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
    echo " Letze Änderung \n";
    echo "  </td>\n";
	echo "  <td>\n";
			$abfrage = "SELECT * FROM signing_requests WHERE Nickname = '".$customer."' ORDER BY Status";
$ergebnis = mysql_query($abfrage);
while($row = mysql_fetch_object($ergebnis))
   {
   
   
    if($row->Status == "0")
            $status = "orange";
        elseif ($row->Status == "1")
            $status = "green";
			else
			$status = "red";
   
   echo " <tr style=\"color:".$status."\">\n";
        echo "  <td>\n";
        echo basename($row->CSR_Path);
        echo "  </td>\n";
        echo "  <td>\n";
        echo "$row->Nickname";
        echo "  </td>\n";
        echo "  <td>\n";
        if($row->Status == "0")
           echo "Antrag wird bearbeitet" ;
        elseif ($row->Status == "1")
            echo "Zertifikat liegt vor";
			else
			echo "Zertifikatantrag abgelehnt";
        echo "  </td>\n";
        echo "  <td>\n";
        echo "$row->Validity";
        echo "  </td>\n";
		echo "  <td>\n";
        echo "$row->RequestDate";
        echo "  </td>\n";
		/////////// Download Zert //////////////
		echo "  <td>\n";
		if ($row->Status == "1") {
			echo " <form action='download_file.php' method='post'>";
			echo "<button type='submit' name='download' value= ".$row->RequestID." >Download </button>";
			echo"</form>";
		}
        echo "  </td>\n";
		
		
		
		echo "  </tr>\n";

		//$pfad = $row->CSR_Path
		
   }
   		echo " </table>";
		///////////////////////////////////////// ENDE ZERITFIKAT ANZEIGEN //////////////////////////////////////////////

			echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<h3>Daten ändern</h3>";
			echo "</div>";
			echo "<div class='span-6'>";
            echo "<span style=\"font-weight:bold;\" ".
                 " title=\"Ihre.Adresse@Ihr-Anbieter.de\">\n".
                 "Email-Adresse:\n".
                 "</span>\n";
				             echo "<form ".
                 " name=\"Daten\" ".
                 " action=\"".$_SERVER['PHP_SELF']."\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
            echo "<input type=\"text\" name=\"Email\" maxlength=\"70\" value=\"".htmlentities($row['Email'], ENT_QUOTES)."\">\n";
            echo "<input type=\"submit\" name=\"submit\" value=\"E-mail ändern\">\n";
            echo "</form>\n";
			echo "<br>\n";
            echo "<br>\n";
			echo "</div>";
			echo "<div class='span-6'>";
            echo "<form ".
                 " name=\"Passwort\" ".
                 " action=\"".$_SERVER['PHP_SELF']."\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
            echo "<span style=\"font-weight:bold;\" ".
                 " title=\"min.6\">\n".
                 "Altes Passwort :\n".
                 "</span>\n";
            echo "<input type=\"password\" name=\"Altes_Passwort\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\" ".
                 " title=\"min.6\">\n".
                 "Neues Passwort :\n".
                 "</span>\n";
            echo "<input type=\"password\" name=\"Passwort\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\" ".
                 " title=\"min.6\">\n".
                 "Neues Passwort wiederholen:\n".
                 "</span>\n";
            echo "<input type=\"password\" name=\"Passwortwiederholung\">\n";
			echo "<br>\n";
            echo "<input type=\"submit\" name=\"submit\" value=\"Passwort ändern\">\n";
            echo "</form>\n";
			echo "</div>";
			echo "<br>\n";
        }
    }
	include("footer.inc");
?>