<?php
    define ('MYSQL_HOST', 'localhost');
    define ('MYSQL_USER', 'root');
    define ('MYSQL_PASS', 'MaxHeikoTim');
    define ('MYSQL_DATA', 'loginsystem');
    $connid = @mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR die("Error: ".mysql_error());
    $sql = "CREATE DATABASE IF NOT EXISTS `".MYSQL_DATA."`
            DEFAULT CHARACTER SET latin1
            COLLATE latin1_general_ci
           ";
    if(!mysql_query($sql)){
                  echo "<p>MySQL Datenbank ".MYSQL_DATA." konnte nicht erzeugt werden.</p>";
                  echo "<h2>Query</h2>\n";
                  echo "<pre>".$sql."</pre>\n";
                  echo "<h2>Fehlermeldung</h2>";
                  echo "<p>".mysql_error()."</p>";
                  die();
    }
    mysql_select_db(MYSQL_DATA) OR die("Error: ".mysql_error());
?>