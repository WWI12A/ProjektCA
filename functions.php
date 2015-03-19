<?php
    // Prüft die Länge jedes Wortes eines Strings und korrigiert diese evtl.
    function shorten($str, $max=30, $range=5)
    {
            // aufteilen in Zeilen
         $lines = explode("\n", $str);
         foreach($lines as $key_line => $line){
                 // aufteilen in Wörter
                 $words = explode(" ", $line);
                 // prüfen der Länge jeden Wortes
                 foreach($words as $key_word => $word){
                        if (strlen($word) > $max)
                                $words[$key_word] = substr($word,0,$max-3-$range)."...".substr($word,-$range);
                 }
                 // zusammenfügen der neuen Zeile
                 $lines[$key_line] = implode(" ", $words);
         }
         // zusammenfügen des neues Textes
         $str = implode("\n", $lines);
         return $str;
    }


    // loggt einen User aus, ..
    function doLogout()
    {
         // .. indem das Cookie und ..
         if(isset($_COOKIE['Autologin']))
             setcookie("Autologin", "", time()-60*60);
         // .. die Session ID aus der Datenbank gelöscht werden
		 if(isset($_SESSION['UserID'])){
         $sql = "UPDATE
                           User
                 SET
                           SessionID = NULL,
                           Autologin = NULL,
                           IP = NULL

                 WHERE
                           ID = '".$_SESSION['UserID']."'
                ";
			mysql_query($sql) OR die("<pre>\nEs gab einen Fehler, wenden Sie sich an den Support.</pre>\n");
		 }
         else die("<pre>\nSie sind nicht eingeloggt.</pre>\n");
    }

    // liefert die Rechte eines Users ..
    function getRights()
    {
        $rights = array();
        // .. indem die Rechte eines User aus der Datenbank ausgewählt werden..
        if(isset($_SESSION['UserID'])){
            $sql = "SELECT
                            Recht
                    FROM
                            User_Rechte
                    WHERE
                            UserID = '".$_SESSION['UserID']."'
                   ";
            $result = mysql_query($sql) OR die ("<pre>\n".$sql."</pre>\n".mysql_error());
            $rights = array();
            // .. und als array zurückgegeben werden
            while($row = mysql_fetch_assoc($result))
                    $rights[] = $row['Recht'];
        }
        return $rights;
    }

    // Loggt einen User ein, ..
    function doLogin($ID, $Autologin=false)
    {
        // .. indem die aktuelle Session ID in der Datenbank gespeichert wird
        $sql = "UPDATE
                        User
                SET
                        SessionID = '".mysql_real_escape_string(session_id())."',
                        Autologin = NULL,
                        IP = '".$_SERVER['REMOTE_ADDR']."',
                        Letzte_Aktion = '".mysql_real_escape_string(time())."',
                        Letzter_Login = '".mysql_real_escape_string(time())."'
                WHERE
                        ID = '".$ID."'
                ";
        mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
        // Wenn 'eingeloggt bleiben' aktiviert wurde
        if($Autologin){
            // Zufallscode erzeugen
            $part_one = substr(time()-rand(100, 100000),5,10);
            $part_two = substr(time()-rand(100, 100000),-5);
            $Login_ID = md5($part_one.$part_two);
            // Code im Cookie speichern, 10 Jahre dürfte genügen
            setcookie("Autologin", $Login_ID, time()+60*60*24*365*10);
            $sql = "UPDATE
                            User
                    SET
                            Autologin = '".$Login_ID."'
                    WHERE
                            ID = '".$ID."'
                   ";
            mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
        }

        // Daten des Users in der Session speichern
        $sql = "SELECT
                        Nickname
                FROM
                        User
                WHERE
                        ID = '".$ID."'
               ";
        $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());

        $row = mysql_fetch_assoc($result);
        $_SESSION['UserID'] = $ID;
        $_SESSION['Nickname'] = $row['Nickname'];
        // Rechte in der Session speichern
        $_SESSION['Rechte'] = getRights();
    }
?>