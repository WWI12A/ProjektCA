<?php
    error_reporting(E_ALL);

    // Prüfen, ob ein Autologin des Users stattfinden muss
    if(isset($_COOKIE['Autologin']) AND !isset($_SESSION['UserID'])){
        $sql = "SELECT
                        ID
                FROM
                        User
                WHERE
                        Autologin = '".mysql_real_escape_string($_COOKIE['Autologin'])."'
               ";
        $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
        $row = mysql_fetch_assoc($result);
        if(mysql_num_rows($result) == 1)
            doLogin($row['ID'], '1');
    }

    // Online Status der User aktualisieren
    if(isset($_SESSION['UserID'])){
        $sql = "UPDATE
                        User
                SET
                        Letzte_Aktion = '".time()."'
                WHERE
                        ID = '".$_SESSION['UserID']."'
               ";
        mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
    }

    // User ohne Autologin ausloggen
    $sql = "UPDATE
                    User
            SET
                    SessionID = NULL,
                    Autologin = NULL,
                    IP = NULL
            WHERE
                    '".(time()-60*20)."' > Letzte_Aktion AND
                    Autologin IS NULL
           ";
    mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());

    // Kontrollieren, ob ein automatisch ausgeloggter User noch eine gültige Session besitzt
    if(isset($_SESSION['UserID'])){
        $sql = "SELECT
                        SessionID
                FROM
                        User
                WHERE
                        ID = '".$_SESSION['UserID']."'
               ";
        $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
        $row = mysql_fetch_assoc($result);
        if(!$row['SessionID']){
            $_SESSION = array();
            session_destroy();
        }
    }
?>