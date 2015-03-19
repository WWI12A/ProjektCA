<?php
    error_reporting(E_ALL);
    include("mysql.php");
    include("functions.php");


    session_start();
    include("autologout.php");



    // $_GET-Parameter prüfen
    if(!isset($_GET['id'])) {
        echo "Sie haben keinen Benutzer ausgewählt.<br>\n".
              "Bitte benutzen Sie einen Link aus der <a href=\"userliste.php\">Userliste</a>\n";
    }
    else{
         // $_GET-Parameter als Integer casten
         $_GET['id'] = (int)$_GET['id'];
         $sql = "SELECT
                         SessionID,
                         Nickname,
                         Email,
                         Show_Email,
                         DATE_FORMAT(Registrierungsdatum, '%d.%m.%Y') as Datum,
                         Wohnort,
                         Homepage,
                         ICQ,
                         AIM,
                         YIM,
                         MSN,
                         Avatar,
                         Letzte_Aktion,
                         Letzter_Login
                 FROM
                     User
                 WHERE
                     ID = '".mysql_real_escape_string($_GET['id'])."'
                ";
    $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
         $row = mysql_fetch_assoc($result);
         if(!$row){
             echo "Sie haben keinen gültigen Benutzer ausgewählt.<br>\n".
                  "Bitte benutzen Sie einen Link aus der <b>»</b> <a href=\"/userliste.php/\">Userliste</a>\n";
         }
         else{
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
        echo "  <td>\n";
        echo "Wohnort :\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo htmlentities($row['Wohnort'], ENT_QUOTES)."\n";
        echo "  </td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "Homepage :\n";
        echo "  </td>\n";
        echo "  <td>\n";
        if (trim($row['Homepage'])!= ""){
          if (strtolower(substr($row['Homepage'], 0, 7)) =='http://')
            echo "<a href=\"".htmlentities($row['Homepage'], ENT_QUOTES)."\" target=\"_blank\">".htmlentities(shorten($row['Homepage']), ENT_QUOTES)."</a>\n";
          // Falls kein http:// eingegeben wurde wird es automatisch eingefügt, um einen gültigen Link zu erzeugen
          else
            echo "<a href=\"http://".htmlentities($row['Homepage'], ENT_QUOTES)."\" target=\"_blank\">".htmlentities(shorten($row['Homepage']), ENT_QUOTES)."</a>\n";
               }
        echo "  </td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "ICQ :\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo htmlentities($row['ICQ'], ENT_QUOTES)."\n";
        echo "  </td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "AIM :\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo htmlentities($row['AIM'], ENT_QUOTES)."\n";
        echo "  </td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "YIM :\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo htmlentities($row['YIM'], ENT_QUOTES)."\n";
        echo "  </td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "MSN :\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo htmlentities($row['MSN'], ENT_QUOTES)."\n";
        echo "  </td>\n";
        echo " </tr>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "Avatar :\n";
        echo "  </td>\n";
        echo "  <td>\n";
               if($row['Avatar']=='')
                   echo "Kein Avatar vorhanden.\n";
               else
                   echo "<img src=\"avatare/".htmlentities($row['Avatar'], ENT_QUOTES)."\">\n";
                 echo "  </td>\n";
        echo " </tr>\n";
        echo "</table>\n";
        }
    }
?>