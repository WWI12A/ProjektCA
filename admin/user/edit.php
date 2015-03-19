<?php
    error_reporting(E_ALL);

    if(isset($_POST['ID']) AND $_POST['ID'] != 0) {
        // Avatar hochladen
        if(isset($_POST['submit']) AND $_POST['submit'] == "Avatar hochladen") {
            $errors = array();
            // Uploadfehler prüfen
            switch ($_FILES['pic']['error']){
                case 1: $errors[] = "Bitte wählen Sie eine Datei aus, die kleiner als 20 KB ist.";
                                    break;
                case 2: $errors[] = "Bitte wählen Sie eine Datei aus, die kleiner als 20 KB ist.";
                                    break;
                case 3: $errors[] = "Die Datei wurde nur teilweise hochgeladen.";
                                    break;
                case 4: $errors[] = "Es wurde keine Datei ausgewählt.";
                                    break;
                default : break;
            }
            // Prüfen, ob eine Grafikdatei vorliegt
            if(!@getimagesize($_FILES['pic']['tmp_name']))
                $errors[] = "Ihre Datei ist keine gültige Grafikdatei.";
            else {
                // Mime-Typ prüfen
                $erlaubte_typen = array('image/pjpeg',
                                        'image/jpeg',
                                        'image/gif',
                                        'image/png'
                                       );
                if(!in_array($_FILES['pic']['type'], $erlaubte_typen))
                    $errors[] = "Der Mime-Typ der Datei ist verboten.";
                    // Endung prüfen
                    $erlaubte_endungen = array('jpeg',
                                               'jpg',
                                               'gif',
                                               'png'
                                              );
                    $endung = strtolower(substr($_FILES['pic']['name'], strrpos($_FILES['pic']['name'], '.')+1));
                    if(!in_array($endung, $erlaubte_endungen))
                        $errors[] = "Die Dateiendung muss .jpeg .jpg .gif oder .png lauten ";

                    // Ausmaße prüfen
                    $size = getimagesize($_FILES['pic']['tmp_name']);
                        if ($size[0] > 150 OR $size[1] > 150)
                            $errors[] = "Die Datei darf maximal 150 Pixel breit und 150 Pixel hoch sein.";
            }
            // Dateigröße prüfen
            if($_FILES['pic']['size'] > 0.2*1024*1024)
                $errors[] = "Bitte wählen Sie eine Datei aus, die kleiner als 20 KB ist.";

            if(count($errors)){
                echo "Das Avatar konnte nicht gespeichert werden.<br>\n".
                     "<br>\n";
                foreach($errors as $error)
                    echo $error."<br>\n";
            }
            else {
                // Bild auf dem Server speichern
                $uploaddir = '../avatare/';
                // neuen Bildname erstellen
                $Name = "IMG_".substr(microtime(),-8).".".$endung;
                if (move_uploaded_file($_FILES['pic']['tmp_name'], $uploaddir.$Name)) {
                    $sql = "UPDATE
                                    User
                            SET
                                    Avatar = '".mysql_real_escape_string(trim($Name))."'
                            WHERE
                                    ID = ".$_POST['ID']."
                           ";
                    mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                    echo "Das Avatar wurde erfolgreich gespeichert.<br>\n";
                }
                else {
                    echo "Es trat ein Fehler auf, bitte versuche es später erneut.<br>\n";
                }
            }
        }
        // Avatar löschen
        elseif(isset($_POST['submit']) AND $_POST['submit'] == 'Avatar löschen') {
            // Bildname des Avatars aus der Datenbank holen
            $sql = "SELECT
                            Avatar
                    FROM
                            User
                    WHERE
                            ID = '".$_POST['ID']."'
                   ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            $row = mysql_fetch_assoc($result);
            // Datei löschen
            unlink('../avatare/'.$row['Avatar']);
            // Bildname des Avatars als leeren String setzen
            $sql = "UPDATE
                            User
                    SET
                            Avatar = ''
                    WHERE
                            ID = '".$_POST['ID']."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            echo "Der Avatar wurde erfolgreich gelöscht.<br>\n";
        }
        elseif(isset($_POST['submit']) AND $_POST['submit']=='Daten ändern'){
            // Fehlerarray anlegen
            $errors = array();
            // Prüfen, ob alle Formularfelder vorhanden sind
            if(!isset($_POST['Email'],
                      $_POST['Show_Email'],
                      $_POST['Homepage'],
                      $_POST['Wohnort'],
                      $_POST['ICQ'],
                      $_POST['AIM'],
                      $_POST['YIM'],
                      $_POST['MSN']))
                // Ein Element im Fehlerarray hinzufügen
                $errors[]= "Bitte benutzen Sie das Formular aus dem User-Menu.";
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

                $sql = "SELECT
                                Email
                        FROM
                                User
                        WHERE
                                ID = '".mysql_real_escape_string($_POST['ID'])."'
                       ";
                $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                $row = mysql_fetch_assoc($result);
                if(trim($_POST['Email'])=='')
                    $errors[]= "Bitte geben Sie dase Email-Adresse ein.";
                elseif(!preg_match('§^[\w\.-]+@[\w\.-]+\.[\w]{2,4}$§', trim($_POST['Email'])))
                    $errors[]= "Ihre Email Adresse hat eine falsche Syntax.";
                elseif(in_array(trim($_POST['Email']), $emails) AND trim($_POST['Email'])!= $row['Email'])
                    $errors[]= "Diese Email-Adresse ist bereits vergeben.";
            }
            if(count($errors)){
                echo "Die Daten konnten nicht bearbeitet werden.<br>\n".
                     "<br>\n";
                foreach($errors as $error)
                    echo $error."<br>\n";
            }
            else{
                $sql = "UPDATE
                                User
                        SET
                                Email =  '".mysql_real_escape_string(trim($_POST['Email']))."',
                                Show_Email = '".mysql_real_escape_string(trim($_POST['Show_Email']))."',
                                Wohnort = '".mysql_real_escape_string(trim($_POST['Wohnort']))."',
                                Homepage = '".mysql_real_escape_string(trim($_POST['Homepage']))."',
                                ICQ = '".mysql_real_escape_string(trim($_POST['ICQ']))."',
                                AIM = '".mysql_real_escape_string(trim($_POST['AIM']))."',
                                YIM = '".mysql_real_escape_string(trim($_POST['YIM']))."',
                                MSN = '".mysql_real_escape_string(trim($_POST['MSN']))."'
                        WHERE
                                ID = '".mysql_real_escape_string($_POST['ID'])."'
                       ";
                mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                echo "Die Daten wurden erfolgreich gespeichert.<br>\n";
            }
        }
        elseif(isset($_POST['submit']) AND $_POST['submit'] == 'Passwort ändern') {
            $errors=array();
            if(!isset($_POST['Passwort'],
                      $_POST['Passwortwiederholung']))
                $errors[]= "Bitte benutzen Sie das Formular aus dem User-Menu.";
            else {
                if(trim($_POST['Passwort'])=="")
                    $errors[]= "Bitte geben Sie das Passwort ein.";
                elseif(strlen(trim($_POST['Passwort'])) < 6)
                    $errors[]= "Ihr Passwort muss mindestens 6 Zeichen lang sein.";
                if(trim($_POST['Passwortwiederholung'])=="")
                    $errors[]= "Bitte wiederholen Sie das Passwort.";
                elseif(trim($_POST['Passwort']) != trim($_POST['Passwortwiederholung']))
                    $errors[]= "Ihre Passwortwiederholung war nicht korrekt.";
            }
            if(count($errors)){
                echo "Das Passwort konnte nicht gespeichert werden.<br>\n".
                     "<br>\n";
                foreach($errors as $error)
                    echo $error."<br>\n";
            }
            else{
                $sql = "UPDATE
                                    User
                        SET
                                    Passwort ='".md5(trim($_POST['Passwort']))."'
                        WHERE
                                    ID = '".$_POST['ID']."'
                       ";
                mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                echo "Das Passwort wurde erfolgreich gespeichert.<br>\n";
            }
        }
        // Rechte ändern
        elseif(isset($_POST['submit']) AND $_POST['submit'] == 'Rechte ändern') {
            // Alle Rechte löschen
            $sql = "DELETE FROM
                            User_Rechte
                    WHERE
                            UserID = '".$_POST['ID']."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            // Ausgewählte Rechte speichern
            if(isset($_POST['Rechte'])){
                foreach($_POST['Rechte'] as $recht){
                    $sql = "INSERT INTO
                                    User_Rechte
                                    (UserID,
                                     Recht
                                    )
                            VALUES
                                    ('".$_POST['ID']."',
                                     '".$recht."'
                                    )
                           ";
                    mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
                }
            }
            echo "Die Rechte wurden gespeichert.<br>\n";
        }
        else {
            $sql = "SELECT
                        Nickname,
                        Email,
                        Show_Email,
                        Wohnort,
                        Homepage,
                        ICQ,
                        AIM,
                        YIM,
                        MSN,
                        Avatar
                 FROM
                        User
                 WHERE
                        ID = '".mysql_real_escape_string($_POST['ID'])."'
                ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            $row = mysql_fetch_assoc($result);
            echo "<form ".
                 " name=\"Daten\" ".
                 " action=\"index.php?page=user&action=edit\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
            echo "<h5>Obligatorische Angaben</h5>\n";
            echo "<span>\n".
                 "Nickname :\n".
                 "</span>\n";
            echo htmlentities($row['Nickname'], ENT_QUOTES)."\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\" ".
                 " title=\"Ihre.Adresse@Ihr-Anbieter.de\">\n".
                 "Email-Adresse:\n".
                 "</span>\n";
            echo "<input type=\"text\" name=\"Email\" maxlength=\"70\" value=\"".htmlentities($row['Email'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<span>\n".
                 "Email-Adresse anzeigen:\n".
                 "</span>\n";
            if($row['Show_Email']==1){
                echo "<input type=\"radio\" name=\"Show_Email\" value=\"1\" checked> ja\n";
                echo "<input type=\"radio\" name=\"Show_Email\" value=\"0\"> nein\n";
            }
            else{
                echo "<input type=\"radio\" name=\"Show_Email\" value=\"1\"> ja\n";
                echo "<input type=\"radio\" name=\"Show_Email\" value=\"0\" checked> nein\n";
            }
            echo "<h5>Freiwillige Angaben</h5>\n";
            echo "<span style=\"font-weight:bold;\">\n".
                 "Homepage :\n";
                 "</span>\n";
            echo "<input type=\"text\" name=\"Homepage\" maxlength=\"70\" value=\"".htmlentities($row['Homepage'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\">\n".
                 "Wohnort :\n".
                 "</span>\n";
            echo "<input type=\"text\" name=\"Wohnort\" maxlength=\"70\" value=\"".htmlentities($row['Wohnort'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\">\n".
                 "ICQ :\n".
                 "</span>\n";
            echo "<input type=\"text\" name=\"ICQ\" maxlength=\"20\" value=\"".htmlentities($row['ICQ'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\">\n".
                 "AIM :\n".
                 "</span>\n";
            echo "<input type=\"text\" name=\"AIM\" maxlength=\"70\" value=\"".htmlentities($row['AIM'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\">\n".
                 "YIM :\n".
                 "</span>\n";
            echo "<input type=\"text\" name=\"YIM\" maxlength=\"70\" value=\"".htmlentities($row['YIM'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<span style=\"font-weight:bold;\">\n".
                 "MSN :\n".
                 "</span>\n";
            echo "<input type=\"text\" name=\"MSN\" maxlength=\"70\" value=\"".htmlentities($row['MSN'], ENT_QUOTES)."\">\n";
            echo "<br>\n";
            echo "<input type=\"submit\" name=\"submit\" value=\"Daten ändern\">\n";
            echo "<input type=\"hidden\" name=\"ID\" value=\"".$_POST['ID']."\">\n";
            echo "</form>\n";

            echo "<form ".
                 " name=\"Passwort\" ".
                 " action=\"index.php?page=user&action=edit\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
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
            echo "<input type=\"hidden\" name=\"ID\" value=\"".$_POST['ID']."\">\n";
            echo "</form>\n";

            // Avatar
            echo "<form ".
                 " name=\"Avatar\" ".
                 " action=\"index.php?page=user&action=edit\" ".
                 " method=\"post\" ".
                 " enctype=\"multipart/form-data\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
            echo "<span style=\"font-weight:bold;\" ".
                 " title=\"max. 20kb\nmax 150x150 Pixel\n .jpg .gif oder .png\">\n".
                 "Avatar :\n".
                 "</span>\n";
            if($row['Avatar']=='')
                echo "Kein Avatar vorhanden.\n";
            else
                echo "<img src=\"../avatare/".htmlentities($row['Avatar'], ENT_QUOTES)."\">\n";
            if($row['Avatar']=='') {
                echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".(0.02*1024*1024)."\">";
                echo "<input name=\"pic\" type=\"file\">\n";
                echo "<input type=\"submit\" name=\"submit\" value=\"Avatar hochladen\">\n";
            }
            else
                echo "<input type=\"submit\" name=\"submit\" value=\"Avatar löschen\">\n";
            echo "<input type=\"hidden\" name=\"ID\" value=\"".$_POST['ID']."\">\n";
            echo "</form>\n";

            // Rechte
            echo "<form ".
                 " name=\"Rechte\" ".
                 " action=\"index.php?page=user&action=edit\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
            $sql = "SELECT
                            Recht
                    FROM
                            User_Rechte
                    WHERE
                            UserID = '".$_POST['ID']."'
                   ";
           $result_rechte = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
           $User_Rechte = array();
           while($row_rechte = mysql_fetch_assoc($result_rechte))
               $User_Rechte[] = $row_rechte['Recht'];

           $rechte = array('Adminbereich',
                           'User administrieren'
                          );
           foreach($rechte as $recht){
               if(in_array($recht, $User_Rechte))
                   echo "<input type=\"checkbox\" name=\"Rechte[]\" value=\"".$recht."\" checked>\n";
               else
                   echo "<input type=\"checkbox\" name=\"Rechte[]\" value=\"".$recht."\">\n";
               echo "<span>\n".
                    $recht."\n".
                    "</span>\n";
               echo "<br>\n";
           }
           echo "<input type=\"submit\" name=\"submit\" value=\"Rechte ändern\">\n";
           echo "<input type=\"hidden\" name=\"ID\" value=\"".$_POST['ID']."\">\n";
           echo "</form>\n";
       }
    }
    else {
        $sql = "SELECT
                        ID,
                        Nickname
                FROM
                        User
                ORDER BY
                        Nickname ASC
               ";
        $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
        if(!mysql_num_rows($result))
            echo "Es befinden sich keine User in der Datenbank\n";
        else {
            echo "<form ".
                 " action=\"index.php?page=user&action=edit\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">";
            echo "<select name=\"ID\">\n";
            echo " <option value=\"0\">Bitte einen User wählen</option>\n";
            while($row = mysql_fetch_assoc($result)) {
                echo " <option value=\"".$row['ID']."\">\n";
                echo $row['Nickname']."\n";
                echo " </option>\n";
            }
            echo "</select>\n";
            echo "<input type=\"submit\" name=\"submit\" value=\"User auswählen\">";
            echo "</form>\n";
        }
    }
?>