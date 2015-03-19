<?php
    error_reporting(E_ALL);
    include("mysql.php");

    session_start();

    if(isset($_POST['submit']) AND $_POST['submit']=='Registrieren'){
        // Fehlerarray anlegen
        $errors = array();
        // Prüfen, ob alle Formularfelder vorhanden sind
        if(!isset($_POST['Nickname'],
                  $_POST['Passwort'],
                  $_POST['Passwortwiederholung'],
                  $_POST['Email']))
            // Ein Element im Fehlerarray hinzufügen
            $errors = "Bitte benutzen Sie das Formular aus dem Registrierungsbereich";
        else{
            // Prüfung der einzelnen obligatorischen Felder
            // Alle Nicknames und Emailadressen zum Vergleich aus der Datenbank holen
            $nicknames = array();
            $emails = array();
            $sql = "SELECT
                             Nickname,
                             Email
                     FROM
                             User
                    ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            while($row = mysql_fetch_assoc($result)){
                     $nicknames[] = $row['Nickname'];
                     $emails[] = $row['Email'];
            }
            // Prüft, ob ein Nickname eingegeben wurde
            if(trim($_POST['Nickname'])=='')
                $errors[]= "Bitte geben Sie einen Nickname ein.";
            // Prüft, ob der Nickname mindestens 3 Zeichen enthält
            elseif(strlen(trim($_POST['Nickname'])) < 3)
                $errors[]= "Ihr Name muss mindestens 3 Zeichen lang sein.";
            // Prüft, ob der Nickname nur gültige Zeichen enthält
            elseif(!preg_match('/^\w+$/', trim($_POST['Nickname'])))
                $errors[]= "Benutzen Sie bitte nur alphanumerische Zeichen (Zahlen, Buchstaben und den Unterstrich).";
            // Prüft, ob der Nickname bereits vergeben ist
            elseif(in_array(trim($_POST['Nickname']), $nicknames))
                $errors[]= "Dieser Nickname ist bereits vergeben.";
            // Prüft, ob eine Email-Adresse eingegeben wurde
            if(trim($_POST['Email'])=='')
                $errors[]= "Bitte geben Sie Ihre Email-Adresse ein.";
            // Prüft, ob die Email-Adresse gültig ist
            elseif(!preg_match('§^[\w\.-]+@[\w\.-]+\.[\w]{2,4}$§', trim($_POST['Email'])))
                $errors[]= "Ihre Email Adresse hat eine falsche Syntax.";
            // Prüft, ob die Email-Adresse bereits vergeben ist
            elseif(in_array(trim($_POST['Email']), $emails))
                $errors[]= "Diese Email-Adresse ist bereits vergeben.";
            // Prüft, ob ein Passwort eingegeben wurde
            if(trim($_POST['Passwort'])=='')
                $errors[]= "Bitte geben Sie Ihr Passwort ein.";
            // Prüft, ob das Passwort mindestens 6 Zeichen enthält
            elseif (strlen(trim($_POST['Passwort'])) < 6)
                $errors[]= "Ihr Passwort muss mindestens 6 Zeichen lang sein.";
            // Prüft, ob eine Passwortwiederholung eingegeben wurde
            if(trim($_POST['Passwortwiederholung'])=='')
                $errors[]= "Bitte wiederholen Sie Ihr Passwort.";
            // Prüft, ob das Passwort und die Passwortwiederholung übereinstimmen
            elseif (trim($_POST['Passwort']) != trim($_POST['Passwortwiederholung']))
                $errors[]= "Ihre Passwortwiederholung war nicht korrekt.";
        }
        // Prüft, ob Fehler aufgetreten sind
        if(count($errors)){
             echo "Ihr Account konnte nicht erstellt werden.<br>\n".
                  "<br>\n";
             foreach($errors as $error)
                 echo $error."<br>\n";
             echo "<br>\n".
                  "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Registrierungsformular</a>\n";
        }
        else{
	/*---------------------------------------Heiko Anpassung für Anlage der Ordnerstrukutr beim Registrieren--------------------------------*/		
	
			$ordnername = trim($_POST['Nickname']);
			
			// Gewünschte Verzeichnisstruktur angeben und der Nickname als Ordnername
			
			$structure = "../../Customers/$ordnername/";
			$unterordner  = "../../Customers/$ordnername/CSRs";
			$certordner = "../../Customers/$ordnername/Zertifikate";
			
			// Zur Erstellung der verschachtelten Struktur muss der $recursive-Parameter 
			// von mkdir() angegeben werden

			if (!mkdir($structure, 0, true)) {
				die('Erstellung der Verzeichnisse schlug fehl. Bitte an Support wenden');
			}
			//Unterordner anlegen für CSR
			
			if (!mkdir($unterordner, 0, true)) {
				die('Erstellung des CSR-Ordners schlug fehl. Bitte an Support wenden');
			}
			
			//Unterordner anlegen für Zertifikate
			
			if (!mkdir($certordner, 0, true)) {
				die('Erstellung des Zertifikatsordner schlug fehl. Bitte an Support wenden');
			}
	/*--------------------------------------------------------------------------------------------------------------------------------*/		
            // Daten in die Datenbanktabelle einfügen
            $sql = "INSERT INTO
                           User
                            (Nickname,
                             Email,
                             Passwort
                            )
                    VALUES
                            ('".mysql_real_escape_string(trim($_POST['Nickname']))."',
                             '".mysql_real_escape_string(trim($_POST['Email']))."',
                             '".md5(trim($_POST['Passwort']))."'
                            )
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            echo "Vielen Dank!\n<br>".
                 "Ihr Accout wurde erfolgreich erstellt.\n<br>".
                 "Sie können sich nun mit Ihren Daten einloggen.\n<br>".
                 "<a href=\"index.php\">Zum Login</a>\n";
        }
    }
    else {
        echo "<form ".
             " name=\"Registrierung\" ".
             " action=\"".$_SERVER['PHP_SELF']."\" ".
             " method=\"post\" ".
             " accept-charset=\"ISO-8859-1\">\n";
        echo "<h5>Obligatorische Angaben</h5>\n";
        echo "<span style=\"font-weight:bold;\" ".
             " title=\"min.3\nmax.32\nNur Zahlen, Buchstaben und Unterstrich\">\n".
             "Nickname :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"Nickname\" maxlength=\"32\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\" ".
             " title=\"min.6\">\n".
             "Passwort :\n".
             "</span>\n";
        echo "<input type=\"password\" name=\"Passwort\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\" ".
             " title=\"min.6\">\n".
             "Passwort wiederholen:\n".
             "</span>\n";
        echo "<input type=\"password\" name=\"Passwortwiederholung\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\" ".
             " title=\"Ihre.Adresse@Ihr-Anbieter.de\">\n".
             "Email-Adresse:\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"Email\" maxlength=\"70\">\n";
        echo "<br>\n";
        /*echo "<span>\n".
             "Email-Adresse anzeigen:\n".
             "</span>\n";
        echo "<input type=\"radio\" name=\"Show_Email\" value=\"1\"> ja\n";
        echo "<input type=\"radio\" name=\"Show_Email\" value=\"0\" checked> nein\n <br>";
        echo "<h5>Freiwillige Angaben</h5>\n";
        echo "<span style=\"font-weight:bold;\">\n".
             "Homepage :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"Homepage\" maxlength=\"70\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\">\n".
             "Wohnort :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"Wohnort\" maxlength=\"70\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\">\n".
             "ICQ :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"ICQ\" maxlength=\"20\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\">\n".
             "AIM :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"AIM\" maxlength=\"70\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\">\n".
             "YIM :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"YIM\" maxlength=\"70\">\n";
        echo "<br>\n";
        echo "<span style=\"font-weight:bold;\">\n".
             "MSN :\n".
             "</span>\n";
        echo "<input type=\"text\" name=\"MSN\" maxlength=\"70\">\n";
        echo "<br>\n";*/
        echo "<input type=\"submit\" name=\"submit\" value=\"Registrieren\">\n";
        echo "<input type=\"reset\" value=\"Zurücksetzen\">\n";
        echo "</form>\n";
    }
?>