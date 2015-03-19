<?php
    error_reporting(E_ALL);
    include("mysql.php");

    session_start();

    echo "<table>";
    echo " <tr>\n";
    echo "  <td>\n";
    echo "Nickname\n";
    echo "  </td>\n";
    echo "  <td>\n";
    echo "Registrierungsdatum\n";
    echo "  </td>\n";
    echo "  <td>\n";
    echo "Letzter Login\n";
    echo "  </td>\n";
    echo "  <td>\n";
    echo " \n";
    echo "  </td>\n";
    echo " </tr>\n";

    $sql = "SELECT
                    ID,
                    SessionID,
                    Nickname,
                    DATE_FORMAT(Registrierungsdatum, '%d.%m.%Y') as Datum,
                    Letzter_Login,
                    Letzte_Aktion
            FROM
                    User
            ORDER BY
                    Nickname ASC
           ";
    $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        // existiert eine Session ID und der User
        // war nicht länger als 2 Minuten inaktiv, so wird er als online betrachtet
        if($row['SessionID'] AND (time()-60*2 < $row['Letzte_Aktion']))
            $online = "<span style=\"color:green\">online</span>\n";
        else
            $online = "<span style=\"color:red\">offline</span>\n";
        echo " <tr>\n";
        echo "  <td>\n";
        echo "<a href=\"profil.php?id=".$row['ID']."\">".$row['Nickname']."</a>\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo $row['Datum']."\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo date('d.m.Y H:i \U\h\r', $row['Letzter_Login'])."\n";
        echo "  </td>\n";
        echo "  <td>\n";
        echo $online;
        echo "  </td>\n";
        echo " </tr>\n";
    }
    echo "</table>";
?>