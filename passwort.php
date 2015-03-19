<?php
    error_reporting(E_ALL);
    include("mysql.php");

    session_start();

    if(isset($_POST['submit']) AND $_POST['submit']=='Abschicken'){
        // Daten prüfen
        $errors = array();
        if(!isset($_POST['Nickname']))
            $errors[] = "Bitte benutzen Sie unser Passwortformular";
        else{
            if(trim($_POST['Nickname']) == "")
                $errors[] = "Geben Sie Ihren Nickname an.";
            // Nickname suchen
            $sql = "SELECT
                        Email
                    FROM
                        User
                    WHERE
                        Nickname = '".mysql_real_escape_string(trim($_POST['Nickname']))."'
                        ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            $row = mysql_fetch_assoc($result);
            if(!$row)
                $errors[] = "Ihr Nickname konnte nicht gefunden werden.\n";
        }
        if(count($errors)){
            echo "Ihr Passwort konnte nicht versendet werden.<br>\n".
                 "<br>\n";
            foreach($errors as $error)
                echo $error."<br>\n";
            echo "<br>\n";
            echo "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Formular</a>\n";
        }
        else {
            // Neues Passwort erstellen
            $passwort = substr(md5(microtime()),0,8);
            $sql = "UPDATE
                        User
                    SET
                        Passwort = '".md5(trim($passwort))."'
                    WHERE
                        Nickname = '".mysql_real_escape_string(trim($_POST['Nickname']))."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());

            // Email verschicken
            $empfaenger = $row['Email'];
            $titel = "Neues Passwort";
            $mailbody = "Ihr neues Passwort lautet:\n\n".
                        $passwort."\n\n".
                        "Ihr altes Passwort wurde gelöscht.";
            $header = "From: webmaster@mywebsolution.de\n";
            if(@mail($empfaenger, $titel, $mailbody, $header)){
                echo "Ihr neues Passwort wurde erfolgreich an Ihre Email-Adresse versandt.<br>\n".
                     "Zurück zur <a href=\"index.php\">Startseite</a>\n";
            }
            // Im Fehlerfall wird die Mailadresse des Webmasters für den direkten Versandt eingeblendet
            else{
                echo "Beim Senden der Email trat ein Fehler auf.<br>\n".
                     "Bitte wenden Sie sich direkt an den <a href=\"mailto:webmaster@website.de\">Webmaster</a>.\n";
            }
        }
    }
    else{
            echo "<form ".
                 " name=\"Passwort\" ".
                 " action=\"".$_SERVER['PHP_SELF']."\" ".
                 " method=\"post\" ".
                 " accept-charset=\"ISO-8859-1\">\n";
            echo "Nickname :\n";
            echo "<input type=\"text\" name=\"Nickname\" maxlength=\"32\">\n";
            echo "<br>\n";
            echo "<input type=\"submit\" name=\"submit\" value=\"Abschicken\">\n";
            echo "</form>\n";
    }
?>