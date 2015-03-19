<?php
    error_reporting(E_ALL);

    if(isset($_POST['ID']) AND $_POST['ID'] != 0) {
        if(isset($_POST['submit']) AND $_POST['submit'] == 'User löschen') {
            // Rechte löschen
            $sql = "DELETE FROM
                                User_Rechte
                    WHERE
                                UserID = '".$_POST['ID']."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            // User löschen
            $sql = "DELETE FROM
                                User
                    WHERE
                                ID = '".$_POST['ID']."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());

            echo "Der User wurde gelöscht.<br>\n";
        }
        elseif(isset($_POST['submit']) AND $_POST['submit'] == 'User auswählen') {
            echo "Wollen Sie diesen User wirklich löschen?<br>\n";
            $sql = "SELECT
                            SessionID,
                            Nickname,
                            Email,
                            Show_Email,
                            DATE_FORMAT(Registrierungsdatum, '%d.%m.%Y') as Datum,
                            Letzte_Aktion,
                            Letzter_Login
                    FROM
                            User
                    WHERE
                            ID = '".mysql_real_escape_string($_POST['ID'])."'
                   ";
            $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
            $row = mysql_fetch_assoc($result);
            echo "<table>\n";
            echo " <tr>\n";
            echo "  <td>\n";
            echo "Nickname :\n";
            echo "  </td>\n";
            echo "  <td>\n";
            echo htmlentities($row['Nickname'], ENT_QUOTES)."\n";
            echo " (";
            if($row['SessionID'] AND (time()-60*2 < $row['Letzte_Aktion']))
                echo "<span style=\"color:green\">online</span>\n";
            else
                echo "<span style=\"color:red\">offline</span>\n";
            echo ")";
            echo "  </td>\n";
            echo " </tr>\n";
            echo " <tr>\n";
            echo "  <td>\n";
            echo "Email-Adresse :\n";
            echo "  </td>\n";
            echo "  <td>\n";
            if($row['Show_Email']==1)
                echo htmlentities($row['Email'], ENT_QUOTES)."\n";
            echo "  </td>\n";
            echo " </tr>\n";
            echo " <tr>\n";
            echo "  <td>\n";
            echo "Registrierungsdatum :\n";
            echo "  </td>\n";
            echo "  <td>\n";
            echo $row['Datum']."\n";
            echo "  </td>\n";
            echo " </tr>\n";
            echo " <tr>\n";
            echo "  <td>\n";
            echo "Letzter Login :\n";
            echo "  </td>\n";
            echo "  <td>\n";
            echo date('d.m.Y H:i \U\h\r', $row['Letzter_Login'])."\n";
            echo "  </td>\n";
            echo " </tr>\n";
            echo " <tr>\n";
            echo "</table>\n";

            echo "<form ".
                 "action=\"index.php?section=admin&page=user&action=delete\" ".
                 "method=\"post\">\n";
            echo "<input type=\"hidden\" name=\"ID\" value=\"".$_POST['ID']."\">\n";
            echo "<input type=\"submit\" name=\"submit\" value=\"User löschen\">\n";
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
                 " action=\"index.php?page=user&action=delete\" ".
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